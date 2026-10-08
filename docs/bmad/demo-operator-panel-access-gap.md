---
title: "Demo PA operator — panel access gap analysis"
type: bmad-gap-analysis
status: resolved
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, demo, operator, authorization, panel]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - demo-operator-panel-access-expected-2026-09-27.md
  - ../wiki/concepts/demo-operator-panel-access.md
---

# Observed behavior

The demo user seeder creates a citizen and an operator account, both as
`customer_user`. `BaseUser::canAccessPanel()` checks the role matching the
Filament panel ID unless the ID is the generic `admin`. The active Fixcity panel
ID is `fixcity::admin`; `PanelRoleSeeder` creates that role but does not assign
it to demo users. The Fixcity policies separately grant work-queue capabilities
to `operator`, `supervisor` or `admin`.

The runtime audit found that the demo operator and citizen both failed
`canAccessPanel()` for `fixcity::admin`. The intended operator identity also
lacked the policy role assignment in the Fixcity demo seed path. A single
`operator` role cannot satisfy the panel gate; assigning only the panel role
would enter the panel without granting the intended ticket operations.

# Risk and scope

The failure prevents a real PA demo and can be mistaken for a broken login.
Granting `admin` or `super-admin` to make the demo work would be overbroad and
could expose delete or global privileges. Keep panel membership
(`fixcity::admin`) separate from domain role (`operator`), assign them only to
the known operator demo account, and leave the citizen unchanged.

# Owner and correction

`database/seeders/DemoUsersSeeder.php` had an active file lock and remains
untouched. `DemoOperatorPanelAccessSeeder` now runs after demo users in the
Fixcity orchestrator, creates and assigns the distinct panel and operator roles,
and is environment guarded. SQLite Feature tests and an authenticated local
panel smoke pass. The live local DB confirms operator access and citizen denial.
See STORY-526 and the Fixcity correction plan.
