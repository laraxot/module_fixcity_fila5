---
id: refreshdatabase-wipe-shared-test-db
title: "RefreshDatabase wipeava fixcity_data_test (schema Blog su categories)"
type: story
status: done
priority: P0
owner: module:Xot
created: 2026-10-22
updated: 2026-10-22
qmd: "refreshdatabase migrate fresh fixcity_data_test categories blog schema dati sacri"
related:
  - ./test-db-fixcity-user-test-ready.story.md
  - ./dcp-103-test-rossi-residui-modulo-fixcity.story.md
  - ../REGOLA_DATI_SACRI.md
  - ../bmad/filament-blueprint.md
---

# RefreshDatabase wipeava `fixcity_data_test`

## Perché

In testing `blog`, `fixcity` e `mariadb` puntano allo stesso MariaDB `fixcity_data_test`.
`ArticleTest` (e altri) usavano `RefreshDatabase`: il trait sul figlio **sovrascrive**
il no-op di `Tests\TestCase::refreshDatabase()`, esegue `migrate:fresh` e ricrea
`categories` con lo schema Blog (`title` bigint) al posto di Fixcity (`name` string id).

Sintomo: `CategoryMigrationTest` vedeva la tabella ma senza colonna `name`;
in parallelo sparivano `tickets` e decine di tabelle.

## Fix (DRY + KISS)

1. Rimosso `use RefreshDatabase` / `RefreshDatabase::class` da 22 test (Blog, Notify, User, Geo, Xot).
2. `DatabaseTransactions` su TestCase di Blog, Notify, Geo; no-op `refreshDatabase()` su `XotBaseTestCase`.
3. Geo: rimosso `module:migrate` da ogni `setUp` (mutava lo schema condiviso).
4. `CategoryMigrationTest` usa solo `Schema::connection('fixcity')` e indici reali di produzione.

## Verifica

- Clonare struttura: `CREATE TABLE … LIKE fixcity_data.*` → 94 tabelle, `categories.name` presente.
- Preferire `bashscripts/tools/pest-scratch-db.sh` per suite lunghe (DB usa-e-getta).
- Gate: Pest `CategoryMigrationTest` verde; PHPStan Modules invariato a 0.

## Related

- [REGOLA_DATI_SACRI](../REGOLA_DATI_SACRI.md)
- [test-db ready](./test-db-fixcity-user-test-ready.story.md)
- [filament-blueprint](../bmad/filament-blueprint.md)
