---
title: "Fixcity — roadmap completamento progetto"
type: overview
tags: [fixcity, roadmap, completion, phpstan, testing, segnalazioni]
created: 2026-06-13
updated: 2026-06-13
qmd: "Fixcity completare progetto roadmap segnalazioni mappe wizard seeders Actions"
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/52"
discussions:
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/53"
related:
  - ../concepts/phpstan-pest-testcase-helpers.md
  - ../concepts/fixcity-best-practices.md
  - ../../roadmap/README.md
  - ../../../../Themes/Sixteen/docs/wiki/overviews/completion-roadmap.md
---

# Fixcity — roadmap completamento progetto

## Stato qualità (2026-06-13)

| Gate | Stato | Nota |
|------|-------|------|
| PHPStan `Modules/Fixcity` | ✅ | 0 errori codice post-sessione helper Pest |
| PHPStan `Modules` globale | ✅ | **[OK] No errors** (6275 file, 2026-06-13 gate chef) |
| Pest Fixcity | 🔄 | Eseguire suite dopo ogni batch |
| Cartella `tests/` lowercase | 🔄 | Namespace `Modules\Fixcity\Tests` ancora PascalCase — [#370](https://github.com/laraxot/base_fixcity_fila5/issues/370) |

## Completato in questa sessione

- PHPStan globale `Modules` → 0 errori (sessione gate chef Cursor)
- 88 errori PHPStan su test Fixcity → 0 (helper TestCase, action locali, `mockService`) — sessione precedente
- `tests/helpers/PestHelper.php`: PHPDoc `class-string` allineato (helper opzionale, non ancora referenziato nei test)
- Rimosso `RefreshDatabase` dai test Fixcity toccati
- `GetTicketSlaMetricsActionTest`: eliminato `seedTicket()` fantasma → `TicketFactory`
- Documentazione: [phpstan-pest-testcase-helpers.md](../concepts/phpstan-pest-testcase-helpers.md)
- Hub piattaforma: [platform-completion-roadmap](../../../Xot/docs/wiki/overviews/platform-completion-roadmap.md)

## Priorità per chiudere Fixcity + piattaforma

### P0 — Gate ingresso (obbligatorio prima di feature)

1. `cd laravel && ./vendor/bin/phpstan analyse Modules` → zero errori codice
2. `./vendor/bin/pest` su moduli toccati
3. Issue + discussion su repo owner (pattern [#52](https://github.com/laraxot/module_fixcity_fila5/issues/52))

### P1 — Dominio segnalazioni (business)

| Area | Cosa fare | Perché |
|------|-----------|--------|
| Mappe FO | Chiudere debito GeoJSON/popup ([segnalazioni-elenco-map-architecture](../concepts/segnalazioni-elenco-map-architecture.md)) | UX cittadino + parity Design Comuni |
| Wizard crea ticket | Allineare theme Sixteen + widget Filament ([frontoffice-wizard-theme-runtime-boundary](../concepts/frontoffice-wizard-theme-runtime-boundary.md)) | Flusso core Fixcity |
| `profiles` | Rispettare contratto UUID owner Fixcity | Dati sacri, 1 migrazione |
| Seeders demo | [#368](https://github.com/laraxot/base_fixcity_fila5/issues/368) orchestrazione User + Fixcity | FO testabile con dati realistici |

### P2 — Architettura (DRY + religione Laraxot)

| Area | Cosa fare |
|------|-----------|
| `Services/` → `Actions/` | Migrare `NotificationService`, `WorkflowService`, `TicketService` a QueueableAction; aggiornare test |
| Namespace test | Allineare `Modules\Fixcity\tests` (cartella) con PSR-4 dopo #370 |
| Parità artefatti | `bashscripts/tools/audit-module-artifact-parity.sh Fixcity` |

### P3 — Copertura trasversale

- Pest coverage tutti i moduli (`phpunit.xml` centrale)
- Themes in scope PHPStan quando `paths` includerà `./Themes/` in neon
- Playwright/visual — [TwentyOne visual-testing](../../../../Themes/TwentyOne/docs/wiki/concepts/visual-testing-playwright-puppeteer.md); FO owner [Sixteen completion](../../../../Themes/Sixteen/docs/wiki/overviews/completion-roadmap.md)

## Moduli satellite (dipendenze Fixcity)

| Modulo | Ruolo per Fixcity | Doc sessione |
|--------|-------------------|--------------|
| Geo | Mappe, coordinate ticket | — |
| User | Auth FO, `getUserClass()` | — |
| Cms | `<x-page>` blocchi segnalazioni | [testing](../../../Cms/docs/wiki/concepts/testing.md) |
| Rating | Rating cittadino ticket | — |
| Comment | Commenti ticket | — |
| Notify | Notifiche workflow | [test doubles](../../../Notify/docs/wiki/concepts/phpstan-pest-test-doubles.md) |
| UI | Componenti Filament condivisi | [testing](../../../UI/docs/wiki/concepts/testing.md) |
| Xot | TestCase base, bridge Pest | [phpstan-pest-bridge](../../../Xot/docs/wiki/concepts/phpstan-pest-bridge-discipline.md) |

## Definition of Done (modulo Fixcity)

- [ ] PHPStan + Pest green su `Modules/Fixcity`
- [ ] Zero `Services/` nuovi; piano migrazione Actions documentato
- [ ] Wizard + lista + dettaglio segnalazione FO verificati su Sixteen
- [ ] Seeders idempotenti con dati demo
- [ ] Wiki aggiornata + link GitHub in frontmatter
