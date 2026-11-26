---
title: "Contratto — SignTicketMediaUrlAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SignTicketMediaUrlAction"
module: Fixcity
status: accepted
tags: [contract, action, media, firma, sicurezza]
created: 2026-11-26
updated: 2026-11-26
---

# SignTicketMediaUrlAction

Sorgente: `app/Actions/SignTicketMediaUrlAction.php` (SEC-04). Firma a scadenza per le foto del disco privato dei ticket, per chi
ha gia' superato un controllo che la route dei media non puo' ripetere: il possessore del codice di tracciamento
(`/tickets/track?code=...`) vede le foto della propria segnalazione anche non pubblica e senza login. HMAC propria con `app.key`,
nessuna dipendenza dai nomi delle route Folio.

## Input

- `execute(string $path, int $ttlSeconds = 3600)`: percorso relativo, es. `/api/ticket-media/12/foto.jpg`.
- `isValid(string $path, mixed $expires, mixed $signature): bool`: i parametri `expires` e `signature` della query.

## Output

- `execute`: lo stesso percorso con `?expires=<timestamp>&signature=<hex>`.
- `isValid`: `true` solo se la firma e' quella attesa e non e' scaduta.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-MEDIA-SIGN-INV-1 | Un URL firmato da `execute` e' accettato da `isValid` e dalla route per la foto di un ticket non pubblico, senza login. | `TICKET-MEDIA-SIGN-INV-1` |
| TICKET-MEDIA-SIGN-INV-2 | Una firma alterata (stessa query, `signature` sostituita) e' rifiutata: la route risponde 404. Il confronto e' `hash_equals`. | `TICKET-MEDIA-SIGN-INV-2` |
| TICKET-MEDIA-SIGN-INV-3 | Una firma scaduta (`expires` nel passato), con `expires` non numerico o con `signature` vuota o non stringa e' rifiutata. | — (non dimostrato) |
| TICKET-MEDIA-SIGN-INV-4 | La firma copre percorso e scadenza (`HMAC-SHA256('fixcity-ticket-media|{path}|{expires}', app.key)`): non vale per un altro file ne' con un'altra scadenza. | — (non dimostrato) |

## Effetti collaterali

- Nessuna scrittura: legge `app.key` e l'ora corrente e calcola un HMAC.

## Errori

- Nessuna eccezione propria; `isValid` risponde `false` per qualunque input non valido.
- Limite noto: e' un **bearer token**. La firma non e' legata al ticket ne' all'utente: chi ha l'URL lo usa fino alla scadenza
  (1 ora di default). Ruotare `app.key` invalida tutte le firme in circolazione.
- Limite noto: se `app.key` manca (`null`) o non e' scalare, la firma si calcola con chiave vuota (`SafeStringCastAction` restituisce `''`), quindi
  prevedibile: nessun controllo la rifiuta.

## Non fa

- Non verifica che il percorso esista o che il media appartenga a un ticket: lo fa la route.
- Non decide chi puo' ottenere una firma: lo decide il chiamante (`BuildTicketPublicImageUrlsAction` con `accessGranted`).
- Non funziona per i PDF: la route accetta la firma solo per le immagini.
