---
title: BMAD Plan — Services conversion to Spatie Queueable Actions
decision: No-Services architecture (Geo precedent 2026-10-08)
status: verified
module: Fixcity
date: 2026-10-20
updated: 2026-10-20
relates_to: services-to-actions-geo.story.md, decision-log.md (Geo), docs/INDEX.md
tags: [fixcity, services-to-actions, bmad, plan, verified, no-services, queueable-action]
type: note
qmd: "bmad   conversione services → spatie queueable actions fixcity"
---

# BMAD — Conversione Services → Spatie Queueable Actions

## B (Benchmark / Because)
Per `Modules/Geo` la decisione 2026-10-08 ha eliminato `BaseGeoService`, `GoogleMapsService`, `GeographicalScopes` → sostituiti da `Actions/` (e.g., `FetchGoogleMapsElevationAction`, `BuildHaversineSqlAction`, `GeoTrait::scopeWithDistance`). Il classpath (`autoload_classmap.php`) era stato pulito con `composer dump-autoload`. Il codice di produzione (`app/`) passa PHPStan (`trait.unused` solo `HasPlaceTrait`, non errore). La regola è: **Nessuna cartella `app/Services/` deve sopravvivere**.

## M (Model / Meaning)
Lo scopo dei tre servizi Fixcity:
- `WorkflowService` (264 righe): gestione transizioni stato ticket (es. `canTransitionTo()`, `transition()`, `getValidNextStates()`) — **logica di business del flusso**.
- `NotificationService` (206 righe): invio notifiche (email, push) sui ticket — **operazione asyncronizzabile**.
- `TicketService` (264 righe): CRUD ticket, ricerca, stato — **operazioni sui record**.

Tutti hanno un **scopo chiaro** ma violano il principio Laraxot (No-Controllers, Actions-over-Services): sono monoliti stateful con dipendenze dirette, difficili da testare isolatamente (v. `WorkflowServiceTest.php`: 125 errori PHPStan, `method.notFound` su `canTransitionTo()` perché il metodo non esiste più o è stato rimosso in un merge). La fonte dell'errore non è un bug del metodo, ma che **il servizio come classe non deve esistere**.

## A (Audit / Actual)
- `Seo/MetatagService.php`: **zombie** — già sostituito da `Adapters/MetatagFacadeAdapter` + `MetatagState` + QueueableActions (provider `SeoServiceProvider`). Rimuovere il file residuo.
- `Comment/app/Services/`: directory vuota (0 file verificate) — eliminare.
- `Fixcity/app/Services/`: 3 file attivi ma violano No-Services. Il test `WorkflowServiceTest.php` (125 errori PHPStan) è il **segnale**: se il servizio non esiste, anche il test deve essere riconvertito in test dell'Action corrispondente.

## D (Deliver / Decision)
1. **Seo**: `MetatagService` è stato rimosso; `MetatagFacadeAdapter` delega a `Get/Replace/MergeMetatagDataAction`.
2. **Comment**: la directory `app/Services` vuota è stata rimossa.
3. **Fixcity — WorkflowService**: il monolite è stato eliminato. `TransitionTicketStatusAction` usa realmente `Spatie\QueueableAction\QueueableAction` e `execute(Ticket, targetStatus)`; le operazioni con regole proprie restano nelle Actions specializzate già presenti.
4. **Fixcity — NotificationService**: gli invii usati in produzione sono già rappresentati da `NotifyTicketAssigneeOfAssignmentAction` e `NotifyTicketSubscribersOfStatusChangeAction`; non si reintroduce un wrapper Service per mantenere test obsoleti.
5. **Fixcity — TicketService**: creazione, assegnazione, stato, chiusura, risoluzione, rifiuto, query e timeline sono Actions separate; il vecchio CRUD monolitico non viene ricreato.
6. **Test conversion**: i test sotto `tests/Unit/Services` non possono importare classi cancellate; vengono rimossi/riscritti nelle suite `tests/**/Actions` durante il lane PHPStan dedicato.
7. **Theme**: anche `Themes/Sixteen/app/Services/ThemeService.php` è stato rimosso perché inutilizzato; restano fuori scope le classi in `src/Services`, che sono un namespace infrastrutturale distinto dalla convenzione `app/Services` dei moduli.

Regole applicate:
- **No Controllers**: nessun `Http/Controllers/` creato (azioni Spatie non sono controller).
- **No Services**: `app/Services/` rimosso dopo conversione.
- **Filament Blueprint**: Action = SSoT per operazione; il widget/form usa il Service/Ovia `getFormSchema()` non cambia.
- **Second Brain**: questo documento persiste il perché, non solo il come.
- **Graphify**: `graphify-out/` non versionato (`.gitignore`), rigenerabile.

## Verifica post
- `find Modules/Fixcity/app/Services/` → assente.
- `php artisan` → no errori di bootstrap (nessuna classe mancante se Action registrato).
- `vendor/bin/phpstan analyse Modules/Fixcity/app/Actions/` → 0 errori nuovi.
