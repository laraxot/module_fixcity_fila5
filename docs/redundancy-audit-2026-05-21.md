---
title: "Fixcity redundancy audit 2026-05-21"
type: audit
module: Fixcity
tags: [redundancy, duplicate-code, docs]
created: 2026-05-21
related:
  - https://github.com/laraxot/base_fixcity_fila5/issues/89
---

# Fixcity redundancy audit 2026-05-21

Static metrics: 1045 files scanned, 4 case-only groups, 16 duplicate hash groups, 1 duplicate FQCN.

Findings:
- Duplicate FQCN `Modules\Fixcity\Actions\GenerateTicketsJsonAction` exists in both `Actions/GenerateTicketsJsonAction.php` and `app/Actions/GenerateTicketsJsonAction.php`.
- `docs/llm-wiki/concepts/*` duplicates `docs/wiki/concepts/*` for wizard parity rules and audits.
- Case-only `.github` files duplicate `CONTRIBUTING`, `FUNDING`, and `SECURITY`.
- `resources/css/app.css` and `resources/assets/js/app.js` are byte-identical empty boilerplate.

Risk:
- Duplicate FQCN is high-risk for Composer autoload and fatal redeclare.
- `docs/llm-wiki` vs `docs/wiki` splits second-brain source of truth.

Suggested cleanup order:
1. Keep runtime action under module PSR-4 canonical `app/Actions` unless Composer config proves otherwise.
2. Consolidate `docs/llm-wiki` into `docs/wiki` with redirects or removal.
3. Normalize `.github` casing separately.

Evidence commands:
- Per-owner static scan for case-only paths, byte-identical files, and duplicate FQCN.
- GitHub tracker: issue #89.
