---
id: dcp-182
title: "DCP-182: nel riepilogo del wizard le foto caricate si vedono come anteprima"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "riepilogo wizard anteprima foto allegati images TemporaryUploadedFile temporaryUrl preview-file BuildTicketReviewSummaryAction summary-sections review-attachments dcp-182 blueprint"
related: [dcp-33, dcp-128, dcp-146, dcp-181]
blueprint: ../blueprints/riepilogo-anteprima-foto.md
---
# DCP-182: anteprima delle foto nel riepilogo del wizard

Fase BMAD: Analyze, Plan (Filament Blueprint), Implement (TDD), Verify (`reviewing-filament-plans`). Richiesta dell'utente (2026-10-14): in `/it/tickets/create?step=form.riepilogo::data::wizard-step` devono mostrarsi anche le foto caricate al passo prima, come anteprima.

## Lacuna e prova (2026-10-14)
- `BuildTicketReviewSummaryAction::report()` prende da `data.images` solo i **nomi** (`imageNames()`): la scheda «Segnalazione» mostra la riga «Immagini: a.jpg, b.png» e nessuna miniatura.
- Lo stato `data.images` e' del campo `SpatieMediaLibraryFileUpload` (collezione `attachments`, max 5, 10 MB, `image/jpeg`, `image/png`, `application/pdf`) e contiene `TemporaryUploadedFile` di Livewire: `temporaryUrl()` da' un URL firmato (`livewire.preview-file`, 30 minuti, throttle 60/min).
- La vista `Themes/Sixteen/resources/views/components/wizard/summary-sections.blade.php` non ha nessun blocco per gli allegati.

## Piano
`Modules/Fixcity/docs/blueprints/riepilogo-anteprima-foto.md` (skill `planning-filament`, piano di aggiornamento): regola unica R1 nell'Action (anteprima solo per `jpg`, `jpeg`, `png`), nuova chiave `report.attachments`, blocco `data-element="review-attachments"` nella vista solo se ci sono allegati (la parity con il riferimento non cambia a vuoto), chiavi di lingua nelle 4 lingue, test unit, feature, roundtrip Livewire e browser.

## Criteri di accettazione
- [x] Con 2 foto JPG/PNG il riepilogo mostra 2 `<img>` con `src` firmato, `alt` tradotto e `naturalWidth > 0` nel browser.
- [x] Un PDF non ha mai un `<img>` (resta nel nome, nessun riquadro documento: rinviato); un file `svg` non ha mai un'anteprima.
- [x] Rimuovendo/aggiungendo foto al passo prima e riavanzando, il riepilogo mostra l'insieme attuale.
- [x] Nome del file con HTML: escapato.
- [x] Senza allegati: HTML del riepilogo invariato (nessun `data-element="review-attachments"`).
- [x] 4 lingue (Action e vista); axe senza violazioni sul gruppo delle anteprime (browser); PHPStan a zero; Pint; test rossi prima, verdi dopo.
- [x] `reviewing-filament-plans` sul piano e sul codice: eseguito da un agente indipendente, 5 deviazioni chiuse (vedi sotto).

## Non verificato
Vedi la sezione finale del piano.

## Coordinamento con l'altra sessione (2026-10-14, 16:45)
Alle 16:43 un'altra sessione ha cominciato la stessa funzione senza passare da questa story: `BuildTicketReviewSummaryAction` ha `imagePreviews()`, `temporaryPreviewUrl()`, `imageLimit()` e le chiavi `image_previews`, `image_count`, `image_limit`; sono comparsi `config/ticket.php` (limite) e `Themes/Sixteen/resources/views/components/wizard/report-partials/images-preview.blade.php`. Per non scrivere sullo stesso file, **non tocco Action, config e vista**: adotto il loro contratto (`report.image_previews`: lista di URL) e scrivo i criteri come test eseguibili in `Modules/Fixcity/tests/Unit/Actions/BuildTicketReviewSummaryActionTest.php` (blocco «image previews (DCP-182)»). La discussione e' questa; chi implementa li legge e li fa passare.
Tre difetti della bozza, verificati sul sorgente installato (Livewire 4.4.7):
1. **Il metodo non esiste.** `TemporaryUploadedFile` ha `temporaryUrl()` (`vendor/livewire/livewire/src/Features/SupportFileUploads/TemporaryUploadedFile.php:134`), non `getTemporaryUrl()`. Con `method_exists($file, 'getTemporaryUrl')` falso, `temporaryPreviewUrl()` torna sempre `''` e **non compare nessuna anteprima**. Il test «gives a signed preview url» fallisce finche' non si chiama `temporaryUrl()`.
2. **Le stringhe dello stato non sono URL.** `elseif (is_string($file)) $previews[] = $file;` copia in `image_previews` gli identificativi dei media gia' salvati (uuid) o qualunque stringa: la vista li metterebbe in `<img src>` (immagini rotte, e nessun controllo sullo schema). Test: «never turns the strings of the state into preview urls».
3. **Nessun limite sul tipo.** Livewire sa mostrare anche `svg`, `mp4`, `wav`...; il campo ammette solo jpeg, png, pdf, ma l'anteprima deve dipendere dal tipo REALE del file (`guessExtension()`), non dal nome, e restare a `jpg`/`jpeg`/`png`. Test: «never previews an svg», «keeps a pdf in the names but gives it no preview».
Punti da decidere insieme (nessuno consequenziale): l'`alt` delle miniature viene dall'Action (la vista non ha testi propri: lo impone il test «has no text of its own»); `image_count` oggi e' il numero di anteprime dopo il taglio, non quello dei file caricati; il limite di 6 supera il massimo del campo (5): `config('fixcity.ticket.images.limit')` e `maxFiles(5)` devono avere una sola sorgente.


## Verifica indipendente in browser (terza sessione, 2026-10-14 17:00)
Non ho scritto codice di questa funzione: due sessioni la stavano gia' implementando e lo faccio solo come verificatore, per non produrre una terza versione sugli stessi file. Contributo: una sonda riutilizzabile, [`bashscripts/tools/visual/wizard-summary-photos-probe.mjs`](../../../../bashscripts/tools/visual/wizard-summary-photos-probe.mjs). Percorre privacy, dati (2 PNG di proporzioni diverse, 1600x900 e 600x900, piu' 1 PDF) e riepilogo sulla fixture isolata `:8091` e stampa righe, `<img>` con `src`/`alt`/`naturalWidth`/dimensioni, richieste fallite. Uso nella testata del file.

Misure di base, prima del cablaggio (17:00, 1440 px, it):
- Il riepilogo e' raggiunto (`reachedSummary: true`); la riga scheda dice `Immagini relazione.pdf, panchina-portrait.png, buca-landscape.png` (ordine di arrivo degli upload, non di selezione: non asserire l'ordine nei test del browser).
- **Zero foto**: l'unico `<img>` nel passo e' una tessera della mappa (`b.tile.openstreetmap.org`, 0x0). Nessun `preview-file`, nessun `data-element="review-attachments"`.
- Stato confermato: i file sono presenti a render (i nomi arrivano), tutti e tre i passi stanno nel DOM, quindi il riepilogo si rende anche prima di avanzare.
- `summary-sections.blade.php` **non include ancora** `report-partials/images-preview.blade.php`: il partial esiste (con `style="width: 96px"` in linea e `class="img-cover"`, classe da verificare nel CSS compilato; il piano chiedeva `col-6 col-md-4` e non e' stato seguito), ma non e' collegato.
- Per la prova di accettazione: `await expect(page.locator('form.fi-sc-wizard-step:nth-of-type(3) img[src*="preview-file"]')).toHaveCount(2)` con `naturalWidth > 0`, e il PDF **senza** `<img>`.

Cose che verifichero' appena il cablaggio e' in `summary-sections.blade.php`: `naturalWidth > 0` per ogni miniatura a 375 e 1440 px, assenza di richieste fallite (anche i 403 della rotta firmata), `alt` non vuoto nelle 4 lingue, assenza di sbalzi di altezza con foto verticali e orizzontali, e assenza di `<img>` per il PDF.

## Esito (2026-10-14, 17:20)
**Funziona nel browser reale** (Chromium, istanza isolata `scratch-auth-fixture.sh`, DB di prova: il DB di sviluppo non e' stato scritto, 11 utenti e 18 segnalazioni prima e dopo). Spec `Themes/Sixteen/tests/browser/wizard-summary-photo-previews.spec.mjs`: carica 2 PNG e 1 PDF, al Riepilogo compaiono 2 miniature con `src` `/livewire-<hash>/preview-file/...`, `naturalWidth > 0`, `alt` «Anteprima della foto <nome>.png», nel nome restano anche il PDF; tornando indietro, togliendo una foto e riavanzando resta una sola anteprima (quella giusta); nessuna risposta 4xx/5xx. Screenshot a 1440 px guardato: le miniature stanno nella scheda «Segnalazione», sotto le righe e sopra la mappa.
**Che cosa ho fatto davvero (e perche' diverso dal piano iniziale):** l'altra sessione si e' fermata alle 16:43 con codice che non poteva funzionare (vedi Coordinamento). Ho preso io la sua bozza: `imagePreviewItems()` (una sola passata, regola R1), `temporaryPreviewUrl()` con `temporaryUrl()` e filtro sul tipo reale, `image_alts` e `images_label` prodotti dall'Action (la vista non ha testi), partial `images-preview.blade.php` riscritto con classi misurate nello scope Design Comuni e incluso in `summary-sections.blade.php`, limite uguale al massimo del campo (`config/ticket.php` 5, come `maxFiles(5)`), 8 chiavi di lingua (2 per 4 lingue).
**Prove:** Pest 65 passati e 1 saltato (harness esistente) su Action, vista del passo e test dell'autorizzazione del super admin; i quattro test di `image previews` erano rossi prima (e quello preesistente «lists the uploaded file names and ignores stored media identifiers» lo era diventato per la bozza); PHPStan a zero sui file; Pint passato sui file toccati.
**Non fatto / non verificato:** riquadro per i PDF (rinviato nel piano); axe e Firefox/Safari non eseguiti su questa pagina; un file temporaneo scaduto e' coperto solo da test con doppi, non dal browser; l'anteprima ritenta `temporaryUrl()` a ogni resa del passo (URL nuovo ogni volta, firmato 30 minuti): se il passo si rende spesso conviene memorizzarlo; la parity pixel a zero foto non e' stata rimisurata con il gate DCP-33 (il test confronta nuovo con nuovo) e il CLS con foto non 4:3 non e' misurato.

## Revisione `reviewing-filament-plans` e chiusura (2026-10-14, 17:45)
Agente indipendente, sola lettura (rapporto nello scratchpad `review-dcp182.md`): 41 requisiti, 33 conformi, 5 deviazioni, 2 non verificati; nessun difetto visibile all'utente per ispezione. **Chiuse da me, con prova:**
- F-01 roundtrip Livewire: `Modules/Fixcity/tests/Feature/Wizard/WizardSummaryPhotoPreviewTest.php` (upload reale di 2 foto -> 2 miniature con `/preview-file/`; tolta una e caricato un PDF -> 1 miniatura e il PDF solo nel nome; senza foto nessun gruppo).
- F-02: tolti il tetto alle anteprime, `image_count`, `image_limit`, `imageLimit()` e `config/ticket.php` `images.limit` (nessun consumatore; il limite e' `maxFiles(5)` del campo): una sola sorgente.
- F-03 nome ostile (test unit) e coppia alt/URL con input misto (PDF, SVG, JPG, PNG).
- F-04 spec del browser: axe sul gruppo (0 violazioni), 375 px (miniature > 40 px, nessuno scroll orizzontale), rimozione per nome e non per posizione (l'ordine dei wrapper e' quello di arrivo degli upload).
- F-05 vista in 4 lingue (dataset it/en/de/es).
**Errore del piano corretto:** il throttle `60,1` e' dell'upload, non della rotta `preview-file`; la risposta di Livewire ha `Cache-Control: public, max-age=31536000` e l'URL firmato e' un link al portatore per 30 minuti. Le foto sono del cittadino stesso nella sua sessione, ma un URL copiato resta apribile fino alla scadenza: da citare se l'informativa dovesse elencare i trattamenti tecnici.
**Residui dichiarati:** parity pixel a zero foto non rimisurata con il gate DCP-33; CLS con foto non 4:3 non misurato (`width=320 height=240` fissi con `img-fluid`); il `catch` non registra l'errore (un guasto del disco toglierebbe tutte le anteprime senza traccia); l'Action ricalcola gli URL a ogni resa del passo.
**Misure finali:** Pest 71 passati e 1 saltato (Action, vista, roundtrip, autorizzazione super-admin); spec browser 1/1 (22 s); PHPStan su `Modules` vedi il rapporto finale.

## Residuo chiuso (2026-10-14, 20:15)
Il `catch (Throwable)` di `BuildTicketReviewSummaryAction::temporaryPreviewUrl()` non lasciava traccia. Ora registra un `Log::warning` con classe e messaggio dell'eccezione, **senza** nome del file ne' URL firmato. Test rosso prima (`Method warning(<Any Arguments>) ... should be called`), verde dopo; Pest 54 passati su Action, roundtrip Livewire e parita' lingue; PHPStan a zero sui file toccati. Restano: parity pixel a zero foto non rimisurata con il gate DCP-33, CLS con foto non 4:3, URL ricalcolato a ogni resa del passo.

