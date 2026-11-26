---
title: "Il ciclo di vita Ticket è governato da Actions"
type: decision
status: accepted
created: 2026-11-23
updated: 2026-11-26
module: Fixcity
decision-makers: [team Laraxot]
---

# Il ciclo di vita Ticket è governato da Actions

## Contesto

Una segnalazione attraversa creazione, assegnazione, comunicazione, stato e chiusura, con effetti su audit (`ticket_activities`) e notifiche. Se ogni view, widget o controller scrive lo stato per conto
proprio, l'autorizzazione, la cronologia visibile al cittadino e le notifiche divergono. Chi ha il diritto di modificare il ciclo di vita di un ticket, e dove?

## Driver della decisione

- Rendere transizioni e side effect espliciti e testabili.
- Un solo punto in cui si valida la transizione di stato (`TicketStatusEnum::allowedTransitions()`), si scrive la cronologia e partono le notifiche.
- Il browser non decide stato e priorita': i campi `Hidden` e le proprieta' pubbliche Livewire sono riscrivibili dal client.
- Dati e privacy del dettaglio pubblico in un solo posto.

## Alternative scartate

- Mutare lo stato direttamente da Blade o Livewire.
- Distribuire la stessa transizione in piu' controller.

## Decisione

Le transizioni del ticket passano da Actions e dalla Policy Fixcity; view e widget non modificano direttamente il ciclo di vita. Nel codice: `CreateTicketAction` (creazione), `AssignTicketAction`,
`ChangeStatus` e le Action di moderazione `ApproveTicketAction`, `RejectTicketAction`, `CloseTicketAction`, `ConfirmTicketResolutionAction` (stato, tutte con `LockTicketForStatusChangeAction`),
`RecordTicketCommunicationAction` (comunicazioni). Lo stato e la priorita' iniziali sono imposti dal server: `CreateTicket::mutateFormDataBeforeCreate` e `EditTicket::mutateFormDataBeforeSave` scartano i valori
inviati dal browser.

Lo stesso confine vale per il dettaglio pubblico: il contenitore CMS `tickets.view` fornisce il blocco, ma `BuildPublicTicketDetailAction` e' la fonte unica dei dati esposti e restituisce `null` quando il
ticket non e' visibile al visitatore (decisione in [0003](0003-public-ticket-detail-route-ownership.md)).

## Conseguenze

- Buono: autorizzazione, audit e notifiche restano coordinati; una nuova transizione richiede Action, contract e test owner.
- Buono: la privacy del dettaglio pubblico resta nel modulo Fixcity e non viene duplicata nelle shell Folio o nel componente CMS generico.
- Cattivo: `ChangeStatus` non autorizza da sola (l'autorizzazione e' dell'azione Filament che la chiama): una chiamata diretta da codice nuovo aggira la Policy ([change-status.md](../contracts/change-status.md)).
- Cattivo: la regola non ha una guardia automatica che impedisca una scrittura diretta di `status` fuori dalle Action: oggi si regge sulla review.

## Conferma

Nessuna guardia Arch contro scritture dirette di `status` fuori dalle Action. Prove esistenti, da `laravel/`:

```bash
vendor/bin/pest Modules/Fixcity/tests/Feature/Actions/TicketStatusChangeLockTest.php
vendor/bin/pest Modules/Fixcity/tests/Feature/Actions/CreateTicketAtomicityTest.php
vendor/bin/pest Modules/Fixcity/tests/Feature/Filament/ChangeStatusActionTargetsTest.php
vendor/bin/pest Modules/Fixcity/tests/Unit/Filament/EditTicketWorkflowFieldsTest.php
vendor/bin/pest Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardTamperingTest.php
```

Rispettivamente: una transizione decisa su una copia vecchia del ticket e un doppio clic non producono due cambi; la creazione e' atomica; l'azione offre solo i passaggi della matrice; lo stato e la
priorita' del form di modifica sono scartati; lo stato e la priorita' manomessi dal client in creazione sono ignorati. Controllo di review: `rg -n "setStatus\(|->status = " Modules/Fixcity/app` deve elencare
solo le Action di lifecycle, `Models/Ticket.php` e commenti (oggi: `ChangeStatus`, `ApproveTicketAction`, `RejectTicketAction`, `CloseTicketAction`, `ConfirmTicketResolutionAction`, la riga commentata di
`Listeners/TicketCreatedListener.php`). Proposta: un test Arch che vieti `setStatus` fuori da `Modules/Fixcity/app/Actions`.

## Stato attuale

Due eccezioni al principio, verificate nel codice. `TransitionTicketStatusAction` (`app/Actions/Workflow/`) e' la primitiva "sola transizione" prevista dalla decisione, ma nessun codice di produzione la
chiama (`rg TransitionTicketStatusAction app` trova la sola definizione e il suo contratto [transition-ticket-status-action.md](../contracts/transition-ticket-status-action.md)); il percorso vero e' `ChangeStatus`.
`Listeners/TicketCreatedListener.php` contiene ancora, commentata, la scrittura diretta dello stato alla creazione e non e' registrato in `EventServiceProvider`.
