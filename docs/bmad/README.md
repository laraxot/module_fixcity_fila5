# FixCity BMAD — piano canonico del modulo

Questa è la sede BMAD del dominio FixCity. Il prodotto è un servizio civico per la raccolta e gestione di segnalazioni urbane: cittadino, operatore PA, supervisor e admin devono completare un unico flusso verificabile.

## Vertical slice obbligatorio

`privacy → dati → posizione → riepilogo → creazione → conferma → tracking → assegnazione → lavorazione → risoluzione → feedback`.

## Stato reale

- modello Ticket, wizard, pagine Folio e `TicketResource` Filament presenti;
- PHPStan su `Modules` verde;
- relazione PA `assignee` riallineata alla colonna reale `responsible_id`;
- azione Filament per assegnare/rimuovere l'operatore implementata;
- test applicativi end-to-end ancora da eseguire: il DB `fixcity_data_test` rifiuta l'utente configurato;
- notifiche, transizioni, policies, timeline e accessibilità devono essere dimostrate con test integrati, non solo con la presenza delle classi.

## Definition of Done

Il modulo è pronto per il pilota quando un test staging dimostra creazione, isolamento cittadino, presa in carico, assegnazione, cambio stato, notifica, timeline, chiusura e rating; inoltre i gate PHPStan, Pest, browser, accessibilità, backup e monitoraggio sono verdi.

Documenti:

- [gap analysis](gap-analysis.md)
- [release plan](release-plan.md)
- [stories](stories/)

## Regole consolidate dal Second Brain

- namespace del modulo: `Modules\\Fixcity\\...`, determinato dal `composer.json` del modulo;
- policy: `app/Policies`, quindi `Modules\\Fixcity\\Policies`;
- gerarchia policy: `TicketPolicy` estende `Modules\\Fixcity\\Policies\\BasePolicy`, che estende `UserBasePolicy`;
- autorizzazioni: i metodi ricevono `Modules\\Xot\\Contracts\\UserContract`, mai `Modules\\User\\Models\\User`;
- pagine Filament: solo wrapper `Modules\\Xot\\Filament\\...` (`XotBase*`);
- dashboard Filament: `XotBaseDashboard`, non `XotBasePage`;
- FK verso model: `foreignIdFor(Model::class, 'column')`; User dinamico con `XotData`;
- migration storiche forward-only: non si riscrivono, si correggono con migration evolutive.


## Fonte architetturale canonica

Per namespace, policy, contratti utente, Filament, FK e ownership Frontoffice/tema, consulta il [contratto architetturale canonico](../wiki/concepts/fixcity-architecture-contract-2026-09-26.md). Le story registrano criteri e cambiamenti specifici; non duplicare in ogni story la regola completa.
