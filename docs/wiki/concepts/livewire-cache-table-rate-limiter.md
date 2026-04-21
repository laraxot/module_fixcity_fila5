# livewire cache table rate limiter

## Problema Riscontrato

Durante l'interazione con il wizard (Livewire), si è verificato un errore fatale:
`SQLSTATE[42S02]: Base table or view not found: 1146 Table 'fixcity_data.cache' doesn't exist`.

## Causa Radice

Laravel 11+ e Livewire utilizzano pesantemente il `RateLimiter` per la
sicurezza e la gestione dei checksum. Se l'applicazione usa lo store
`database` per la cache (caso frequente in installazioni Laraxot),
Laravel tenta di interrogare la tabella `cache`.

## Soluzione

È obbligatorio assicurarsi che le tabelle di supporto per la cache siano presenti nel database principale del modulo (`fixcity_data`).

1. **Migrazione cache principale**:
   - `php artisan migrate --path=database/migrations/2026_04_21_111944_create_cache_table.php --force --no-interaction`
2. **Hardening duplicati**:
   - resa idempotente `database/migrations/2026_04_21_112114_create_cache_table.php` con guard `hasTable('cache')`.
   - eseguita anche la migrazione duplicata in sicurezza
     (senza collisione tabella).
3. **Verifica operativa**:
   - `Schema::hasTable('cache') === true`
   - `Schema::hasTable('cache_locks') === true`
   - `RateLimiter::hit()` / `RateLimiter::attempts()` funzionanti.

## Best Practice Laraxot

Ogni nuovo modulo o ambiente deve verificare preventivamente la
disponibilità degli store persistenti (Cache, Session, Jobs)
prima di esporre componenti Livewire complessi come i wizard.
