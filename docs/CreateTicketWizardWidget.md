# CreateTicketWizardWidget Documentation

**Path**: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`  
**Base**: `XotBaseWizardWidget`  
**Purpose**: Multi-step frontoffice ticket creation.

---

## 🏛 Architecture (The Zen)

The widget follows the **Laraxot Convention-over-Configuration** (Religion):

1.  **Step Generation**: Steps are defined via `$this->getStepByName('name')` in `getWizardSteps()`, which automatically maps to `get{Name}Schema()`. Ogni step può usare `->description()` come nella [Filament doc “Using a wizard”](https://filamentphp.com/docs/5.x/resources/creating-records#using-a-wizard); testi in `fixcity::ticket_wizard.steps.{privacy|data|summary}.description`.
2.  **Schema Definition**:
    - `getPrivacySchema()`: Step 1 (privacy notice read-only + privacy checkbox).
    - `getDataSchema()`: Step 2 structured in three Design Comuni sections:
      - `place` (luogo): `LeafletMarkerMapInput` che aggiorna automaticamente i campi nascosti `latitude` e `longitude`
      - `inefficiency` (disservizio)
      - `author` (autore della segnalazione, read-only info + contatto email)
    - `getSummarySchema()`: Step 3 (read-only review built con visualizzazione coordinate e altre informazioni).
3.  **Submission**: `submit()` valida il form (`validateWizardSubmission()`), poi `prepareTicketData()` (`normalizeWizardFormState` + unset `images`/`privacyAccepted` + assicurarsi che `latitude` e `longitude` siano presenti come stringhe + `owner_id` se auth), `createTicket()`, `TicketCreatedEvent`, redirect. Errori: `handleSubmissionError()` (notifica + `addError('submit', …)`; in local anche `report($e)`).
4.  **Tipologia (`type_id`)**: `Select::make('type_id')->options(TicketTypeEnum::class)` — Filament (`HasOptions`) costruisce `[value => getLabel()]`. **IMPORTANTE**: Mai usare `TicketTypeEnum::cases()` come `options`: non è il contratto previsto da Filament. Nel summary, il tipo viene gestito controllando se il valore è già un'istanza di `TicketTypeEnum` prima della conversione. Dettaglio: [filament-select-enum-best-practices.md](filament-select-enum-best-practices.md).
5.  **Auto-Labeling**: No `->label()` calls are used. Translations sono risolte via `LangServiceProvider` usando le chiavi `fixcity::create_ticket_wizard.fields.{name}.label` e `fixcity::segnalazione.fields.*` dove applicabile.

---

## 🧘 Religion & Rules

- **DRY**: Stato wizard normalizzato una sola volta (`normalizeWizardFormState`); persistenza ticket nel widget finché non esiste un’Action modulo condivisa documentata.
- **KISS**: No "merda" methods or complex payload builders. The form state maps directly to the model attributes.
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

---

## 🎨 Frontend Integration

- **View**: `fixcity::filament.widgets.ticket-create-wizard`
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
