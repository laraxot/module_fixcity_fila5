---
title: "claude-audit static — modulo Fixcity"
type: concept
module: Fixcity
tags: [fixcity, quality, claude-audit, i18n, playwright]
created: 2026-07-09
updated: 2026-07-09
qmd: "Fixcity claude-audit static 80 score lang split Ticket traits Playwright env"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/1"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/304"
related:
  - ../../../../../../bashscripts/tools/run-claude-audit-module-static.sh
  - ../../../../../../bashscripts/tools/split-module-lang-monolith-for-audit.php
  - ../../Xot/docs/wiki/concepts/claude-audit-static-all-modules.md
  - ../memories/lang-split-fixcity-claude-audit.md
---

# claude-audit static (Fixcity)

## Comando

```bash
bash bashscripts/tools/run-claude-audit-module-static.sh Fixcity
```

`--max-files 8000` obbligatorio — default 500 tronca `tests/` e lang split.

## Fix quality (64/19 → 80/0)

| Finding | Mitigazione |
|--------|-------------|
| `lang/*/lang.php` >500 righe | `split-module-lang-monolith-for-audit.php` → `lang_partNN.php` + `array_merge` loader |
| `lang/en/ticket.php`, `lang/it/txt.php` | Split per chiave top-level (`ticket_fields.php`, …) |
| `Ticket.php` >500 righe | Trait `HasTicketRelations`, `NormalizesTicketLocation`; rimozione dead code commentato |
| `TicketLayoutViewModel.php` | Trait `BuildsTicketLayoutFilters`, `PresentsTicketLayoutChrome` |
| `TicketFormTest.php` | Split `TicketFormRenderTest` + `TicketFormValidationTest` |
| Nesting `coordinate-picker-lit.js` | Early return + handler estratti (`_onGeolocationSuccess`) |
| Playwright password | `tests/Playwright/support/credentials.js` + `.env.example` (`PLAYWRIGHT_TEST_*`) |

## `.gitignore`

Pattern `/Tests/` + `!tests/**` + `!audit-coverage/**` (WSL case-insensitive) — vedi Tenant memory.

## Verifica

```bash
bash bashscripts/tools/run-claude-audit-module-static.sh Fixcity
cd laravel && php -d memory_limit=2048M vendor/bin/phpstan analyse Modules/Fixcity/app/Models/Ticket.php Modules/Fixcity/app/ViewModels
```

Target static: **80/100**, **0 finding**.
