---
title: "tests placement policy"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "tests placement policy"
issues: []
discussions: []
status: draft
---

# Test Placement Policy — Fixcity Module

## Rule: Playwright Tests Live Inside Modules/Themes

### Permanent Constraint

```
OBBLIGATORIO: Playwright tests go inside their owning module/theme:
  Modules/{Name}/tests/Playwright/
  Themes/{Name}/tests/Playwright/

VIETATO: Top-level tests/Playwright/ directory
VIETATO: Shared test fixtures that cross module boundaries
```

### Why Tests Belong to the Module/Theme

| Reason | Explanation |
|--------|-------------|
| **Context ownership** | Tests document behavior of their owning module; co-located with source |
| **Maintainability** | Moving a module keeps its tests; no orphaned external test dirs |
| **Module-level verification** | `npm run build` + Playwright inside the module validates the module in isolation |
| **Token efficiency** | Context-mode indexes module docs/tests together; cross-dir searches cost more |
| **CLAUDE.md scope** | Each module/theme CLAUDE.md references its own tests; top-level CLAUDE.md stays lean |

### Correct Structure

```
laravel/Modules/Fixcity/
  ├── tests/
  │   ├── Feature/         ← PHPUnit feature tests
  │   ├── Unit/            ← PHPUnit unit tests
  │   └── Playwright/     ← Browser tests (ticket-list.spec.js, etc.)
  ├── docs/
  │   ├── wiki/            ← LLM wiki
  │   └── tests-placement-policy.md  ← this file
  └── ...

laravel/Themes/Sixteen/
  ├── tests/
  │   └── Playwright/     ← Theme-specific browser tests
  ├── docs/
  │   └── tests-placement-policy.md
  └── ...
```

### Migration Example

```
❌ WRONG:
/var/www/_bases/base_fixcity_fila5/tests/Playwright/ticket-list.spec.js

✅ CORRECT:
laravel/Modules/Fixcity/tests/Playwright/ticket-list.spec.js
```

### References

- Story 8-74: Agent directory audit and reduction
- Modules/Geo/docs/agents-consolidation.md — agent cleanup rationale
- bashscripts/ai/.claude/rules/second-brain-always-first.md — context ownership

## ticket business logic phpstan (2026-10)

`TicketBusinessLogicTest` è una classe PHPUnit su `Modules\Fixcity\Tests\TestCase` con `Assert` e factory `createOne()` per tipizzazione statica.

Verifica suite test (2026-10-20):

```bash
cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity/tests --no-progress
```

Esito atteso: **0 errori**. Regole operative: allineare i test al contratto reale del dominio ticket; non introdurre case enum inventati; preferire `Assert::assert*` o `expect()` coerente, mai mix che confonde Larastan/Pest.

Ordine Pest + Larastan: tutti gli `use` (incluso `Modules\Fixcity\Tests\TestCase`), poi `uses(TestCase::class);`. Nei file con `namespace Modules\Fixcity\Tests\…` importare sempre il TestCase del modulo, non un `TestCase` relativo al sotto-namespace. PHPDoc `@var TestCase $this` richiede lo stesso import.

