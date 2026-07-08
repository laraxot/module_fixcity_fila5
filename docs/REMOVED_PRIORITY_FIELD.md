# Rimosso campo 'priority' dal form wizard

Descrizione
- Per migliorare l'usabilità della pagina di creazione segnalazione (wizard), il campo `priority` (select) è stato rimosso dal form gestito da `Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm`.

Motivazione
- Il campo non era necessario nell'esperienza front-office dei test e causava confusione/visual glitches nel tema Sixteen.
- Riduce la complessità del form e risolve il problema di sovrapposizione/visualizzazione del select nel wizard.

Cosa è stato cambiato
- Rimosso l'elemento `Select::make('priority')` da `getDataSchema()`.
- Rimosso il valore predefinito `priority` da `getDefaultFormState()`.

Follow-up
- Se il campo priority è necessario lato back-office, considerare di mantenerlo solo in Filament admin resources.
- Eseguire `./vendor/bin/phpstan analyse` e tests per verificare eventuali riferimenti residui a `priority`.
