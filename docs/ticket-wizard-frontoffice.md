---
title: "ticket wizard frontoffice"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "ticket wizard frontoffice"
issues: []
discussions: []
status: draft
---

# Ticket Wizard Frontoffice

## Accesso (middleware nel JSON)
`/it/tickets/create` si apre solo da autenticati: `tickets.create.json` (le tre copie `config/local`, `config/eu`, `Modules/Fixcity/config`, identiche) contiene `"middleware": ["auth"]`, letto da `PageSlugMiddleware` via `Page::getMiddlewareBySlug`. **Esattamente `["auth"]`**: `web` non e' un alias risolvibile da `PageSlugMiddleware` (500, `Target class [web] does not exist`) e `guest` blocca gli utenti autenticati. Il `middleware('auth')` della pagina del modulo `pages/tickets/create.blade.php` non protegge nulla perche' Folio serve la pagina dinamica del tema, che oscura quella del modulo. Guardia: `tests/Unit/TicketCreatePageMiddlewareGuardTest.php`. Storia: DCP-42 (decisione dell'utente del 2026-10-13), STORY-357 (2026-06-12), commit `e040344c0` che aveva cancellato la chiave.

## Decisione
La pagina produttiva `tickets.create` (`/it/tickets/create`) è definita dal JSON CMS `config/local/fixcity/database/content/pages/tickets.create.json` (slug `tickets.create`), con un solo blocco `ticket_create` (`data.view = pub_theme::components.blocks.tests.ticket-crea`). Correzione verificata 2026-10-13: nessuna delle tre copie del JSON (`config/local`, `config/eu`, `Modules/Fixcity/config/database/content/pages`) ha il campo `middleware` e un ospite riceve `200` senza redirect a `/it/auth/login`; la Folio del modulo `Modules/Fixcity/resources/views/pages/tickets/create.blade.php` (con `middleware('auth')`) non è quella che risponde (vedi [ticket-crea-url-routing-chain.md](wiki/concepts/ticket-crea-url-routing-chain.md)). Se l'`auth` va ripristinato, va messo nel JSON come alias risolvibile (`auth`), mai `web`.

Correzione verificata 2026-10-13: l'entrypoint del flusso è `tickets.create`, non `tests.segnalazione-crea`. Per `/it/tests/segnalazione-crea` `page-json-for-url.sh` esce con codice 2 (nessun JSON `tests.segnalazione-crea.json` in nessun tenant) e la risposta (200) non contiene il wizard (0 occorrenze di `fi-sc-wizard` e di `$wire.save()`, contro 5 e 3 su `/it/tickets/create`).

**Composizione CMS-driven**: la pagina è composta da **blocchi JSON** (`content_blocks`), non da markup hardcoded:
Verificato 2026-10-13: `tickets.create.json` dichiara un solo blocco, `ticket_create`, che monta `CreateTicketWizardWidget` tramite `Themes/Sixteen/resources/views/components/blocks/tests/ticket-crea.blade.php`. Non esistono blocchi JSON `breadcrumb` o `contacts-card` per questa pagina; la composizione di breadcrumb, stepper e contatti è in lavorazione in DCP-33/DCP-34 e non è descritta qui.

**Nota parity DOM**: per allineare il markup al reference Design Comuni, il tag `<body>` sulle pagine test non deve accumulare classi tipo `page-tests-{slug}`; gli stili di parity restano su wrapper/`data-tests-slug` (vedi [html-parity-body-policy.md](../../../Themes/Sixteen/docs/html-parity-body-policy.md)).

**Id `main-container` (unicità)**: nel layout Sixteen l’`id="main-container"` è sul `<main>`. Il wrapper Blade del wizard (`Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php`) **non** deve ripetere lo stesso id sul `div.container` (HTML invalido e selettori CSS `main #main-container` che non matchano). Usare una classe dedicata (es. `wizard-dc-heading-shell`) per lo shell interno.

**Testo checkbox privacy**: la label mostrata allo step 1 è la stringa `fixcity::segnalazione.privacy.checkbox.label` (il metodo `TicketForm::getFrontofficePrivacySchema()` qui citato non esiste più; oggi `TicketForm::getPrivacySchema()` non imposta `->label()` sul checkbox). I file `create_ticket_wizard` / `ticket_form` devono restare allineati per fallback LangService.
Le pagine statiche legacy restano disponibili per riferimento o test di parità HTML:
- `segnalazione-01-privacy`
- `segnalazione-02-dati`
- `segnalazione-03-riepilogo`
- `segnalazione-04-conferma`

## Architettura
Il widget segue i principi Laraxot ed estende `XotBaseWizardWidget`.

**Implementazione (Filament way)**: il flusso multi-step è definito in **`Filament\Schemas\Components\Wizard`** + **`Wizard\Step`** dentro `CreateTicketWizardWidget::getFormSchema()`, con stato form in `data` tramite lo schema/Xot wrapper (vedi [documentazione Filament v5 — Wizards](https://filamentphp.com/docs/5.x/schemas/wizards)). La vista Blade `create-ticket-wizard.blade.php` è un **wrapper markup-only**: titolo, sidebar editoriale e parity layer attorno a `{{ $this->form }}` dentro un **`div`** (nessun `<form>` esterno: ogni Step Filament è già un `<form>`; form annidate corrompono il DOM e nascondono step). Invio conferma nello schema: handler Alpine **`$wire.save()`** sull’ultimo step (`HasWizard` + `HasXotFormAction::getSubmitFormLivewireMethodName()`). Il widget espone comunque **`submit()`** che delega alla stessa persistenza — solo compat esterna. Regola: **la macchina a stati degli step resta in Filament**, non in Blade.

**Schema SSoT**: il provider canonico dei campi è `TicketResource/Schemas/TicketForm`. `CreateTicketWizardWidget` usa `TicketForm::getDataSchema()` e `TicketForm::getSummarySchema()`; la privacy è in `TicketForm::getPrivacySchema()` / `getGdprHtml()` (il metodo `getFrontofficePrivacySchema()` citato in precedenza non esiste più: 0 occorrenze in `app/`, `tests/`, `resources/` di Fixcity, verificato 2026-10-13). Non deve creare un secondo schema parallelo per il frontoffice. Nel widget restano solo contesto CMS (`blockData`), stato Livewire, **`Ticket::create`** con **`$this->form->getState()`** più **`owner_id`** se auth, e redirect. La normalizzazione specifica del backoffice è applicata dalla Action `GetTicketFormDataForPersistAction` tramite l'hook di `CreateTicket`, senza metodo statico intermedio sul Resource.

**Picker Geo sibling**: i componenti mappa alternativi commentati con `NON CANCELLARE QUESTO` vivono nello schema owner `TicketForm`, non nel widget. Servono come memoria tecnica per confronti/runtime Geo senza reintrodurre duplicazione nel frontoffice.

**Risoluzione vista (widget shell — DRY XotBaseWidget)**: `CreateTicketWizardWidget` **non** dichiara `protected string $view`. Il costruttore di **`XotBaseWidget`** chiama `resolveView()` → **`GetViewByClassAction`**: viene scelta **la prima vista esistente** tra **`pub_theme::filament.widgets.create-ticket-wizard`** (parity Design Comuni nel tema pubblico quando presente) e **`fixcity::filament.widgets.create-ticket-wizard`** (`Modules/Fixcity/resources/views/filament/widgets/create-ticket-wizard.blade.php`, fallback modulo).

È tollerato (solo ergonomia sul sorgente) riportare le stringhe in un commento della classe figlia — **vietato** reintrodurre `protected string $view` sul widget perché bypassa l’overlay del tema e duplica ciò che la base risolve già. Per parity e contesto storico: story [7-52](../../../../_bmad-output/implementation-artifacts/7-52-segnalazione-crea-wizard-ultra-parity.md).

**Storia**: prima della migrazione lo step era gestito in Blade con `$currentStep` e `nextStep()` manuali; la story **[7-34](../../../../_bmad-output/implementation-artifacts/7-34-create-ticket-wizard-filament-schema-wizard-refactor.md)** documenta il passaggio.

### Filosofia CMS-Driven (Zen Laraxot)

| Principio | Significato |
|-----------|-------------|
| **Composizione CMS** | I blocchi sono dichiarati nel JSON pagina, non hardcoded nelle Blade |
| **Separazione** | Widget = wizard; Pagina = wizard + contacts (dal JSON) |
| **Riuso** | Stesso `contacts-card` usato in 17+ pagine |
| **Politica** | MAI duplicare markup nei template se può essere blocco CMS |

### Caratteristiche principali:
- **Base Class**: `Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget`
- **Estensione**: `Modules\Xot\Filament\Widgets\XotBaseWizardWidget` (a sua volta `XotBaseWidget`); documentazione: [xot-base-wizard-widget.md](../../Xot/docs/wiki/concepts/filament-wizard-architecture-right-way.md)
- **Schema owner**: `Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm`
- **Navigazione**: `Wizard` Filament (next/previous, validazione per step); `persistStepInQueryString('step')` quando l’override query è consentito. I pulsanti sono in `Themes/Sixteen/resources/views/components/wizard/cmp-nav-steps.blade.php` e usano Alpine (`goToPreviousStep`, `requestNextStep`, `$wire.save()`), non `wire:click`. Correzione verificata 2026-10-13: `nextStep()` e `previousStep()` sono metodi di `Filament\Schemas\Components\Wizard` (`vendor/filament/schemas/src/Components/Wizard.php`, righe 96 e 159), non di `XotBaseWizardWidget` (nessun `function nextStep` in `Modules/Xot/app` né in `Modules/Fixcity/app`): il flusso resta governato dal Wizard Filament.
- **Contratto Livewire schema**: `callSchemaComponentMethod()` è già `public` in `Filament\Schemas\Concerns\InteractsWithSchemas` (`vendor/filament/schemas/src/Concerns/InteractsWithSchemas.php`, riga 83). Il re-dichiararlo in `XotBaseWizardWidget` con `parent::` è un no-op e NON è la causa di `MethodNotFoundException`: l'errore cliccando «Avanti» veniva dal `wire:id` del Volt della pagina caduto dentro il widget, risolto dando al `@volt` la radice `div.page-content` (vedi [D15](../../../Themes/Sixteen/docs/decision-log/D15-volt-fragment-explicit-root.md)).
- **Campo tipologia**: nello schema canonico `TicketForm` lo step dati usa **`Select::make('type')`** (come la create admin): niente riconciliazione `type_id` → `type` nel widget.
- **Validazione**: step tramite validazione nativa del wizard; allo submit **`$this->form->getState()`** poi merge **`owner_id`** se serve, infine **`Ticket::create`** senza trasformazioni intermedie e senza dipendere dalla Action di normalizzazione backoffice; upload/media come da regole modulo (campi dehydrate/collection).
- **Naming**: Usa sempre `Ticket` invece di `Segnalazione` nel codice PHP.

### `Wizard` (admin) vs `PubThemeWizard` (frontoffice)

**Regola**: nel **backoffice** (pannello Filament) il wizard deve usare **`Filament\Schemas\Components\Wizard`** senza vista `pub_theme` — è la skin standard del pannello. Nel **frontoffice** (pagina cittadino con `pub_theme`) si usa **`PubThemeWizard`** (o equivalente `Wizard::make(...)->view('pub_theme::components.wizard')`), perché il “vestito” è quello del tema pubblico, non quello dell’admin.

`TicketForm::getFormSchema()` applica questo ramo con **`inAdmin()`**: admin → `Wizard::make($steps)`; non admin → `PubThemeWizard::make($steps)` (stessi `skippable()` e `persistStepInQueryString()` dove consentito).

Il widget `CreateTicketWizardWidget` resta un entrypoint **solo frontoffice** e continua a costruire il wizard in `makeWizard()` con `PubThemeWizard` (coerente con la regola sopra).

**Anti-pattern**: usare `PubThemeWizard` ovunque, incluso contesto admin — mescola la skin del tema pubblico con il layout Filament del pannello.

Riferimento wiki: [filament-admin-pub-theme-wizard-boundary](../../../../docs/wiki/concepts/filament-admin-pub-theme-wizard-boundary.md). Story BMAD correlata: **7-113**.

### Step del Wizard (3):
1. **Privacy** (chiave `form.autorizzazioni-e-condizioni::data::wizard-step` in `it`): privacy notice read-only + link informativa + checkbox finale (`privacyAccepted`).
2. **Dati** (chiave `form.dati-di-segnalazione::data::wizard-step` in `it`): indirizzo, tipo disservizio, `name`, `content`, email, immagini (upload), dati autore guest.
3. **Riepilogo + Submit** (chiave **`form.riepilogo::data::wizard-step`** in `it`, `form.summary::data::wizard-step` in `en`): riepilogo dei dati degli step precedenti con **[Infolists Filament v5](https://filamentphp.com/docs/5.x/infolists/overview)** — `TextEntry` in **`TicketFormReviewInfolist`** (nessun `TextInput` disabilitato / `Placeholder`). Nella **stessa** vista, **autore e contatti** restano campi **`TextInput`** in `TicketForm` (raccolta finale prima dell'invio).

**Conferma**: correzione verificata 2026-10-13 in `CreateTicketWizardWidget::redirectAfterSuccess()`: il redirect post-invio va a `blockData['confirmation_path']` (default `/tickets/confirmation`), localizzato con `LaravelLocalization`, con `?code=<codice ticket>`; non usa la route `tests.view` né `confirmation_slug`. La pagina di esito resta **esterna al wizard**.

**Owner**: se l’utente è autenticato, `owner_id` viene impostato; in caso contrario il payload non include `owner_id` (eventuale policy guest va allineata ai vincoli DB / prodotto).

**Query `?step=` (deeplink Filament)**:

- Nel wizard schema Filament v5, `persistStepInQueryString('step')` persiste **`step` come stringa**: il valore confrontato sul server è `$step->getId()` (= in pratica **`$step->getKey()`**), **non** un indice numerico `1…N` né un path sintetico tipo `form.privacy`.
- Per questo widget, nell’HTML (input nascosto `x-ref="stepsData"`) le chiavi visibili sono tipicamente:
  - step 1 (privacy): `form.autorizzazioni-e-condizioni::data::wizard-step` in `it`, `form.privacy-and-terms::data::wizard-step` in `en`
  - step 2 (dati): `form.dati-di-segnalazione::data::wizard-step` in `it`, `form.report-details::data::wizard-step` in `en`
  - step 3 (riepilogo + invio): `form.riepilogo::data::wizard-step` in `it`, `form.summary::data::wizard-step` in `en`

  Correzione verificata 2026-10-13 sulle risposte di `/it/tickets/create` e `/en/tickets/create`: la chiave è lo slug dell'etichetta tradotta dello step (`fixcity::fixcity.ticket.steps.*.label`), quindi dipende dalla lingua; `form.privacy` e `form.data` non esistono in nessuna lingua.

  Esempio valido browser (URL-encoded):  
  `/it/tickets/create?step=form.dati-di-segnalazione%3A%3Adata%3A%3Awizard-step`  
  Valori incompleti (es. `?step=form.privacy`) **non** matchano alcuno step (`Wizard::getStartStep()` nella vendor Filament cicla sugli step e confronta **`getId()`** con il query param): in quel caso si ricade sullo **`startStep` predefinito (1)**. Non conta come “deeplink allo step privacy” perché è solo un mismatch di stringa.
- Una policy tipo `FIXCITY_WIZARD_ALLOW_STEP_QUERY` / “solo local” **non** è implementata in `Wizard::class` upstream: eventualmente sarebbe solo in override Laraxot; **non dire** ai QA che `?step=2` apre sempre lo step 2 — con il codice Filament stock **non succede**.
- Troubleshooting **`CreateTicketWizardWidget` non monta**: errore fetale tipo `NormalizesWizardFormState`/`include … Traits` → **cancellare** ogni **`use`** verso quel namespace: il trait **non esiste e non deve essere introdotto**; allinearsi al codice corrente; da `laravel/` eseguire `composer dump-autoload`.

**Nota su `#` nell’URL**: un solo hash **non** imposta lo step: usare **`?step=`** con il valore **esatto** di `getKey()` come sopra, oppure nessuna query (montaggio sullo step 1).

### 🎨 CSS / JS Assets (Frontend)

**Layout Sixteen** (`Themes/Sixteen/resources/views/components/layouts/main.blade.php`):
Quando `$usesFrontendLivewire` è true (route `tests/segnalazione-crea`), il layout include:
- `@livewireStyles` + `@filamentStyles` nel `<head>`
- `@livewireScripts` + `@filamentScripts` nel `<body>`

**⚠️ Critico**: Senza `@filamentScripts`, il wizard Filament v5 NON funziona — tutti gli Alpine component (`wizardSchemaComponent`, `filamentSchemaComponent`, ecc.) sono `undefined`.

**Vite**: `resources/js/app.js` e `resources/css/app.css` sono caricati dal tema Sixteen.

### Geolocalizzazione — mappa + coordinate (step 2)

Lo scopo è **localizzare il disservizio** sul dominio `Ticket` con **`latitude` / `longitude`** (colonne in `fillable`). Il flusso frontoffice usa oggi **`LatitudeLongitudeInput`** (Geo): input annidati nello stato `location`, mappa interattiva, marker trascinabile, click sulla mappa, geolocalizzazione e fullscreen. Il componente deve mantenere **sincronizzazione bidirezionale** tra marker, center mappa e input visibili di latitudine/longitudine, senza refresh distruttivi durante il drag.

**Componente**: `Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput` — owned by **Geo module**. Vedi [Geo: Filament forms components](../../Geo/docs/filament-forms-components.md).

**Utilizzo nel wizard** (estratto):
```php
use Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput;

LatitudeLongitudeInput::make('location')
    ->hiddenLabel()
    ->defaultCenter(41.9028, 12.4964) // Roma di default
    ->defaultZoom(13)
    ->mapHeight('340px'),
```

### Struttura canonica step 2

Lo step `Dati di segnalazione` deve restare allineato al reference Design Comuni con **tre sezioni reali**:

- `Luogo`
- `Disservizio`
- `Autore della segnalazione`

La sezione `Autore della segnalazione` è concettualmente **read-only** quando i dati sono già noti, quindi la resa preferita è una card informativa con componenti `Filament\Schemas\Components\Text`, non un gruppo di input fake.
Implementazione corrente: `Section` + `Grid` + `Text` read-only + input email contatto.

**Parity visuale**: per aumentare davvero la somiglianza col reference, `Section` deve diventare anche l’unità primaria di gerarchia visiva dello step 2, non solo un wrapper tecnico. L’obiettivo è ottenere blocchi percepibili e leggibili per `Luogo`, `Disservizio` e `Autore della segnalazione`.

**Cleanup layout step 2**: evitare sezioni annidate non necessarie (es. `Contatti` collassabile dentro `Autore`) quando degradano leggibilità e ritmo visivo. Nel caso corrente, l'email resta un campo diretto nella sezione autore.

**Regola CSS schema-first**: con componenti `Filament\Schemas`, i fix devono colpire classi renderizzate `fi-section`, `fi-section-content-ctn`, `fi-sc-text`; se compare doppio titolo, nascondere il wrapper `fi-sc-section-label-ctn` invece di duplicare markup.

**Regola HTML parity no-wrapper**: nel blocco pagina `segnalazione-crea` non introdurre contenitori extra (es. `div.segnalazione-crea-wrapper`) se non presenti nel reference Design Comuni. Il blocco deve montare direttamente il widget Livewire, lasciando alla view del widget la sola struttura minima necessaria.

**Story parity finale step 2**: backlog operativo consolidato in [7-50 segnalazione-crea step2 high html visual parity](../../../../_bmad-output/implementation-artifacts/7-50-segnalazione-crea-step2-high-html-visual-parity.md) con focus su legend obbligatori, autore read-only coerente e azioni finali allineate al reference.

**Follow-up parity ultra-mirata (14 aprile 2026)**: la story [7-51 segnalazione-crea step2 columns header ultra parity](../../../../_bmad-output/implementation-artifacts/7-51-segnalazione-crea-step2-columns-header-ultra-parity.md) aggiunge requisiti screenshot-driven sulla URL reale dello step 2, con focus esplicito su:
- leggibilita colonna sinistra
- larghezza colonna sinistra e centrale
- riduzione spacing verticale eccessivo
- leggibilita header/top chrome, incluso `Accedi all'area personale`
- divieto assoluto di `->label()/->placeholder()/->tooltip()` runtime (tutto via i18n auto-resolve)
- AC1–AC10 + quality gate (phpstan, phpmd .phar, phpinsights, pest)

**Gap visuali ancora espliciti nello scope 7-50**: non basta avere le 3 sezioni. La parity finale deve correggere anche:
- colonna laterale sinistra troppo stretta o poco leggibile;
- colonna centrale del form troppo stretta rispetto al reference;
- eccesso di spazio verticale tra heading, legend, sezioni e campi;
- leggibilita insufficiente nell'header generale della pagina (es. area `Accedi all'area personale`).
Per il passaggio finale a parity estrema vedere [7-51 segnalazione-crea step2 columns header ultra parity](../../../../_bmad-output/implementation-artifacts/7-51-segnalazione-crea-step2-columns-header-ultra-parity.md), che stringe i criteri su larghezze colonne, leggibilita header, spazi verticali e divieto di `->label()/->placeholder()/->tooltip()` runtime.

**Nota runtime hardening (step 2)**: nel widget usare solo componenti `Schemas` (`Text`, `Section`, `Grid`) e API realmente supportate dai custom field Geo (`LatitudeLongitudeInput`, oppure `AddressInput` dove serve indirizzo testuale). Regressioni tipiche da evitare: `Placeholder::make()`/`TextEntry::make()` in schema form, reimplementazione manuale degli step in Blade e chiamate non supportate sui componenti custom.

**Regola mappa step 2** (story [8-10](../../../../_bmad-output/implementation-artifacts/8-10-segnalazione-crea-map-bidirectional-sync-and-no-refresh-on-marker-drag.md)):
Il protocollo corretto è:
- **preview locale durante `drag`**: aggiorna DOM inputs throttled ogni ~200ms, niente Livewire
- **persistenza Livewire su `dragend`**: chiama `commitCoordinates()` che dispatches change event → `wire:model.change` syncs
- **click mappa / geolocalizzazione**: stesso flusso di dragend
- **input manuali**: ricentrano mappa e spostano marker; change event syncs a Livewire
- **idempotenza**: global instance registry, `wire:ignore` shell, `isProgrammaticInputUpdate` flag evitano doppie inizializzazioni e sync circolari
- **nessun `wire.set()` diretto**: usiamo change event per attivare `wire:model.change` — questo evita re-render aggressivi
- **nessun `wire:model.live`** che causi churn su marker drag continuo

**Traduzioni pagina test `segnalazione-02-dati`**: mantenere allineate le chiavi `fixcity::segnalazione.breadcrumb.*`, `fixcity::segnalazione.fields.required.note.*`, `fixcity::segnalazione.actions.save*`, `fixcity::segnalazione.actions.remove_*` e `fixcity::segnalazione.inefficiency_types.*` per evitare fallback raw key nella UI.

**Parità checkbox (step 1)**: gli stili del checkbox privacy devono usare hook stabili del widget e del layout (`.ticket-wizard-root`, classi strutturali reali, classi Filament/Bootstrap Italia compatibili), evitando selettori fragili basati sullo slug runtime della pagina.

### Stato form (`data`):
I campi del wizard vivono nello stato Filament con `statePath('data')` (vedi `XotBaseWidget`). Al submit il widget passa a **`Ticket::create`** l’array restituito da **`$this->form->getState()`** (forma esattamente quella che Filament/schema espone al salvataggio), più eventuale **`owner_id`**; nessun helper di appiattimento tra form e `create`. Chiavi solo UX restano fuori dal dehydrate Filament (es. `privacyAccepted` con `dehydrated(false)`). `blockData` resta per CMS (titolo pagina, contatti).

### Regola step 3 — review semantica

Nel passo finale di riepilogo, dentro un `Form Schema`, il linguaggio corretto è **Schemas Components** (`Text`, `Section`, `Grid`) e non `Placeholder` come struttura primaria.
Consolidamento attuale: nel widget non restano `Placeholder`; anche la notice privacy read-only usa `Text` con rendering HTML controllato.

### Guardrail runtime

Per questo wizard il criterio di accettazione minimo non è solo la correttezza semantica del codice. Dopo ogni refactor del render path servono anche smoke check runtime reali: il widget compila solo davvero se `/it/tickets/create` risponde `200` (`/it/tests/segnalazione-crea` risponde 200 ma senza wizard, verificato 2026-10-13) entro timeout ragionevole. Mount, model binding e summary pre-submit devono restare minimali.

### Verifica automatica (Issue #75)

| Layer | File | Cosa verifica |
|-------|------|---------------|
| Pest unit | `Modules/Fixcity/tests/Unit/CreateTicketWizardWidgetViewTest.php` | assenza override `$view`, wrapper senza `<form>` esterno attorno a `{{ $this->form }}`, modals dopo lo shell |
| Pest feature | `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardWidgetTest.php` | risoluzione view runtime via `GetViewByClassAction`, HTML con `$wire.save()`, metodi `submit()` / `save()` |
| Playwright | `Modules/Fixcity/tests/Playwright/ticket-create-wizard.spec.js` | root: **`$wire.save()`**, `.fi-sc-wizard`; deeplink **`form.summary::data::wizard-step`**: più `.fi-in-entry` + CTA conferma visibile |

Comandi:

```bash
cd laravel && ./vendor/bin/pest \
  Modules/Fixcity/tests/Unit/CreateTicketWizardWidgetViewTest.php \
  Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardWidgetTest.php

cd laravel/Modules/Fixcity && PLAYWRIGHT_BASE_URL=http://127.0.0.1:8000 \
  npx playwright test tests/Playwright/ticket-create-wizard.spec.js
```

**Debito test (Playwright)**: lo spec `ticket-create-wizard.spec.js` visita `/it/tests/segnalazione-crea`, che oggi non contiene il wizard (0 `fi-sc-wizard`, verificato con curl il 2026-10-13): va puntato a `/it/tickets/create`.

**Debito test**: submit Livewire end-to-end con auth e creazione `Ticket` resta bloccato dal test harness multi-connessione (`user` vs `fixcity`).

### Regola multilingua runtime

- Nel PHP runtime non devono comparire label o frasi in italiano.
- Il testo utente vive nei file `lang/{locale}`.
- Gli slug di contenuto non vanno hardcodati nel widget: devono arrivare da CMS/config (`confirmation_slug`) e il redirect deve passare da route name.

### Metodi chiave:
- `TicketForm::getDefaultFormState()` (chiamato da `mount()`; l'ex `defaultFormData()` non esiste più, verificato 2026-10-13): inizializza **tutte** le chiavi dei campi degli step (anche vuote), non solo `privacyAccepted`. Se manca una chiave (es. `content`, `type_id`, `images`), Livewire segnala errori **Entangle** in console perché Alpine non trova `data.<campo>` su `$data`. Lo stato deve essere coerente con ciò che va persistito su `Ticket` dopo normalizzazione.
- `save()` / `submit()` (alias): leggono **`$this->form->getState()`**, **`owner_id` con `??=`** se auth, poi `CreateTicketAction` (non più `Ticket::create` diretto), redirect a conferma. Nessun `NormalizeTicketLocationDataAction` sul widget — normalizzazioni restano nel backoffice (`GetTicketFormDataForPersistAction` / mutate create) e in schema/model.
- `getSteps()`: compone i 3 step nel loro ordine

### Classe naming convention:
- **CORRETTO**: `CreateTicketWizardWidget` (Ticket, non Segnalazione)
- **SBAGLIATO**: ~~`CreateSegnalazioneWizardWidget`~~

## Traduzioni
Segue il pattern richiesto: `fixcity::segnalazione.steps.<item>.<tipo>`.
Esempio: `fixcity::segnalazione.steps.privacy.label`.

**Design Comuni (stepper)**: il primo step nello stepper deve essere etichettato **Autorizzazioni e condizioni**, come nel reference e in [stepper-component.md](../../../Themes/Sixteen/docs/design-comuni/stepper-component.md); non usare «Informativa sulla privacy» come titolo dello step (quella resta copy nel link/checkbox). Allineamento implementativo: story [7-32](../../../../_bmad-output/implementation-artifacts/7-32-segnalazione-crea-design-comuni-step1-cta-stepper-labels-header-parity.md).

**Privacy notice**: nello step 1 il blocco legale informativo deve essere contenuto principale read-only, non semplice helper text del checkbox. Se si usa Filament puro, preferire un componente read-only coerente con contenuto editoriale/informativo invece di lasciare solo il checkbox.

**Regola di scelta componente**: usare **Infolist** quando si mostrano dati read-only strutturati (summary, card autore, label/value); usare invece contenuto read-only nel **Form Schema** quando il blocco e editoriale o legale, come nello step privacy. Il primo step non e un record viewer: e una soglia di consenso informato. Story dedicata: [7-47 segnalazione-crea step1 privacy notice parity](../../../../_bmad-output/implementation-artifacts/7-47-segnalazione-crea-step1-privacy-notice-design-comuni-parity.md).

**Parity step 1 (gdpr notice)**: il testo "Il Comune di Firenze gestisce i dati personali..." deve essere visibile nello step privacy prima del checkbox, con link a informativa (`privacy_link` da `blockData`).

Il file principale è `laravel/Modules/Fixcity/lang/{locale}/segnalazione.php`.

## File coinvolti
- **Widget**: `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- **View Widget**: auto-risolta da `pub_theme::filament.widgets.create-ticket-wizard`, fallback `fixcity::filament.widgets.create-ticket-wizard` (`laravel/Modules/Fixcity/resources/views/filament/widgets/create-ticket-wizard.blade.php`)
- **Blocco Tema**: `laravel/Themes/Sixteen/resources/views/components/blocks/tests/ticket-crea.blade.php` (quello di `tickets.create`; `tests/segnalazione-crea.blade.php` esiste ma nessun JSON lo usa)
- **CMS JSON**: `laravel/config/local/fixcity/database/content/pages/tickets.create.json` (`tests.segnalazione-crea.json` non esiste, verificato 2026-10-13)

## See Also

- Story BMAD (step 1: label stepper, CTA `mobile-full` vs `cmp-nav-steps`, header/search): [7-32 segnalazione-crea design comuni step1 CTA stepper labels header parity](../../../../_bmad-output/implementation-artifacts/7-32-segnalazione-crea-design-comuni-step1-cta-stepper-labels-header-parity.md)
- Story BMAD (step 2: geolocalizzazione + `?step=`): [7-33 segnalazione-crea step2 geolocation use my location and step query](../../../../_bmad-output/implementation-artifacts/7-33-segnalazione-crea-step2-geolocation-use-my-location-and-step-query.md)
- Story BMAD (step 2: spinner / busy feedback su usa la tua posizione): [7-42 segnalazione-crea use my location busy feedback](../../../../_bmad-output/implementation-artifacts/7-42-segnalazione-crea-use-my-location-busy-feedback.md)
- Story BMAD (step 2: tre sezioni + autore via Infolist): [7-43 segnalazione-crea step2 three sections parity and author infolist](../../../../_bmad-output/implementation-artifacts/7-43-segnalazione-crea-step2-three-sections-parity-and-author-infolist.md)
- Story BMAD (step 2: parity consolidata con section e autore read-only): [7-48 segnalazione-crea step2 section parity and author readonly](../../../../_bmad-output/implementation-artifacts/7-48-segnalazione-crea-step2-section-parity-and-author-readonly.md)
- Story BMAD (step 3: Infolist invece di Placeholder nel summary): [7-44 create ticket wizard summary infolist over placeholders](../../../../_bmad-output/implementation-artifacts/7-44-create-ticket-wizard-summary-infolist-over-placeholders.md)
- Story BMAD (anti-regressione loop/timeout del render path): [7-45 segnalazione-crea render loop regression guard](../../../../_bmad-output/implementation-artifacts/7-45-segnalazione-crea-render-loop-regression-guard.md)
- Story BMAD (step 1: privacy notice completo e parity): [7-47 segnalazione-crea step1 privacy notice parity](../../../../_bmad-output/implementation-artifacts/7-47-segnalazione-crea-step1-privacy-notice-design-comuni-parity.md)
- Story BMAD (step 2: visual parity guidata da Section e Infolist): [7-48 segnalazione-crea step2 visual parity via sections and infolist](../../../../_bmad-output/implementation-artifacts/7-48-segnalazione-crea-step2-visual-parity-via-sections-and-infolist.md)
- Story BMAD (header hamburger, testo Cerca, stepper responsive): [7-29 segnalazione-crea header stepper responsive multilingual](../../../../_bmad-output/implementation-artifacts/7-29-segnalazione-crea-header-stepper-responsive-multilingual.md)
- Story BMAD (step 1 parity, checkbox, navigazione, `?step=`): [7-2 segnalazione-crea step1 parity checkbox navigation](../../../../_bmad-output/implementation-artifacts/7-2-segnalazione-crea-step1-parity-checkbox-navigation.md)

- [Fixcity Module README](README.md)
- [Laraxot Core Architecture](../../Xot/docs/architecture.md)
- [Sixteen Theme Design Comuni](../../../Themes/Sixteen/docs/design-comuni/README.md)
- Story BMAD (implementazione / verifica): [7-1 unified segnalazione-crea ticket wizard](../../../../_bmad-output/implementation-artifacts/7-1-unified-segnalazione-crea-ticket-wizard.md)
- Story BMAD (refactor Filament Schema Wizard v5, vista slim): [7-34 create ticket wizard filament schema wizard refactor](../../../../_bmad-output/implementation-artifacts/7-34-create-ticket-wizard-filament-schema-wizard-refactor.md) — stato `done`
- Story BMAD (parity visiva step 1 vs [statiche privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html), wizard solo Filament): [7-39 segnalazione 01 privacy design comuni filament visual parity](../../../../_bmad-output/implementation-artifacts/7-39-segnalazione-01-privacy-design-comuni-filament-visual-parity.md) — `ready-for-dev`
