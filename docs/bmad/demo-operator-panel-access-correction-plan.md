---
title: "Demo PA operator — least-privilege panel access correction plan"
type: bmad-implementation-plan
status: implemented
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, demo, operator, panel-access, security]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - demo-operator-panel-access-expected-2026-09-27.md
  - demo-operator-panel-access-gap-2026-09-27.md
  - stories/STORY-526-demo-operator-panel-access.md
---

# Implementation plan and completion

1. Leave the locked `DemoUsersSeeder` untouched. Add a separate Fixcity access
   seeder after demo users in `FixcityDatabaseSeeder`.
2. Guard it to `local`, `testing` and `demo`; resolve the configured user model
   through `XotData` and select only `operatore@fixcity.demo`.
3. Ensure `operator` and `fixcity::admin` roles exist, then assign both
   idempotently. Grant no global admin role and make no ticket changes.
4. Verify citizen denial and rerun behavior with the isolated SQLite Feature
   harness; smoke the local panel and read back roles from the local user DB.

## Evidence

- Targeted Feature tests: 2 passed, 16 assertions under SQLite.
- Full Modules PHPStan: zero errors.
- Authenticated Playwright smoke: login succeeded; `/fixcity/admin/tickets`
  loaded with no browser page errors.
- Local DB readback: operator has exactly `operator` and `fixcity::admin`, and
  `canAccessPanel` is true. Citizen has no roles and panel access is false.
- The live demo seed changed only those two operator role assignments; no ticket
  records were written by the browser smoke.
- Repository wiki quality gate and touched PHP syntax checks passed.

The demo identity is intentionally created in the verified local environment.
The seeder is environment guarded and adds no account or ticket mutations.
