---
title: "BMAD 06 — Quality assurance FixCity"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, qa, phpstan, pest, fixcity]
module: Fixcity
qmd: "bmad qa phpstan pest quality gate fixcity"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - 05-implementation.md
  - 09-release.md
  - ../gap-analysis.md
---

# Quality assurance

**Perché:** senza prove classificate (app vs ambiente) non si può dichiarare
chiusa la vertical slice.

## Passi

1. `php -l` sui file toccati.
2. PHPStan sul modulo, poi su `Modules` se il cambio è trasversale — **mai**
   modificare `phpstan.neon`.
3. Pest del comportamento (creazione, isolation, assign, transition, rating).
4. PHPMD/Insights se disponibili nel modulo.
5. Marker conflitto, JSON, frontmatter wiki, lock residui.
6. Classificare blocker: applicativo / DB test / concorrenza / UI ambiente.

## Evidenza recente

- Suite Fixcity SQLite: `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity` — **350 test / 1.440 asserzioni** verdi (2026-09-27), incluse email transazionali per status/assegnazione.
- PHPStan completo `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress`: **zero errori**.
- Browser guest: home, auth, elenco, redirect da route protette e tracking terminano su pagine HTTP 200 senza errori JS. `/it/segnalazioni`: viewport 320/768/1.440 px, zero richieste fallite o overflow; click reale Mappa/Elenco verificato. Report: `../ui-ux-runtime-audit-2026-09-26.md`.
- Build Sixteen, `composer validate --no-check-publish`, Pint mirato, `view:cache`, `git diff --check` e `verify-llm-wiki.sh`: PASS.
- MySQL test diretto resta bloccato da credenziali/grant; non aggirare cambiando credenziali locali. Wizard, tracking con capability code, rating in browser e console PA/staging restano da collaudare con account.
- Per ogni modifica PHP: rilanciare PHPStan `analyse Modules`, Pint sui file toccati e gate wiki richiesto dal repository.

## Gate

Nessun errore non classificato; exit 1 incompleto ≠ verde.

## Output

Report con comandi, exit code, artefact path, blocker riproducibili.

## Baseline più recente — 2026-09-27

Comando: `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests --compact`.
Esito: **392 passati, 4 falliti, 1.711 asserzioni**. I quattro casi in
`Modules/Fixcity/tests/Feature/Pages/TicketPagesTest.php` richiedono HTTP 200 da
`/it/segnalazioni`; la rotta canonica `/it/tickets` è una decisione di prodotto
già verificata e il percorso storico risponde 301. È un test obsoleto, non un
fallimento del percorso canonico. Il file test è sotto un lock preesistente e
non è stato modificato in questa sessione; la suite resta non verde finché quelle
assertion non vengono aggiornate.

PHPStan completo (`php -d memory_limit=2048M vendor/bin/phpstan analyse Modules
--no-progress`): **zero errori**. Il passaggio completo Playwright è registrato
nei relativi report owner; questo rerun della suite Pest non sostituisce gli smoke
autenticati, PA, screen reader o staging.

## Evidenza privacy — 2026-09-27

Evidenza aggiornata 2026-09-27: PHPStan `analyse Modules` zero errori; Pint mirato, `git diff --check` e `verify-llm-wiki.sh` passano. La suite FixCity ha 391 passati / 4 falliti / 1.705 asserzioni: i quattro failure sono richieste legacy a `/it/segnalazioni` che ricevono il redirect 301 al canonico `/it/tickets`; il test file è lockato e non è stato modificato. La CTA sul percorso canonico passa 2 test / 9 asserzioni.

Playwright/Chromium sono stati avviati con dipendenze estratte sotto `/tmp` senza modificare il sistema. Una matrice guest ha controllato home, lista tickets, servizi, dettaglio servizio, privacy e form tracking in IT/EN/DE/ES a 320 e 1440 px: 48 casi HTTP 200, zero overflow, chiavi di traduzione grezze o errori JavaScript. La screenshot IT mobile è `/tmp/fixcity-home-current-it-390.png`. Non è una prova dei flussi autenticati, del back office PA, delle tecnologie assistive o dello staging. La policy demo tenant resta priva di approvazione legale.

La geometria del form di tracking è stata verificata separatamente con la spec
`Themes/Sixteen/tests/browser/tracking-form-responsive.spec.mjs`: 24 combinazioni
(4 lingue × 6 breakpoint 320/390/575/576/768/1440), un test Playwright passato,
campo e pulsante impilati sotto 576 px e inline da 576 px, focus tastiera, required,
label/help associati, nessun overflow o errore JS. Screenshot prima/dopo in `/tmp`.

## Rerun completo dopo STORY-526 — 2026-09-27

Comando: `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest
Modules/Fixcity/tests --compact`.
Esito: **394 passati, 4 falliti, 1.727 asserzioni** in 116,92 s. I quattro
failure restano le assertion HTTP 200 del test lockato
`Feature/Pages/TicketPagesTest.php` per `/it/segnalazioni`; il 301 verso
`/it/tickets` è il contratto canonico. I due nuovi test di STORY-526 sono verdi
(accesso panel least-privilege e idempotenza).

PHPStan completo `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules
--no-progress`: zero errori. Playwright Chromium, usando le librerie già
estratte in `/tmp`, ha completato login operatore e apertura
`/fixcity/admin/tickets`: **1 test passato**, nessun errore pagina. Il DB demo
locale assegna solo `operator` e `fixcity::admin` all'operatore nominato; il
cittadino resta escluso. `verify-llm-wiki.sh`, Pint mirato, `git diff --check`
e PHP syntax passano. La suite resta non verde finché il lock del test legacy
non viene rilasciato e le quattro assertion vengono riallineate.
