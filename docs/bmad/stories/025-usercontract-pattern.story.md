---
title: STORY-025 — UserContract Pattern (No direct User model usage)
status: done
module: Fixcity
owner: developer-agent
github_issue: https://github.com/laraxot/fixcity_fila5/issues/25
discussion: https://github.com/laraxot/fixcity_fila5/discussions/25
type: story
created: legacy
updated: '2026-09-26'
tags:
- bmad
- fixcity
qmd: 025 usercontract pattern.story FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Lesson
Always use `UserContract` for authorization checks, never `Modules\User\Models\User` directly.

## Rationale (Second Brain)
- UserContract provides abstraction over User model (allows different implementations)
- BasePolicy uses `hasRole()` from UserContract interface
- Direct model dependency breaks modular architecture

## Applied
- TicketPolicy: uses UserContract in method signatures
- BasePolicy: uses UserContract, extends UserBasePolicy, defines PA_ROLES
- All policies: no direct `use Modules\User\Models\User;` import

## Related Rules
- `docs/wiki/second-brain/module-contracts-naming-placement.md`
- `docs/wiki/rules/module-contracts-naming-placement.md`
