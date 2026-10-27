---
title: "DB di test fixcity_*_test pronti + Pest verificabile"
type: story
status: done
module: Fixcity
tags: [bmad, testing, database, pest, graphify]
qmd: "fixcity_user_test fixcity_data_test mariaDB sqlite pest graphify update"
related:
  - ../../User/docs/wiki/concepts/local-mysql-user-provision.md
  - ../../Themes/Sixteen/docs/architecture-testing-env.md
  - ../compliance/00-scopo-del-progetto.md
---

# Scopo

I test Pest devono essere eseguibili e verdi su questa macchina: niente alibi
"manca il database". Lo stesso per `graphify update .`.

# Causa

- `.env.testing` punta a `fixcity_user_test` / `fixcity_data_test`.
- I DB esistevano a meta (schema incompleto) o la config cache puntava ai DB di sviluppo.
- `graphify-out/graph.json` da 418MB + lock concorrenti facevano scadere l'update.

# Fix

1. `CREATE DATABASE IF NOT EXISTS` per entrambi i `*_test` (utf8mb4).
2. Clonate le tabelle mancanti (solo struttura) da `fixcity_user` / `fixcity_data`.
3. `php artisan config:clear`; fixture SQLite `database/fixcity_user_test.sqlite`
   e `fixcity_data_test.sqlite` allineate a `test_data.sqlite` (118 tabelle).
4. `LoginWidgetTest` usa `Modules\User\Tests\TestCase` (no RefreshDatabase root).
5. Vietato `RefreshDatabase` su suite che condividono `fixcity_data_test` con Blog/Notify — vedi [refreshdatabase-wipe-shared-test-db](./refreshdatabase-wipe-shared-test-db.story.md).
6. `XotBaseMigration::updateTimestamps` usa `getColumnListing` fresco.
7. `.graphifyignore` alleggerito; un solo `graphify update .` → 52877 nodi, query OK.

# Verifica

```bash
mysql -umarco -pmarco -h127.0.0.1 -e "SHOW DATABASES LIKE 'fixcity%_test'"
cd laravel && APP_ENV=testing ./vendor/bin/pest Modules/Fixcity/tests/Unit/TicketResourceRelationManagersTest.php
cd .. && graphify update .   # completa; poi graphify query "TicketResource"
```
