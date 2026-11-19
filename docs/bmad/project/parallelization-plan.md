---
title: "FixCity parallel implementation waves"
type: parallelization-plan
status: planned
updated: 2026-10-07
tags: [bmad, parallel, swarm, dependencies]
qmd: "FixCity parallel waves module owned scope dependency graph"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Parallel waves

Parallelism is bounded by module ownership and shared boundaries; “random order” is
not safe when dependencies or files overlap.

| Wave | Stories | Rule |
|---|---|---|
| 1 | 1.1, 2.3 | catalog is independent; privacy decision is owner-blocked |
| 2 | 1.2, 1.3, 6.2 | disjoint docs/tracker/theme classification |
| 3 | 2.1, 2.2, 3.1, 3.2, 4.1, 5.2 | only after scopes are locked; serialize shared User/Fixcity/Job files |
| 4 | 3.3, 4.2, 5.1, 6.1 | dependent audit/ops/privacy/UX work |
| 5 | 7.1 | readiness evidence aggregation |
| 6 | 7.2 | named sign-off only |

## Dispatch rules

Each implementation agent receives one story, an explicit owned scope, current
architecture/UX/security docs and the relevant module lock. Agents never touch another
story's scope, never rewrite history, and report evidence plus learnings back to the
decision log. Shared modules (Xot/User/Tenant/Notify/Fixcity/Sixteen) are serialized.
