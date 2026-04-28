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
