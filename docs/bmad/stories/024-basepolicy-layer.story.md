---
title: STORY-024 — BasePolicy Architecture (Fixcity Policies Layer)
status: done
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/24
discussion: https://github.com/laraxot/fixcity_fila5/discussions/24
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
- STORY-020 — Merge conflict resolution
- STORY-021 — PHPStan errors
- STORY-022 — AuditCoverage cleanup
- STORY-023 — Wizard UI/UX responsive parity
