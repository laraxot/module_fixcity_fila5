# Global Git Conflict Report

Generated on: Wed Apr 22 14:04:36 CEST 2026

## Run 2026-04-28 19:41:39 UTC

### Snapshot conflitti Git reali (pre-fix)

Rilevati con:

`git diff --name-only --diff-filter=U`

- `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `laravel/Modules/Fixcity/app/Models/Ticket.php`

### File selezionato casualmente e risolto

- file: `laravel/Modules/Fixcity/app/Models/Ticket.php`
- criterio random: scelta casuale tra i file `U` della snapshot

### Errore osservato

- durante il merge erano presenti due versioni sovrapposte della stessa logica `location()` e dei metodi helper (`normalizeCoordinateString`, `normalizeText`, `normalizeNullableText`);
- il file conteneva quindi metodi duplicati nella stessa classe, con rischio di errore fatale per ridefinizione metodi.

### Risoluzione applicata

- mantenuta la versione canonica gia presente nella parte alta della classe (gestione JSON location con normalizzazione e filtro coerente);
- rimosso il blocco duplicato nella parte finale del file;
- marcato il file come risolto (`git add`).

### Stato post-fix

Comando di verifica:

`git diff --name-only --diff-filter=U`

Output: nessun conflitto `U` residuo.

### Quality gate PHPStan (run richiesto)

Comando eseguito da `laravel/`:

`./vendor/bin/phpstan analyse Modules`

Esito:

- risolto blocco fatale nel perimetro Fixcity:
  - duplicate method `saveDraft()` in `CreateTicketWizardWidget`;
  - incompatibilita' type-hints su proprieta' ereditate nei model base Fixcity (`BaseModel`, `BasePivot`, `Category`).
- run ora arriva al termine scansione, ma resta **non verde** per errori parse pre-esistenti in altri moduli (Cms, Lang, Tenant, User, Xot) e internal error su test Geo.

## Affected Files

* ./Modules/Geo/docs/wiki/README.md
* ./Modules/Geo/docs/wiki/AGENTS.md
* ./Modules/Geo/docs/merge-conflicts-list.md
* ./Modules/docs/docs/wiki/log.md
* ./Modules/docs/docs/wiki/README.md
* ./Modules/docs/docs/wiki/index.md
* ./Modules/docs/docs/merge-conflicts-list.md
* ./Modules/docs/merge-conflict-task-list.md
* ./Modules/docs/merge-conflict-marker-task-list.md
* ./Modules/Seo/docs/wiki/README.md
* ./Modules/Seo/docs/merge-conflicts-list.md
* ./Modules/Cms/docs/wiki/_archive/filament-4x-compatibility.md
* ./Modules/Cms/docs/wiki/README.md
* ./Modules/Cms/docs/phpstan-scripts-roadmap.md
* ./Modules/Cms/docs/roadmap.md
* ./Modules/Cms/docs/roadmap/legacy/legacy-roadmap-x.md
* ./Modules/Cms/docs/roadmap/legacy/legacy-roadmap-3.md
* ./Modules/Cms/docs/quality-status-11.md
* ./Modules/Cms/docs/errors/git-conflicts-themes-resolution.md
* ./Modules/Cms/docs/quality-status-2025-11.md
* ./Modules/Cms/docs/merge-conflicts-list.md
* ./Modules/Cms/app/Models/Attachment.php
* ./Modules/Cms/database/seeders/CmsMassSeeder.php
* ./Modules/Lang/docs/wiki/README.md
* ./Modules/Lang/docs/merge-conflicts-list.md
* ./Modules/User/docs/phpstan-syntax-blockers.md
* ./Modules/User/docs/wiki/README.md
* ./Modules/User/docs/tasks/fix-doc-merge-markers.md
* ./Modules/User/docs/tasks/fixoc-merge-kers.md
* ./Modules/User/docs/merge-conflicts-list.md
* ./Modules/User/docs/README.md
* ./Modules/Notify/docs/ai-agents/git-error-path-does-not-have-our-version.md
* ./Modules/Notify/docs/wiki/index.md
* ./Modules/Notify/docs/merge-conflicts-list.md
* ./Modules/Notify/docs/correzioni-phpstan-completate_3.md
* ./Modules/Notify/docs/project_docs/riepilogo-risoluzione-conflitti-2025-09-30.md
* ./Modules/Notify/.planning/debug/knowledge-base.md
* ./Modules/Notify/.planning/debug/resolved/sqlite-model-contract-fix.md
* ./Modules/Blog/docs/wiki/README.md
* ./Modules/Blog/docs/merge-conflicts-list.md
* ./Modules/Xot/docs/phpstan-audit.md
* ./Modules/Xot/docs/phpstan-fixes.md
* ./Modules/Xot/docs/merge-conflicts-list.md
* ./Modules/Media/docs/wiki/README.md
* ./Modules/Media/docs/merge-conflicts-list.md
* ./Modules/Media/docs/phpstan_level10_fixes.md
* ./Modules/Gdpr/docs/wiki/README.md
* ./Modules/Gdpr/docs/merge-conflicts-list.md
* ./Modules/Comment/docs/wiki/README.md
* ./Modules/Comment/docs/merge-conflicts-list.md
* ./Modules/Tenant/docs/wiki/README.md
* ./Modules/Tenant/docs/merge-conflicts-list.md
* ./Modules/Job/docs/wiki/README.md
* ./Modules/Job/docs/merge-conflicts-list.md
* ./Modules/UI/docs/merge-conflicts-list.md
* ./Modules/Fixcity/docs/wiki/README.md
* ./Modules/Fixcity/docs/merge-conflicts-list.md
* ./Modules/AI/docs/merge-conflicts-list.md
* ./Modules/Rating/docs/merge-conflicts-list.md
* ./Modules/Activity/docs/merge-conflicts-list.md
* ./composer.phar
* ./Themes/docs/docs/wiki/log.md
* ./Themes/docs/docs/wiki/README.md
* ./Themes/docs/docs/wiki/index.md
* ./Themes/docs/docs/merge-conflicts-list.md
* ./Themes/docs/merge-conflict-task-list.md
* ./Themes/docs/merge-conflict-marker-task-list.md
* ./Themes/TwentyOne/docs/merge-conflicts-list.md
* ./Themes/Meetup/docs/merge-conflicts-list.md
* ./Themes/Sixteen/docs/css-js-parity.md
* ./Themes/Sixteen/docs/design-comuni/SEGNALAZIONE-FIX-SESSION-2026-04-07.md
* ./Themes/Sixteen/docs/design-comuni/HTML_PARITY_COMPLETE.md
* ./Themes/Sixteen/docs/design-comuni/screenshots/comparison/POST-FIX-VERIFICATION.md
* ./Themes/Sixteen/docs/design-comuni/screenshots/ref-homepage.png
* ./Themes/Sixteen/docs/design-comuni/screenshots/reference/homepage-desktop.png
* ./Themes/Sixteen/docs/design-comuni/screenshots/local-homepage-full.png
* ./Themes/Sixteen/docs/MERGE_CONFLICT_RESOLUTION_LOG.md
* ./Themes/Sixteen/docs/00-INDEX.md
* ./Themes/Sixteen/docs/merge-conflicts-list.md
* ./Themes/Sixteen/docs/screenshots/homepage/reference-full-2026-04-07.png
* ./Themes/Sixteen/docs/screenshots/homepage/reference-viewport-2026-04-07.png
