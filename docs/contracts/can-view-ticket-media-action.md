---
title: "Contratto — CanViewTicketMediaAction"
type: contract
action: "Modules\\Fixcity\\Actions\\CanViewTicketMediaAction"
module: Fixcity
status: accepted
tags: [contract, action, media, autorizzazione, privacy, file]
created: 2026-11-26
updated: 2026-11-26
---

# CanViewTicketMediaAction

Sorgente: `app/Actions/CanViewTicketMediaAction.php` (SEC-04, DB-19). Decide chi puo' scaricare un allegato di una segnalazione. E'
chiamata dalla route `GET /api/ticket-media/{media_id}/{file}` (`resources/views/pages/api/ticket-media/[...segments].blade.php`):
l'id nel percorso e' enumerabile ma non basta. Gli allegati nuovi stanno sul disco privato (`ResolveTicketMediaDiskAction`),
fuori da `/storage`: questo e' l'unico accesso HTTP.

## Input

- `Media $media`: l'allegato richiesto.
- `Ticket $ticket`: la segnalazione a cui la route lo ha associato (`media.model_id`).
- `?Authenticatable $viewer`: chi guarda; `null` = ospite.

## Output

- `bool`: `true` se `$viewer` puo' scaricare quell'allegato di quel ticket.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-MEDIA-ACCESS-INV-1 | Un allegato che non appartiene al ticket (`model_type` e `model_id` diversi) e' negato, qualunque sia il visitatore (`belongsTo`). | — (non dimostrato) |
| TICKET-MEDIA-ACCESS-INV-2 | Una foto (`image/*`) e' accessibile solo a chi puo' vedere il ticket per `TicketPolicy::view`: ticket in stato pubblico, proprietario, operatore PA o permesso `ticket.view`. La foto di un ticket `pending` e' negata a ospiti e ad altri cittadini, non al proprietario. | `TICKET-MEDIA-ACCESS-INV-2` |
| TICKET-MEDIA-ACCESS-INV-3 | Un file non immagine (PDF, documenti, ricevute) e' accessibile al proprietario e mai a un ospite, nemmeno quando il ticket e' pubblico. Il cittadino non pubblica i propri documenti. | `TICKET-MEDIA-ACCESS-INV-3` |
| TICKET-MEDIA-ACCESS-INV-4 | Un file non immagine e' accessibile anche a chi ha `viewAny` sui ticket (back office), non solo al proprietario. | — (non dimostrato: nessun test apre un PDF con un ruolo PA) |

## Effetti collaterali

- Solo letture: confronto di `model_type`/`model_id`, valutazione di `TicketPolicy::view` e, per i non-immagine, `viewAny`.
  Le valutazioni della Policy possono inserire la riga di un permesso mancante la prima volta (`TICKET-POLICY-INV-8` in `ticket-policy.md`).
- Nessuna scrittura sul media, sul ticket o sul disco.

## Errori

- Nessuna eccezione propria: `false` = negato. La route traduce ogni rifiuto in **404** (mai 403), cosi' non si scopre quali id
  esistono (test `PublicApiMediaVisibilityTest`: 404 per ospite e per un altro utente).
- Limite noto: la route accetta in alternativa una firma a scadenza per le sole foto (`sign-ticket-media-url-action.md`); in quel caso
  questa Action non viene consultata.
- Limite noto: chi ha un ruolo PA o `ticket.viewAny` vede i PDF di ogni segnalazione, senza distinzione di comune.

## Non fa

- Non serve il file, non imposta intestazioni (`Cache-Control: private`, `nosniff`, CSP sandbox, `inline`/`attachment` li imposta la route).
- Non controlla che il file esista sul disco ne' che il percorso richiesto sia l'originale o una conversione di quel media (lo fa la route).
- Non genera ne' valida URL firmati.
- Non decide la visibilita' del ticket: delega a `TicketPolicy::view` (vedi `ticket-policy.md`).
