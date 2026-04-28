# Profiles UUID Contract

## Contratto

Nel modulo Fixcity, `profiles` deve avere:

- `id` intero auto-increment come chiave primaria relazionale
- `uuid` separato come identificatore esterno stabile
- `credits` nullable (campo opzionale, non bloccante in creazione profilo)

## Fonte di verita'

La fonte di verita' non e' una migrazione additiva, ma:

- `laravel/Modules/Fixcity/database/migrations/2026_04_27_190000_create_profiles_table.php`

## Regola operativa

- se manca `uuid`, si corregge la migrazione canonica
- `credits` resta nullable sia nel create che nel change idempotente
- non si crea `add_uuid_to_profiles_table`
- non si crea `add_credits_nullable_to_profiles_table`
- non si crea `repair_profiles_id_and_uuid_contract`

## Nota runtime

Se il DB locale e' stato creato prima del fix, modificare il file di migrazione da solo non riallinea automaticamente la tabella gia' esistente: serve una sincronizzazione forward-only dello schema reale.
