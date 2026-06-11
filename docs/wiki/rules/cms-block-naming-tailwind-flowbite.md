---
title: "CMS Block naming — Tailwind UI / Flowbite"
type: rule
confidence: high
created: 2026-06-01
updated: 2026-06-10
tags: [cms, blocks, naming-convention, tailwind, flowbite, views, critical]
issues:
  - https://github.com/laraxot/base_fixcity_fila5/issues/194
discussions:
  - https://github.com/laraxot/base_fixcity_fila5/discussions/195
related:
  - ../../../../Themes/Sixteen/docs/blocks/folder-vocabulary.md
  - no-italian-component-names.md
  - frontend-stack-canonical.md
---

# CMS Block naming — Tailwind UI / Flowbite

## Regola (obbligatoria)

Le **sottocartelle** di `resources/views/components/blocks/` devono usare nomi presi da:

- [Flowbite Blocks](https://flowbite.com/blocks/)
- [Tailwind CSS UI Blocks](https://tailwindcss.com/plus/ui-blocks)

**Inglese**, **kebab-case**, **no dominio** (`ticket`, `segnalazione`).  
Vocabolario completo + contenuto ammesso: [folder-vocabulary.md](../../../../Themes/Sixteen/docs/blocks/folder-vocabulary.md).

## Shape

```
blocks/<categoria-tailwind-o-flowbite>/<variante>.blade.php
```

JSON CMS:

```json
{
  "type": "grid",
  "data": {
    "view": "pub_theme::components.blocks.grid.2col"
  }
}
```

## Esempi

| ✅ Corretto | ❌ Vietato |
|------------|-----------|
| `blocks/hero/default.blade.php` | `blocks/segnalazioni/layout.blade.php` |
| `blocks/grid/2col.blade.php` | `blocks/ticket-layout/layout.blade.php` (legacy) |
| `blocks/vertical-navigation/contacts.blade.php` | `blocks/governance-calendario/` |
| `blocks/cta/ticket.blade.php` | `__('Chiudi')` come chiave i18n |

## Eccezioni legacy (non aggiungere)

`tests/`, `flow/`, `design-comuni/`, `ticket/`, `ticket-layout/`, `ticket-list/`, `administration/`, `governance/`, `thematic/`, `feature_sections/`, `topics-grid/`

Nuovi blocchi **sempre** su allowlist.

## Verifica

```bash
bash bashscripts/quality-gates/check-blocks-folder-names.sh
```

## Riferimenti

- STORY-111: `docs/stories/STORY-111-home-json-cms-blocks-refactor.md`
- Script: `bashscripts/quality-gates/check-blocks-folder-names.sh`
