---
title: "Contratto — MarkPersonalAreaNotificationsAsReadAction"
type: contract
action: "Modules\\Fixcity\\Actions\\MarkPersonalAreaNotificationsAsReadAction"
module: Fixcity
status: accepted
tags: [contract, action, notifiche, area-personale, autorizzazione]
created: 2026-11-26
updated: 2026-11-26
---

# MarkPersonalAreaNotificationsAsReadAction

Sorgente: `app/Actions/MarkPersonalAreaNotificationsAsReadAction.php` (DCP-111). "Segna come letta" dell'area personale del cittadino. Scrive solo `read_at` e solo sulle notifiche di stato
del cittadino che agisce: la query e' la stessa dell'elenco (`BuildAuthenticatedUserTicketNotificationsQueryAction`), cosi' un id altrui o di un altro tipo non cambia nulla.

## Input

- `UserContract $user`: il cittadino autenticato.
- `?string $notificationId`: l'id di una notifica; `null` = tutte le non lette del cittadino.

## Output

- `int`: numero di notifiche segnate come lette (0 se nessuna).

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| PERSONAL-NOTIF-READ-INV-1 | Con un id segna come letta solo quella notifica del cittadino; l'id di una notifica altrui non cambia nulla (le non lette dell'altro utente restano) e restituisce 0. | `PERSONAL-NOTIF-READ-INV-1` |
| PERSONAL-NOTIF-READ-INV-2 | Senza id segna come lette tutte le non lette del cittadino e nessuna di altri utenti. | `PERSONAL-NOTIF-READ-INV-2` |
| PERSONAL-NOTIF-READ-INV-3 | Scrive solo la colonna `read_at` (ora corrente): nessun altro campo della notifica cambia e l'ora di lettura di una notifica gia' letta non viene sovrascritta. | — (non dimostrato) |
| PERSONAL-NOTIF-READ-INV-4 | Una notifica gia' letta non conta nel risultato: ripetere la chiamata con lo stesso id restituisce 0 (la query esclude `read_at` valorizzato). | `PERSONAL-NOTIF-READ-INV-4` |
| PERSONAL-NOTIF-READ-INV-5 | Un id inesistente o di una notifica di un altro tipo (non di stato dei ticket) restituisce 0 e non cambia nulla: il filtro e' quello della query dell'elenco. | — (non dimostrato: il test copre solo l'id altrui) |

## Effetti collaterali

- Un solo `UPDATE notifications SET read_at = now()` con i filtri sopra (il conteggio e' quello delle righe aggiornate).
- Nessuna lettura preliminare, nessun evento, nessuna cancellazione.

## Errori

- Nessuna eccezione propria. Un id non valido o non del cittadino non e' un errore: restituisce 0.
- Limite noto: l'`UPDATE` passa dalla query Eloquent condivisa con l'elenco: se un giorno la query dell'elenco cambia (per esempio per includere altri tipi), questa Action
  ne eredita il perimetro senza segnalarlo.

## Non fa

- Non cancella le notifiche e non le segna come non lette.
- Non verifica i permessi sul ticket: il perimetro e' "le mie notifiche di stato".
- Non gestisce le notifiche dell'operatore del back office (Filament ha le sue).
- Non restituisce l'elenco aggiornato: lo ricostruisce il chiamante (`BuildPersonalAreaNotificationsDataAction`).
