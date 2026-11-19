---
title: "Make the code keep the privacy notice promises: retention, photo metadata, account deletion"
type: story
tags: [fixcity, gdpr, privacy, retention, activitylog, exif, account-deletion]
created: 2026-10-26
updated: 2026-10-26
qmd: "privacy notice retention activitylog clean purge deleted accounts exif gps account deletion"
issues: []
discussions: []
status: review
related: [legal-documents-ente-interpolation.story.md]
---

# Make the code keep the privacy notice promises

- BMAD phase: Quick Flow (dev story), follow-ups 3, 4 and 6 of `legal-documents-ente-interpolation.story.md`.
- Owner: Modules/Fixcity (with small changes in Gdpr, User, Media and the Sixteen theme).

## Problem

`config/local/fixcity/lang/it/policy.md` promises things the code did not do:

1. "Registri degli accessi e delle operazioni: 12 mesi". Nothing was scheduled. The config used the v4 key `delete_records_older_than_days`, which spatie/laravel-activitylog v5 no longer reads (v5 reads `clean_after_days`).
2. A deleted account "è disattivato subito ... poi cancellati". The user could sign in again with the password, and nothing ever deleted the account.
3. EXIF and GPS data stayed in the photos attached to a report.
4. The deletion route `/{lang}/account/delete` existed, but no page linked to it, and its POST returned 500 (`consents.subject_type` does not exist, also after the dev DB migration).

## Decision

- Retention values are config in days: `activitylog.clean_after_days` = 365, `gdpr.retention.authentication_log_days` = 365, `gdpr.retention.deleted_account_days` = 30 (`config/local/fixcity/activitylog.php`, `config/local/fixcity/gdpr.php`).
- Laravel 13 scheduling in `routes/console.php` (the only schedule in the project), every task daily and `withoutOverlapping`: `activitylog:clean --force`, `model:prune --model=AuthenticationLog` (`MassPrunable` on `created_at`), and `purge-deleted-accounts`.
- `Fixcity\Actions\Privacy\PurgeDeletedAccountsAction` deletes an account when `deleted_at` is older than the retention and no report of the user is still open (any status but draft, resolved, closed). Profile, notifications and Google links go with it. Reports, comments, ratings and consents stay.
- The login widget signs in only accounts without `deleted_at` (`Auth::attemptWhen`).
- Photos: `Media\Actions\Image\StripImageMetadataAction` re-encodes JPEG/PNG originals with Intervention Image 4 (already installed, GD). It applies the EXIF orientation first. The Fixcity listener on `MediaHasBeenAddedEvent` runs it for Ticket media, so every upload path is covered (wizard, back office, API). PDFs are not touched. Nothing new was installed.
- Settings page (`personal-area-settings/settings.blade.php`): new section `#account-deletion` with an index link, an optional reason, a required checkbox (`confirm` = `cancella`, the value the Action validates) and a POST form. Texts are in `fixcity::personal_area_settings.settings.deletion.*` (it, en, de, es). `rights.how_today` no longer says that deletion is not possible here.
- `DeleteAccountAction` keys the deletion consent on `user_id` + `user_type` (as `RevokeGdprConsentsAction`) and flashes `gdpr::account.flash.account_deleted`. The theme layout showed no flash because its `<script>` was nested inside another `<script>`; that is fixed.

## Still not true in policy.md (text or decision needed)

- The account retention has no number of days in the notice. The code waits 30 days and for all open reports to close. Either add the 30 days to the text or change the config.
- "Le foto possono contenere metadati ... disattiva la posizione nella fotocamera": the warning is now out of date, since the metadata is removed on upload. The text can say so.
- "Nome, cognome, foto e preferenze di notifica si possono modificare nell'area personale": the settings page shows the account data read-only. Deleting the account is now possible from the personal area; the text can say so.
- "Prove dei consensi: per la durata dell'account e successivamente per il tempo necessario": no consent purge exists, and no period is set.
- Google sign-in (`Modules/User/app/Actions/Socialite/LoginUserAction.php`) does not check `deleted_at`: a deleted account can still sign in with Google until it is purged.
- `DeleteAccountAction` writes users and consents on two connections in one `user` transaction: the consent write is not atomic with the user update.

## Verification

- `PEST_OUT=... bash bashscripts/tools/pest-scratch-db.sh --test-directory=Modules/Fixcity/tests Modules/Fixcity/tests/Feature/Privacy Modules/Fixcity/tests/Feature/Pages/PersonalAreaSettingsPagesTest.php Modules/Fixcity/tests/Unit/PersonalAreaSettingsLangParityTest.php`: 51 passed.
- `php artisan schedule:list` shows the three daily tasks.
- PHPStan 0 errors on the touched PHP files; Pint passed.
