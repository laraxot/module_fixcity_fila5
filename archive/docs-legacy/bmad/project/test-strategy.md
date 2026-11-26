---
title: "FixCity verification strategy"
type: test-strategy
status: active
updated: 2026-10-07
tags: [bmad, testing, phpstan, pest, browser, accessibility]
qmd: "FixCity test strategy PHPStan Pest browser accessibility candidate release"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Verification strategy

## Gates

1. Parse/lint and PHPStan on all `laravel/Modules` at max.
2. Unit/feature tests for Actions, policies, transitions, privacy serializers and
   notification retry/idempotency.
3. Integration tests with isolated MySQL candidate plus SQLite fast feedback.
4. Browser matrix for guest, citizen A/B, operator, supervisor and admin at mobile and
   desktop sizes; include denial, upload, rate-limit and error paths.
5. Keyboard/screen-reader/contrast/reduced-motion checks on critical Sixteen flows.
6. Queue/SMTP/failed-job, backup/restore and monitoring drills.

## Evidence rule

Each result records command, commit/candidate identifier, environment, timestamp,
scope, pass/fail and known limitations. Local green is not staging green. A failed
environment prerequisite is documented, never bypassed with fake data or credentials.
