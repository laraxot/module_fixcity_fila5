---
title: "Blueprint: anteprima delle foto nel passo riepilogo del wizard di segnalazione"
type: blueprint
status: implemented
created: 2026-10-14
updated: 2026-10-18
module: Fixcity
tags: [blueprint, filament, wizard, riepilogo, upload, anteprima, dcp-182]
qmd: "blueprint filament wizard riepilogo anteprima foto allegati images SpatieMediaLibraryFileUpload TemporaryUploadedFile temporaryUrl preview-file BuildTicketReviewSummaryAction summary-sections dcp-182"
issues: []
discussions: []
related:
  - ../stories/dcp-182-riepilogo-wizard-anteprima-delle-foto.story.md
  - ../stories/dcp-128-wizard-passo2-cablaggio-design-comuni.story.md
---

# Blueprint: anteprima delle foto nel passo riepilogo

Piano `planning-filament` (filament/blueprint 2.4.0, Filament 5.10.1, Livewire 4.4.7, Laravel 13.35.0). **E' un piano di aggiornamento**: nessun resource, modello o migrazione nuovi; solo modifiche (Modify/Add) a un'Action, a una vista Blade, ai file di lingua e ai test. Non contiene codice applicativo.
Quando si modifica `resources/js/app.js`, `camera-upload.js`, `package.json` o
la configurazione Vite del tema, il build Sixteen è parte obbligatoria della
verifica: il comportamento mobile esiste solo nel bundle servito dal manifest.

## Richiesta
Alla pagina `/{lang}/tickets/create?step=form.riepilogo::data::wizard-step` (passo `summary` del wizard) il cittadino deve vedere come **anteprima** le foto caricate nel passo precedente (`data`). Oggi vede solo i nomi dei file nella riga «Immagini» e nella lista `report.images`.

## Decisioni (nessuna consequenziale: si procede)
| Scelta | Valore | Perche' non e' bloccante |
|---|---|---|
| Quali file hanno l'anteprima | Solo `jpg`, `jpeg`, `png` | sono i soli tipi immagine che il campo accetta (`acceptedFileTypes(['image/jpeg','image/png','application/pdf'])`); `svg` e gli altri tipi che Livewire sa mostrare restano esclusi per sicurezza |
| PDF | Nessuna immagine: un riquadro con icona documento e nome del file | il PDF non ha un'anteprima sicura nel browser senza un renderer |
| Ingrandimento al clic | No | puramente cosmetico, aggiungibile |
| Dimensione | miniature in griglia, 2 per riga su mobile, 3 da `md` | cosmetico |
| Dove | Sotto la riga di testo, dentro la scheda «Segnalazione», prima della mappa | non cambia schema ne' permessi |
Se non ci sono allegati **non si aggiunge nessun markup**: la parity pixel/HTML con `sito/segnalazione-03-riepilogo.html` (fixture senza foto) non deve cambiare.

## Cross-requirement: regola unica e stadi
- **Regola R1 (anteprima):** un allegato ha anteprima se e solo se `$file instanceof Illuminate\Http\UploadedFile`, `$file instanceof Livewire\Features\SupportFileUploads\TemporaryUploadedFile`, la sua estensione indovinata (`guessExtension()`) e' in `['jpg','jpeg','png']` e `isPreviewable()` e' vero. L'URL e' `$file->temporaryUrl()`: rotta firmata `livewire-<hash>/preview-file/...`, valida 30 minuti. **Correzione (revisione 2026-10-14):** il throttle `60,1` e' dell'UPLOAD, non di questa rotta (`FilePreviewController` ha solo il middleware `web`); la risposta ha `Cache-Control: public, max-age=31536000`, e l'URL firmato e' un link al portatore, non legato alla sessione, per la sua validita'. La regola vive in **un solo punto**: `BuildTicketReviewSummaryAction`. La vista non decide nulla.
- **Stadi:** (1) passo `data`: l'utente carica, lo stato `data.images` contiene `TemporaryUploadedFile` indicizzati per chiave; (2) clic su Avanti: il passo `summary` si rende da `$livewire->data` (`TicketForm::getReviewStepSchema()` chiama l'Action a ogni render); (3) l'utente torna indietro, rimuove una foto, ne aggiunge un'altra, riavanza: il riepilogo deve mostrare l'insieme **attuale**, non quello del primo ingresso; (4) Invia: i file diventano media della collezione `attachments` e l'anteprima temporanea non serve piu'.
- **Consistency check (discriminante):** 2 foto -> 2 anteprime; rimossa 1 e aggiunto 1 PDF -> 1 anteprima + 1 riquadro documento; nessuna foto -> nessun blocco. Il numero di elementi nel riepilogo deve coincidere con il numero di voci in `data.images` in ogni stadio.
- **Denial prima della mutazione:** il riepilogo non scrive nulla; l'anteprima non cambia lo stato del form. Un file con estensione non ammessa non arriva mai a `temporaryUrl()` (verificato prima).

## 1. Commands
Nessun comando di scaffolding. Dopo le modifiche: `php artisan view:clear` (le viste sono in cache) e, se si è modificato un asset del tema, `npm run build` da `Themes/Sixteen`, seguito dalla verifica del manifest e della presenza di `FixcityCameraUpload` nel bundle.

## 2. Models
Nessuna modifica. Lo stato del campo e' `data.images` (campo `Filament\Forms\Components\SpatieMediaLibraryFileUpload`, collezione `attachments`, `maxFiles(5)`, `maxSize(10 * 1024)` KB, `acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])`, definito in `TicketForm::getDataSchema()`).

## 3. Resources (componenti da modificare)
> **Aggiornamento 16:45:** un'altra sessione ha gia' cominciato questa Action con il contratto `report.image_previews` (lista di URL, vedi la story DCP-182, sezione Coordinamento). Il contratto con `attachments` descritto sotto e' superato dai test del blocco «image previews (DCP-182)»: restano valide la regola R1 (solo tipo reale jpg/jpeg/png, URL = `temporaryUrl()`), il comportamento con file scaduti e il trattamento di PDF/SVG come semplici nomi. I riquadri documento per i PDF sono rinviati.

### 3.1 Modify: `Modules\Fixcity\Actions\BuildTicketReviewSummaryAction` (`laravel/Modules/Fixcity/app/Actions/BuildTicketReviewSummaryAction.php`)
Non e' un componente Filament: e' l'Action (Spatie `QueueableAction`) che prepara i dati del passo; Docs del flusso: https://filamentphp.com/docs/5.x/schemas/wizards.
- **Add** chiave `report.attachments`: `list<array{name: string, kind: 'image'|'document', preview_url: string|null, alt: string}>`, una voce per ogni `UploadedFile` di `data.images`, nello stesso ordine. `kind = 'image'` e `preview_url` valorizzato solo se vale R1; altrimenti `kind = 'document'`, `preview_url = null`. `alt` = testo tradotto `fixcity::ticket_review.report.attachment_alt` con `:name`.
- **Mantenere** `report.images` (lista dei nomi) e la riga `images` in `rows`: i test esistenti (`BuildTicketReviewSummaryActionTest`) li asseriscono e sono il testo alternativo per chi non vede le miniature.
- Il nome viene da `getClientOriginalName()` (input non fidato): la Action lo passa cosi' com'e', **l'escape e' di Blade** (`{{ }}`); non usare `{!! !!}`.
- Se `temporaryUrl()` lancia un'eccezione (file temporaneo scaduto o non piu' presente): catturare `Throwable`, trattare l'allegato come `document`, non rompere il passo. Nessun `@` per sopprimere errori.
- Aggiornare il docblock `@return` di `execute()` e di `report()` con la nuova chiave.

### 3.2 Modify: `laravel/Themes/Sixteen/resources/views/components/wizard/summary-sections.blade.php`
Subito dopo `@include($parts.'.info-summary', ...)` della scheda «Segnalazione» e **prima** del blocco `@if (filled($report['location'] ?? null))` della mappa, aggiungere `@if (filled($report['attachments'] ?? []))` con:
- un contenitore `<div class="row mt-3" data-element="review-attachments" role="list" aria-label="...">` (aria-label = `fixcity::ticket_review.report.attachments_label`);
- per ogni allegato `<div class="col-6 col-md-4 mb-3" role="listitem">`; se `kind === 'image'`: `<img class="img-fluid rounded border" loading="lazy" decoding="async" width="320" height="240" src="{{ $a['preview_url'] }}" alt="{{ $a['alt'] }}">`; se `document`: `<div class="d-flex align-items-center gap-2 border rounded p-3">` con l'icona `<svg class="icon icon-sm" aria-hidden="true"><use href="{{ $icon('it-clip') }}"></use></svg>` e `<span class="text-paragraph-small text-truncate">{{ $a['name'] }}</span>`.
- **Classi misurate nel CSS compilato `app-UD7Kp0l8.css` (2026-10-14)**: esistono come regole `body:has(.page-content[data-dc]) ...` (cioe' valgono nelle pagine Design Comuni) `row`, `col-6`, `col-md-4`, `mt-3`, `mb-3`, `p-3`, `img-fluid`, `rounded`, `border`, `d-flex`, `align-items-center`, `gap-2`, `text-truncate`, `text-paragraph-small`, `icon`, `icon-sm`, `w-100`, `h-100`, `d-block`. **Non esistono** nello scope DC `g-3`, `gy-3`, `overflow-hidden`, `text-break`, `object-fit-cover`, `ratio-1x1`: non usarle. Le regole del tema legacy (`.img-fluid:not(:where(body:has(.page-content[data-dc])...))`) sono escluse dalle pagine DC dal plugin PostCSS: contano solo quelle con prefisso `body:has(.page-content[data-dc])`. Il sprite `it-clip` esiste in `public/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg`.
- Larghezze/altezze esplicite sulle `<img>` per evitare CLS (WCAG 1.4.10 e Core Web Vitals).

### 3.3 Add: chiavi di lingua `fixcity::ticket_review.report.attachments_label` e `.attachment_alt` (con `:name`) e `.attachment_document` (testo del riquadro per i PDF)
File: `laravel/Modules/Fixcity/lang/{it,en,de,es}/ticket_review.php` (oggi 34 righe it/en, 31 de/es). Testi:
- it: `Foto e documenti allegati` / `Anteprima della foto :name` / `Documento`
- en: `Attached photos and documents` / `Preview of photo :name` / `Document`
- de: `Angehaengte Fotos und Dokumente` / `Vorschau des Fotos :name` / `Dokument`
- es: `Fotos y documentos adjuntos` / `Vista previa de la foto :name` / `Documento`
(de: scrivere i caratteri accentati reali se il file e' UTF-8, come gli altri file della cartella.)

## 4. Authorization
Nessuna nuova. Il wizard e' dietro `auth` (`tickets.create.json`, `middleware: ["auth"]`, decisione D17). L'URL di anteprima e' **firmato** da Livewire, scade in 30 minuti ed e' contenuto solo nella pagina dell'utente che ha caricato il file. Non rendere l'URL stabile ne' pubblico, non salvarlo in DB, non loggarlo.

## 5. Widgets
Nessuno.

## 6. Tests
Pest, con `pest-scratch-db.sh` (mai il DB di sviluppo); i test di `Modules/Gdpr` non avviano l'app, questi stanno in Fixcity.
1. **Unit** `Modules/Fixcity/tests/Unit/Actions/BuildTicketReviewSummaryActionTest.php` (estendere): (a) due `TemporaryUploadedFile` (jpg, png) -> `attachments` con 2 voci `image`, `preview_url` che contiene `livewire/preview-file`, stesso ordine di `images`; (b) un PDF -> `document`, `preview_url === null`; (c) un file `svg` -> `document` (non si mostra mai, anche se Livewire lo considera anteprimabile); (d) `temporaryUrl()` che lancia -> `document`, nessuna eccezione; (e) `images` vuoto o non array -> `attachments === []`; (f) nome con `<script>` conservato verbatim nei dati. Usare dobles come il test gia' presente alla riga ~210 (`Mockery::mock(TemporaryUploadedFile::class)`).
2. **Feature** `Modules/Fixcity/tests/Feature/Wizard/TicketReviewSummaryStepViewTest.php` (estendere): vista resa con `attachments` -> contiene `data-element="review-attachments"`, un `<img` per ogni immagine con `src` uguale a `preview_url` e `alt` tradotto, e **nessun** `data-element="review-attachments"` quando `attachments` e' vuoto; il nome `<script>x</script>` compare **escapato** (`&lt;script&gt;`), mai come tag. In 4 lingue.
3. **Roundtrip Livewire** (Feature, `Livewire::test(CreateTicketWizardWidget::class)` con utente autenticato): caricare `UploadedFile::fake()->image('a.jpg')` in `data.images`, andare al passo `summary` -> la vista contiene l'anteprima; rimuovere la foto e aggiungerne un'altra -> il riepilogo mostra solo la nuova (stadio 3 della regola R1). Se il test del widget esistente `persists a valid wizard submission` e' rosso per cause preesistenti (nota DCP-34), non toccarlo e dichiararlo.
4. **Browser** (Playwright, utente di prova autenticato, `http://127.0.0.1:8091`): caricare 2 PNG, avanzare al riepilogo, `await expect(page.locator('[data-element="review-attachments"] img')).toHaveCount(2)` e `naturalWidth > 0` per ciascuna (immagine caricata davvero); axe senza violazioni; nessuna richiesta fallita; schermate a 375 e 1440 px.
5. **Parity**: con zero foto il gate di pixel del riepilogo (`DCP-33`) non cambia (stesso HTML).

## Verifiche fatte e non fatte
Fatte sul sorgente installato: `TemporaryUploadedFile::isPreviewable()` e `temporaryUrl()` (Livewire 4.4.7: rotta firmata `livewire.preview-file`, scadenza 30 minuti, throttle `60,1`), campo `SpatieMediaLibraryFileUpload` e mime ammessi in `TicketForm.php`, struttura e test della `BuildTicketReviewSummaryAction`.
**Non fatte:** `search-docs` di Boost non raggiungibile in questa sessione (server MCP non connesso): gli URL `filamentphp.com/docs/5.x/...` non sono stati aperti; il comportamento esatto dello stato `data.images` dopo ogni passaggio (`livewire-file:` o oggetto) va confermato dal test 3; le classi CSS del punto 3.2 sono state misurate nel CSS compilato (tutte presenti, tranne quelle elencate come assenti); l'aspetto reale va confermato dal test del browser.

## Correzione 2026-10-18 — fotocamera mobile

Le fotocamere iOS possono fornire file HEIC/HEIF, mentre la collezione `attachments` accetta intenzionalmente solo JPEG, PNG e PDF. Il picker client espone HEIC/HEIF e il bundle Sixteen li converte in JPEG con `heic2any` prima di chiamare `$wire.upload()`; la whitelist server non viene ampliata e quindi il vincolo di sicurezza resta unico. Il fallback con canvas copre i browser che decodificano nativamente HEIC. Se nessun decoder e' disponibile, l'upload viene rifiutato senza creare ticket o media orfani. Le estensioni JPEG/PNG/PDF vengono riconosciute anche quando il browser mobile omette il MIME.


## Esito della revisione `reviewing-filament-plans` (2026-10-14)
Revisione indipendente in sola lettura (agente): 41 requisiti in scope, 33 conformi, 5 deviazioni, 2 non verificati. Deviazioni e come sono state chiuse: F-01 roundtrip Livewire mancante -> `tests/Feature/Wizard/WizardSummaryPhotoPreviewTest.php`; F-02 tetto alle anteprime e chiavi `image_count`/`image_limit` senza consumatore -> tolti (`imageLimit()`, `array_slice`, config `images.limit`): il limite e' `maxFiles(5)` del campo; F-03 nome ostile -> test unit; F-04 axe e 375 px mancanti -> aggiunti allo spec del browser (passa, axe senza violazioni sul gruppo); F-05 quattro lingue nella vista -> dataset it/en/de/es. Errore del piano corretto (throttle). Residui: parity pixel a zero foto non rimisurata con il gate DCP-33 (il test confronta nuovo con nuovo); CLS con foto non 4:3 non misurato; `catch` senza log.
