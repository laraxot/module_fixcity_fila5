---
title: Front office test baseline repair
status: review
date: 2026-10-27
owner: Fixcity
related:
  - ../../../Blog/docs/stories/front-office-demo-content-population.story.md
  - ../../../../Themes/Sixteen/docs/stories/header-footer-design-comuni-markup.story.md
---

# Front office test baseline repair

## Goal

Make the failing tests in Blog, Sixteen and Fixcity tell the truth: fix the code where the page is wrong, fix the test where the test is out of date, and leave a decision to the owner where neither is clear.

## Root cause shared by many failures

`laravel/tests/TestCase.php` ran `migrate` inside `setUp()`, after `DatabaseTransactions` had opened the transactions. In MySQL a DDL statement commits implicitly, so the rows written by the first test of each process stayed in the scratch DB. Later tests counted 8 or 14 extra articles.

Fix: the migration runs in `beginDatabaseTransaction()`, before the transaction opens (trait alias `openDatabaseTransactions`). The method has no native return type, because subclasses that use `DatabaseTransactions` again redeclare the untyped trait method.

## Code fixed

- Public ticket list: card links and the pager were absolute (`http://host/it/...`), unlike the rest of the front office. `ListPublicTicketsAction` and the `ticket-list/design-comuni` block now build paths (`route(..., false)`, `FrontofficeRoutes::ticketsIndex(false)`).
- Demo seeder contract (Blog): see the Blog story.

## Tests fixed (the page was right)

- `<title>` assertions: the SEO layout adds ` | <site name>` on purpose (DCP-288, `ResolvePageHeadAction`). Assertions now check `<title>Page | `.
- `FrontofficeTicketLinksContractTest`: links go through `FrontofficeRoutes` and `BuildTicketDetailUrlAction` / `BuildTicketTrackUrlAction`, which wrap `container0.*`. The test checks those builders.
- `ContentLocaleParityTest`: "Demo", the header region placeholder kept by decision D35, is a proper noun equal in every language.

## Results (scratch DB runner)

| Suite | Before | After |
|---|---|---|
| Blog | 13 failed, 29 passed | 42 passed |
| Sixteen | 48 failed, 985 passed | 23 failed, 1118 passed |
| Fixcity | 31 failed, 1221 passed | 1 failed (`LegacyTicketPagesRetiredTest`, see below), 1531 passed |

The config checksum (`laravel/config`) is identical before and after each suite.

## Open, for the owner

- Sixteen: the 23 failures are all `ComunePagesTest`, which tests `comune.*` routes that no longer exist. Rewrite or delete.
- Fixcity: `LegacyTicketPagesRetiredTest` wants `[id].blade.php.old` and `[slug].blade.php.old*`, but `.gitignore` excludes `*.old`, so the files exist on no fresh clone.
- Gdpr: the register-page tests check copy from another project ("Create Your FREE Account", "pizza tips"). They do not describe FixCity.

## Review of the pull of 2026-10-27 afternoon (802 files, commits 7430d1efe6..18f775ca88)

Mostly screenshots and docs. What needed action:

| Finding | Fix |
|---|---|
| `Modules/User/tests/Feature/LoginMarcoTest.php` held the owner's email and a password, and posted to `/it/auth/login`, a route that does not exist (login is Folio + Livewire) | file removed; the same password removed from `Modules/User/docs/login-fix.md` |
| `config/image.php` and the `config/media-library.php` default moved to `imagick`; `php -m` shows no Imagick here, so every image read would fail. `Modules/Media/docs/mobile-camera-upload-fix.md` claimed Imagick installed and PHP limits at 20M: both false (`upload_max_filesize=2M`) | defaults back to `gd` (`IMAGE_DRIVER=imagick` still possible where installed); correction appended to the doc and its two copies |
| `CameraPhotoUpload` used 1920 px, `TicketForm` overrode it with 2048, the public wizard (`camera-image.js`) uses 2048 | one value: `CameraPhotoUpload::MAX_SIDE = 2048`, override removed, test reads the constant |
| `Modules/Xot/tests/.../MorphManyRelationSpyTest.php`: `namespace` after `use`, a fatal error that stopped the whole Xot suite; same tests as `MorphManyActionTest.php` | duplicate removed |
| `Modules/UI/tests/Feature/TestWorkerTypeTest.php`: older copy of `GroupColumnTest.php`, redeclared its helper function | duplicate removed |
| Conflict marker in `Themes/Sixteen/docs/bmad/filament-blueprint.md` | both sides kept |
| `storage/` and `bootstrap/cache` owned by `www-data`, 755: views could not compile, 15 upload/privacy tests red | `marco:www-data`, group writable |

Checked and fine: `Modules/Fixcity/config` is the module's mirror of the tenant config, registered as `fixcity.*` (it does not override `ente`, `media-library` or the CMS content), with no secrets; the new `.env.latest` Google lines are empty; the Vite manifest has no missing file; `/it`, `/it/auth/login`, `/it/tests/servizi-categoria-14` answer 200.

Proof: upload, wizard and privacy tests 33 passed; Xot `Model/Update`, docs and validation tests 109 passed; UI suite 17 failed, 209 passed (same 17 as the baseline); `CameraPhotoUploadTest` 2 passed; PHPStan 0 on the changed PHP files.

Still tracked and pre-existing (not from this pull): `laravel/.env.fixcity` and `.env.testing` hold the local DB password and an `APP_KEY` equal to the one in the real `.env`.
