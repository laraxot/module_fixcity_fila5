---
title: "CreateTicketWizardWidget"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-10-13
qmd: "CreateTicketWizardWidget"
issues: []
discussions: []
status: draft
---

# CreateTicketWizardWidget Documentation

**Path**: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`  
**Base**: `XotBaseWizardWidget`  
**Purpose**: Multi-step frontoffice ticket creation.

---
## 🏛 Architecture (The Zen)

The widget follows the **Laraxot Convention-over-Configuration** (Religion):

1.  **Step Generation** (verificato 2026-10-13, DOCS-ARRETRATO DCP-44): il widget non costruisce gli step. `CreateTicketWizardWidget::getSteps()` restituisce `app(TicketForm::class)->getSteps()`. `TicketForm::getSteps()` crea `privacy` e `data` con `static::getStepByName('privacy')` e `static::getStepByName('data')` (`XotBaseResourceForm::getStepByName()`, `protected static`: mappa il nome su `get{Name}Schema()` e, se il metodo esiste, ne usa l'array come schema dello step) e `summary` con `Step::make('summary')->schema(array_values(static::getReviewStepSchema()))` (DCP-33). Le etichette sono `fixcity::fixcity.ticket.steps.{auth|data|summary}.label`; `getSteps()` non chiama `->description()` (le chiavi `description` esistono in `lang/it/fixcity.php` ma non sono applicate; `->description()` e' comunque disponibile come nella [Filament doc “Using a wizard”](https://filamentphp.com/docs/5.x/resources/creating-records#using-a-wizard)). Le chiavi `fixcity::ticket_wizard.steps.*` sono in `lang/*/ticket_wizard.php` ma nessun codice di `Fixcity/app`, `Fixcity/resources`, `Fixcity/tests`, `Sixteen/resources` e `Xot/app` le legge.
2.  **Schema Definition**:
    - `TicketForm` (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`) e' la sorgente canonica dello schema ticket.
    - Gli schemi sono metodi `public static` senza argomenti di `TicketForm`: `getPrivacySchema()`, `getDataSchema()`, `getReviewStepSchema()` (riepilogo front office, DCP-33) e `getSummarySchema()` (riepilogo piatto, usato da `getFormSchema()` per il back office). `CreateTicketWizardWidget` non dichiara nessuno di questi metodi (`grep -n "function " app/Filament/Widgets/CreateTicketWizardWidget.php`: `mount`, `getSteps`, `hasSkippableSteps`, `save`, `submit`, `redirectAfterSuccess`, `getViewData`).
    - Il link privacy non e' un parametro: `TicketForm::getPrivacySchema()` usa `getGdprHtml()`, che legge la policy pubblicata (`GetPublishedPrivacyPolicyAction`) e calcola gli URL con `LaravelLocalization::getLocalizedURL(app()->getLocale(), '/privacy')`. Non esiste `getFrontofficePrivacySchema()` (nessuna `function` con questo nome in `Modules/Fixcity/app` e `Modules/Xot/app`) e `privacy_link` e' solo una chiave di traduzione (`lang/it/ticket.php`, `lang/en/ticket_gdpr_notice.php`), non un parametro di `blockData`.
    - I parametri runtime del blocco CMS (`blockData`) restano nel widget (`mount()`, `getViewData()`, `redirectAfterSuccess()` con `confirmation_path`); gli step li leggono dal Livewire, ad esempio `getReviewStepSchema()` passa `data_get($livewire, 'blockData', [])` alla vista `pub_theme::components.wizard.summary-step`. `getSteps()` non riceve parametri.
    - Il widget non deve ridefinire campi ticket gia' presenti in `TicketForm`: eventuali modifiche a privacy, data step o summary partono da `TicketForm`.
3.  **Submission**: `submit()` usa **`$this->form->getState()`** tale e quale (dehydrate Filament + forma definita da `TicketForm`/schema); merge **`owner_id`** solo se l'utente è autenticato (`??=`); poi `Ticket::create($data)`. Il frontoffice non dipende dal Resource Filament né dal relativo hook di normalizzazione backoffice.
4.  **Tipologia (`type`)**: `TicketForm` usa `Select::make('type')->options(TicketTypeEnum::class)` — Filament (`HasOptions`) costruisce `[value => getLabel()]`. **IMPORTANTE**: Mai usare `TicketTypeEnum::cases()` come `options`: non è il contratto previsto da Filament.
5.  **Auto-Labeling**: No `->label()` calls are used. Translations sono risolte via `LangServiceProvider` usando le chiavi `fixcity::create_ticket_wizard.fields.{name}.label` e `fixcity::segnalazione.fields.*` dove applicabile.

---

## 🧘 Religion & Rules

- **DRY**: Nessun helper intermedio sul payload tra `getState()` e `create`: la forma delle chiavi è responsabilità di **schema / dehydrate Filament**, non del widget.
- **KISS**: No metodi che riscrirono lo stato dopo `getState()`. Il dominio usa il contratto Filament così com’è salvo merge esplicito di campi auth.
- **Unified schemas, distinct semantics**: in un wizard Filament v5 si possono mescolare `Forms`, `Infolists` e `Schemas`, ma ogni famiglia va usata per il proprio scopo.
- **Component semantics**:
  - dati read-only strutturati -> `Infolists` (`TextEntry`, `ImageEntry`, ...)
  - testo statico / notice / microcopy -> `Schemas` prime (`Text`, `Image`, ...)
  - input utente -> `Forms`
- **Anti-loop naming**: i campi review usano naming `review_*` per evitare collisioni nello stato con `Get`.
- **Namespace safety**: import allineati al package corretto (`Schemas` vs `Infolists`) per evitare class resolution fallita.
- **Guard command**: run `composer run-script guard:fixcity-wizard` from `laravel/` before considering the widget stable.
- **Render safety first**: semantic upgrades are valid only if the frontoffice wizard still passes a real HTTP smoke check. For create flows, model binding, mount filling, and pre-submit media rendering must stay conservative.
- **Clean Code**: Strict typing, single responsibility methods, and clear naming.
- **Progress feedback**: per `AddressInput`, la geolocalizzazione espone loading state (spinner + busy attributes). Per `LeafletMarkerMapInput`, il focus è mappa + marker; il pulsante “posizione corrente” usa l’API browser senza reverse geocoding obbligatorio.
- **HTML parity strategy**: Data step mirrors Design Comuni grouping (place/disservice/author) so hierarchy and cognitive flow remain equivalent to the reference page.
- **Privacy step semantics**: the legal privacy text is first-class read-only content, not checkbox helper text.
- **Privacy parity rule**: if the reference Design Comuni step contains GDPR copy before the checkbox, the local wizard must expose equivalent first-class content before consent. A checkbox without context is not acceptable parity.

## Placeholder Policy

- `Filament\\Forms\\Components\\Placeholder` e deprecated in Filament 5.x.
- Nel wizard non va piu usato come default.
- La migrazione corretta dipende dal contenuto:
  - `Placeholder` usato come dato read-only -> `TextEntry`
  - `Placeholder` usato come testo/HTML statico -> `Text`

Questa distinzione evita il falso dogma "tutto diventa Infolist" e mantiene chiara la semantica UI.

## Process Discipline

- Prima di modificare il widget, aggiornare e riallineare docs/memory/rules canoniche dell'area.
- Evitare nuovi file docs se una regola può vivere meglio in una memoria o documento già esistente.
- Lavorare assumendo agenti paralleli: cambi piccoli, espliciti, facili da fondere.
- `TicketForm` è il riferimento canonico per lo schema del ticket wizard. Il widget frontoffice deve delegare a `TicketForm` e non duplicare campi o summary.
- Le firme convenzionali `get{Name}Schema()` (`getPrivacySchema()`, `getDataSchema()`) restano `public static` senza argomenti perché `XotBaseResourceForm::getStepByName()` le invoca come `static::$methodName()`. (Esiste anche `XotBaseWidget::getStepByName()`, di istanza, che invoca `$this->get{Name}Schema()` sul widget: `CreateTicketWizardWidget` non la usa.) Oggi nessun passo riceve parametri runtime: lo stato arriva dal Livewire (`$livewire->data`, `$livewire->blockData`, come in `getReviewStepSchema()`). Un eventuale metodo con argomenti andrebbe cablato in modo esplicito in `TicketForm::getSteps()`, non scoperto per convenzione; `getFrontofficePrivacySchema()` non e' mai esistito.
- Non introdurre micro-helper tipo `authorTextEntry()` per tre entry leggibili: sarebbe un'astrazione cosmetica, non DRY utile. Il DRY corretto è condividere il provider schema (`TicketForm`) o isolare un vero comportamento riusabile, non nascondere tre righe dichiarative.
- La differenza tra admin resource e widget frontoffice deve rimanere esplicita: lo schema comune vive in `TicketForm`; stato Livewire, dati autenticati, redirect, submit e payload persistence restano nel widget.
- Non dichiarare `protected string $view` nel widget se il nome segue la convenzione: `XotBaseWidget` calcola `pub_theme::filament.widgets.create-ticket-wizard` e poi il fallback `fixcity::filament.widgets.create-ticket-wizard`. Nel PHP può restare solo un commento di promemoria.

---

## 🎨 Frontend Integration

- **View**: auto-risolta da `XotBaseWidget` / `GetViewByClassAction`: `pub_theme::filament.widgets.create-ticket-wizard`, fallback `fixcity::filament.widgets.create-ticket-wizard`.
- **Theme**: Sixteen (Design Comuni styling applied via CSS scoping).
- **Auth**: correzione verificata 2026-10-13: `tickets.create.json` non dichiara `middleware` in nessuna delle tre copie e un ospite riceve `200` su `/it/tickets/create`; il wizard è visibile e `fixcity::ticket.auth.required` compare solo all'invio (`CreateTicketWizardWidget::save`). Se si ripristina `auth` va messo nel JSON (alias `auth`, mai `web`: è già nello stack HTTP/Folio).
  - Aggiornamento 14:05 UTC: `"middleware": ["auth"]` ripristinato nel JSON `local` (D17, DCP-42); ospite -> 302 al login. `save()` e `CreateTicketAction` continuano a rifiutare un ticket senza owner (difesa in profondita').
- **Redirect**: Localized redirect to the confirmation page defined in `blockData['confirmation_slug']`.

---

## 📚 Related

- [Filament Wizard Pattern](./filament-wizard-pattern.md)
- [XotBaseWizardWidget](../../Xot/docs/filament/widgets/xot-base-wizard-widget-philosophy.md)
- [Infolists for Summary](../../Xot/docs/filament/widgets/infolists-for-summary.md)
- [Schemas Unified Religion](../../../../docs/schemas-unified-religion.md)
- [Filament Schemas Overview](https://filamentphp.com/docs/5.x/schemas/overview)
- [Filament Prime Components](https://filamentphp.com/docs/5.x/schemas/primes)
- [7-47 segnalazione-crea step1 privacy notice parity](../../../../_bmad-output/implementation-artifacts/7-47-segnalazione-crea-step1-privacy-notice-design-comuni-parity.md)
- [7-48 segnalazione-crea step2 visual parity via sections and infolist](../../../../_bmad-output/implementation-artifacts/7-48-segnalazione-crea-step2-visual-parity-via-sections-and-infolist.md)

## Update: Summary Infolist Migration

- Implementation commit: see [_bmad-output/implementation-artifacts/9-01-summary-infolist-migration.md](/_bmad-output/implementation-artifacts/9-01-summary-infolist-migration.md)
- Purpose: migrate getSummarySchema() to use Filament Infolist components (TextEntry/ImageEntry) mapped via Get utilities to wizard state.
- QA: Syntax-checked and smoke-tested locally; please run UI smoke tests and update screenshots in docs if needed.
# CreateTicketWizardWidget

## Livewire/Filament schema protocol

The widget is a Filament schema component hosted inside a Livewire component.
Filament schema controls call `callSchemaComponentMethod()` for wizard
navigation and interactive fields such as the coordinate picker.

The method is redeclared in `Modules\Xot\Filament\Widgets\XotBaseWizardWidget`
and delegates to `parent::`. Correzione verificata 2026-10-13: questa
ridichiarazione è un no-op e non è la causa di `MethodNotFoundException`, perché
`callSchemaComponentMethod()` è già `public` in
`Filament\Schemas\Concerns\InteractsWithSchemas`
(`vendor/filament/schemas/src/Concerns/InteractsWithSchemas.php`, riga 83).
Causa vera: il `wire:id` del Volt della pagina finiva dentro il widget; risolto
dando al `@volt` la radice `div.page-content`
(D15, `Themes/Sixteen/docs/decision-log/D15-volt-fragment-explicit-root.md`).

Do not add a fake method to the Fixcity widget or replace the Filament wizard
with Blade/Alpine navigation. The shared Xot wizard boundary is the ownership
point for this protocol.

Regression check:

```bash
php artisan test Modules/Fixcity/tests/Unit/CreateTicketWizardWidgetViewTest.php --compact
```

The browser check must also open `/it/tickets/create`, activate the privacy
checkbox, and advance to step two without a Livewire `MethodNotFoundException`.
