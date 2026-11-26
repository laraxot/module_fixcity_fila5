---
title: "BMAD Planning — ciclo di vita della segnalazione"
type: bmad-planning
status: ready-for-dev
module: Fixcity
created: 2026-10-16
tags: [bmad, planning, ticket, workflow, timeline, privacy, filament]
related:
  - ./ticket-lifecycle-blueprint.md
  - ./ticket-lifecycle-filament-blueprint.md
  - ./ticket-lifecycle-brainstorming.md
  - ../../Comment/docs/bmad/ticket-comments-planning.md
  - ../../../Themes/Sixteen/docs/bmad/ticket-detail-planning.md
updated: 2026-10-17
qmd: "obiettivo fixcity"
---

# Obiettivo

Rendere la segnalazione un procedimento tracciabile: il cittadino invia, il
moderatore valuta, l'operatore assegna e comunica con l'ente competente, il
cittadino vede i passaggi autorizzati, e il ticket si chiude solo dopo una
risoluzione reale.

## Invarianti di prodotto

1. `pending` resta privato al proprietario e agli operatori autorizzati.
2. Foto inappropriate, bestemmie e dati sensibili passano dalla moderazione.
3. “Non è di nostra competenza”, delega, “lo faremo presto” e sollecito non
   chiudono il ticket: sono eventi o stati di attesa.
4. Ogni invio email, risposta, sollecito, modifica e allegato è un evento
   immutabile in `TicketActivity`.
5. `closed` è raggiungibile solo da `resolved`.
6. Il front office non espone email, header, note interne, capability code o
   commenti privati.

## Architettura BMAD

| Fase | Story | Owner | Dipendenze | Criterio di uscita |
|---|---|---|---|---|
| 1 | Contratto timeline e visibility | Fixcity | nessuna | eventi normalizzati e filtrati |
| 2 | Moderazione e transizioni | Fixcity | 1 | reject/approve/resolve con motivo |
| 3 | Assegnazione e comunicazioni | Fixcity + Notify | 2 | invio, retry, reply e sollecito registrati |
| 4 | Media e passaggi cittadino | Fixcity + Media | 1 | foto/documenti presenti nella cronologia |
| 5 | Commenti autorizzati | Comment | 1 | proprietario/operatori possono discutere |
| 6 | UI Design Comuni | Sixteen | 1, 4, 5 | timeline, prossimi passi e scadenze accessibili |
| 7 | Security/release gate | trasversale | 2–6 | actor matrix e privacy verificate |

## Contratto eventi

Usare `TicketActivity` e `RecordTicketActivityAction`; non creare una seconda
tabella timeline. Gli eventi sono `status_change`, `approved`, `rejected`,
`assignment`, `mail_sent`, `mail_received`, `followup`, `media_added` e
`closed`. Il payload contiene solo metadati tipizzati necessari all'audit.

Per email: conservare destinatario, oggetto, provider/message id, esito e
codice errore secondo la policy; mai header grezzi o segreti nel payload
pubblico. Ogni retry è una nuova attività con idempotency key.

## Backlog implementativo

- Creare azioni esplicite `ApproveTicket`, `RejectTicket`, `ResolveTicket` e
  `RecordTicketCommunication`, riusando policy e activity ledger.
- Collegare l'aggiunta Media Library a `media_added` con nome sicuro e visibilità.
- Aggiungere azione operatore per sollecito manuale; automatizzare solo dopo la
  definizione di SLA tenant-specifici.
- Presentare “Passaggi”, “Tempi e scadenze” e “Prossimi passi” come proiezione
  del ledger, mai come stato duplicato.

## Security e QA pianificati

- guest, proprietario, altro cittadino, moderatore e operatore su ogni stato;
- Livewire con `commentableType`/`commentableKey` falsificati;
- HTML nei commenti e nomi file ostili;
- nessuna fuga di email o note interne;
- Blade cache, PHP syntax, Pest con database di test disponibile e smoke HTTP.

## Decisioni bloccanti

- Il provider inbound email e il threading richiedono un contratto approvato:
  non inventare webhook o parser nel primo incremento.
- Le scadenze sono configurazione del tenant/ente, non numeri copiati dagli
  esempi Design Comuni.
- La fonte binaria resta Spatie Media Library; i file `TimelineStep` non sono
  adottati perché duplicano `TicketActivity`.

