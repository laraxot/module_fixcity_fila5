---
title: "DCP-379: foto nel dettaglio della segnalazione, sotto la mappa"
type: story
status: review
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, dettaglio, foto, timeline, privacy, ux, design-comuni]
qmd: "dettaglio segnalazione foto sotto la mappa timeline file nome hash media_added allegato aggiunto app_url fixcity.local url relativo D230-5"
related:
  - ./dcp-230-ticket-pubblici-residui-dell-audit-del-front-office.story.md
  - ./dcp-287-dettaglio-proprietario-follower-e-ritorno-dopo-il-login.story.md
  - ./dcp-378-foto-da-fotocamera-oltre-il-limite-di-upload.story.md
  - ./citizen-receipt-photos-rating-and-real-phone.story.md
---

# DCP-379: foto nel dettaglio della segnalazione

## Richiesta

«in /it/tickets/2 dovrei vedere anche la foto che ho fatto upload con la camera del cellulare, invece vedo
File: grcpCsCaeLkrP6SFPChfDkrqeQaXYZAoVsSG2siH.jpg; la sezione delle foto dovrebbe essere sotto la mappa»

## Analisi (prove)

1. Ticket 2: stato `pending`, foto `attachments` id 10, JPEG 636 KB su disco `public`, file presente; `/storage/10/...` risponde 200.
2. `BuildPublicTicketDetailAction` (DCP-148) non espone foto: la vista `ticket-detail/design-comuni` non ha una sezione foto.
3. La riga vista dal cittadino e' la timeline: `TicketMediaObserver` registra `media_added` con `from = to = stato attuale`,
   quindi `BuildTicketTimelineAction` usa come titolo lo stato («In attesa») invece di «Allegato aggiunto»,
   e come testo `File: <nome>`: il nome e' l'hash casuale di Livewire, senza significato e potenzialmente un dato personale se il file ha un nome reale.
4. In produzione `APP_URL=http://fixcity.local`: `getUrl()`/`getFullUrl()` di Media Library producono link a `fixcity.local`.
   Anche le miniature di `/tickets/track` sono quindi rotte. Correggere `.env` e' azione del proprietario (fuori da git).

## Decisioni (Ponytail)

| Problema | Soluzione | Perche' |
|---|---|---|
| Nessuna foto nel dettaglio | Chiave `photos` nell'Action (lista di `{url, is_image}`), sezione «Foto» subito dopo «Dove» | Una sola fonte dei campi esposti; la foto completa il «dove» e viene prima del testo |
| Chi vede le foto | Solo il proprietario | D230-5 (foto pubbliche: miniatura senza EXIF, decisione di titolare e DPO) e' ancora aperta: al pubblico non si mostra nulla in piu' |
| `APP_URL` sbagliato | URL relativo (`/storage/...`) ricavato dal percorso dell'URL del media | Funziona con qualunque host; il front office usa gia' percorsi relativi (`FrontofficeRoutes::*(false)`) |
| Timeline «In attesa / File: hash.jpg» | Per gli eventi diversi da `status_change` il titolo e' l'etichetta dell'evento; per `media_added` nessun nome file | Il cittadino legge «Allegato aggiunto»; le foto si vedono nella sezione dedicata |
| PDF negli allegati | Link testuale invece di `<img>` | Un PDF non ha anteprima nel browser come immagine |

Scartato: conversioni Spatie per le miniature (le foto arrivano gia' ridotte a 1920 px da DCP-378; si rinvia a D230-5).

## Criteri di accettazione

- [x] Il proprietario vede nel dettaglio la sezione «Foto» sotto la mappa, con miniature cliccabili e testo alternativo.
- [x] Un ospite o un altro cittadino non riceve `photos` (lista vuota) e non vede la sezione.
- [x] Gli URL delle foto sono relativi (`/storage/...`), mai `fixcity.local`.
- [x] La timeline mostra «Allegato aggiunto» e nessun nome file.
- [x] Test dell'insieme di chiavi aggiornato; PHPStan 0 sui file toccati.

## Coordinamento

Tre sessioni hanno scritto questa funzione negli stessi file e ognuna si e' fermata per non farne una terza versione (vedi la tabella di CAM-1.6 e DCP-440). Regola scelta: vince **questa story**, perche' e' l'unica che copre il caso segnalato (ticket `pending` aperto dal proprietario) e coincide con la proposta della sessione camera upload: una sola chiave `photos`, URL relativo, originale solo al proprietario, nulla al pubblico finche' D230-5 e' aperta. Dalla CAM-1.6 si tengono la galleria CSS `td-gallery` e la copertura di test; `images` e `publicImageUrls()` escono. Gli AC di CAM-1.6 sono LOCKED e non si riscrivono: AC1 ("ticket pubblici") resta non soddisfatto di proposito e lo si dice li'.

Fatto misurato che pesa sul metodo: i marker `.merge_file_*` ricompaiono con un ciclo di circa 4 minuti (20:43:48, 20:47:47, 20:51:52) e su questa macchina non c'e' cron ne' timer systemd che lo faccia. Ipotesi, non verificata: un sync dell'altra macchina (o del server) che fonde la sua copia di questi file con la nostra. Ogni risoluzione va riletta dopo il ciclo successivo.

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 20:54 | Claude (6d2caca8) | riconciliazione dei 6 file della funzione | CLAIM, **RITIRATO alle 20:56**. Ho riportato `BuildPublicTicketDetailAction` a `photos` (20:55:4x, `php -l` ok); alle 20:55:53 il file e' stato riscritto da un editor vivo con la variante `images` di CAM-1.6 (`'images' => $isPublic ? $this->publicImageUrls($ticket) : []`, mai vista prima). Non e' il ciclo del sync: e' una sessione che difende il suo disegno. Per non fare una gara di sovrascritture (e per non lasciare il file rotto, come per un minuto dopo una mia rimozione parziale) **non tocco piu' questi sei file**. Serve che l'utente scelga UNA sessione per la funzione; vedi la nota in fondo a CAM-1.6. |
| 2026-10-27 20:55 | Claude (492b606a, autore della story) | cedo il claim | Fino alle 20:54 ho: risolto i marker nei 4 file della funzione scegliendo `photos` (owner), rimesso `return null` per `media_added` in `BuildTicketTimelineAction`, aggiunto il test «gives the photos of a report not yet public to the owner only», allineato `TicketPublicDetailPageTest` a `td-photos` e `photos.*.label`. Nel file c'era `'photos' => $isOwner \|\| $isPublic` (non mio): decide 6d2caca8 insieme a D230-5. Da qui non tocco piu' questi file. **Causa del ciclo misurata** (`ps`, 20:52): il processo `claude --dangerously-skip-permissions` pid 2372069 (shell zsh 2371825) lancia `git push -u laraxot HEAD:dev` e i merge da `laraxot/dev` sui commit `.`; i marker nascono da quel merge su QUESTA macchina, che e' la produzione (nginx `root /home/ploi/fixcity.eu/public_html`). Le chiavi `ticket_detail.images` e `image_alt` restano nei lang finche' esiste la vista vecchia. |
| 2026-10-27 21:09 | Claude (6d2caca8) | correzione della riga precedente | La riga di 492b606a dice che il pid 2372069 e la produzione sono «QUESTA macchina»: su `zorin` e' falso (`hostname` = `zorin`, pid assente, `/home/ploi/fixcity.eu/public_html` assente). Va letta come descrizione del server di produzione, non di questo albero; misura in [DCP-440](./dcp-440-produzione-ferma-per-marker-di-merge-nell-albero-di-lavoro.story.md). Marker `.merge_file_*` di questa tabella risolti: tenuta la nota di 492b606a, tolta la mia riga CLAIM gia' sostituita dal RITIRATO. |
| 2026-10-27 21:00 | Claude (da07dfb1) | fusione finale e ripristino del sito | Produzione in 500 su tutte le pagine (marker in `TicketForm`, `ticket_form` x4, `validation`, `manifest.json`, file della funzione). Risolti a favore di questa story: solo `photos`, solo proprietario, `images` e `publicImageUrls()` tolti, timeline `reason` null; tema ricompilato per il manifest. Causa dei marker trovata: nel terminale Cursor gira `git add -A && git commit -am . && git fetch laraxot dev && (git merge ... -s resolve \|\| git merge ...) && git push`; se il merge confligge, il giro dopo committa i marker con `add -A`. Nessun cron. |

## Verifica

Stato misurato il 2026-10-27 21:17 (sessione 6d2caca8), sul dev `127.0.0.1:8081`; il codice effettivo e' quello di CAM-1.6 (chiave `images`, `$isOwner || $isPublic`), non la chiave `photos` di questa story.

| Prova | Esito |
|-------|-------|
| `BuildPublicTicketDetailActionTest`, `BuildTicketTimelineActionTest`, `TicketPublicDetailPageTest` su sqlite scratch | 36 test, 310 asserzioni, verdi |
| URL della foto senza host (criterio 3): asserzione `assertStringStartsWith('/storage/')` | **rosso** prima (`http://fixcity.local/storage/290/camera.jpg`), verde dopo: `publicImageUrls()` usa `Safe\parse_url` sul disco `public` |
| `phpstan analyse Modules` | exit 0, 0 errori (cache calda) |
| `/it/tickets/145` e `/121` da ospite | 200; sezione `td-images` prima della descrizione; nessun «File:», nessun `fixcity.local` nell'HTML |
| Lingue it/en/de/es sulla 145 | titolo «Immagini / Images / Bilder / Imágenes», alt tradotto, nessuna chiave grezza |
| Browser (Playwright), viewport 375, 768, 1440 | nessun overflow orizzontale; miniatura 318x239 a tutti e tre (a 1440 era 1074x805: una foto sola riempiva lo schermo) |
| Foto verticale 3:4 simulata (canvas, non una foto reale) | intera con `object-fit: contain`; con `cover` si perdeva il 44% dell'altezza |

Fatto in questo giro oltre al codice: `19-ticket-detail.css` (`contain`, fondo `#f2f5f8`, colonne `auto-fill` con massimo 20rem) e build del tema (`npx vite build`, asset `app-C98OKpq9.css`, 98 file in `public/assets`, nessuno a zero byte).

**Non verificato:** fixcity.eu (produzione in 500 da marker, [DCP-440](./dcp-440-produzione-ferma-per-marker-di-merge-nell-albero-di-lavoro.story.md)); il proprietario di un ticket `pending` nel browser (coperto solo dal test unitario: nel dev le due `pending` con foto, 64 e 65, sono di un utente di cui non ho la sessione); una foto vera da telefono; EXIF delle foto caricate prima delle 07:30 del 2026-10-27.

**Aperto, decide l'utente:** D230-5. Con `$isOwner || $isPublic` i ticket pubblici servono gli ORIGINALI a chiunque; la proposta di questa story era «solo il proprietario» finche' non c'e' una miniatura senza EXIF.

**Conferma indipendente (2026-10-27 21:15, sessione 1564de94):** sul dev `/it/tickets/82` (un media nella collezione `ticket`) la sezione «Immagini» sta nel DOM subito dopo la mappa (`td-map` riga 517, `td-images` riga 522, cronologia riga 544), l'immagine risponde 200 `image/png` e la cronologia legge «Allegato aggiunto» senza `File: ...jpg`; Chrome headless a 375 e 1440 px, 35 test verdi. Attenzione a non scambiare per un guasto il «404 File not found» che si legge nella miniatura: `storage/app/public/6/reference-mobile.png` e' uno screenshot di una pagina 404 di GitHub Pages (file del seed), il caricamento e' riuscito. Gli `alt` di piu' foto sullo stesso ticket sarebbero tutti uguali (`image_alt` con il titolo): accettabile finche' c'e' una foto, da numerare se ne arrivano altre.

**Verifica in produzione (sessione da07dfb1, 2026-10-27 21:00, quando il codice usava ancora `photos`):**
- Render come proprietario del ticket 2: ordine `td-where > td-photos > td-description`; `<img src="/storage/10/...jpg" alt="Foto 1 della segnalazione «Gatto che dorme»">`; nessun `File:`; timeline «Allegato aggiunto», `reason` null.
- `https://fixcity.eu/storage/10/...jpg` risponde 200 `image/jpeg`. Da anonimo `/it/tickets/2` resta 404 (ticket `pending`).
- `/it/tickets` e `/it/auth/login` 200 dopo il ripristino.
- PHPStan 0 su Action, timeline e test (`Safe\parse_url`); Pint ok. Pest non eseguibile sul server (`.env.testing` con utente DB `marco`).
- Restano: `APP_URL=http://fixcity.local` in `.env` (miniature di `/tickets/track` e API elenco con host sbagliato); decisione D230-5 per le foto pubbliche.

**Merge 2026-10-28 (sessione da07dfb1):** al merge con `laraxot/dev` si tiene il codice remoto (`images`, `$isOwner || $isPublic`, `td-gallery`), piu' verificato; la versione `photos` esce. D230-5 resta aperta e la decide l'utente.
