# Fixcity Wiki Log

## [2026-04-20] fix | profiles.uuid riportato nella migrazione owner
- sources:
  - `database/migrations/2026_04_20_000009_create_profiles_table.php`
- pages:
  - `concepts/profiles-uuid-contract.md` (new)
- summary:
  - la migrazione owner `create_profiles_table` di Fixcity ora dichiara `uuid` nello schema base
  - aggiunto anche guard idempotente in `tableUpdate()` per installazioni legacy con tabella `profiles` senza colonna `uuid`
  - timestamp migrazione riallineato per mantenere la regola "1 modello = 1 migrazione"

## [2026-04-15] init | wiki bootstrap
- Struttura wiki/log.md inizializzata.
- Layer raw: tutti i file in `docs/` (eccetto `wiki/`).
- Layer wiki: `docs/wiki/` — LLM-maintained, sintesi ad alto riuso.
- Schema: `docs/.schema/WIKI_SCHEMA.md`
- Adozione moduli: `docs/project/llm-wiki-module-adoption.md`
