# wizard multilanguage runtime strings guard

## contesto

Nel widget `CreateTicketWizardWidget` erano presenti stringhe UI italiane hardcoded nello step summary.
In un sito multilingua questo rompe la coerenza i18n e rende il codice non scalabile.

## problema

- testi runtime fuori dai file lang;
- rischio di mixed-language UI;
- maggiore costo manutenzione per nuove lingue.

## decisione

1. rimossi i literal italiani nei testi runtime del summary (`Section::make()`, `description()`, ecc.);
2. sostituiti con chiavi `__('fixcity::create_ticket_wizard.summary...')`;
3. aggiunte le chiavi mancanti in `lang/it/create_ticket_wizard.php` e `lang/en/create_ticket_wizard.php`;
4. aggiornata la regola wizard: no locale literals nel codice runtime.

## criteri di accettazione

- [x] nessuna stringa UI italiana hardcoded nel runtime del widget;
- [x] chiavi summary presenti in en + it;
- [x] docs modulo/tema/rules aggiornate;
- [x] lint pulito.

## collegamenti

- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [Theme wizard report](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
