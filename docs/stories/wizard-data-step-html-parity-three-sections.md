# wizard data step html parity three sections

## contesto

La pagina di riferimento Design Comuni per il passo dati della segnalazione presenta tre blocchi semantici distinti:

1. luogo
2. disservizio
3. autore della segnalazione

Nel widget Filament questo raggruppamento era perso, riducendo la parita di struttura e percezione.

## decisione

- rifattorizzare `getDataSchema()` in tre `Section` allineate al reference
- mantenere naming dominio (`type`, `name`, `content`) per coerenza con `Ticket`
- usare blocco autore read-only con componenti Filament (`Placeholder`) in stile infolist
- mantenere campo contatto (`email`) nel blocco autore

## perche

- **business**: riduce ambiguita nei dati richiesti e migliora completamento del form
- **ux**: stessa gerarchia cognitiva del design di riferimento
- **architettura**: nessun campo fake persistito; solo mapping coerente col model
- **i18n**: sezioni e contenuti guidati da chiavi traduzione

## file aggiornati

- `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `laravel/Modules/Fixcity/lang/it/create_ticket_wizard.php`
- `laravel/Modules/Fixcity/lang/en/create_ticket_wizard.php`
- `laravel/Modules/Fixcity/docs/CreateTicketWizardWidget.md`

## collegamenti

- [stories index](./index.md)
- [create ticket wizard widget](../CreateTicketWizardWidget.md)
- [ticket creation wizard theme doc](../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
- [design comuni reference](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html)
