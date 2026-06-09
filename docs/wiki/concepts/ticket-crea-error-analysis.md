---
name: segnalazione-crea-error-analysis
description: "Analisi dell'errore di routing fatto dall'AI: ha confuso il Folio page hardcoded con il percorso CMS JSON-driven"
type: feedback
---

# Error Analysis: Segnalazione-Crea Routing Confusion

## L'errore

Quando chiesto "dove viene gestita la URL /it/tests/segnalazione-crea", l'AI ha:

1. Cercato file con `SegnalazionePage` nel nome → non trovato
2. Trovato `segnalazione-crea.blade.php` → assunto che QUELLA fosse la pagina
3. Spiegato il flusso partendo dal Folio page hardcoded, ignorando il JSON CMS

## Root cause

L'AI ha seguito il pattern di ricerca sbagliato:

1. **Cercato il Livewire widget** → trovato, corretto
2. **Cercato il file blade che lo monta** → trovato `segnalazione-crea.blade.php`
3. **Saltato il JSON CMS** → errore critico

Il file `segnalazione-crea.blade.php` ha route name `segnalazione.crea`, che NON corrisponde all'URL `/it/tests/segnalazione-crea`.

La URL `/it/tests/segnalazione-crea` ha route name `tests.view` → gestita da `tests/[slug].blade.php` → JSON CMS → block view → widget.

## Perché l'AI ha sbagliato

- Non ha verificato la route name del Folio page vs l'URL richiesto
- Ha trovato un file con nome simile e si è fermata lì
- Non ha cercato il JSON di configurazione CMS come prima source of truth
- Non ha usato QMD/wiki per verificare l'architettura CMS-driven

## Come non rifarlo

### REGOLA DI RICERCA

Quando un URL inizia con `/it/tests/` o `/it/*`:

1. **PRIMA**: Verificare la route name (Folio `name('...')`) — corrisponde all'URL?
2. **POI**: Se il prefix è `/it/tests/` → cercare JSON in `config/local/fixcity/database/content/pages/tests.{slug}.json`
3. **INFINE**: Tracciare il chain JSON → block view → widget

### CHECKLIST VERIFICA

```
□ La route name del Folio page corrisponde al prefisso dell'URL?
□ Esiste un JSON CMS per questa pagina? (cercare in config/local/fixcity/database/content/pages/)
□ Il JSON definisce content_blocks con view?
□ La block view monta il componente/widget?
```

### COMANDI UTILI

```bash
# Trovare il JSON per una pagina
find config -name "tests.${slug}.json" -type f

# Verificare la route name di un Folio page
grep "name(" laravel/Themes/Sixteen/resources/views/pages/segnalazione-crea.blade.php

# Trovare il file route che gestisce un URL
grep -rl "tests\[slug\]" laravel/Themes/*/resources/views/pages/tests/
```

## Documentazione correlata

- `segnalazione-crea-url-routing-chain.md` — catena completa
- `theme-cms-block-architecture-segnalazione-crea.md` — architettura theme-level
- `cms-block-driven-page-routing-rule.md` — regola permanente
