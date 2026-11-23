---
title: "ADR — Il dettaglio pubblico del ticket è un contenitore CMS con dati governati da Fixcity"
type: decision
status: accepted
module: Fixcity
created: 2026-11-23
updated: 2026-11-23
issues: []
---

# Il dettaglio pubblico del ticket separa contenitore, dati e presentazione

## Contesto

`/{lang}/tickets/{id}` deve essere condivisibile e leggibile anche da un ospite,
ma l'ID numerico non è uno slug CMS e non può generare una pagina JSON per ogni
record. La pagina deve inoltre restituire lo stesso 404 per ticket inesistenti e
ticket non visibili, senza esporre dati privati del segnalante.

## Driver della decisione

- Una sola policy di visibilità per ospite e proprietario.
- Nessuna query o logica di dominio nelle shell Folio del tema.
- Contenuti e metadati comuni configurabili dal CMS.
- Presentazione coerente con Design Comuni e testabile senza duplicare il dominio.

## Alternative scartate

- Creare una view Folio semantica per i ticket o una pagina CMS per ogni ID.
- Caricare il modello direttamente nella Blade o nel componente generico CMS.
- Usare `/tickets/track` come sostituto del dettaglio pubblico: il tracking
  richiede un codice-capability e ha un contratto di privacy differente.

## Decisione

La catena canonica è:

```text
Sixteen Folio shell
  → PageSlugMiddleware
  → <x-page> con data bag (container0=tickets, slug0=id)
  → contenitore CMS tickets.view
  → blocco Sixteen ticket-detail
  → BuildPublicTicketDetailAction
```

`BuildPublicTicketDetailAction` è la fonte unica del payload pubblico: applica
policy e stato, include i dati ammessi e restituisce `null` per un ticket non
visibile. Il componente CMS prepara il payload e i metadati prima dell'head;
il componente di presentazione riceve il data bag e rende il blocco. La shell
non conosce il modello.

## Conseguenze

- Buono, perché l'ID resta un parametro applicativo e il CMS continua a
  governare blocchi, layout e contenuti tradotti.
- Buono, perché Fixcity mantiene la privacy in un'Action unica, con test Pest
  per ospite e proprietario.
- Cattivo, perché il componente di blocco contiene un piccolo adattamento tra
  data bag e Action; quel confine è preferibile alla duplicazione nelle shell.

## Conferma

- `Modules/Fixcity/tests/Feature/Pages/TicketPublicDetailPageTest.php` verifica
  accesso pubblico, lingue, contenuto e dati esclusi.
- `Themes/Sixteen/tests/Feature/Pages/DetailNotFoundTest.php` verifica il 404
  per slug e record inesistenti.
- `Modules/Fixcity/tests/Unit/Actions/BuildPublicTicketDetailActionTest.php`
  verifica il payload e la privacy dell'Action.
