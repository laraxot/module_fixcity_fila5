---
title: "STORY-526 — Give the demo PA operator least-privilege panel access"
type: story
status: completed
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
priority: Must
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../demo-operator-panel-access-expected-2026-09-27.md
  - ../demo-operator-panel-access-gap-2026-09-27.md
  - ../demo-operator-panel-access-correction-plan-2026-09-27.md
  - ../../../database/seeders/DemoUsersSeeder.php
  - ../../../database/seeders/FixcityDatabaseSeeder.php
---

# User story

As a municipality operator evaluating FixCity, I want the demo operator account
to enter the PA panel and process tickets with its normal operator capabilities,
while the citizen demo stays outside the panel.

## Acceptance criteria

- [x] The Fixcity demo seed flow assigns `operator` and `fixcity::admin` only to
      `operatore@fixcity.demo` after the account exists.
- [x] Seeding is idempotent and runs only in local, testing or demo environments.
- [x] The operator has no `admin` or `super-admin` role from this access path.
- [x] The citizen demo receives neither panel nor operator role and
      `canAccessPanel(fixcity::admin)` remains false.
- [x] An isolated SQLite Feature test verifies role assignment, panel access,
      citizen denial and repeat-seed behavior.
- [x] An authenticated read-only panel smoke verifies operator ticket-list
      access; the only live demo DB change was the intended role assignment.
- [x] PHPStan for Modules, targeted Pest and `verify-llm-wiki.sh` pass.

## Implementation constraints

Keep the locked `DemoUsersSeeder.php` untouched. Add a dedicated Fixcity seeder
and invoke it from the unlocked Fixcity demo orchestrator after the demo-user
seeder. Do not create controllers/services, bypass policy checks, or grant a
global administrator role. Panel access role and domain capability role are
separate responsibilities.

## Verification record — 2026-09-27

Targeted SQLite Pest: 2 passed / 16 assertions. Full Modules PHPStan: zero
errors. Playwright with the existing Chromium runtime libraries loaded from
`/tmp`: 1 passed; operator login and `/fixcity/admin/tickets` succeeded with no
page errors. The demo seeder was applied only in the verified local environment.
Read-only DB inspection confirms exactly the two intended operator roles and
denial for the citizen. No ticket records were changed. Wiki quality gate and
PHP syntax checks passed. The shared Playwright credentials helper was changed
to valid JavaScript after its TypeScript annotation blocked test loading.
