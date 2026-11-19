---
title: "Fixcity — Allegati nelle API pubbliche e GeoJSON della mappa"
type: story
bmad: true
status: review
created: 2026-11-10
owner: Modules/Fixcity
tags: [fixcity, bmad, security, privacy, performance, media, geojson, audit]
related:
  - ../audit/SECURITY_AUDIT.md
  - ../audit/DATABASE_AUDIT.md
  - ../wiki/concepts/folio-api-no-controllers.md
---

# Allegati nelle API pubbliche e GeoJSON della mappa

Correzioni SEC-04, SEC-12 di `SECURITY_AUDIT.md` e DB-12, DB-13, DB-19 di `DATABASE_AUDIT.md`.

## Problema

1. `/api/tickets/list` e `/api/ticket-details/{id}` restituivano `getFullUrl()` di **tutti** gli allegati, PDF compresi.
   Lo stesso nel blocco `segnalazioni` del tema (PDF in un `<img>`).
2. I file dei ticket stanno sul disco `public` (`/storage/{media_id}/{nome}`): id sequenziale e nome originale.
   Le foto e i PDF dei ticket `pending` o rifiutati si aprono con un URL indovinabile.
3. `/api/tickets/geojson` caricava tutti i ticket pubblici con tutte le colonne (`content` compreso), senza limite, senza cache,
   senza throttle. `/api/tickets/list` faceva una query media per riga (N+1).

## Modifiche

| File | Cosa cambia |
|------|-------------|
| `app/Actions/BuildTicketPublicImageUrlsAction.php` (nuovo) | Unica fonte degli URL foto in front office: solo `image/*`, solo ticket pubblici o del proprietario. URL relativi: `/storage/...` (disco `public`) o `/api/ticket-media/...` (disco privato). Con `accessGranted` (pagina track) foto in ogni stato con URL firmato. |
| `app/Actions/BuildPublicTicketDetailAction.php` | Usa la nuova Action (stesso comportamento: foto a pubblico e proprietario, mai PDF). |
| `app/Actions/BuildTicketPublicDetailsPayloadAction.php` | Usa la nuova Action. Nuovo parametro `accessGranted` (default `false`). |
| `resources/views/pages/api/tickets/list.blade.php` | `with('media')` (DB-13), solo foto pubbliche, `throttle:120,1`. |
| `resources/views/pages/api/ticket-details/[ticket].blade.php` | `throttle:240,1`. |
| `pages/tickets/track.blade.php`, tema `components/pages/ticket-track.blade.php` | Passano `accessGranted: true`: l'accesso e' gia' verificato (codice o `Gate::authorize`). |
| tema `components/blocks/segnalazioni/layout.blade.php` | `with('media')` e URL dalla nuova Action: niente PDF in `<img>`. |
| `app/Actions/ResolveTicketMediaDiskAction.php` (nuovo) | Nome e config del disco privato `fixcity_ticket_media` (`storage/app/private/fixcity-ticket-media`, senza `serve`). |
| `config/config.php` | `media.private_disk` (env `FIXCITY_TICKET_MEDIA_DISK`; vuoto = disattivato). |
| `app/Providers/FixcityServiceProvider.php` | Registra il disco se il tenant non lo definisce. |
| `app/Observers/TicketMediaObserver.php` | `creating`: i NUOVI media di un ticket sul disco predefinito vanno sul disco privato. I media esistenti non cambiano. Scelto al posto di `Ticket::registerMediaCollections()->useDisk()` per non toccare il modello. |
| `app/Actions/CanViewTicketMediaAction.php` (nuovo) | Foto: `TicketPolicy::view`. PDF: proprietario o `viewAny` (back office). |
| `app/Actions/SignTicketMediaUrlAction.php` (nuovo) | Firma HMAC a scadenza (1 ora) per le foto della pagina track. |
| `resources/views/pages/api/ticket-media/[...segments].blade.php` (nuovo) | `GET /api/ticket-media/{media_id}/{file}`: controlla disco, appartenenza al ticket, permesso o firma, percorso dentro la cartella del media. Rifiuto = 404. `Cache-Control: private`, `nosniff`, PDF in `attachment`. |
| `app/Datas/PublicTicketGeoJsonOptions.php` (nuovo) | `limit` (1..2000, default 2000) e `bbox=minLng,minLat,maxLng,maxLat`; valori non validi ignorati. |
| `app/Actions/BuildTicketsGeoJsonAction.php` | `select` delle sole colonne della feature, `limit` e `bbox` opzionali, chiavi `limit` e `truncated` in risposta. |
| `app/Actions/BuildPublicTicketsGeoJsonResponseAction.php` (nuovo) | Filtri + opzioni + cache 60 s solo per gli ospiti (chiave: lingua, filtri, opzioni). |
| `resources/views/pages/api/tickets/geojson.blade.php` | Usa la nuova Action, `throttle:120,1`. |

### Contratto del front-end

- `map-lit`, viste mobile e widget admin chiamano `/api/tickets/geojson` senza parametri: ricevono la stessa forma di prima
  (`type`, `generated_at`, `total`, `features[].geometry`, `features[].properties` con le stesse chiavi). Le chiavi nuove
  `limit` e `truncated` non sono lette da nessuno. Sopra 2000 ticket la mappa mostra i 2000 piu' recenti e `truncated` vale `true`.
- `total` resta il numero di feature restituite.
- `images` di `/api/tickets/list` e `/api/ticket-details/{id}` resta un array di stringhe URL, ora relative.
- Il popup di `map-lit` non cambia.

## Test

- `tests/Feature/Api/PublicApiMediaVisibilityTest.php` (nuovo): disco privato per i nuovi upload, niente PDF nelle API, nessuna foto di
  ticket non pubblici a estranei, route media (ospite, proprietario, altro utente, PDF, firma valida e falsa, path traversal),
  numero di query costante in `/api/tickets/list`.
- `tests/Feature/Api/TicketsGeoJsonLimitAndCacheTest.php` (nuovo): contratto senza parametri, chiavi esatte delle `properties`,
  `limit`/`truncated`, `bbox`, ticket non pubblici, cache solo per gli ospiti.
- Aggiornati per il disco privato: `tests/TestCase.php` (`Storage::fake` del disco privato), `BuildPublicTicketDetailActionTest`
  (URL `/api/ticket-media/`), `TicketPhotoMetadataTest`, `CreateTicketWizardWidgetTest`, `CreateTicketWizardForgedUploadTest`
  (controllano anche il disco privato).

## Verifica

- `php -l` su tutti i file PHP toccati: nessun errore.
- Pest e PHPStan non eseguiti in questo ambiente (niente `vendor/`). Da eseguire:
  `cd laravel && vendor/bin/pest Modules/Fixcity/tests/Feature/Api Modules/Fixcity/tests/Unit/Actions/BuildPublicTicketDetailActionTest.php Modules/Fixcity/tests/Feature/Privacy Modules/Fixcity/tests/Feature/Filament Modules/Fixcity/tests/Feature/Pages`
  e `./vendor/bin/phpstan analyse Modules/Fixcity`.
- Manuale: caricare una foto da wizard, aprire `/api/ticket-media/{id}/{file}` da ospite (404 finche' `pending`), da proprietario (200);
  approvare il ticket e riaprire da ospite (200). Aprire la pagina track con il codice: le foto si vedono.

## Aperto

1. **Spostamento dei file esistenti (ops, umano).** I media gia' caricati restano sul disco `public` e sotto `/storage`.
   Procedura, a manutenzione attiva e con backup di `storage/app/public` e della tabella `media`:
   1. per ogni riga `media` con `model_type` = morph di Ticket e `disk = 'public'`: copiare la cartella
      `storage/app/public/{id}/` in `storage/app/private/fixcity-ticket-media/{id}/`;
   2. verificare la copia (stesso numero di file e stessa dimensione);
   3. aggiornare `disk` e `conversions_disk` a `fixcity_ticket_media` per quelle righe;
   4. controllare una foto pubblica e una `pending` via `/api/ticket-media/...`;
   5. solo dopo, cancellare le cartelle originali sotto `storage/app/public/{id}/`.
   Se Apache elenca le directory di `/storage`, disattivare `Options Indexes` subito.
2. **Path generator con UUID** per il disco `public` di altri modelli: non necessario per i ticket (route autorizzata).
3. **Coordinate esatte e indirizzo** nel GeoJSON (SEC-12): decisione di prodotto (PP-02 di DCP-104), non cambiata.
4. **Ticket senza `latitude`/`longitude`** valorizzate escono solo senza `bbox` (il filtro usa le colonne).
5. `BuildSegnalazioniFilterAggregateAction` chiama ancora `BuildTicketsGeoJsonAction` senza limite (solo conteggi): da sostituire con
   un `GROUP BY`.
6. Le viste mobile scaricano tutta la collezione per mostrare un ticket: usare `/api/ticket-details/{id}`.
7. La vista legacy `components/blocks/ticket_list/ticket_list.blade.php` usa `$media->getUrl()` su `$ticket->media`: non montata
   (SEC-13), da cancellare.
8. Il disco privato e' selezionato da un observer su `Media::creating`. Se si preferisce dichiararlo nel modello,
   `Ticket::registerMediaCollections()` puo' usare `->useDisk(...)` (file condiviso con altre story in corso: coordinarsi qui).

## Revisione indipendente del 2026-11-10

- **Kill switch del disco (M3).** `config/config.php` del modulo legge `$_ENV`, vuoto con la config in cache. Il nome del disco
  ora si legge da `config/filesystems.php` (`fixcity_ticket_media_disk`, `env('FIXCITY_TICKET_MEDIA_DISK')`), con il modulo
  come ripiego. La config del tenant si unisce con `array_replace_recursive`, quindi la chiave resta.
- **`MEDIA_PREFIX` (L1).** La rotta `api/ticket-media` toglie il prefisso di `media-library.prefix` prima di leggere l'id.
- **`conversions_disk` (L2).** Le miniature si servono dal `conversions_disk` del media e solo se e' il disco privato.
- **Ancora aperto (L3):** i limiti per IP (240/min sui media, 120/min su GeoJSON e lista) sono condivisi da tutti gli ospiti
  dietro lo stesso NAT, per esempio la rete di un Comune. Da misurare in produzione prima di alzarli.
