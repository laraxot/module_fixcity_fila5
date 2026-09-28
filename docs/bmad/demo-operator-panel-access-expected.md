---
title: "Demo PA operator — expected panel access"
type: bmad-ux-contract
status: baseline
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, demo, operator, panel-access, rbac]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - demo-operator-panel-access-gap-2026-09-27.md
  - stories/STORY-526-demo-operator-panel-access.md
---

# Expected access contract

## PA operator demo account

- The seeded operator can sign into the Fixcity Filament panel whose runtime
  ID is `fixcity::admin`.
- The account has the domain role `operator`, which grants only the existing
  Fixcity operator capabilities enforced by owner-module policies.
- The account has the panel role `fixcity::admin`, required by
  `BaseUser::canAccessPanel()`; this role is created without domain permissions.
- Seeding is idempotent and limited to local, testing and demo environments.
- The operator receives neither `admin` nor `super-admin` from this demo-access
  path; deletion remains governed by the explicit admin capability.

## Citizen demo account

- A citizen can use the public front office and access only their own private
  practices according to policy.
- A citizen does not receive the `operator` or `fixcity::admin` role and cannot
  enter the Fixcity panel.

## Evidence required

Verify both roles and `canAccessPanel()` in an isolated SQLite Feature test,
including repeat seeding. Then run the panel login path in a dedicated demo/test
runtime and confirm the operator can view the ticket queue while the citizen is
denied. A panel role alone is navigation access, not proof of each resource
capability; policies remain the authorization source of truth.
