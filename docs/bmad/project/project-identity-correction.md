---
title: "Project identity correction — FixCity, not Quaeris"
type: bmad-correction
status: active
updated: 2026-10-07
tags: [bmad, correction, fixcity, documentation]
qmd: "FixCity project identity correction stale Quaeris documentation"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Correction

This repository is **FixCity** (`base_fixcity_fila5`), a civic-reporting platform.
It is not Quaeris. Several historical documents under `docs/` and especially the
Barthelemy shared-components archive contain copied references to Quaeris, old paths,
old databases and old modules. Those references are historical contamination, not
current architecture or product requirements.

## Current source of truth

- Product purpose: `PRODUCT.md` and `bmad-output/product-brief.md`.
- Runtime stack: `laravel/composer.json` (PHP 8.4/Laravel 13).
- Components: `bmad-output/component-catalog.md` and module/theme directories.
- Domain: `laravel/Modules/Fixcity/docs/bmad/`.
- Active public theme: Sixteen, pending explicit classification of alternatives.

## Rule for future work

Do not copy a Quaeris document into FixCity. When an old document is useful, extract
the transferable technical fact, verify it against current code, and rewrite it under
the owning module with FixCity paths and evidence. Never treat a stale database name,
module name or route as a current requirement.
