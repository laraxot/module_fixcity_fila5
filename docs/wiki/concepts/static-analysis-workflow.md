---
name: static-analysis-workflow
description: Mandatory static analysis steps after every file edit.
type: concept
---

# Static Analysis Workflow

## Rule: Run after every file modification

> **OBBLIGATORIO**: each time you modify a PHP file, run the following tools **before** considering the task complete.

| Tool | Installation | Command | Purpose |
|------|--------------|---------|---------|
| **PHPStan** | Download `phpstan.phar` (standalone) | `php phpstan.phar analyse <file> --level=max` | Type‑error detection, dead code |
| **PHPMD** | Download `phpmd.phar` (standalone) | `php phpmd.phar <file> text unusedcode,design` | Code‑smell, complexity |
| **PHP Insights** | Download `phpinsights.phar` (standalone) | `php phpinsights.phar analyse <file> --no‑interaction` | Architecture, style, quality |
| **Visual Parity** | Install `percy` or `backstopjs` (pick one) | `npx backstop test` (after running `npm run build`) | Ensure UI matches reference designs |

> **VIETATO** install them via Composer `require‑dev`. Use the `.phar` files placed in `bashscripts/tools/` and reference them in the project‑wide `scripts/` directory.

## Setup (one‑time)

```bash
# Download tools (example)
cd bashscripts/tools
curl -LO https://github.com/phpstan/phpstan/releases/latest/download/phpstan.phar
curl -LO https://github.com/phpmd/phpmd/releases/latest/download/phpmd.phar
curl -LO https://github.com/nunomaduro/phpinsights/releases/latest/download/phpinsights.phar
chmod +x *.phar
```

## After‑edit checklist

1. Run PHPStan on the changed file(s).
2. Run PHPMD on the changed file(s).
3. Run PHP Insights (if the file is part of a module).
4. Build theme assets (`npm run build` in `Themes/Sixteen`) and run visual parity test.
5. If any tool reports an error, fix it **before** marking the task done.

## Automation

Add a pre‑commit hook (`.git/hooks/pre‑commit`) that executes the three tools on staged `.php` files:

```bash
#!/bin/bash
php bashscripts/tools/phpstan.phar analyse $(git diff --cached --name-only -- '*.php') --error‑format=raw
php bashscripts/tools/phpmd.phar $(git diff --cached --name-only -- '*.php') text unusedcode,design
php bashscripts/tools/phpinsights.phar analyse $(git diff --cached --name-only -- '*.php') --no‑interaction
```

## Documentation

- Keep this document in `docs/wiki/concepts/static-analysis-workflow.md` and reference it from `docs/wiki/index.md`.
- Add a memory entry `static-analysis-workflow` to remind the assistant before every edit.

---
