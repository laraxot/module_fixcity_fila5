# livewire cache table rate limiter

## Problema Riscontrato

Durante l'interazione con il wizard (Livewire), si è verificato un errore fatale:
`SQLSTATE[42S02]: Base table or view not found: 1146 Table 'fixcity_data.cache' doesn't exist`.

## Causa Radice

Laravel 11+ e Livewire utilizzano pesantemente il `RateLimiter` per la sicurezza e la gestione dei checksum. Se l'applicazione è configurata per utilizzare lo store `database` per la memoria cache (default in molte installazioni Laraxot per consistenza multi-nodo), Laravel tenta di interrogare la tabella `cache`.

## Soluzione

È obbligatorio assicurarsi che le tabelle di supporto per la cache siano presenti nel database principale del modulo (`fixcity_data`).

1. **Migrazione**: Creazione della migrazione tramite `php artisan cache:table`.
2. **Esecuzione**: `php artisan migrate`.

## Best Practice Laraxot

Ogni nuovo modulo o ambiente deve verificare preventivamente la disponibilità degli store persistenti (Cache, Session, Jobs) prima di esporre componenti Livewire complessi come i wizard.
