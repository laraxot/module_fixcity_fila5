---
title: "README"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-27
qmd: "README"
issues: []
discussions: []
---

# FixCity BMAD — piano canonico del modulo

Questa è la sede BMAD del dominio FixCity. Il prodotto è un servizio civico per la raccolta e gestione di segnalazioni urbane: cittadino, operatore PA, supervisor e admin devono completare un unico flusso verificabile.

## Religione stack (FO / BO)

| Canale | Stack | Mai |
|--------|-------|-----|
| Front office | Folio + Volt + Filament (widget) + Actions | `Http\Controllers`, Services |
| Back office | Filament `XotBase*` | Controller MVC |

Memoria: [no-controllers-folio-volt-filament](../wiki/concepts/no-controllers-folio-volt-filament.md) ·
[contratto architetturale](../wiki/concepts/fixcity-architecture-contract-2026-09-26.md).

## Vertical slice obbligatorio

`privacy → dati → posizione → riepilogo → creazione → conferma → tracking → assegnazione → lavorazione → risoluzione → feedback`.

## Stato reale

- modello Ticket, wizard, pagine Folio e `TicketResource` Filament presenti;
- PHPStan su `Modules` verde;
- relazione PA `assignee` riallineata alla colonna reale `responsible_id`;
- azione Filament per assegnare/rimuovere l'operatore implementata;
- suite Fixcity SQLite, ultima esecuzione 27 settembre 2026: **392 passati / 4 falliti / 1.711 asserzioni**. I quattro fallimenti chiedono `/it/segnalazioni` e si aspettano 200 invece del redirect canonico a `/it/tickets`; `TicketPagesTest.php` è sotto un lock preesistente e non è stato modificato. Il nuovo Pest mirato seed demo passa: 2 test / 10 asserzioni;
- PHPStan completo `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress`: zero errori (27 settembre 2026);
- notifiche in-app + email dopo commit per indirizzi verificati; preferenza email cittadino e retry automatici sono implementati, ma worker/SMTP/failed-jobs non sono verificati in staging e push resta un gap;
- la lista pubblica e i tab mappa/elenco sono stati controllati in browser a 320, 768 e 1.440 px, ma non equivalgono al collaudo autenticato PA/cittadino su MySQL/staging;
- Chromium/Playwright sono disponibili tramite le librerie browser isolate del workspace. Verificati 48 casi guest: home, tickets, services, service detail, privacy e form tracking; 4 lingue e 320/1440 px, HTTP 200, zero overflow, chiavi grezze o errori JS. Screenshot homepage IT mobile: `/tmp/fixcity-home-current-it-390.png`. Queste prove non coprono autenticazione completa, console PA, screen reader né deploy MySQL/staging.

## Definition of Done

Il modulo è pronto per il pilota quando un test staging dimostra creazione, isolamento cittadino, presa in carico, assegnazione, cambio stato, notifica, timeline, chiusura e rating; inoltre i gate PHPStan, Pest, browser, accessibilità, backup e monitoraggio sono verdi.

Documenti:

- [homepage guest — routing atteso](homepage-guest-routing-expected.md)
- [homepage guest — routing confronto](homepage-guest-routing-comparison.md)
- [homepage guest — routing piano](homepage-guest-routing-correction-plan.md)
- [homepage guest — stato atteso](homepage-guest-expected-visual.md)
- [homepage guest — confronto visuale](homepage-guest-visual-comparison.md)
- [homepage guest — piano correttivo](homepage-guest-visual-correction-plan.md)
- [catalogo workflow BMAD](workflow-catalog.md)
- [workflows eseguibili](workflows/README.md)
- [flussi attore](actor-flows.md)
- [percorsi attesi, presenti e mancanti](actor-journeys.md)
- [mappa dettagliata dei percorsi](../wiki/concepts/user-journey-map.md)
- [gap analysis](gap-analysis.md)
- [audit UI/UX runtime e prove responsive](ui-ux-runtime-audit-2026-09-26.md)
- [verifica runtime tracking e notifiche](tracking-links-capability-2026-09-27.md)
- [STORY-521 — ownership ticket demo e privacy tracking](stories/STORY-521-demo-ticket-owner-assignment.md)
- [release plan](release-plan.md)
- [stories](stories/)
- [STORY-013 — preferenze email cittadino](stories/STORY-013-ticket-notification-preferences.md)
- [Gate pubblicazione informativa privacy](stories/STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md)
- [Semantica e audit della presa visione privacy](stories/STORY-FIXCITY-PRIVACY-ACKNOWLEDGEMENT-AUDIT.md)

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
