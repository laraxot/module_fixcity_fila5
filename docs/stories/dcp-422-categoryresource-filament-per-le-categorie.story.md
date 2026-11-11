---
title: "DCP-422: CategoryResource Filament per le categorie"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, filament, blueprint, category, back-office]
qmd: "CategoryResource Filament categorie back office XotBaseResource LC-08 blueprint sort_order is_active parent_id"
related:
  - ./dcp-408-lista-categorie-senso-origine-e-difetti-misurati.story.md
  - ./dcp-421-lista-categorie-legame-tra-categoria-e-tipo-di-segnalazione.story.md
issues: []
discussions: []
---

# DCP-422: CategoryResource

## Origine

DCP-408, difetto LC-08: la directory pubblica mostra dati curabili solo da seeder; in `Modules/Fixcity/app/Filament/Resources` c'e' solo `TicketResource`. Il piano «Blueprint Filament» di DCP-408 indica una `CategoryResource` nel modulo Fixcity (il tema Sixteen non possiede Resource).

## Acceptance criteria

1. Una `CategoryResource` estende `Modules\Xot\Filament\Resources\XotBaseResource` (mai la `Resource` di Filament), con `getFormSchema()` e senza `getTableColumns()`.
2. I campi sono quelli che esistono davvero nella tabella `categories`: da DCP-408 `name`, `description`, `icon`, `parent_id`, `is_active`, `sort_order`. Lo schema reale va letto prima (`database-schema`), perche' la lista in DCP-408 non e' stata verificata colonna per colonna. Il campo `type` si aggiunge solo se DCP-421 sceglie A.
3. Etichette, segnaposto e suggerimenti arrivano dai file di lingua del modulo (it/en/de/es), mai stringhe nel codice.
4. Una policy come quella di `TicketResource`: solo ruoli autorizzati modificano le categorie.
5. Nessun relation manager. L'auto-discovery di Xot sulle cartelle `RelationManagers` non deve rompere la lista.
6. Test Filament (Pest) che coprono: elenco, creazione, modifica, attivazione e disattivazione, ordinamento, accesso negato a chi non ha il permesso.
7. PHPStan sul modulo Fixcity resta a zero errori nuovi.
8. La pagina pubblica `/it/lista-categorie` riflette una modifica fatta dal back office (verificato con una modifica reale su un database di prova).

## Tasks

- [ ] Leggere lo schema reale di `categories` e le colonne tradotte.
- [ ] Generare la Resource secondo il blueprint (skill `filament-resource`) adattata a XotBase.
- [ ] Lingue, policy, test.
- [ ] PHPStan e Pest con database isolato (`bashscripts/tools/pest-scratch-db.sh`).
- [ ] Controllo a mano nel pannello admin.

## Owned file scope

`laravel/Modules/Fixcity/app/Filament/Resources/CategoryResource*`, lang del modulo, test del modulo. Non toccare la cartella dei `TicketResource`.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
