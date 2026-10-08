---
title: STORY-024 — BasePolicy Architecture (Fixcity Policies Layer)
status: done
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/24
discussion: https://github.com/laraxot/fixcity_fila5/discussions/24
type: story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: 024 basepolicy layer.story FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Requirement
- `TicketPolicy` → extends `Modules\Fixcity\Policies\BasePolicy`
- `BasePolicy` → extends `Modules\User\Models\Policies\UserBasePolicy`
- Architecture: `XotBasePolicy → UserBasePolicy → BasePolicy → Concrete`

## Implementation
- Created `app/Models/Policies/BasePolicy.php` (extends UserBasePolicy)
- Updated `TicketPolicy` to extend BasePolicy (not UserBasePolicy directly)
- BasePolicy provides `canPerformAction()` method for shared authorization logic
- Follows Second Brain pattern: learn from error, document, propagate

## Related BMAD
- Legacy merge resolution: `020-merge-conflict-resolution.story.md`
- Legacy PHPStan proposal: `021-phpstan-errors-fixcity.story.md`
- Legacy AuditCoverage record: `022-auditcoverage-gitignore.story.md`
- STORY-023 — Wizard UI/UX responsive parity
