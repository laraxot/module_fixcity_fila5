# Fixcity Module — Wiki Schema

## Struttura wiki

```
docs/
  wiki/
    index.md       ← indice navigabile (obbligatorio)
    log.md         ← log operazioni (obbligatorio)
    schema.md      ← questo file
    concepts/      ← pattern e regole architetturali
    entities/      ← segnalazioni, ticket, wizard
    summaries/     ← sommari documenti
```

## Regole ingest

- `docs/` = raw source layer (immutabile, non modificare salvo ingest esplicito)
- `docs/wiki/` = compiled wiki layer (LLM sintetizza e aggiorna qui)
- Nuovi concetti → `concepts/<kebab-case>.md`
- Nuove entità → `entities/<kebab-case>.md`

## QMD collection

```bash
qmd search "segnalazione wizard" -c mod-fixcity
```
