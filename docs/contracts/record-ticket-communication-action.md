---
title: "Contratto — RecordTicketCommunicationAction"
type: contract
action: "Modules\\Fixcity\\Actions\\RecordTicketCommunicationAction"
module: Fixcity
status: accepted
tags: [contract, action, email, comunicazioni, autorizzazione, privacy, audit]
created: 2026-11-26
updated: 2026-11-26
---

# RecordTicketCommunicationAction

Sorgente: `app/Actions/RecordTicketCommunicationAction.php`. Comunicazioni dell'operatore con un ufficio esterno sulla singola segnalazione: invio di una mail, sollecito
(`followUp`) e registrazione di una risposta ricevuta (`recordInbound`). Unico chiamante: `Filament/Resources/TicketResource/Pages/ViewTicket.php` (azioni `sendCommunication`,
`sendFollowup`, `recordInboundReply`, tutte con `->authorize('communicate')`). Nessun test del pacchetto esercita questa Action: **tutte le invarianti sono promesse del codice, non prove**.

## Input

- `send(Ticket $ticket, string $recipient, string $recipientLabel, string $subject, string $body, bool $followUp = false, ?string $idempotencyKey = null)`.
- `recordInbound(Ticket $ticket, string $senderLabel, string $summary, ?string $idempotencyKey = null)`.
- Implicito: l'utente autenticato (autore della voce di cronologia).

## Output

- Il `TicketActivity` scritto (o quello gia' esistente per la stessa chiave di idempotenza).

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-COMM-INV-1 | `Gate::authorize('communicate', $ticket)` precede ogni validazione, invio e scrittura, in `send` e in `recordInbound` (ruolo PA o permesso `ticket.communicate`; vedi `ticket-policy.md`, `TICKET-POLICY-INV-10`). | — (non dimostrato) |
| TICKET-COMM-INV-2 | Il destinatario e' un indirizzo valido (`FILTER_VALIDATE_EMAIL`); destinatario, etichetta, oggetto e messaggio non sono vuoti; oggetto e etichetta stanno in 255 caratteri, il messaggio in 20.000, la sintesi di una risposta in 2.000. Altrimenti `InvalidArgumentException` e nessuna mail ne' scrittura. | — (non dimostrato) |
| TICKET-COMM-INV-3 | La stessa chiave di idempotenza sullo stesso ticket non produce una seconda mail ne' una seconda voce: si restituisce la voce esistente. Senza chiave se ne genera una nuova (UUID): una chiamata senza chiave non e' mai idempotente. | — (non dimostrato) |
| TICKET-COMM-INV-4 | Un errore del trasporto non lancia: la voce e' scritta come `mail_failed` con `status = failed`; un invio riuscito e' `mail_sent`, o `followup` con `followUp = true`, con `status = sent`. | — (non dimostrato) |
| TICKET-COMM-INV-5 | L'indirizzo grezzo del destinatario sta solo nel payload della voce (dato interno); la cronologia pubblica mostra solo l'etichetta (`BuildTicketTimelineAction::safeReason`, per `mail_sent`, `mail_failed`, `followup`). | — (non dimostrato) |
| TICKET-COMM-INV-6 | La visibilita' della voce segue lo stato del ticket: `public` se lo stato e' in `TicketStatusEnum::canViewByAll()`, altrimenti `author_only`; lo stato non cambia (`from` = `to`). | — (non dimostrato) |

## Effetti collaterali

`send`, in quest'ordine:

1. Autorizzazione, normalizzazione (`trim`), validazione.
2. Ricerca di una voce con la stessa chiave: carica tutte le voci del ticket (`activities()->get()`) e filtra in PHP.
3. `Mail::to($recipient)->send(TicketCommunicationMail)`: un solo messaggio, **sincrono** nella richiesta dell'operatore (la mail non e' in coda).
4. `Log::error` se il trasporto fallisce, con id del ticket, **indirizzo email** e classe dell'eccezione.
5. `RecordTicketActivityAction`: una riga in `ticket_activities` con autore, stato corrente, visibilita' e payload (`recipient`, `recipient_label`, `subject`, `idempotency_key`, `status`).

`recordInbound`: autorizzazione, validazione, ricerca per chiave e solo la riga di cronologia `mail_received` con sintesi (nel campo `reason`) ed `sender_label`; nessuna mail.

## Errori

- `AuthorizationException`: `communicate` negata.
- `InvalidArgumentException` (messaggi italiani nel codice PHP): indirizzo non valido, campi vuoti, lunghezze oltre i limiti.
- Eccezioni di persistenza da `RecordTicketActivityAction` propagate: se la scrittura fallisce dopo l'invio, la mail e' partita senza traccia in cronologia.
- Limite noto (dato personale nel log): `Log::error` scrive l'indirizzo email del destinatario nel log applicativo; altre Action del pacchetto loggano solo id e classe dell'eccezione.
- Limite noto: l'idempotenza e' "leggi poi invia" e non atomica; due richieste concorrenti con la stessa chiave possono inviare due mail. Le azioni del pannello non passano una chiave: un
  doppio invio del modulo manda due mail.
- Limite noto: la sintesi di `recordInbound` finisce nel campo `reason` e la cronologia pubblica la mostra (`safeReason` restituisce `reason` per `mail_received`) quando lo stato del ticket e' pubblico.
- Limite noto: l'etichetta del destinatario e' testo libero dell'operatore e compare nella cronologia pubblica.

## Non fa

- Non cambia lo stato del ticket e non assegna.
- Non avvisa cittadino, follower o proprietario della comunicazione.
- Non legge la casella di posta: la risposta si registra a mano (`recordInbound`, chiamata solo dalla pagina di dettaglio del back office: `rg RecordTicketCommunicationAction app` non trova altri chiamanti).
- Non ritenta un invio fallito.
- Non usa la coda dei job: l'invio e' sincrono.
