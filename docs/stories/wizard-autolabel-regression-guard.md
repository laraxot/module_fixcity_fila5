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
