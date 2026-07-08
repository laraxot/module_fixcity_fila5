# CreateTicketWizardWidget Documentation

**Path**: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`  
**Base**: `XotBaseWizardWidget`  
**Purpose**: Multi-step frontoffice ticket creation.

---

## 🏛 Architecture (The Zen)

The widget follows the **Laraxot Convention-over-Configuration** (Religion):

1.  **Step Generation**: Steps are defined via `$this->getStepByName('name')` in `getSteps()`, which automatically maps to `get{Name}Schema()`. Ogni step può usare `->description()` come nella [Filament doc “Using a wizard”](https://filamentphp.com/docs/5.x/resources/creating-records#using-a-wizard); testi in `fixcity::ticket_wizard.steps.{privacy|data|summary}.description`.
2.  **Schema Definition**:
    - `TicketForm` (`Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`) e' la sorgente canonica dello schema ticket.
    - `CreateTicketWizardWidget::getPrivacySchema()`, `getDataSchema()` e `getSummarySchema()` delegano a `TicketForm`.
    - Per il link privacy proveniente dal CMS, il widget usa `TicketForm::getFrontofficePrivacySchema($privacyLink)`: e' una variante esplicita, non una firma convenzionale parametrizzata.
    - Il widget costruisce gli step con `$this->getStepByName(...)` per mantenere i parametri runtime del blocco CMS, ad esempio `privacy_link`.
    - Il widget non deve ridefinire campi ticket gia' presenti in `TicketForm`: eventuali modifiche a privacy, data step o summary partono da `TicketForm`.
3.  **Submission**: `submit()` usa **`$this->form->getState()`** tale e quale (dehydrate Filament + forma definita da `TicketForm`/schema); merge **`owner_id`** solo se l'utente è autenticato (`??=`); poi `Ticket::create($data)`. **Non** viene chiamato `TicketResource::prepareFormDataBeforePersist()` — quella rimane sulla create nel pannello.
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
- Le firme convenzionali `get{Name}Schema()` restano senza argomenti perché sono scoperte da `XotBaseResourceForm::getStepByName()` / `XotBaseWizardWidget::getStepByName()`. I parametri runtime usano metodi espliciti non convenzionali, ad esempio `getFrontofficePrivacySchema()`, non `getPrivacySchema($param)`.
- Non introdurre micro-helper tipo `authorTextEntry()` per tre entry leggibili: sarebbe un'astrazione cosmetica, non DRY utile. Il DRY corretto è condividere il provider schema (`TicketForm`) o isolare un vero comportamento riusabile, non nascondere tre righe dichiarative.
- La differenza tra admin resource e widget frontoffice deve rimanere esplicita: lo schema comune vive in `TicketForm`; stato Livewire, dati autenticati, redirect, submit e payload persistence restano nel widget.
- Non dichiarare `protected string $view` nel widget se il nome segue la convenzione: `XotBaseWidget` calcola `pub_theme::filament.widgets.create-ticket-wizard` e poi il fallback `fixcity::filament.widgets.create-ticket-wizard`. Nel PHP può restare solo un commento di promemoria.

---

## 🎨 Frontend Integration

- **View**: auto-risolta da `XotBaseWidget` / `GetViewByClassAction`: `pub_theme::filament.widgets.create-ticket-wizard`, fallback `fixcity::filament.widgets.create-ticket-wizard`.
- **Theme**: Sixteen (Design Comuni styling applied via CSS scoping).
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
