---
type: bmad-initiative
status: open
created: 2026-10-17
updated: 2026-10-17
initiative_id: kp-db-connection-fix
tags: [database, mariadb, sqlite, design-comuni, bmad, second-brain, filament-blueprint]
summary: |
  Correzione connessione DB: il progetto usa MariaDB/MySQL (DB_CONNECTION=mariadb, DB_HOST=127.0.0.1, DB_PORT=3306, DB_DATABASE=fixcity_data). Il database.php aveva 'sqlite' come default fallback, causando SQLiteDatabaseDoesNotExistException su /it/events (e altre pagine) quando il file .sqlite manca sul server. Corretto il fallback a 'mariadb'; verificato .env con DB_CONNECTION=mariadb.
references:
  - laravel/config/database.php
  - laravel/.env (DB_CONNECTION=mariadb)
  - error: SQLiteDatabaseDoesNotExistException at /mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel/database/fixcity_data.sqlite
