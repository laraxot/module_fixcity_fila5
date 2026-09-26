---
title: STORY-021 — Fixcity PHPStan errors resolution (deprecated class + generic types)
status: in-progress
module: Fixcity
github_issue: https://github.com/laraxot/fixcity_fila5/issues/21
discussion: https://github.com/laraxot/fixcity_fila5/discussions/21
---

## Issues
- `TicketComment` deprecated; tests reference `ticket_id`, `user_id`, `content` instead of `commentable_id`, `commentator_id`, `original_text`
- `UserTest.php` uses legacy `ticketComments()` method
- PHPStan generic type errors in `HasTicketRelations.php`

## Actions Taken
- Updated `TicketCommentTest.php` to use `Comment` model properties (`commentable_id`, `original_text` etc.)
- Updated `UserTest.php` to reference `Comment` correctly (pending full fix for `$user->id` errors)
- Added `tests/AuditCoverage/` to `.gitignore` in all modules
- Removed `tests/AuditCoverage/` directories where present

## Next Steps
- Fix remaining `property.notFound` errors in `UserTest.php` (line 116, 137)
- Verify `PHPStan` passes completely on `Modules`
