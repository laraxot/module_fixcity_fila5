---
title: "Contratto — SendTicketToEnteAction"
type: contract
action: "Modules\\Fixcity\\Actions\\SendTicketToEnteAction"
module: Fixcity
status: accepted
tags: [contract, action, email, ente, privacy, audit]
created: 2026-11-26
updated: 2026-11-26
---

# SendTicketToEnteAction

Sorgente: `app/Actions/SendTicketToEnteAction.php`; decisione di prodotto in `docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md`.
Casella di ingresso opzionale del Comune: avvisa l'ente che e' arrivata una segnalazione. Chiamata da `NotifyTicketReceivedAction` dopo la creazione.
**Un invio SMTP riuscito non e' una presa in carico dell'ente.**

## Input

- `Ticket $ticket`.
- Configurazione: `ente.ticket_reports_email`, risolta da `ResolveEnteTicketEmailAction`.

## Output

- `bool`: `true` se la mail e' stata accettata dal trasporto o gia' inviata in precedenza; `false` se la casella non e' configurata o non valida, o se il trasporto ha fallito.

## Invarianti

| ID | Regola | Group Pest |
|---|---|---|
| TICKET-ENTE-MAIL-INV-1 | La mail parte solo verso `ente.ticket_reports_email`, esplicita: mai verso l'indirizzo di contatto generico (`ente.email`) ne' la PEC (`ente.pec`). Casella vuota: `false`, nessuna mail. | `TICKET-ENTE-MAIL-INV-1` |
| TICKET-ENTE-MAIL-INV-2 | Un indirizzo con a-capo (`\r\n`) e' scartato (`false`, nessuna mail): nessuna iniezione di intestazioni. | `TICKET-ENTE-MAIL-INV-2` |
| TICKET-ENTE-MAIL-INV-3 | Il testo della mail contiene il numero interno del ticket e non il titolo, la descrizione ne' il codice di tracciamento. | `TICKET-ENTE-MAIL-INV-3` |
| TICKET-ENTE-MAIL-INV-4 | Un ticket gia' notificato (esiste una voce `mail_sent` con `payload.source = ente_ticket_intake`) non genera una seconda mail: la seconda chiamata restituisce `true` senza inviare. | `TICKET-ENTE-MAIL-INV-4` |
| TICKET-ENTE-MAIL-INV-5 | L'esito del trasporto e' registrato come voce `mail_sent` o `mail_failed`; la voce `mail_sent` ha visibilita' `internal` e `payload.source = ente_ticket_intake`. | `TICKET-ENTE-MAIL-INV-5` |
| TICKET-ENTE-MAIL-INV-6 | Un errore del trasporto non interrompe chi chiama: l'eccezione e' catturata e l'Action restituisce `false`. | `TICKET-ENTE-MAIL-INV-6` |
| TICKET-ENTE-MAIL-INV-7 | Un indirizzo con `?`, `&`, `%`, non stringa o di formato non valido e' scartato (`false`, nessuna mail). | — (non dimostrato: il test usa solo un indirizzo con a-capo) |
| TICKET-ENTE-MAIL-INV-8 | Il testo della mail non contiene l'identita' del cittadino, le foto ne' la posizione; la voce `mail_sent`/`mail_failed` porta `channel = mail`; l'errore di trasporto e' registrato nel log senza indirizzi. | — (non dimostrato: nessun test asserisce questi tre punti) |

## Effetti collaterali

In quest'ordine:

1. Legge `ente.ticket_reports_email` e la valida; se non c'e' esce con `false`.
2. `SELECT` delle voci `mail_sent` del ticket (carica tutte le voci di quel tipo e filtra in PHP su `payload.source`).
3. `Mail::to($ente)->send(TicketCommunicationMail)`: un solo messaggio in sincrono nella richiesta corrente (la mail non e' in coda).
4. `RecordTicketActivityAction` con evento `mail_sent` o `mail_failed`, visibilita' `Internal`, stato invariato.
5. Se la registrazione fallisce: `Log::error` con id e classe dell'eccezione, nessuna propagazione.
6. Se il trasporto fallisce: `Log::warning` con id del ticket e classe dell'eccezione.

## Errori

- Nessuna eccezione propagata: ogni fallimento diventa `false` (trasporto) o log (audit).
- Limite noto: l'idempotenza e' "leggi poi invia", non atomica. Due chiamate concorrenti sullo stesso ticket possono inviare due mail.
- Limite noto: un invio fallito non viene ritentato da questa Action; una nuova chiamata riprova (non c'e' voce `mail_sent`), ma nessuno la richiama oltre alla creazione.
- Limite noto: il testo del messaggio e l'oggetto sono stringhe italiane nel codice PHP (non traducibili); la mail all'ente e' sempre in italiano.
- Limite noto: il messaggio rimanda al "pannello riservato" ma non include alcun collegamento.

## Non fa

- Non e' una presa in carico: l'ente non conferma, lo stato del ticket resta quello.
- Non include dati del cittadino, foto o posizione (invariante 3).
- Non usa la PEC ne' l'indirizzo di contatto come ripiego.
- Non decide se inviare in base al tipo di ticket o alla categoria: invia per ogni ticket creato se la casella e' configurata.
- Non usa la coda dei job: la `Mail::send` e' sincrona.
