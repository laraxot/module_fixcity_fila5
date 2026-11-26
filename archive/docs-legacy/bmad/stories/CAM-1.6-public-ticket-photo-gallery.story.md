---
story_id: CAM-1.6
epic: CAM-1
title: "Mostrare le foto della segnalazione sotto la mappa"
type: story
status: review
owner: Fixcity + Sixteen
priority: P1
---

# CAM-1.6: Mostrare le foto della segnalazione sotto la mappa

## Story

Come cittadino, voglio vedere nella pagina pubblica della segnalazione le foto
che ho caricato dalla fotocamera, sotto la mappa, così posso verificare che la
documentazione del problema sia arrivata correttamente.

## Acceptance Criteria (LOCKED)

1. Il payload del dettaglio pubblico espone solo gli URL delle immagini della
   collection `attachments` per ticket pubblici; PDF e dati privati sono esclusi.
2. La pagina renderizza una galleria fotografica dopo la mappa e prima della
   descrizione, senza mostrare il nome hash del file come contenuto principale.
3. Le immagini hanno alt localizzato, lazy loading, link alla versione completa
   e layout responsive a 375/768/1440 px.
4. Un ticket senza foto non mostra una sezione vuota; il ticket continua a
   funzionare con mappa assente o immagine non disponibile.
5. La timeline non espone più il nome interno/hash del file come messaggio
   pubblico; il contenuto fotografico vive nella galleria.
6. Test unitari e feature verificano URL, privacy, ordine mappa → foto →
   descrizione e assenza di regressioni multilingua.

## Tasks / Subtasks

- [x] Aggiungere `images` al contratto esplicito di
  `BuildPublicTicketDetailAction`, filtrando MIME immagine e visibilità.
  (AC: #1, #4)
- [x] Renderizzare la galleria nel blocco condiviso ticket-detail subito dopo
  la mappa. (AC: #2, #3, #4)
- [x] Rimuovere il filename dal testo pubblico della timeline media. (AC: #5)
- [x] Aggiungere test dell’action e della pagina pubblica con media reale.
  (AC: #1, #6)
- [x] Aggiornare CSS Sixteen e verificare il markup nei quattro locali.
  (AC: #3, #6)

## Dev Notes (LOCKED)

- `BuildPublicTicketDetailAction` è la fonte unica dei campi pubblici e oggi
  omette deliberatamente `images`; il cambiamento deve restare esplicito e
  filtrato. [Source: ../../../app/Actions/BuildPublicTicketDetailAction.php]
- Le foto del wizard finiscono nella collection Ticket `attachments`, che
  accetta JPEG/PNG/PDF; la galleria deve mostrare soltanto immagini.
  [Source: ../../../app/Models/Ticket.php]
- Il blocco reale del dettaglio pubblico è il componente Sixteen
  `blocks/ticket-detail/design-comuni`; la sezione va dopo `td-map`.
  [Source: ../../../../Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php]
- Le etichette `images` e `image_alt` esistono già nel catalogo `ticket_detail`;
  non introdurre stringhe visibili hardcoded. [Source: ../../../lang/it/ticket_detail.php]
- Il Filament Blueprint richiede separazione tra dominio Fixcity, persistenza
  Media e markup del tema. [Source: ../filament-blueprint.md]

## Testing

- **Approach:** unit action + feature public detail + Blade/CSS smoke.
- **Key scenarios:** un’immagine JPEG, più immagini, PDF escluso, nessuna
  immagine, ticket pubblico, ticket privato visto dal proprietario, guest.
- **Per-AC verification:** AC #1 payload assertion; AC #2 DOM order; AC #3
  alt/URL/responsive; AC #4 empty state; AC #5 timeline text; AC #6 locale matrix.
- **Fixtures / mocks:** `UploadedFile::fake()->image()`, PDF fixture, storage
  fake e ticket factory.
- **Edge cases:** URL conversione assente, media cancellato, MIME nullo,
  titolo contenente HTML, viewport stretta.

## Dependency Maps

- **Blocked by:** CAM-1.3 per il contratto Media; CAM-1.1 per il caricamento
  iniziale dei file.
- **Blocks:** CAM-1.5 release gate.
- **External:** storage pubblico/Media Library, CSS Sixteen.

## Owned File/Module Scope

- `laravel/Modules/Fixcity/app/Actions/BuildPublicTicketDetailAction.php`
- `laravel/Modules/Fixcity/app/Actions/BuildTicketTimelineAction.php`
- `laravel/Modules/Fixcity/tests/Unit/Actions/BuildPublicTicketDetailActionTest.php`
- `laravel/Modules/Fixcity/tests/Feature/Pages/TicketPublicDetailPageTest.php`
- `laravel/Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php`
- `laravel/Themes/Sixteen/resources/css/app/19-ticket-detail.css`

## Learnings from Previous Stories

- L’upload riuscito non prova la visualizzazione: persistenza e read-model
  pubblico sono due contratti separati. (from CAM-1.2, CAM-1.3)
- Non esporre il filename interno come sostituto dell’immagine: il payload
  deve fornire URL media espliciti. (from FIXCITY-MEDIA-001)

## Dev Agent Record

- Root cause confirmed: the public detail action omitted media, while the
  timeline exposed the upload filename instead of a visual representation.
- Implemented the smallest cross-layer fix: explicit image-only URLs in the
  Fixcity action, the existing Sixteen ticket-detail block, responsive CSS,
  and localized alt text. PDF and non-public ticket media remain out of the
  public payload.
- Added action and page regression coverage for gallery content, privacy, MIME
  filtering and map/gallery/description order. Real device/production checks
  remain pending.
- Verification 2026-10-27: `./vendor/bin/phpstan analyse Modules` completed
  with exit 0 and no findings; the action suite passed and
  `TicketPublicDetailPageTest` passed (22 tests, 231 assertions). The page
  test also confirms the localized `images` label, image-only DOM and gallery
  order after the map and before the description. Production and real-device
  checks remain outside this environment.

## Coordinamento

Revisione di un'altra sessione (Claude, camera upload), 2026-10-27 20:50. Nessun file di codice toccato.

| # | Trovato | Prova |
|---|---------|-------|
| 1 | Due sessioni scrivono la stessa galleria in `BuildPublicTicketDetailAction`: la chiave `images` (pubblica, originali, questa story) e la chiave `photos` (solo proprietario, percorso relativo, DCP-379, decisione D230-5 aperta). Il test dell'insieme esatto di chiavi non puo' accettarle entrambe. | Action alle 20:48:29 contiene `'images'` (riga 100) e `'photos'` (riga 102); vista e test modificati alle 20:48:43 e 20:48:54 |
| 2 | `images` serve gli ORIGINALI. D230-5 (DCP-230) raccomanda per il pubblico «solo miniatura ri-codificata senza EXIF, originale solo al proprietario e agli operatori», ed e' una decisione del titolare/DPO ancora aperta. `Ticket` non ha `registerMediaConversions` (verificato), quindi miniature non esistono. | `Ticket.php` solo `registerMediaCollections`; DCP-230 riga D230-5 |
| 3 | La rimozione dell'EXIF (`StripTicketPhotoMetadataListener`) esiste dal commit `464e23e` di oggi, 07:30. Le foto caricate prima, o su un server senza quel codice, hanno ancora l'EXIF con GPS e data dello scatto: pubblicarle e il link «versione completa» (AC 3) le espongono. | `git log` del listener |
| 4 | `getFullUrl()` dipende da `APP_URL` (qui `http://fixcity.local`); la nota in `photos()` dice che in produzione non e' l'host pubblico: le immagini risulterebbero rotte. Serve il percorso relativo, come in `photos()`. | `.env` locale; commento nell'Action |
| 5 | Produzione: tutte le 7 URL provate rispondono HTTP 500 (`ParseError` da marker di merge). Nulla di questa galleria e' verificabile su fixcity.eu finche' non e' risolto: [DCP-440](../../stories/dcp-440-produzione-ferma-per-marker-di-merge-nell-albero-di-lavoro.story.md). | `curl`, 20:46 |

Proposta (da confermare dall'utente): una sola chiave `photos`, con percorso relativo; al proprietario l'originale; al pubblico niente finche' D230-5 e' aperta, poi solo una conversione `public` (JPEG ridotto, nessun EXIF) con `nonQueued()`. Per le segnalazioni gia' esistenti servira' `php artisan media-library:regenerate --only-missing` sul server.

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 20:50 | Claude (camera upload) | revisione | fermo: non implemento una terza versione sugli stessi file |
| 2026-10-27 21:03 | Claude (6d2caca8) | verifica dello stato convergente | Alle 21:01 Action, Blade e test usano tutti `images` con `$isOwner \|\| $isPublic`: il proprietario di un ticket `pending` ora riceve le foto, quindi il caso segnalato e' coperto. `phpstan analyse Modules`: exit 0, 0 errori (21:02). **Due difetti restano nel codice, non li tocco:** (1) `getFullUrl()` e' assoluto e dipende da `APP_URL`; `filesystems.public.url` e' `env('APP_URL').'/storage'` e in dev `http://fixcity.local/storage/75/reference-mobile.png` non risolve (`curl` 000) mentre `/storage/75/...` su `127.0.0.1:8081` da' 200: la foto puo' risultare rotta se l'host non coincide; serve il percorso (`parse_url($media->getUrl(), PHP_URL_PATH)`). (2) il ramo `\|\| $isPublic` serve gli ORIGINALI a chiunque per i ticket pubblici, mentre D230-5 (miniatura senza EXIF, volti e targhe) e' aperta: se l'utente non l'ha decisa, `images` dovrebbe uscire solo per il proprietario. Design: `object-fit: cover` a 4/3 taglia circa il 44% di una foto verticale del telefono. |
| 2026-10-27 21:13 | Claude (6d2caca8) | difetto (1) chiuso: URL relativo | File fermi da 7-10 minuti (mtime Action 21:03:46, test 21:00:50), modifica di una riga che non cambia il disegno. TDD: l'asserzione in `BuildPublicTicketDetailActionTest` ('gives the photos of a report not yet public to the owner only') passa da `assertStringContainsString` a `assertStringStartsWith('/storage/')`; **rosso** con `Failed asserting that 'http://fixcity.local/storage/290/camera.jpg' starts with "/storage/"`, cioe' il difetto di produzione riprodotto. Poi `publicImageUrls()` usa `Safe\parse_url($media->getUrl(), PHP_URL_PATH)` per il disco `public` (altri dischi restano su `getFullUrl()`). **Verde:** 3 file (Action, Timeline, `TicketPublicDetailPageTest`) 36 test, 310 asserzioni; `phpstan analyse Modules` exit 0 (la prima versione con `parse_url` nativo e' stata fermata da PHPStan: funzione non Safe). Restano aperti: (2) originali serviti ai ticket pubblici con D230-5 non decisa; `object-fit`; produzione ferma (DCP-440). Non verificato nel browser ne' su fixcity.eu. |
| 2026-10-27 21:17 | Claude (6d2caca8) | CSS della galleria + build + prova nel browser | `19-ticket-detail.css` (fermo dalle 20:45) cambiato in due punti: `object-fit: contain` con fondo `#f2f5f8` (la foto verticale resta intera) e colonne `repeat(auto-fill, minmax(min(100%, 14rem), 20rem))` (misurato a 1440 px: una foto sola era 1074x805 e riempiva lo schermo, ora 318x239). Build `npx vite build` nel tema: asset `app-C98OKpq9.css`, 98 file, nessuno a zero byte, pagina 200 con il CSS nuovo. Playwright su `/it/tickets/145` a 375/768/1440: nessun overflow, sezione prima della descrizione, alt e titolo tradotti in it/en/de/es, nessun `fixcity.local` nell'HTML. **Non rimettere `cover` ne' `1fr`**: sono i due difetti misurati. Dettaglio e limiti in [DCP-379](../../stories/dcp-379-foto-nel-dettaglio-della-segnalazione.story.md) (sezione Verifica). |
