---
title: "Ticket location not saved — mutator Eloquent mancante"
type: troubleshooting
confidence: high
created: 2026-04-28
updated: 2026-04-28
tags: [ticket, location, geo, filament, admin, eloquent, mutator, coordinate-picker]
related:
  - ../concepts/admin-ticket-create-map-visual-contract.md
  - ../concepts/location-capture-map-wizard.md
  - ../../../Geo/docs/wiki/concepts/coordinate-picker-filament5-save-pattern.md
---

## Scopo business

Se la **location** non viene salvata, la segnalazione perde il suo "dove" operativo: peggiora lo smistamento, aumenta l'ambiguità e riduce il valore territoriale del dato.

## Sintomo

- In `/fixcity/admin/tickets` (creazione/modifica), la mappa permette di selezionare un punto, ma dopo il salvataggio:
  - `tickets.latitude` / `tickets.longitude` risultano `null`,
  - i campi derivati (`address`, ecc.) non sono persistiti.

## Root cause (reale — corretto in story 8-64)

> **Nota**: la diagnosi originale di questo documento era errata (indicava `location` mancante da `$fillable`). La causa reale è diversa.

`CoordinatePicker::make('location')` gestisce il proprio stato come **array composito**:

```php
['location' => ['latitude' => 41.9, 'longitude' => 12.4, 'address' => '...']]
```

Filament chiama `$model->fill(['location' => [...]])`. Il modello `Ticket` ha colonne **separate** `latitude` e `longitude` — **non esiste una colonna `location`** nel DB.

Senza un mutator Eloquent, Eloquent tenta di scrivere `location` come colonna (`'location' => 'array'` nel cast) su un campo che non esiste → i dati vengono **silenziosamente scartati**.

## Fix applicato (story 8-64)

**Rimosso** da `Ticket::casts()`:
```php
// 'location' => 'array'  ← RIMOSSO — nessuna colonna location nel DB
```

**Aggiunto** a `Ticket.php`:
```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function location(): Attribute
{
    return Attribute::set(function (mixed $value): array {
        if (! is_array($value)) {
            return [];
        }

        return [
            'latitude'  => isset($value['latitude'])  ? (string) $value['latitude']  : null,
            'longitude' => isset($value['longitude']) ? (string) $value['longitude'] : null,
        ];
    });
}
```

Il mutator intercetta `fill(['location' => [...]])` e smista i valori nelle colonne reali.

## Anti-pattern

- "`location` manca da `$fillable`": **no**, `location` era già in `$fillable`. Il problema era l'assenza del mutator.
- Usare `mutateFormDataBeforeCreate` / `mutateFormDataBeforeSave`: risolve solo per le Page class Filament, non per frontoffice né API future.
- Lasciare `'location' => 'array'` in `casts()` quando non esiste la colonna `location` nel DB: causa scrittura silente su colonna inesistente.

## Pattern di riferimento

Vedi `laravel/Modules/Geo/docs/wiki/concepts/coordinate-picker-filament5-save-pattern.md` e la regola permanente `bashscripts/ai/.claude/rules/coordinatepicker-multi-column-save.md`.

## File coinvolti

- `laravel/Modules/Fixcity/app/Models/Ticket.php` — mutator `location()` aggiunto
- `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` — usa `CoordinatePicker::make('location')`
- `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` — frontoffice (ha `prepareTicketData()` come strato aggiuntivo, non conflittuale)

## Aggiornamento 2026-04-28 — draft frontoffice

Nel flusso frontoffice `saveDraft()`, il picker invia chiavi `lat` / `lng` dentro `location`.
`prepareTicketData()` deve quindi:

- leggere sia `lat` / `lng` sia `latitude` / `longitude`;
- cercare `location` anche in modo ricorsivo nello stato Filament, perche' il Wizard puo' annidare i campi in container/step;
- copiare i valori su `latitude` / `longitude`;
- rimuovere `location` prima di `Ticket::create()`, perche' la tabella `tickets` non ha una colonna `location`.

Il mutator `Ticket::location()` resta utile per i form Filament admin che fanno `fill(['location' => [...]])`, ma deve restituire solo colonne reali.

## Aggiornamento 2026-04-28 — story 8-59 location JSON canonica

La decisione architetturale e' cambiata: la tabella `tickets` deve avere una colonna JSON nullable `location`.
Da questo punto in avanti `location` e' la source of truth per i nuovi salvataggi del wizard, mentre `latitude` e `longitude` restano solo mirror legacy/backward-compatible.

Il flusso frontoffice deve quindi:

- mantenere `location` nel payload di `Ticket::create()`;
- normalizzare `lat` / `lng` e anche `latitude` / `longitude` dentro `location`;
- non fare piu' `unset($state['location'])`;
- salvare draft e submit con lo stesso mapping;
- usare il mutator `Ticket::location()` per persistere il JSON e, se necessario, aggiornare anche le colonne legacy.

La regola precedente "la tabella `tickets` non ha una colonna `location`" resta solo come contesto storico del bug, non come contratto corrente.

## Aggiornamento 2026-04-28 — bridge Lit/Livewire

Il tema Sixteen importa `coordinate-picker-lit-stable.js`, che emette l'evento:

```js
detail: { latitude, longitude, source }
```

La Blade `Geo/resources/views/filament/forms/components/coordinate-picker.blade.php` deve quindi accettare sia `{lat, lng}` sia `{latitude, longitude}`.
Inoltre deve sincronizzare esplicitamente Livewire con `$wire.set(...)`, non limitarsi ad aggiornare lo stato Alpine entangled, altrimenti al click su `saveDraft()` il backend puo' ricevere `location` ancora vuoto.
