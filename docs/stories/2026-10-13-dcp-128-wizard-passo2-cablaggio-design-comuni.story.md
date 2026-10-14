---
id: dcp-128
title: "DCP-128: cablaggio del passo 2 del wizard (dati di segnalazione) con le viste Design Comuni"
type: story
epic: dc-parity-v2
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [wizard, design-comuni, passo-2, cablaggio, upload, i18n]
related:
  - Modules/Fixcity/docs/stories/2026-10-13-dcp-35-tickets-create-privacy-dati-coordinamento.story.md
  - Modules/Fixcity/docs/stories/2026-10-13-dcp-43-wizard-passo2-viste-design-comuni.story.md
  - Modules/Fixcity/docs/stories/2026-10-13-dcp-146-wizard-passo-dati-mappa-sempre-funzionante.story.md
qmd: "dcp-128 cablaggio passo 2 wizard dati di segnalazione design comuni data-step dc-section-card upload"
reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html
local: http://192.168.1.40:8000/it/tickets/create?step=form.dati-di-segnalazione%3A%3Adata%3A%3Awizard-step
---

# DCP-128: cablare le viste Design Comuni nel passo 2

## Fase BMAD
Dev (implementazione) -> Verify (gate pixel/HTML, E2E Playwright, a11y, lingue). Specifica gia' scritta in DCP-35 ("Specifica passo 2") e DCP-43; questa story esegue il cablaggio e registra le misure.

## Richiesta (utente, 2026-10-13)
La pagina del passo 2 "si vede male": farla combaciare con `segnalazione-02-dati` (con in piu' la mappa) e farla funzionare davvero (compilare, caricare file, Avanti -> Riepilogo -> salva -> conferma). Ogni elemento ha uno scopo e una funzione.

## Stato misurato prima di toccare (21:20 UTC, Playwright 1440, utente loggato)
Chrome DC ok, contenuto del passo ancora Filament nativo: etichette doppie, campi senza stile, "Files" sovrapposto a "Aggiungi immagini", niente navscroll, niente `.cmp-card`, Indietro/Avanti del footer; altezza 3285 px, 0 errori console, 0 risposte 4xx/5xx, FilePond montato ma SENZA CSS (l'input `filepond--browser` nativo e' visibile) perche' il tema non carica il CSS di Filament.

## Claim (file -> proprietario)
| File | Proprietario | Nota |
|---|---|---|
| `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` (solo `getSteps()` riga `data`, `getDataSchema()`, `getAuthorHtml()`) | DCP-128 | rilettura e edit ancorati; backup in scratchpad `dcp128-backup/` |
| `Modules/Fixcity/resources/views/filament/forms/components/{dc-select,dc-text-input,dc-textarea}.blade.php` + NUOVA `dc-file-upload.blade.php` | DCP-128 | `dc-place-autocomplete` solo cablata (DCP-127) |
| `Modules/Fixcity/lang/{it,en,de,es}/ticket_form.php` | DCP-128 | tutto TRANNE `fields.location.*` (agente mappa) |
| `Themes/Sixteen/resources/views/components/wizard/**`, `.../report-partials/**` (riuso) | DCP-128 | |
| `Themes/Sixteen/resources/css/design-comuni/overrides.css` | DCP-128 | solo in coda, senza toccare le righe DCP-124/125 |
| `Modules/Fixcity/tests/**` | DCP-128 | |

## Sovrapposizione segnalata
La story DCP-146 (lead, scritta alle 21:18 UTC) assegna a un proprio subagent W3-FORM gli stessi file (`TicketForm.php`, viste Fixcity, `lang/*/ticket_form.php`). Questa story e' stata aperta prima dal coordinatore DCP-129 con lo stesso perimetro: io tocco SOLO i metodi del passo dati e le viste elencate sopra; se W3-FORM ha gia' modificato un file lo rilego e rimergio, mai riscrivere.


> **Nota del coordinatore DCP-129 (21:36 UTC):** la stessa richiesta e' stata presa anche dalla sessione DCP-146 (Modules/Fixcity/docs/stories/2026-10-13-dcp-146-wizard-passo-dati-mappa-sempre-funzionante.story.md: lead + W1-LAYOUT, W2-GEO, W3-FORM, W4-TESTS). Decisione presa li' (sezione "DECISIONE DEL COORDINATORE", leggila): **la posizione approssimata da IP tramite terzi e' ABBANDONATA** (GDPR art. 6 e 44, DCP-137); resta il fuso orario gia' fatto. Catena: punto salvato > centro esplicito > GPS > fuso orario > `geo.map.default_view` solo se configurata > mondo con guardia clic. Una sola mano per file: prima di scrivere `ls -l --time-style=full-iso` e `git diff`, rileggi e ancora le tue modifiche; non riscrivere cio' che un altro ha appena fatto.

## Esito (2026-10-13 21:00-22:05 UTC, Dev + Verify; solo misure fatte da me)
### Cosa e' stato cablato
- `TicketForm::getSteps()['data']` = `getDataSchema(designComuni: true)`; `getDataSchema(bool $designComuni = false)` con il default invariato per il back office (`getFormSchema()`), tre metodi nuovi `getLocationSection|getIssueSection|getAuthorSection(bool)` e `getDataStepValidationMessages()`. Front office: Hidden al primo livello + UN `View` (`wizard/data-step`) con le tre schede (`dc-section-card`, id `report-place|report-info|report-author`). Stato e state path invariati.
- Luogo: `location.address` = `dc-place-autocomplete` con `required()` ("Cerca un luogo*"); mappa (`CoordinatePicker('location')`, `geolocateWhenEmpty()` intatto, nessuna coordinata) in blocco bianco `[data-element="location-map"]`, etichetta nascosta e aiuto tolto (ripeteva la descrizione).
- Disservizio: select nativo `dc-select`, "Titolo" (`dc-text-input`, etichetta dalla chiave nuova `fields.title.label`, solo front office: l'admin resta "Nome"), dettagli `rows(2)` + `maxLength(200)` APPLICATO (regola `max:200`) con `dc-textarea`, allegati con la NUOVA `dc-file-upload.blade.php`.
- Autore: nuova `wizard/data-author.blade.php` = `report-partials/info-card` con dati veri (nome, email; Contatti con telefono del profilo se c'e', come il riepilogo); nascosta all'ospite (anche la voce dell'indice). Codice fiscale: nessuna sorgente, non inventato.
- `data-step`: `data-wizard-nav` sulla navigazione (il ripiego del wizard non si duplica), `progress_label` per il nome accessibile della barra dell'indice.
- Allegati: il tema non carica il CSS di FilePond (l'input nativo `filepond--browser` compariva grezzo). `dc-file-upload` tiene il campo Filament (`SpatieMediaLibraryFileUpload`, regole SEC-04: jpeg/png/pdf, max 5, 10 MB, collezione `attachments`) e sostituisce solo il client: `$wire.upload('data.images.<chiave>')`, `$wire.cancelUpload`, `callSchemaComponentMethod(<key>, 'removeUploadedFile')`, controlli client = regole del server, errori del server sotto il campo.
- Lingua: chiavi nuove in it/en/de/es (`required_notice`, `navscroll.title`, `nav.*`, `hints.max_length`, `fields.title.label`, `fields.images.{upload_button,upload_aria,remove,uploading,error_*}`, `validation.{required,min,max}`); nessuna toccata in `fields.location.*`.
- CSS: `12-wizard-dc-form-shell.css` (il reset legacy `!important` di input/select/textarea cancellava bordo e sfondo dei campi DC: aggiunto `:not(.it-page-section *)` sui soli selettori nudi; backup in scratchpad `dcp128-backup/`), `overrides.css` in coda: `.fi-sr-only`, `.fi-fo-field-wrp-error-message`, `.upload-wrapper .img`, contatti sotto i 992 px.
- FUORI PERIMETRO, minimi e dichiarati: `resources/js/theme/design-comuni-home.js` (`dcNavscroll`: contenitore senza altezza dava `aria-valuenow="NaN"`, axe critical `aria-valid-attr-value`; una guardia `rect.height > 0`) e `report-partials/navscroll.blade.php` (`aria-label` opzionale sulla barra, solo se `progress_label`). Docblock di `getSummarySectionSchema()` (`array<string, SchemaComponent>`) e `getAuthorHtml()` tipizzato con `SafeStringCastAction` per portare PHPStan a zero sul file (comportamento invariato).

### Misure (autenticato, dev `http://192.168.1.40:8000`, `isSecureContext=false`)
Gate `parity-gate.sh` passo 2 contro `sito/segnalazione-02-dati.html`. FILTRI dichiarati: `--hide '[data-element="location-map"]'`, `--hide-ref '.upload-wrapper, .btn-wrapper hr'` (la riga demo dell'allegato pesa 81 px e non e' prodotto), `--exclude '[data-element="location-map"]'`. Baseline prima di toccare: altezza locale 3285 (con mappa), campi senza stile.
| Larghezza | pixel | altezze rif/loc |
|---|---|---|
| 1920 | 99,67% | 2983/2983 |
| 1440 | 99,56% | 2983/2983 |
| 992 | 99,37% | 3031/3031 |
| 768 | 99,66% | 3165/3165 |
| 375 | 99,50% | 3975/3975 |
Solo `<main>` (`--selector main`, stessi filtri): 99,59 (1920, prima misura) / 99,45 (1440) / 99,21 (992) / 99,47 (768) / 99,04 (375). Geometria a 1440 (`dcp128-geom.mjs`, riferimento = locale): breadcrumb, h1, stepper, navscroll 306x203, `.steppers-content` 856x1396, schede 255/573/349, autocomplete 808x113, select 800x64, titolo 800x88, dettagli 800x163, blocco allegati 800x136, card autore 800x199, nav 856x99 (Indietro 82x26, Avanti 121x50), contatti 457/526; a 768 e 375 stesse coincidenze (contatti inclusi dopo la regola sotto 992). Con la mappa visibile l'altezza cresce di 523 px a 1440.
HTML: rigorosa 63,49%, url-normalizzati 66,48% (body intero, filtro mappa). Con in piu' i passi 1 e 3 esclusi (`.fi-sc-wizard-step:not(:has(#report-place))`): 69,00 / 72,39. Il solo contenitore delle sezioni: 47,0% (210 contro 300 token). OBIETTIVO >= 90% NON RAGGIUNTO e non raggiungibile restando Filament; cause misurate. Solo locale: wrapper `fi-grid`/`fi-sc-component` (14 tag), `wire:partial`/`wire:key`/`x-data` sui campi, `wire:model` e `id` `form.*` al posto di `title`/`details`/`inefficiency`, 15 `option` al posto di 1, stato Alpine dell'autocomplete. Solo riferimento: demo upload, "Tipo di disservizio**" e "Dettagli**" (refuso del riferimento, noi un solo `*`), `data-bs-autocomplete`. Stessa conclusione di DCP-33, decisione 7.

### Prove funzionali (Playwright, utente master, nessun errore console, nessun 4xx/5xx)
- Flusso intero a 1440: passo 1 (consenso) -> passo 2: clic mappa (lat/lng + indirizzo da geocodifica inversa), tipo, titolo, dettagli, PNG vero 240x160 (81452 byte), Avanti -> Riepilogo (indirizzo, tipo, titolo, dettagli, immagine, email) -> Invia -> `/it/tickets/confirmation?code=TCK-7QING02OFXYUA5UE`. Nel DB di sviluppo: ticket id 64 "Buca profonda in carreggiata", coordinate 41.812267/13.421195, 1 media `attachments` image/png 81452 byte. UN solo ticket di prova creato.
- 375 e 768: fino al Riepilogo, stesso esito (nessun salvataggio). Il primo clic sulla mappa non piazza il pin (guardia di DCP-127 sotto zoom 10: il clic avvicina soltanto); al secondo si'.
- Validazione a vuoto: resta sul passo, 5 errori leggibili in stile DC (indirizzo, punto sulla mappa, tipo, titolo, dettagli). Allegati: GIF rifiutata ("Formato non consentito: usa JPG, PNG o PDF"), 11 MB rifiutato ("Il file supera 10 MB"), rimozione dalla lista, tetto 5 file. Dettagli: 260 caratteri digitati -> 200. "Mostra tutto" apre Contatti; l'indice laterale evidenzia la sezione e la barra avanza (42% a meta' pagina); Indietro torna al passo 1; etichetta flottante di "Titolo".
- Mobile: scrollWidth - clientWidth = 0 a 375 e 768; bersagli: nessuno < 24 px (escluso l'input file nascosto 1x1, il bersaglio e' il pulsante "Carica file"); sotto 44 px restano campi (37-40), "Usa la tua posizione" 28, "Indietro" 26, link del riferimento (W1-LAYOUT li porta a 44 px con `pointer: coarse`, vedi DCP-146).
- axe-core wcag2a/2aa/21a/21aa (3 stati: iniziale, errori, compilato con allegato; 375 e 1440; `dcp128-axe.cjs`): 0 violazioni dopo la correzione di `dcNavscroll` (prima: 1 critical + 1 serious a 1440 sulla barra dell'indice).
- Lingue (`dcp128-i18n.mjs`, passo attivo con errori, it/en/de/es): 0 chiavi grezze. Italiano residuo PRIMA: 4 messaggi di validazione in en/de/es ("Il campo X e' obbligatorio"). Causa: `Modules/Lang/app/Providers/LangServiceProvider::registerFilamentLabel()` applica `__('user::validation')` a OGNI Field e `Modules/User/lang/{en,de}/validation.php` sono copie dell'italiano (es manca). Rimedio nel perimetro (solo campi del passo): `validationMessages` con `fixcity::ticket_form.validation.*`. DOPO: 0 in en/de; in es resta 1 testo, "Usa la tua posizione" da `geo::address.fields.address.use_my_location` (`Modules/Geo/lang/es/address.php` e' in italiano: lo possiede Geo/DCP-127).
- Back office `/fixcity/admin/tickets/create` = 200 e senza markup DC (0 `it-page-section`); test di non regressione sotto.

### Test e PHPStan
- NUOVI: `Modules/Fixcity/tests/Unit/TicketFormDataStepDesignComuniTest.php` (7 test, 71 asserzioni: schema piatto del back office invariato e front office cablato) e `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardDataStepTest.php` (7 test, 65 asserzioni: widget vero con utente, DOM del passo, ospite senza Autore, `max` 200, errori nella lingua della pagina in it/en/de/es). Prova di efficacia: 7 mutazioni, tutte rilevate (senza `maxLength(200)`, select ricercabile, id scheda sbagliato, vista dc-* nel back office, messaggi propri del titolo, senza `data-wizard-nav`, upload di Filament al posto del DC); file ripristinati (`php -l`, `rg`).
- Esecuzione su DB usa-e-getta (`pest-scratch-db.sh` con `APP_CONFIG_CACHE=/nonexistent/config.php`): 69 passed (363 asserzioni: `TicketFormWizardSummarySchemaTest`, `DcFieldViewsTest`, `CreateTicketWizardTamperingTest`, `TicketCreatePageJsonGuardTest`, `TicketCreateVoltRootTest`, nuovo unit); poi `CreateTicketWizardWidgetTest` (Feature e Unit), `CreateTicketWizardWidgetViewTest`, `DcWizardViewsTest` verdi e `TicketFormLangParityTest` rosso per `en` `nav.aria_label` = "Step" (uguale all'italiano), cambiato in "Step navigation" e 12 passed; infine 39 passed (320 asserzioni) con i test di W4 (`Dcp146StepDatiTest`, `Dcp146StepDatiLangParityTest`) e i miei. `TicketReviewSummaryStepViewTest`: 1 skipped come prima.
- PHPStan (`./vendor/bin/phpstan analyse` da `laravel/`): zero errori su `TicketForm.php` e sui due test nuovi.
- Build: `flock /tmp/claude-1000/sixteen-build.lock npx vite build` (4 build, mai insieme), `view:clear` e sentinelle `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale` = 200 e `/it/tickets/create` = 302 da ospite, dopo ogni modifica.

### Non verificato / aperto
- HTML >= 90% (vedi sopra); campi a 44 px col mouse; Safari/Firefox, dispositivi reali; salvataggio a 375/768; focus a pixel; il gate non e' stato misurato con un allegato caricato (si misura senza allegati e con il riferimento privato della riga demo).
- Aperti per altri: es "Usa la tua posizione" (Geo lang); primo clic mappa ignorato (guardia DCP-127, decisione del lead in DCP-146); `Modules/User/lang/{en,de,es}/validation.php` (copie in italiano, es mancante: ogni altro form Filament del front office mostra l'errore in italiano); `div.steppers-content[aria-live=polite]` tenuto come nel riferimento (W4 lo segnala come rumoroso per Livewire: decisione al lead); contatti: variante narrow sopra 992 px e' di DCP-146 (W1), sotto 992 e' mia.
- Backup pre-modifica in `/tmp/claude-1000/-mnt-nas07-var-www--bases-base-fixcity-fila5/e876ab36-5b61-43ab-9633-b3257500d245/scratchpad/dcp128-backup/` (`TicketForm.php.bak`, `lang/`, `12-wizard-dc-form-shell.css.bak`); sonde nello stesso scratchpad (`dcp128-*.mjs|cjs`).
