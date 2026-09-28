---
title: "FixCity demo operator panel access"
type: concept
tags: [second-brain, demo, panel, operator, least-privilege]
created: 2026-09-27
updated: 2026-09-27
qmd: "FixCity demo operator cannot access fixcity::admin panel role operator PanelRoleSeeder BaseUser canAccessPanel"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../../bmad/demo-operator-panel-access-expected-2026-09-27.md
  - ../../bmad/demo-operator-panel-access-gap-2026-09-27.md
  - ../../bmad/demo-operator-panel-access-correction-plan-2026-09-27.md
  - ../../bmad/stories/STORY-526-demo-operator-panel-access.md
---

# Reusable authorization decision

Fixcity demo operator access needs two distinct role assignments. The Filament
gate `BaseUser::canAccessPanel()` requires the panel-ID role `fixcity::admin`.
Fixcity ticket policies use the domain role `operator` for queue, assignment
and status capabilities. The panel role by itself must not be treated as a
ticket permission.

Assign both only to the known PA demo operator after seeding the user. Leave the
citizen without either role. Never grant `admin` or `super-admin` as a shortcut;
preserve policy-based restrictions such as ticket deletion. Seed only in local,
testing and demo environments, and verify repeat runs against isolated SQLite.

## Related knowledge

- [[../../bmad/demo-operator-panel-access-expected-2026-09-27]]
- [[../../bmad/demo-operator-panel-access-gap-2026-09-27]]
- [[../../bmad/demo-operator-panel-access-correction-plan-2026-09-27]]
- [[../../bmad/stories/STORY-526-demo-operator-panel-access]]
- [[../../../../docs/wiki/concepts/laravel-permission]]
