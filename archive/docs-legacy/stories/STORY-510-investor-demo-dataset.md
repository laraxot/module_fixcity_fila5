---
created: 2026-09-28
updated: 2026-09-28
qmd: "STORY 510 demo investitori seeder"
issues: []
discussions: []
title: "STORY-510 — Dataset demo investitori: 520 segnalazioni in 31 comuni italiani"
type: story
status: done
tags: [seeder, demo, investitori, fixcity, ticket, commenti, ore, sla, citta]
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/510"
discussion_link: "https://github.com/laraxot/fixcity/discussions/510"
created_at: "2026-09-28T08:00:00Z"
---

# STORY-510 — Dataset demo investitori

## Obiettivo
Produrre con un solo comando un quadro credibile per la presentazione investitori:
520 segnalazioni reali in 31 comuni italiani, 21 categorie, 10 uffici comunali,
discussioni fra cittadini e operatori, ore di cantiere e timeline SLA.
Il dataset deve essere riproducibile e rilanciabile senza duplicare nulla.

## Riferimenti
- GitHub Issue #510
- Discussion #510

## Artefatti
- `database/seeders/FixcityDemoSeeder.php` — orchestratore a 6 passi, ambiente `local|testing|demo`
- `database/seeders/Support/DemoText.php` — estrazione di testi non vuoti e slug
- `database/seeders/Support/DemoWeightedChoice.php` — sorteggio pesato deterministico
- `database/seeders/Support/DemoCategoryCatalog.php` — 21 categorie su 10 uffici
- `database/seeders/Support/DemoMunicipalityProvider.php` — 31 comuni, indirizzi, coordinate
- `database/seeders/Support/DemoSlaTimeline.php` — percorsi di stato e SLA
- `database/seeders/Support/DemoPeopleProvider.php` — 60 cittadini, 10 operatori
- `database/seeders/Support/DemoNarrative.php` — testi italiani per stato e ruolo
- `database/seeders/Support/DemoTicketWriter.php` — scritture, attivita' e allegati
- `tests/Feature/Database/FixcityDemoSeederTest.php` — 8 test di regressione

## Scelte progettuali
- Prefisso `DMO` nel `code`: non sfiora i dataset `DEMO-*` (presentazione FO) e
  `INV-*` (campagna preesistente), che restano intatti.
- `tickets` non ha `category_id`: la categoria arriva dal campo `type`
  (`TicketTypeEnum`), come nel backoffice.
- Codice `DMO-0001..DMO-0520` come chiave di matching: il secondo lancio aggiorna
  le stesse righe invece di duplicarle.
- Foto ancorata all'hash del `code`, non al generatore casuale: la selezione degli
  allegati resta identica a ogni rilancio.
- Nomi delle utenze estratti da un generatore dedicato per indice: il pool non
  consuma i numeri del generatore condiviso, altrimenti il dataset cambierebbe in
  base alle utenze gia' presenti nel database.
- Ore contate in mezze ore intere: la somma delle voci resta `<= estimation`
  senza errori di floating point.

## Checklist Evidence
- [x] Un comando produce il dataset completo (`php artisan db:seed --class=FixcityDemoSeeder`)
- [x] 520 ticket, codici e slug univoci, 31 comuni, 21 categorie
- [x] Timeline SLA su ogni attivita' di stato, nessun timestamp nel futuro
- [x] Ufficio assegnato a tutte le pratiche uscite dal triage
- [x] 1174 messaggi di discussione, 792 voci di ore entro la stima
- [x] Idempotenza verificata: due lanci consecutivi producono gli stessi conteggi
- [x] PHPStan level 10 pulito su `Modules/Fixcity/database/seeders`
- [x] `vendor/bin/pint` pulito sui file scritti
- [x] 409 test del modulo Fixcity verdi (1956 asserzioni)

## Fuori scope
- Migrazione delle colonne `citizen_rating` / `citizen_rated_at`: non esistono
  nello schema corrente, il writer le scrive solo se presenti.
- Modifica ai seeder `DEMO-*` e `INV-*`, che restano invariati.
