---
title: "DCP-421: legame tra categoria e tipo di segnalazione"
type: story
status: needs-decision
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, lista-categorie, category, ticket-type, decisione]
qmd: "lista-categorie category TicketTypeEnum types[] category_id legame D-LC-02 LC-07 colonna type mappatura"
related:
  - ./dcp-408-lista-categorie-senso-origine-e-difetti-misurati.story.md
  - ./dcp-422-categoryresource-filament-per-le-categorie.story.md
issues: []
discussions: []
---

# DCP-421: categoria e tipo di segnalazione

In attesa dell'utente: e' la decisione D-LC-02 di [DCP-408](./dcp-408-lista-categorie-senso-origine-e-difetti-misurati.story.md). Il progetto chiede conferma prima di toccare lo schema del database.

## Origine

DCP-408, difetto LC-07: la tabella `categories` (25 righe, 16 radici attive) alimenta `/lista-categorie`, ma `tickets` non ha `category_id` (SQLSTATE 1054). Le segnalazioni si classificano per `type` con `TicketTypeEnum` (15 casi), e l'elenco pubblico gia' filtra con `/tickets?types[]=<valore>`. Quindi una card di categoria non puo' dire quante segnalazioni ha ne' portare a un elenco filtrato.

## Opzioni (da DCP-408)

- **A**: colonna `type` su `categories` (migration).
- **B**: tabella di mappatura categoria-tipo in configurazione.
- **C**: usare direttamente `TicketTypeEnum` come directory, con link `/tickets?types[]=<valore>`; `Category` resta per descrizioni e sottocategorie. Raccomandazione ponytail di DCP-408: C, zero schema nuovo.

## Acceptance criteria

1. L'utente sceglie A, B o C e la scelta e la ragione sono scritte qui.
2. Con C: ogni card porta a un elenco filtrato che esiste, con i testi in it/en/de/es; nessun conteggio finto.
3. Con A: la migration rispetta le regole di Xot (una sola `create_table` per tabella, `XotBaseMigration`), e il seeder e `DemoCategoryCatalog` si aggiornano.
4. Con B: la mappatura e' in un file di configurazione del modulo con un test che fallisce se un `TicketTypeEnum` resta senza categoria.
5. Un test verifica che ogni link prodotto dalla pagina risponda 200 (non un 404 ne' un redirect inatteso).

## Tasks

- [ ] Presentare le tre opzioni all'utente con la raccomandazione.
- [ ] Dopo la risposta, riscrivere i criteri 2, 3 o 4 come tasks e passare a `ready-for-dev`.
- [ ] Se A: chiedere esplicitamente conferma dello schema prima di scrivere la migration.

## Owned file scope (provvisorio, dipende dalla scelta)

`laravel/Modules/Fixcity/app/Actions/ListPublicCategoriesAction.php`, `laravel/Modules/Fixcity/app/Models/Category.php`, `laravel/Modules/Fixcity/resources/views/pages/lista-categorie.blade.php`, lang del modulo.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato; DCP-408 e' in corso di un'altra sessione |
