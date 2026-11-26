---
title: "Contratto — BuildTicketPublicImageUrlsAction"
type: contract
action: "Modules\\Fixcity\\Actions\\BuildTicketPublicImageUrlsAction"
module: Fixcity
status: accepted
tags: [contract, action, media, privacy, file, front-office]
created: 2026-11-26
updated: 2026-11-26
---

# BuildTicketPublicImageUrlsAction

Sorgente: `app/Actions/BuildTicketPublicImageUrlsAction.php` (SEC-04, DCP-379). UNICA fonte degli URL delle foto di un ticket
mostrate in front office: dettaglio pubblico (`BuildPublicTicketDetailAction`), popup della mappa (`/api/ticket-details/{id}`),
elenco (`/api/tickets/list`) e blocco `segnalazioni` del tema.

## Input

- `Ticket $ticket`.
- `?Authenticatable $viewer`: chi guarda (serve a riconoscere il proprietario).
- `bool $accessGranted` (default `false`): `true` solo se il chiamante ha GIA' autorizzato chi guarda per altra via (codice di
  tracciamento, `Gate::authorize('view')` nella pagina track). Il chiamante che elabora molti ticket deve caricare `media` con `with('media')` (DB-13).
- Anche `url(Media $media, bool $signed = false): string`, che costruisce l'URL di un singolo media.

## Output

- `list<string>`: URL relativi delle foto, senza duplicati; lista vuota se chi guarda non puo' vederle.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-MEDIA-URLS-INV-1 | Senza `accessGranted`, le foto escono solo se il ticket e' pubblico (stato in `TicketStatusEnum::canViewByAll()`) o se `$viewer` e' il proprietario; altrimenti `[]`. Violata: le foto di una segnalazione non ancora pubblica finirebbero nelle API pubbliche. | `TICKET-MEDIA-URLS-INV-1` |
| TICKET-MEDIA-URLS-INV-2 | Escono solo immagini (`mime_type` che inizia per `image/`): PDF e altri file non compaiono mai nelle API pubbliche. | `TICKET-MEDIA-URLS-INV-2` |
| TICKET-MEDIA-URLS-INV-3 | Gli URL sono percorsi relativi, mai assoluti, per i dischi locali (niente host da `APP_URL`, che in produzione non e' l'host pubblico); i media del disco privato dei ticket puntano a `/api/ticket-media/...`. | `TICKET-MEDIA-URLS-INV-3` |
| TICKET-MEDIA-URLS-INV-4 | Con `accessGranted = true` le foto escono in qualunque stato e quelle del disco privato hanno una firma a scadenza (`SignTicketMediaUrlAction`), perche' la route dei media non conosce il codice di tracciamento. | — (non dimostrato) |
| TICKET-MEDIA-URLS-INV-5 | Si legge la collezione `attachments`; la collezione `ticket` solo se la prima e' vuota (dati demo e legacy). | — (non dimostrato) |

## Effetti collaterali

- Solo letture: `getMedia()` sul ticket (query su `media` se non precaricato) e lettura della configurazione dei dischi.
- Con `accessGranted`, calcola una firma HMAC per ogni foto del disco privato (nessuna scrittura).

## Errori

- Nessuna eccezione propria: se non c'e' diritto o non ci sono foto restituisce `[]`.
- Limite noto: `accessGranted = true` e' una promessa del chiamante, non un controllo: un chiamante che lo passa senza aver
  autorizzato chi guarda espone le foto di un ticket non pubblico.
- Limite noto: per dischi remoti (S3) l'URL e' assoluto (`getFullUrl()`), non relativo.

## Non fa

- Non autorizza lo scaricamento del file: l'accesso HTTP a `/api/ticket-media/...` lo decide la route con `CanViewTicketMediaAction` o con una firma valida.
- Non espone PDF, ne' dati del file (nome originale, dimensioni, proprietario).
- Non elenca le miniature o le conversioni: solo l'originale.
- Non carica i media in blocco: l'eager loading (`with('media')`) e' del chiamante.
