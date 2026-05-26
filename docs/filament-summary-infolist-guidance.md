# Filament Infolist — step riepilogo wizard (Fixcity)

## Decisione architetturale

- Lo step **`form.summary::data::wizard-step`** (deeplink anche come `?step=form.summary%3A%3Adata%3A%3Awizard-step`) deve mostrare **dati già compilati**, non input.
- In **Filament v5** questo pattern è **`Filament\Infolists\Components\TextEntry`** (ed entry affini) **dentro il flusso schema** del Wizard, NON `TextInput::disabled()`, NON `Placeholder`, NON [`SchemaView`](https://filamentphp.com/docs/5.x/schemas/overview).
- Doc ufficiale Infolists: https://filamentphp.com/docs/5.x/infolists/overview · schema unificato: https://filamentphp.com/docs/5.x/schemas/overview

## Dove sta il codice (SSoT)

| File | Ruolo |
|------|--------|
| [`TicketFormReviewInfolist.php`](../app/Filament/Resources/TicketResource/Schemas/TicketFormReviewInfolist.php) | Costruisce le `TextEntry` con stato via `state(fn (Get $get): …)`, prefissi **`review_*`**, formattazioni enum/geo/allegati. |
| [`TicketForm.php`](../app/Filament/Resources/TicketResource/Schemas/TicketForm.php) | Assembla il wizard; `getSummarySectionSchema()` delega gli entry read-only tramite **`array_values(...)`** (lista ordinata compatibile PHPStan/`Section::schema()`). |
| [`summary.blade.php`](../resources/views/filament/widgets/wizard/steps/summary.blade.php) | Parity Blade riepilogo (se ancora inclusa nel flusso): stesse chiavi **`fixcity::ticket.*`**, mai `segnalazione` sul dominio modulo Filament/ticket. |

## Namespace traduzioni (heading sezione)

- **Titoli `Section`** dello schema (`TicketForm::getSummarySchema()`) → **`fixcity::ticket.sections.*`** (`lang/*/ticket.php`). Il contesto è il **bounded context Filament + modello `Ticket`**: stesse chiavi usate dalla resource (create/edit) e dal wizard che riusa `TicketForm`.
- **`fixcity::segnalazione.*`** serve al **canale pubblico** (copy Design Comuni, pagine `segnalazione-*`, testi nei blocchi CMS). Non è la SSoT per gli heading tecnici delle `Section` ticket: usando `ticket` si evita drift e si rispetta la “religione” namespace-per-dominio (vedi [`translation-namespace-religion.md`](../../../../docs/translation-namespace-religion.md) e [Fixcity: `ticket` vs `segnalazione`](./wiki/concepts/fixcity-ticket-vs-segnalazione-lang.md)).

**Esempi**

| Dove | Chiave heading |
|------|----------------|
| `TicketForm::getSummarySchema()` (Section riepilogo) | **`__('fixcity::ticket.sections.summary.label')`** |
| Parity HTML pagina pubblica `segnalazione-03-riepilogo` | `fixcity::segnalazione.*` (solo layer pubblico/marketing) |

## Regole operative

1. **MAI** `->label()`, `->placeholder()`, `->helperText()` sugli entry: **AutoLabel** risolve **`fixcity::ticket_form.fields.<nome>.`** (vedi `lang/*/ticket_form.php`).
2. Prefisso **`review_*`** sul blocco recap (location, type, priority, name, content, images). **Autore e contatti** nello stesso step sono **`TextInput`** in `TicketForm` (`getAuthorSectionSchema` / `getContactsSectionSchema`): sono dati da raccogliere, non da mostrare come Infolist.
3. Enum leggibili: `formatTicketTypeDisplay` / `formatTicketPriorityDisplay` usando `TicketTypeEnum` / `TicketPriorityEnum` + `tryFrom` sullo stato wizard (campo `type`/`priority`).
4. `review_content`: `->prose()` per contenuto lungo leggibile nel riepilogo.
5. `review_images`: conteggio testuale tramite **`fixcity::ticket_form.summaries.*`** (`trans_choice`); anteprima binary non è richiesta allo step wizard (persistenza dopo submit gestisce Media Library).

## Riferimenti incrociati

- Pattern generico modulo Xot: [infolists-for-summary.md](../../Xot/docs/filament/widgets/infolists-for-summary.md).
- Smoke E2E: `Modules/Fixcity/tests/Playwright/segnalazione-crea-wizard.spec.js` — URL canonico summary + assert `.fi-in-entry`.
- Tema Sixteen parity visiva riepilogo: [wizard-review-parity.md](../../../Themes/Sixteen/docs/wiki/design/wizard-review-parity.md).
