# Migration ownership

Fixcity segue la regola R3: una tabella ha una sola migration owner, che evolve
forward-only tramite `XotBaseMigration::tableUpdate()` e `updateTimestamps()`.

I file `*.php.old` sono migration storiche superseded: non vanno cancellati e
Laravel non li carica. In particolare, la tabella `users` è posseduta da
`Modules/User` sulla connessione `user`, mentre `ticket_subscribers` è
posseduta da `2023_01_01_000000_create_ticket_subscribers_table.php`. La
migration additiva `2026_10_18_180002_repair_ticket_subscribers_table.php` copre
solo installazioni parzialmente migrate: non sposta, cancella o sovrascrive
dati; le
migration `update_*` e `align_*` relative alla stessa tabella (inclusa
`2026_10_18_170300_update_users_id_to_string_uuid.php.old`, che alterava
erroneamente `fixcity.users`) sono state consolidate e disabilitate.
