---
title: "Contratto — GetPublicTicketByCodeAction"
type: contract
action: "Modules\\Fixcity\\Actions\\GetPublicTicketByCodeAction"
module: Fixcity
status: accepted
tags: [contract, action, tracking, capability-token, throttle, privacy]
created: 2026-11-26
updated: 2026-11-26
---

# GetPublicTicketByCodeAction

Sorgente: `app/Actions/GetPublicTicketByCodeAction.php`. Il codice di tracciamento e' un **capability token**: chi lo conosce
vede stato e cronologia di una segnalazione anche non ancora pubblica, senza login. Proprio per questo indovinarlo e' l'attacco, e
il freno sta qui (DCP-149): da qui passano la pagina di tracciamento del tema (`Themes/Sixteen/.../components/pages/ticket-track.blade.php`),
la vista Folio del modulo (`resources/views/pages/tickets/track.blade.php`) e la pagina di conferma
(`ResolveTicketConfirmationDataAction`, `?code=`). Il freno e' a due livelli: questo (Action, per indirizzo, conta solo i
tentativi a vuoto) e quello di pagina (`"middleware": ["throttle:10,1"]` nel JSON CMS di `tickets/track`).

## Input

- `?string $code`: il codice di tracciamento digitato o ricevuto (`PREFISSO-` + 16 caratteri casuali maiuscoli, vedi `AllocateTicketCodeAction`).
- Implicito: l'indirizzo IP della richiesta (`request()->ip()`), che e' la chiave del freno.

## Output

- Il `Ticket` con quel codice, oppure `null` se il codice e' assente, vuoto, solo spazi o sconosciuto. Restituisce il modello
  intero: chi lo consuma decide cosa mostrare (payload pubblico: `BuildTicketPublicDetailsPayloadAction`).

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-TRACK-LOOKUP-INV-1 | Chi conosce il codice trova la segnalazione in qualunque stato (anche `pending`), senza essere autenticato. Il codice sbagliato non trova nulla. | `TICKET-TRACK-LOOKUP-INV-1` |
| TICKET-TRACK-LOOKUP-INV-2 | Codice `null`, vuoto o fatto di soli spazi non e' una ricerca: restituisce `null` e non consuma il budget del freno. | `TICKET-TRACK-LOOKUP-INV-2` |
| TICKET-TRACK-LOOKUP-INV-3 | I tentativi a vuoto (codice sconosciuto) sono limitati per indirizzo: dopo `MAX_MISSES` (10) in `DECAY_SECONDS` (60 s) la ricerca lancia `ThrottleRequestsException` (429) con intestazione `Retry-After`. | `TICKET-TRACK-LOOKUP-INV-3` |
| TICKET-TRACK-LOOKUP-INV-4 | I tentativi riusciti non consumano il budget: ricerche con un codice noto non vengono mai bloccate. | `TICKET-TRACK-LOOKUP-INV-4` |
| TICKET-TRACK-LOOKUP-INV-5 | Un indirizzo bloccato non puo' usare nemmeno un codice valido finche' la finestra non scade (il controllo del freno precede la query). | `TICKET-TRACK-LOOKUP-INV-5` |
| TICKET-TRACK-LOOKUP-INV-6 | Primo livello del freno: il JSON CMS di `tickets/track` dichiara `throttle:10,1` in ogni copia. | — (non dimostrato: `TicketTrackPageJsonGuardTest` lo pretende come unica voce e la copia del tenant ne ha due; vedi Backlog test) |

## Effetti collaterali

In quest'ordine:

1. Nessuna scrittura se il codice e' vuoto (esce subito).
2. `RateLimiter::tooManyAttempts('ticket-code-lookup:{ip}', 10)`: se vero, lancia 429 prima di toccare il database.
3. `SELECT` su `tickets` per `code = trim($code)` (esclude le righe cancellate, soft delete).
4. Se non trova il ticket: `RateLimiter::hit($key, 60)` (scrittura nella cache del limiter).

## Errori

- `ThrottleRequestsException` (HTTP 429, `Retry-After`): troppi tentativi a vuoto dall'indirizzo. Protegge `TICKET-TRACK-LOOKUP-INV-3` e `-5`.
  La pagina del tema la rende come pagina 429 tradotta in it, en, de, es (ultimo test di `GetPublicTicketByCodeThrottleTest`).
- Nessun'altra eccezione propria.
- Limite noto: la chiave e' l'IP di `request()->ip()`. Dietro un proxy non fidato tutti i visitatori condividono lo stesso IP (un
  tentativo a vuoto di uno blocca tutti); dietro un NAT condiviso piu' cittadini si dividono i 10 tentativi. Dipende dalla
  configurazione dei proxy fidati (`TrustedProxiesAndHostsConfigTest`).
- Limite noto: il messaggio dell'eccezione (`Troppi tentativi di ricerca del codice.`) e' una stringa italiana nel codice PHP; la
  pagina 429 mostrata al visitatore usa invece le chiavi di traduzione.

## Non fa

- Non costruisce il payload da mostrare ne' decide quali campi escono (`BuildTicketPublicDetailsPayloadAction`,
  `BuildPublicTicketDetailAction`); chi mostra le foto di un ticket non pubblico deve passare `accessGranted` consapevolmente.
- Non genera ne' valida il formato del codice (nessun controllo di prefisso o lunghezza).
- Non confronta il codice in modo case-insensitive per conto suo: l'esito dipende dalla collation della colonna `code`.
- Non e' il punto di accesso del proprietario autenticato: il proprietario apre il dettaglio per id (`build-public-ticket-detail-action.md`).
- Non registra i tentativi falliti in un log di sicurezza: li conta soltanto nel limiter.
