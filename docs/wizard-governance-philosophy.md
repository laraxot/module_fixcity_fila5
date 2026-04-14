# wizard governance philosophy

## perche'

Il wizard frontoffice e' un punto ad alto impatto: tocca UX, traduzioni, dominio ticket e integrazione tema.
Se il widget dominio contiene anche policy tecniche trasversali (label, logging, wiring azioni), il costo cresce:

- piu' duplicazione tra moduli;
- maggiore rischio regressioni in ogni refactor;
- incoerenza tra frontoffice e backoffice.

## regola

- `CreateTicketWizardWidget` contiene **solo** logica di dominio (step, payload, redirect).
- `XotBaseWizardWidget` contiene logica **cross-cutting** (start step, query `?step=`, submit strategy).
- `getWizardSubmitAction()` vive in `XotBaseWizardWidget` (single source of truth), non nel widget dominio.
- Nessun `->label()` / `->tooltip()` hardcoded nel widget dominio.
- Nessun `Log::error()` locale nel flusso submit frontoffice; errore gestito lato UX (`addError` + notification).
- Lo step riepilogo usa Infolist entries (`TextEntry`) invece di view custom generiche.

## visione

**Layering chiaro**:

- modulo Fixcity = business rule della segnalazione;
- modulo Xot = policy/framework condivise;
- tema Sixteen = veste visuale e parity Design Comuni.

Questa separazione riduce accoppiamento e rende i cambiamenti piu' prevedibili.

## scopo

1. Ridurre complessita' cognitiva del widget.
2. Aumentare riuso tra wizard di moduli diversi.
3. Mantenere i18n coerente via convenzioni.
4. Evitare log noise e duplicazione segnali.

## filosofia, politica, zen

- **filosofia**: DRY + KISS prima di "feature stacking".
- **politica**: le policy comuni stanno nella base, non nei singoli widget.
- **zen**: `module=body, theme=dress` e `log once at final handler`.
- **religione (metafora tecnica)**: nessun hardcode testuale quando esiste un sistema di traduzione canonico.
- **dogma clean code**: no metodi pass-through senza semantica (`createXFromY()` che fa solo una riga senza valore).

## fonti esterne (supporto decisioni)

- Filament wizard docs: [filament wizards](https://filamentphp.com/docs/5.x/schemas/wizards)
- Refactoring (middle man / pass-through wrappers):
  - [middle man smell](https://refactoring.guru/smells/middle-man)
  - [remove middle man](https://refactoring.guru/remove-middle-man)
- Clean architecture boundaries/logging once:
  - [clean architecture mistakes](https://asadali.dev/blog/the-clean-architecture-mistakes-i-keep-seeing-even-in-senior-teams/)
  - [logger in clean architecture](https://stackoverflow.com/questions/53419938/logger-in-clean-architecture)
  - [logging without noise](https://dev.to/kumaraish/logging-without-the-noise-a-tiered-strategy-for-clean-architecture-20pg)

## anti-ridondanza

Per evitare file doppi:

- documento canonico del tema: `Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md`
- documento canonico modulo: `Modules/Fixcity/docs/CreateTicketWizardWidget.md`
- documento canonico governance: **questo file**
- le storie stanno solo in `docs/stories/` e vengono indicizzate da `docs/stories/index.md`

## collegamenti bidirezionali

- [Fixcity docs index](./INDEX.md)
- [CreateTicketWizardWidget](./CreateTicketWizardWidget.md)
- [Filament wizard rules](./rules/filament-wizard-rules.md)
- [Governance story](./stories/wizard-governance-langserviceprovider-and-xotbase-refactor.md)
- [Infolist summary story](./stories/wizard-summary-infolist-alignment.md)
- [Theme bridge](../../Themes/Sixteen/docs/design-comuni/wizard-governance-bridge.md)
- [Xot base wizard](../../Xot/docs/filament/widgets/xot-base-wizard-widget.md)
