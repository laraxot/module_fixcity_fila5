---
title: "lang split Fixcity — claude-audit large file"
type: memory
module: Fixcity
tags: [fixcity, i18n, claude-audit, lang-split]
created: 2026-07-09
updated: 2026-07-09
qmd: "Fixcity lang split array_merge loader claude-audit 500 righe"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/1"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/304"
related:
  - ./claude-audit-static.md
  - ../../Xot/docs/wiki/concepts/claude-audit-static-all-modules.md
---

# Split lang Fixcity (claude-audit)

## Script

```bash
php bashscripts/tools/split-module-lang-monolith-for-audit.php Fixcity <locale> lang
php bashscripts/tools/split-module-lang-monolith-for-audit.php Fixcity en ticket
```

## Pattern

- Array flat → chunk `lang_part01.php` …
- Array nested (ticket) → file per chiave top-level
- Loader `lang.php`: `return array_merge(require …);`
- Namespace Laravel invariato (`fixcity::`)

## Loader path

I `require` devono usare prefisso file (`lang_part01.php`, non `part01.php`).
