---
title: "Contratto — CreateTicketAction"
type: contract
action: "Modules\\Fixcity\\Actions\\CreateTicketAction"
module: Fixcity
status: accepted
tags: [contract, action, ticket, transazione]
created: 2026-11-18
updated: 2026-11-26
---

# CreateTicketAction

Contratto di comportamento (regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`).
Sorgente: `app/Actions/CreateTicketAction.php`. Unico punto di creazione di una segnalazione: wizard del cittadino, widget
legacy e back office. Chi la imita o la aggira (un `Ticket::create()` diretto) perde codice, transazione e ricevuta.

## Input

- `array $data`: i campi della segnalazione. `owner_id` e' facoltativo (se manca si usa l'utente autenticato); `code` e'
  facoltativo (se vuoto lo alloca l'Action); `ticket_prefix` sceglie il prefisso del codice (default `TCK`).
- `?string $locale`: lingua della pagina di conferma.
- `?Closure $afterCreate`: eseguita nella transazione, dopo l'insert, con il `Ticket` appena scritto (salva gli allegati).
- `bool $flashConfirmation`: `true` per il cittadino, `false` nel back office.

## Output

- Il `Ticket` salvato, con `code` di tracciamento univoco.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-CREATE-INV-1 | Ogni segnalazione creata ha un `code` di tracciamento: se `data['code']` e' vuoto lo alloca `AllocateTicketCodeAction` (prefisso maiuscolo, 16 caratteri casuali). Senza codice il cittadino non puo' tracciare ne' ricevere notifiche. | `TICKET-CREATE-INV-1` |
| TICKET-CREATE-INV-2 | Nessun ticket senza `owner_id`: se manca e nessuno e' autenticato parte una `ValidationException` sul campo `owner_id` (messaggio `fixcity::ticket_submit.auth_required`) e non si scrive nulla. | — (non dimostrato) |
| TICKET-CREATE-INV-3 | Riga del ticket, allegati (callback) e prima voce di cronologia ci sono tutti o nessuno: una transazione copre le connessioni `fixcity` e `media`. Se la callback lancia, il ticket non resta, nessuna notifica parte e la sessione non riceve la conferma. | `TICKET-CREATE-INV-3` |
| TICKET-CREATE-INV-4 | `TicketCreatedEvent` e' dispatchato solo dopo il commit, mai su un rollback; se il chiamante ha gia' una transazione aperta, l'evento aspetta il suo commit. | `TICKET-CREATE-INV-4` |
| TICKET-CREATE-INV-5 | La prima voce di cronologia e' «ricevuta»: evento `StatusChange`, visibilita' `AuthorOnly`, autore il proprietario, `payload.source = submission`. Chi non e' l'autore non la vede (il ticket non e' ancora pubblico). | `TICKET-CREATE-INV-5` |
| TICKET-CREATE-INV-6 | La conferma al cittadino (`BuildTicketConfirmationDataAction::SESSION_KEY` in sessione) si scrive solo se `flashConfirmation` e' vero e la creazione e' riuscita; con `false` (back office) la sessione non si tocca. | `TICKET-CREATE-INV-6` |
| TICKET-CREATE-INV-7 | La ricevuta al proprietario e' una sola, quella del listener `NotifyTicketReceivedListener` (`TicketLifecycleNotification` evento `Received`); nessuna `TicketStatusChangedNotification` in piu'. | `TICKET-CREATE-INV-7` |

## Effetti collaterali

In quest'ordine:

1. Se manca `owner_id`, lo risolve dall'utente autenticato (o lancia, vedi Errori).
2. Apre la transazione su `media` poi su `fixcity` (a mano, perche' i callback "dopo il commit" di Laravel si agganciano
   all'ultima transazione aperta: con il commit di `fixcity` per ultimo partono solo a righe `media` salvate).
3. `INSERT` del ticket; il `code` e' allocato prima di ogni tentativo e, se collide con l'indice unique, fino a 3 tentativi
   (ognuno in un savepoint).
4. `RecordTicketActivityAction` scrive la voce «ricevuta».
5. Esegue `$afterCreate($ticket)` (salvataggio degli allegati sulla connessione `media`).
6. Commit di `media`, poi di `fixcity`.
7. `TicketCreatedEvent::dispatch($ticket)`: il listener `NotifyTicketReceivedListener` manda la ricevuta al proprietario,
   avvisa la PA e prova l'invio alla casella ente (`SendTicketToEnteAction`).
8. Se `$flashConfirmation`, scrive in sessione i dati della pagina di conferma.

## Errori

- `ValidationException` su `owner_id`: nessun proprietario ne' utente autenticato. Protegge `TICKET-CREATE-INV-2`.
- `UniqueConstraintViolationException`: il codice collide 3 volte di fila (o il chiamante ha passato un `code` gia' usato).
  Protegge `TICKET-CREATE-INV-1`.
- Qualunque eccezione di `$afterCreate`: rollback totale e rilancio. Protegge `TICKET-CREATE-INV-3`.
- Se il solo commit di `fixcity` fallisce dopo quello di `media`, restano righe `media` senza ticket ma nessuna ricevuta e'
  partita: e' il guasto meno grave scelto di proposito. Limite noto: non c'e' pulizia automatica di quelle righe.

## Non fa

- Non valida i campi del modulo (lo fa il wizard o il form).
- Non manda email ne' notifiche: lo fa il listener dell'evento, dopo il commit.
- Non pubblica il ticket: lo stato lo decidono i dati passati e il default del modello; la visibilita' pubblica dipende da
  `TicketStatusEnum::canViewByAll()`.
- Non autorizza (nessun `Gate`): chi chiama decide chi puo' creare. Alla pagina `tickets/create` basta l'utente autenticato
  (middleware `auth` nel JSON della pagina, guardia `TicketCreatePageMiddlewareGuardTest`).
- Non e' idempotente: l'idempotenza dell'invio del wizard e' di `SubmitTicketOnceAction`.
