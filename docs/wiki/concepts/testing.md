---
title: "Testing in Fixcity"
type: concept
tags: [fixcity, testing, pest, phpstan]
created: 2026-06-05
updated: 2026-06-13
qmd: "Fixcity testing Pest PHPStan TestCase DatabaseTransactions quality gate"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/52"
discussions:
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/53"
related:
  - ./phpstan-pest-testcase-helpers.md
  - ../overviews/completion-roadmap.md
  - ../../../Xot/docs/wiki/rules/module-testcase-xotbase-hierarchy.md
---

# Testing in Fixcity

## Pest PHP

Tutti i test usano **Pest PHP**. Vietate classi PHPUnit (`extends TestCase`). Vietato rinominare in `.pest.php`.

## TestCase

- `Modules\Fixcity\Tests\TestCase` estende `XotBaseTestCase`
- `DatabaseTransactions` — **mai** `RefreshDatabase` (dati sacri)
- Connessioni: `fixcity`, `user`, `comment`, `media`

## PHPStan + Pest (2026-06-13)

Usare helper non-null: `ticket()`, `authUser()`, `workflow()`, `ticketService()`, `notification()`.

Dettaglio: [phpstan-pest-testcase-helpers.md](./phpstan-pest-testcase-helpers.md)

## Quality gate

```bash
cd laravel
./vendor/bin/pest Modules/Fixcity/tests
./vendor/bin/phpstan analyse Modules/Fixcity
```

Livello: **max** (da `phpstan.neon` root). Solo l'utente modifica neon.

## Roadmap

[completion-roadmap.md](../overviews/completion-roadmap.md) — cosa resta per chiudere Fixcity.
