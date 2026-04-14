# wizard summary infolist alignment

## contesto

Nel widget `CreateTicketWizardWidget`, lo step `getSummarySchema()` deve rimanere un blocco read-only puro.
Per governance Filament v5, il riepilogo deve usare componenti Infolist dedicati (`TextEntry`, `ImageEntry`) e non `Placeholder` generici.

## problema

- il riepilogo non usava il builder semantico Infolist;
- si perdeva coerenza con la regola "tipo di contenuto = tipo di componente";
- maggiore rischio di drift tra stato wizard e markup custom.

## obiettivo

Allineare `getSummarySchema()` a Infolist (`TextEntry` + `ImageEntry`) con mapping esplicito da stato wizard (`Get $get`).

## decisioni

1. step 3 usa `Section` + `Grid` + `TextEntry`/`ImageEntry` nello `Step::schema()`;
2. ogni entry usa chiave `review_*` per evitare collisioni con i campi editabili;
3. mapping typed dello stato (`issueType` -> `TicketTypeEnum::getLabel()`).
4. `Placeholder` esclusi dal blocco summary principale (accettabili solo per contenuti accessori non-dato).

## criteri di accettazione

- [x] `SchemaView` rimosso dal riepilogo wizard;
- [x] riepilogo implementato con Infolist entries;
- [x] nessun `Placeholder` usato come entry principale in `getSummarySchema()`;
- [x] nessun errore lint nel widget;
- [x] documentazione modulo/tema aggiornata.

## riferimenti

- [Filament infolists overview](https://filamentphp.com/docs/5.x/infolists/overview/)
- [Filament wizards](https://filamentphp.com/docs/5.x/schemas/wizards)
- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [Theme wizard report](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
