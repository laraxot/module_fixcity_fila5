---
title: "wizard autolabel regression guard"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "wizard autolabel regression guard"
issues: []
discussions: []
---

# wizard autolabel regression guard

## contesto

Nel refactor dello step summary con Infolist sono riapparsi `->label()` e `->placeholder()` hardcoded in `CreateTicketWizardWidget`.
Questo contraddice la policy del modulo (LangServiceProvider + AutoLabel).

## causa radice

- copy di pattern Filament generico nel riepilogo;
- mancata applicazione della regola specifica progetto su Infolist entries;
- assenza di guardrail documentato esplicito "no label/no placeholder anche in summary".

## decisione

1. rimossi tutti i `->label()` e `->placeholder()` dal metodo `makeStepSummary()`;
2. mantenuto solo mapping stato con `->state(...)` e naming `review_*`;
3. aggiornata la regola wizard per coprire esplicitamente anche gli entry Infolist.

## criteri di accettazione

- [x] `CreateTicketWizardWidget` non contiene `->label(`;
- [x] `CreateTicketWizardWidget` non contiene `->placeholder(`;
- [x] docs rules aggiornate con guardrail anti-regressione;
- [x] nessun errore lint sui file toccati.

## collegamenti

- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [Wizard summary infolist alignment](./wizard-summary-infolist-alignment.md)

Correzione verificata 2026-10-13 (DOCS-ARRETRATO, DCP-44): `makeStepSummary()` non esiste (nessuna `function makeStepSummary` in `Modules/Fixcity/app` e `Modules/Xot/app`; `git log -S"makeStepSummary" -- app` nel repo Fixcity: nessun commit). Il riepilogo front office e' `TicketForm::getReviewStepSchema()` (View `pub_theme::components.wizard.summary-step`); gli entry Infolist del riepilogo back office sono in `TicketFormReviewInfolist::summarySectionEntries()` richiamato da `TicketForm::getSummarySectionSchema()`. Il testo sopra e' storico e non e' stato riscritto.
