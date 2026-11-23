---
status: accepted
module: Fixcity
---

# Il ciclo di vita Ticket è governato da Actions

## Contesto

Una segnalazione attraversa creazione, assegnazione, comunicazione, stato e chiusura, con effetti su audit e notifiche.

## Driver della decisione

- Rendere transizioni e side effect espliciti e testabili.

## Decisione

Le transizioni del ticket passano da Actions e policy Fixcity; view e widget non modificano direttamente il ciclo di vita.
Lo stesso confine vale per il dettaglio pubblico: il contenitore CMS `tickets.view`
fornisce il blocco, ma `BuildPublicTicketDetailAction` è la fonte unica dei dati
esposti e restituisce `null` quando il ticket non è visibile al visitatore.

## Alternative scartate

- Mutare lo stato direttamente da Blade o Livewire.
- Distribuire la stessa transizione in più controller.

## Conseguenze

Autorizzazione, audit e notifiche restano coordinati; una nuova transizione richiede Action, contract e test owner.
La privacy del dettaglio pubblico resta nel modulo Fixcity e non viene duplicata
nelle shell Folio o nel componente CMS generico.

## Conferma

Verificare policy Ticket, Actions di lifecycle e test di atomicità/idempotenza.
