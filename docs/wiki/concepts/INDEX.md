---
title: "concepts index — Fixcity"
type: index
tags: [concepts, Fixcity]
created: 2026-05-11
updated: 2026-06-13
---

# concepts Index — Fixcity

Concetti specifici del modulo Fixcity. Carica on-demand via `qmd search` o consulta il [trigger map root](../../../../../../docs/wiki/rules/00-TRIGGER_MAP.md).

- [profiles-uuid-contract](./profiles-uuid-contract.md) — owner `profiles`: 1 migrazione, `uuid` + `credits` nullable, bump timestamp
- [xotbasewidget-child-no-explicit-widget-view](./xotbasewidget-child-no-explicit-widget-view.md) — widget che estende `XotBaseWidget`: niente `$view` sul figlio; docblock pigro ok
- [fixcity-ticket-vs-segnalazione-lang](./fixcity-ticket-vs-segnalazione-lang.md) — `fixcity::ticket.*` per schema Filament Ticket vs `segnalazione` pubblico
- [tickets-view-cms-folio-page.md](./tickets-view-cms-folio-page.md) — `/it/tickets/{id}` + CMS `tickets.view` (STORY-134)
- [no-controllers-folio-volt-filament](./no-controllers-folio-volt-filament.md) — VIETATO usare Controllers; stack ufficiale Folio + Volt + Filament + Actions
- [folio-api-no-controllers](./folio-api-no-controllers.md) — API `/api/*` via Folio + Actions (STORY-069)
- [ticket-citizen-rating-via-rating-module](./ticket-citizen-rating-via-rating-module.md) — RatingMorph, no colonne su tickets (STORY-071)
- [module-basemodel-rule](./module-basemodel-rule.md) — Ticket extends Fixcity BaseModel (STORY-047)
- [phpstan-pest-testcase-helpers](./phpstan-pest-testcase-helpers.md) — helper TestCase + PHPStan Pest (2026-06-13)
- [testing](./testing.md) — quality gate Pest + PHPStan
- [completion-roadmap](../overviews/completion-roadmap.md) — cosa resta per chiudere Fixcity
