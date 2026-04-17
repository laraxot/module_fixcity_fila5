# Filament Wizard Rules

## Rule: docs-first before runtime edits

- Prima di modificare widget, Blade, CSS o traduzioni del wizard, aggiornare e consolidare i docs modulo/tema gia esistenti.
- Usare sempre gli indici (`README.md`, `INDEX.md`, stories index, docs tema) per evitare nuovi file doppi o semantiche replicate.
- Se una regola esiste gia in forma parziale, estenderla invece di crearne una seconda versione quasi uguale.

### Motivazione (DRY + KISS)

- gli stessi task vengono spesso assegnati a piu agenti in parallelo;
- senza docs-first si generano fix divergenti, storie duplicate e regole in conflitto;
- la knowledge base deve diventare piu chiara ad ogni intervento, non piu rumorosa.

## Rule: concurrent-agents safe behavior

- Assumere sempre che altri agenti possano lavorare sullo stesso task nello stesso momento.
- Preferire modifiche piccole, tracciabili e facilmente mergeabili.
- Non spostare o riscrivere grandi blocchi se basta una correzione locale.
- Prima di introdurre un nuovo file doc, verificare se esiste gia un documento canonico da aggiornare.

## Rule: Wizard Widget Base Class

**SEMPRE** estendere `XotBaseWizardWidget` per widget wizard multi-step.
**MAI** estendere `XotBaseWidget` direttamente per wizard.

### Motivazione (Zen)

- **Single Responsibility**: XotBaseWidget = form singolo step; XotBaseWizardWidget = multi-step
- **DRY**: Logica navigazione step, persistenza `?step=`, normalizzazione stato in un solo posto
- **Sicurezza**: Override `?step=` controllato (solo local/debug o esplicita config)

### Esempio

```php
// ✅ CORRETTO
class CreateTicketWizardWidget extends XotBaseWizardWidget { ... }

// ❌ SBAGLIATO
class CreateTicketWizardWidget extends XotBaseWidget { ... }
```

## Rule: Wizard Schema Structure

Usare `Wizard::make([Step::make(), ...])` dentro `getFormSchema()`.

Ogni step definisce i propri campi Filament (`TextInput`, `Select`, ecc.).

### Rule: `Select::options()` con enum backed (`HasLabel`)

**Contratto Filament** (`Filament\Forms\Components\Concerns\HasOptions`): passare il **nome classe enum come stringa**, non `::cases()` né `collect(...)->reduce()` / `mapWithKeys` manuali, se l’enum implementa `Filament\Support\Contracts\HasLabel` (o si accettano le label `name`).

```php
// ✅ CORRETTO — una riga, stesso mapping che farebbe a mano (value => getLabel())
Select::make('type_id')->options(TicketTypeEnum::class)

// ❌ SBAGLIATO — `cases()` restituisce array PHP: non ha `->reduce()`; anche `->options($enum::cases())` rompe transformOptionsForJs (label = Enum)
// ❌ RIDONDANTE — collect(TicketTypeEnum::cases())->mapWithKeys(...) duplica HasOptions::getOptions()
```

**Perché (filosofia / zen)**:

- **DRY**: la riduzione `cases → [value => label]` è già centralizzata in Filament; reinventarla è rumore e rischio drift.
- **KISS**: `TicketTypeEnum::class` è il contratto documentato; meno codice, meno bug.
- **Tipo**: `options(TicketTypeEnum::cases())` passa indici `0,1,2` con valori `Enum`, e il Select interpreta male chiavi/etichette → `TypeError` in `isOptionDisabled`.
- **Politica modulo**: stesso pattern di `TicketResource` e degli altri Select enum nel monorepo.

### Rule: Summary step via Infolist

- Lo step finale di review/riepilogo deve usare componenti Infolist (`TextEntry`, ecc.) nello `Step::schema()`.
- Per leggere lo stato del wizard usare `->state(fn (Get $get) => ...)`.
- Usare nomi entry `review_*` per evitare collisioni con i campi editabili.
- Anche negli entry Infolist vale la regola: no `->label()` e no `->placeholder()` hardcoded.
- Evitare `SchemaView` come soluzione primaria del riepilogo: accettabile solo per casi visuali eccezionali, documentati.
- Per `ImageEntry` con limite immagini usare API Filament supportate: `->limit()` + `->limitedRemainingText()`.
- Non usare `->limitMessage()` su `ImageEntry`: il metodo non esiste in Filament Infolists v5.

### Rule: Infolist is not a universal Placeholder replacement

- `Placeholder`, `Text`, `View` e `Infolist` non sono intercambiabili per definizione.
- Usare **Infolist** quando il contenuto e read-only strutturato come dato o summary.
- Usare **Schemas `Text` / `View` / contenuto read-only** quando il contenuto vive dentro un `Form Wizard` ma non e un dataset label/value.
- Non inserire componenti `Filament\Infolists\Components\*` dentro `getDataSchema()` o altri schema di form solo per uniformita visiva: puo degradare il render path e rompe la semantica del componente.

### Filosofia / zen

- `Form Wizard` = raccolta input + contenuto di supporto al task.
- `Infolist` = lettura strutturata di dati gia noti.
- Se si forza un `TextEntry` dentro uno schema form, non si sta "semplificando": si stanno mescolando due religioni UI diverse.

### Rule: runtime class guard (anti-regressione)

- Ogni modifica al wizard Fixcity deve eseguire `composer run-script guard:fixcity-wizard` dalla cartella `laravel/`.
- Il guard blocca simboli/regressioni che hanno gia' causato fatal runtime:
  - `InfolistSection::make(...)`
  - fallback a namespace locale `Modules\Fixcity\Filament\Widgets\*` per componenti Filament
  - assenza di entry Infolist (`TextEntry`/`ImageEntry`) nel riepilogo
- Script: `../../../../bashscripts/quality/check-fixcity-wizard-guards.sh`

### Submit Action

- `submitAction` resta centralizzato in `XotBaseWizardWidget::getWizardSubmitAction()`.
- Il widget dominio non deve overrideare `getWizardSubmitAction()`.
- Eventuali variazioni visuali passano dal tema (`pub_theme::filament.wizard.submit-button`) o dalla base.

### Step Navigation

- `->nextAction()` per bottone "Avanti"
- `->previousAction()` per bottone "Indietro"
- `->startOnStep(fn () => $this->wizardStartStep)` per step iniziale dinamamico

### Rule: no label/tooltip hardcoded

- **MAI** usare `->label()` e `->tooltip()` nel widget dominio per campi/azioni standard.
- Le etichette sono risolte dal sistema traduzioni (`LangServiceProvider` / AutoLabel) o dai default Filament localizzati.
- Se serve una variazione, preferire il layer base (`XotBaseWizardWidget`) o view dedicate, non override sparsi nel widget.

### Rule: no locale literals in runtime code

- In un progetto multilingua, nessuna stringa UI in lingua naturale va hardcodata nel PHP runtime.
- Anche `Section::make('...')`, `description('...')` e ogni testo runtime dei componenti devono usare chiavi `__('modulo::...')`.
- Le stringhe utente vivono solo nei file `lang/<locale>/`.

### Rule: route names for localized redirects

- I redirect del wizard devono usare route name (`route('tests.view', ...)`) e non path concatenati a mano.
- Gli slug di destinazione sono dati di contenuto: leggerli da CMS/config (`confirmation_slug`), non scriverli dentro il widget.
- Questo evita di congelare termini italiani nel PHP runtime e mantiene separati routing, contenuto e traduzioni.

## Rule: Submit Handler

Nel metodo `submit()`:

1. Usare `$this->form->getState()` (validazione Filament integrata)
2. Preferire campi gia' allineati al model (`name/content/type`) invece di alias widget-specifici
3. Persistere e dispatchare evento dominio
4. Redirect alla pagina di conferma
5. Gestire errori con `addError()` + `Notification::danger()`

### Rule: no custom payload layer in simple create wizard

- Se il caso d'uso e' "create record a step", preferire form-state diretto Filament.
- Evitare metodi payload dedicati quando il mapping e' banale e locale al submit.
- Consentite solo mutazioni minime e leggibili prima del `create()`.
- I campi solo UX vanno marcati `dehydrated(false)`, non rimossi con `unset()`.
- Upload/relazioni Filament vanno persistiti con `saveRelationships()` dopo il `create()`.

### Rule: no pass-through methods

- Evitare metodi wrapper a una riga senza semantica aggiunta (code smell "Middle Man").
- Se un metodo fa solo `return Foo::create($this->bar($data));` senza policy, side-effect o naming di dominio reale, va rimosso e inlined.
- Creare metodi solo quando aggiungono:
  - semantica di business,
  - policy di validazione/normalizzazione,
  - isolamento di side effect non banali.

### Rule: no local Log::error nel wizard frontoffice

- Nel widget frontoffice evitare `Log::error()` locale su eccezioni gestibili.
- Per UX usare `addError()` + `Notification::danger()`.
- Il logging applicativo globale resta responsabilita di infrastruttura/osservabilita, non del widget UI.

## Rule: Custom Components

Per componenti custom dentro gli step, preferire **Filament form components** del modulo specializzato.

**✅ CORRETTO** - Filament component:
```php
use Modules\Geo\Filament\Forms\Components\AddressInput;

AddressInput::make('address')
    ->required()
```

**❌ SBAGLIATO** - Blade::render:
```php
Placeholder::make('name')
    ->content(new HtmlString(\Blade::render('view::path')))
```

## Rule: Geolocation Component

La geolocalizzazione appartiene al **modulo Geo** come Filament Form Component.

**Component**: `Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput`
**Classe**: `laravel/Modules/Geo/app/Filament/Forms/Components/LatitudeLongitudeInput.php`
**View**: `laravel/Modules/Geo/resources/views/filament/forms/components/latitude-longitude-input.blade.php`

Usage in wizard:
```php
use Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput;

LatitudeLongitudeInput::make('location')
    ->defaultCenter(41.9028, 12.4964)
    ->defaultZoom(13)
    ->mapHeight('340px')
    ->showMap(true),
```

**Zen**: 
- **Domain-Driven Design**: Ogni modulo possiede il suo dominio. Geo = geolocalizzazione
- **Filament Way**: Componenti form propri che si integrano con Livewire state
- **Single Responsibility**: Geolocation è geo-spaziale, non logica ticket
- **Reusability**: Qualsiasi modulo può consumare `LatitudeLongitudeInput` senza copiare codice
- **Data Precision**: Salva direttamente le coordinate latitude/longitude nel field state annidato

### Rule: no destructive live sync on interactive map drag

- Nei field mappa interattivi, `drag` del marker non deve generare persistenza Livewire aggressiva.
- Il sync continuo durante il drag deve restare **locale** al componente.
- La persistenza verso Livewire va eseguita su eventi stabili (`dragend`, click mappa, geolocalizzazione, `change` input).
- Se il field espone input numerici visibili, questi devono usare un binding non distruttivo per JS interop, preferendo `wire:model.change` rispetto a `wire:model.live` quando il valore viene mosso anche da Leaflet.

**Perché**:
- evita refresh distruttivi e perdita della shell `wire:ignore`;
- mantiene marker, input e center nella stessa verità;
- preserva l'integrazione Filament/Livewire senza degradare UX mobile.

## Rule: Blade wrapper only, Wizard state never manual

- La view Blade del widget può aggiungere titolo, sidebar editoriale, wrapper parity e pulsanti custom.
- La view Blade **non** deve essere la fonte di verità dello step corrente.
- Lo step corrente appartiene al `Wizard` Filament e ai suoi hook (`startOnStep`, `persistStepInQueryString`, `nextStep`, `previousStep`).
- Se serve parity sullo stepper, si preferisce lo styling dell'header Filament o un layer visuale che non sostituisca la navigazione del Wizard.

**Perché**:
- **Regola**: una sola state machine per i passi.
- **Visione**: Blade descrive il frame, Filament governa il flusso.
- **Politica**: niente doppia verità tra query string, Livewire e markup.
- **Zen**: parity sì, reimplementazione no.

## Rule: Translations

Tutte le label wizard nel namespace `modulo::segnalazione.*`:
- `steps.<step_key>.label`
- `actions.<action_key>.label`
- `fields.<field_key>.label`
- `geolocation.*` per messaggi geolocalizzazione

### Rule: context-first translation prototype

- La chiave traduzione deve riflettere il dominio del dato, non il widget che la visualizza.
- Se il testo riguarda immagini ticket, usare contesto `ticket.*` e un tipo finale esplicito (es. `fixcity::ticket.messages.images_uploaded.text`).
- Evitare chiavi duplicate semantiche in `create_ticket_wizard.*` quando la stessa regola e' di dominio ticket.
- Mantenere prototipo completo a 5 elementi semantici: `namespace::context.collection.element.type`
  - esempio corretto: `fixcity::ticket.messages.images_uploaded.text`
  - esempio sbagliato: `fixcity::ticket.messages.no_images`
  - esempio sbagliato: `fixcity::ticket.rules.image.empty_message`
