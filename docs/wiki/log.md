# Modules Wiki Log

## [2026-07-12] fix | PHPStan L10 — TicketLayoutViewModel trait types

- `PresentsTicketLayoutChrome`: `@property-read` per `$blockData`, `$selectedTypes`, `$liveTickets`; shape `list<array{id,label,active}>` su tab/breadcrumb
- `TicketLayoutViewModel`: `Collection<int, Ticket|object>` per demo Design Comuni + live query
- Scopo: VM FO elenco segnalazioni — dati CMS in `$blockData`, tab attiva per pannelli mappa/lista
- PHPStan: `vendor/bin/phpstan analyse Modules` → 0 errori
- GitHub: [#372](https://github.com/laraxot/base_fixcity_fila5/issues/372)

## [2026-06-13] docs | Gate chef — hub completamento + Activity/Xot test docs

- Hub: [platform-completion-roadmap](../Xot/docs/wiki/overviews/platform-completion-roadmap.md)
- Activity: 7 file test Assert; [completion-status](../Activity/docs/wiki/overviews/completion-status.md)
- Temi: Barthelemy, TwentyOne, Meetup completion roadmaps
- GitHub: [#372](https://github.com/laraxot/base_fixcity_fila5/issues/372)

## [2026-06-13] docs | PHPStan Pest sessione — helper test + roadmap completamento

- Fixcity: [phpstan-pest-testcase-helpers](../Fixcity/docs/wiki/concepts/phpstan-pest-testcase-helpers.md), [completion-roadmap](../Fixcity/docs/wiki/overviews/completion-roadmap.md)
- Notify: [phpstan-pest-test-doubles](../Notify/docs/wiki/concepts/phpstan-pest-test-doubles.md)
- Xot: aggiornato [phpstan-pest-bridge-discipline](../Xot/docs/wiki/concepts/phpstan-pest-bridge-discipline.md)
- UI, Tenant, Cms: `testing.md` aggiornati
- Sixteen: [theme-component-test-contract](../../Themes/Sixteen/docs/wiki/concepts/theme-component-test-contract.md), [completion-roadmap](../../Themes/Sixteen/docs/wiki/overviews/completion-roadmap.md)
- GitHub: [Fixcity#52](https://github.com/laraxot/module_fixcity_fila5/issues/52) / [D#53](https://github.com/laraxot/module_fixcity_fila5/discussions/53)

## [2026-06-05] docs | HackerNoon harness — tips 001-022 in wiki locale

- Stub/checklist: second-brain → canon Xot, ai-harness, [hackernoon map](../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md), [llm-wiki.txt](../../../../bashscripts/tools/prompts/llm-wiki.txt)
- GitHub: [#272](https://github.com/laraxot/base_fixcity_fila5/issues/272) / [D#273](https://github.com/laraxot/base_fixcity_fila5/discussions/273)

# Modules Wiki Log

## [2026-06-05] docs | AI harness + HackerNoon propagati a tutti i moduli

- Creati `ai-harness-module-discipline.md`, aggiornato `second-brain-operating-model.md`
- Index wiki aggiornati (18 moduli) con sezione AI / second brain
- Stub `second-brain-local-discipline.md` allineati → canon Xot + mappa #272
- `index.md`: link `migration-update-timestamps-only` + `audit-migration-timestamp-redundancy.sh`

## [2026-06-05] architecture | parità N modelli per modulo (index cross-modulo)

- `wiki/index.md`: link a BMAD pilastro 3, script audit, snapshot root
- Canon: `docs/wiki/bmad/architecture-module-model-artifact-parity.md`

## [2026-04-28] docs | second brain operativo per i moduli

- creato `index.md` per rendere la wiki dei moduli navigabile come knowledge base.
- aggiunto `concepts/second-brain-operating-model.md` con modello operativo CODE + PARA adattato al repository.
- incluse sezioni best practices, bad practices, false friends e link verificati di approfondimento.

## [2026-07-12] phpstan | view-model chrome typed shapes

- `PresentsTicketLayoutChrome` usa shape PHPDoc su breadcrumb, tabs, CTA, contatti e collection live tickets.
- Motivo: PHPStan analizza il trait nel contesto di `TicketLayoutViewModel`; le shape devono essere visibili al consumer reale, non solo al file trait isolato.
