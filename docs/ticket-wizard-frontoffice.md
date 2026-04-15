# Ticket Wizard Frontoffice

## Decisione
La pagina pubblica `tests.segnalazione-crea` è l'entrypoint unificato del flusso utente per la creazione di segnalazioni (Ticket).

**Composizione CMS-driven**: la pagina è composta da **blocchi JSON** (`content_blocks`), non da markup hardcoded:
1. `breadcrumb` → navigazione
2. `segnalazione-crea` → widget wizard (questo)
3. `contacts-card` → sezione "Contatta il comune"

**Nota parity DOM**: per allineare il markup al reference Design Comuni, il tag `<body>` sulle pagine test non deve accumulare classi tipo `page-tests-{slug}`; gli stili di parity restano su wrapper/`data-tests-slug` (vedi [html-parity-body-policy.md](../../Themes/Sixteen/docs/html-parity-body-policy.md)).
Le pagine statiche legacy restano disponibili per riferimento o test di parità HTML:
- `segnalazione-01-privacy`
- `segnalazione-02-dati`
- `segnalazione-03-riepilogo`
- `segnalazione-04-conferma`

## Architettura
Il widget segue i principi Laraxot ed estende `XotBaseWizardWidget`.

**Implementazione (Filament way)**: il flusso multi-step è definito in **`Filament\Schemas\Components\Wizard`** + **`Wizard\Step`** dentro `CreateTicketWizardWidget::getFormSchema()`, con stato form in `data` tramite `XotBaseWizardWidget::form()` (vedi [documentazione Filament v5 — Wizards](https://filamentphp.com/docs/5.x/schemas/wizards)). La vista Blade `ticket-create-wizard.blade.php` è un **wrapper** (titolo, stepper, sidebar solo step «dati») + `{{ $this->form }}` dentro `<form wire:submit="submit">`; la parity HTML Design Comuni su stepper/card va recuperata con CSS tema (`segnalazione-parity.css`) e/o field custom, non con centinaia di righe di markup duplicato.

**Risoluzione vista**: `GetViewByClassAction` prova per primo `pub_theme::filament.widgets.createticketwizard` (wrapper tema senza sidebar). Il widget imposta esplicitamente `protected string $view = 'fixcity::filament.widgets.ticket-create-wizard'` così il frontoffice usa sempre il layout modulo (colonna «informazioni richieste» allo step 2). Regola anti-regressione: non rimuovere questa proprietà senza verificare parity e [story 7-52](../../../../_bmad-output/implementation-artifacts/7-52-segnalazione-crea-wizard-ultra-parity.md).

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
- **Estensione**: `Modules\Xot\Filament\Widgets\XotBaseWizardWidget` (a sua volta `XotBaseWidget`); documentazione: [xot-base-wizard-widget.md](../../Xot/docs/filament/widgets/xot-base-wizard-widget.md)
- **Navigazione**: `Wizard` Filament (next/previous, validazione per step); `persistStepInQueryString('step')` quando l’override query è consentito. I pulsanti custom in `ticket-create-wizard.blade.php` usano `wire:click="nextStep"` / `previousStep`: i metodi sono definiti su `XotBaseWizardWidget` e delegano a `callSchemaComponentMethod(<chiave wizard>, 'nextStep'|'previousStep', …)` — non sono metodi «magici» Livewire.
- **Campo tipologia**: lo schema usa `Select::make('type_id')` allineato a `Ticket::$fillable` e al cast `type_id` → `TicketTypeEnum`. Un campo nome `type` risultava fuori dal contratto del modello e poteva fallire la validazione o il salvataggio.
- **Validazione**: step tramite validazione nativa del wizard; submit finale con `Ticket::query()->create()` dopo `normalizeWizardFormState()` e arricchimenti (`owner_id`); upload `images` escluso dal payload create come da logica esistente.
- **Naming**: Usa sempre `Ticket` invece di `Segnalazione` nel codice PHP.

### Step del Wizard (3):
1. **Privacy** (step id `1`): privacy notice read-only + link informativa + checkbox finale (`privacyAccepted`).
2. **Dati** (step id `2`): indirizzo, tipo disservizio, `name`, `content`, email, immagini (upload), dati autore guest.
3. **Riepilogo + Submit** (step id `3`): blocco read-only con componenti Schemas (`Text`) + `submitAction` del wizard; invio con `wire:submit="submit"`.

**Conferma**: Il redirect post-invio usa la route nominata `tests.view` con slug letto da `blockData['confirmation_slug']` o da `config('fixcity.wizard.confirmation_slug')`. La pagina di esito resta **esterna al wizard**.

**Owner**: se l’utente è autenticato, `owner_id` viene impostato; in caso contrario il payload non include `owner_id` (eventuale policy guest va allineata ai vincoli DB / prodotto).

**Query `?step=` (QA / verifica)**: sulla stessa URL della pagina (`/{locale}/tests/segnalazione-crea?step=2`) è possibile impostare lo step iniziale (`1`, `2` o `3`) solo se è consentito dall’ambiente: **locale `local`**, oppure **`APP_DEBUG=true`**, oppure `FIXCITY_WIZARD_ALLOW_STEP_QUERY=true` in `.env` (legge `config('fixcity.wizard.allow_step_query_override')`). In produzione senza questi flag, valori diversi da `1` vengono ignorati (resta lo step 1). Lo **stepper in pagina** è solo indicativo (nessun salto step da click).

**Nota su `#` nell’URL**: un solo hash (`.../segnalazione-crea#`) **non** imposta lo step lato server: lo stato è il wizard Filament (e/o query `step` se persistita). Per aprire direttamente lo step 2 usare **`?step=2`** (id step Filament `2`), non l’hash.

### 🎨 CSS / JS Assets (Frontend)

**Layout Sixteen** (`Themes/Sixteen/resources/views/components/layouts/main.blade.php`):
Quando `$usesFrontendLivewire` è true (route `tests/segnalazione-crea`), il layout include:
- `@livewireStyles` + `@filamentStyles` nel `<head>`
- `@livewireScripts` + `@filamentScripts` nel `<body>`

**⚠️ Critico**: Senza `@filamentScripts`, il wizard Filament v5 NON funziona — tutti gli Alpine component (`wizardSchemaComponent`, `filamentSchemaComponent`, ecc.) sono `undefined`.

**Vite**: `resources/js/app.js` e `resources/css/app.css` sono caricati dal tema Sixteen.

### Geolocalizzazione — mappa + coordinate (step 2)

Lo scopo è **localizzare il disservizio** sul dominio `Ticket` con **`latitude` / `longitude`** (colonne in `fillable`). Il flusso frontoffice usa **`LeafletMarkerMapInput`** (Geo): tile OpenStreetMap, marker trascinabile, click sulla mappa, pulsante **«Usa la tua posizione»** (Geolocation API dopo gesto utente). Il componente aggiorna automaticamente i campi sibling `latitude` e `longitude` nello stesso schema quando l'utente interagisce con la mappa.

**Componente**: `Modules\Geo\Filament\Forms\Components\LeafletMarkerMapInput` — owned by **Geo module**. Vedi [Geo: Filament forms components](../../Geo/docs/filament-forms-components.md).

**Utilizzo nel wizard** (estratto):
```php
use Modules\Geo\Filament\Forms\Components\LeafletMarkerMapInput;

LeafletMarkerMapInput::make('location')
    ->label(__('fixcity::segnalazione.fields.place.section.label'))
    ->defaultCenter(41.9028, 12.4964) // Roma di default
    ->defaultZoom(13)
    ->mapHeight('340px')
    ->showMap(true),
// Campi sibling nascosti che verranno automaticamente aggiornati dal componente:
TextInput::make('latitude')->numeric()->hidden(),
TextInput::make('longitude')->numeric()->hidden(),
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

**Nota runtime hardening (step 2)**: nel widget usare solo componenti `Schemas` (`Text`, `Section`, `Grid`) e API realmente supportate dai custom field Geo (`LeafletMarkerMapInput`, oppure `AddressInput` dove serve indirizzo testuale). Regressioni tipiche da evitare: `Placeholder::make()`/`TextEntry::make()` in schema form e chiamate non supportate (`->placeholder()` su componenti custom non compatibili).

**Traduzioni pagina test `segnalazione-02-dati`**: mantenere allineate le chiavi `fixcity::segnalazione.breadcrumb.*`, `fixcity::segnalazione.fields.required.note.*`, `fixcity::segnalazione.actions.save*`, `fixcity::segnalazione.actions.remove_*` e `fixcity::segnalazione.inefficiency_types.*` per evitare fallback raw key nella UI.

**Parità checkbox (step 1)**: gli stili del checkbox privacy sono allineati a `segnalazione-01-privacy` tramite regole CSS page-scoped su `div.page-content[data-slug="tests.segnalazione-crea"] .ticket-wizard-root .form-check`. Non aggiungere classi al `<body>` per forzare la parity.

### Stato form (`data`):
I campi del wizard vivono nello stato Filament con `statePath('data')` (vedi `XotBaseWidget`), con wrapper `wizard` (vedi `XotBaseWizardWidget::getWizardSchemaWrapperKey()`). Chiavi persistite sul ticket dopo `normalizeWizardFormState`: `latitude`, `longitude`, `type_id`, `name`, `content`, `email`, `images` (poi `unset` in `prepareTicketData` dove serve). Chiavi solo UX: `location_map` (`dehydrated(false)`), `privacyAccepted`, ecc. `blockData` resta per CMS (titolo pagina, contatti).

### Regola step 3 — review semantica

Nel passo finale di riepilogo, dentro un `Form Schema`, il linguaggio corretto è **Schemas Components** (`Text`, `Section`, `Grid`) e non `Placeholder` come struttura primaria.
Consolidamento attuale: nel widget non restano `Placeholder`; anche la notice privacy read-only usa `Text` con rendering HTML controllato.

### Guardrail runtime

Per questo wizard il criterio di accettazione minimo non è solo la correttezza semantica del codice. Dopo ogni refactor del render path servono anche smoke check runtime reali: il widget compila solo davvero se `/it/tests/segnalazione-crea` risponde `200` entro timeout ragionevole. Mount, model binding e summary pre-submit devono restare minimali.

### Regola multilingua runtime

- Nel PHP runtime non devono comparire label o frasi in italiano.
- Il testo utente vive nei file `lang/{locale}`.
- Gli slug di contenuto non vanno hardcodati nel widget: devono arrivare da CMS/config (`confirmation_slug`) e il redirect deve passare da route name.

### Metodi chiave:
- `defaultFormData()`: inizializza **tutte** le chiavi dei campi degli step (anche vuote), non solo `privacyAccepted`. Se manca una chiave (es. `content`, `type_id`, `images`), Livewire segnala errori **Entangle** in console perché Alpine non trova `data.<campo>` su `$data`. Lo stato deve essere coerente con ciò che va persistito su `Ticket` dopo normalizzazione.
- `submit()`: legge lo stato Filament, aggiunge `owner_id` se disponibile, crea `Ticket`, salva relazioni upload, dispatch evento, redirect a conferma
- `getWizardSteps()`: compone i 3 step nel loro ordine

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
- **View Widget**: `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`
- **Blocco Tema**: `laravel/Themes/Sixteen/resources/views/components/blocks/tests/segnalazione-crea.blade.php`
- **CMS JSON**: `laravel/config/local/fixcity/database/content/pages/tests.segnalazione-crea.json`

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
