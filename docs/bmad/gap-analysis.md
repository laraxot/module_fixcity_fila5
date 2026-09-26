# FixCity — Gap analysis BMAD

## Bloccanti

| ID | Gap | Chiusura richiesta |
|---|---|---|
| G-01 | database Pest non accessibile (`fixcity_data_test`) | ambiente test riproducibile e documentato |
| G-02 | vertical slice non provato in staging | smoke cittadino → PA → chiusura |
| G-03 | assegnazione non era esposta correttamente | relazione e azione PA implementate; manca test |
| G-04 | transizione ignora la motivazione e non dimostra audit/notifica | test e integrazione con activity/Notify |
| G-05 | policies/isolamento tra cittadino e PA non provati end-to-end | test autorizzativi |
| G-06 | accessibilità, backup, restore e monitoraggio non provati | report e runbook di release |

## Verifiche funzionali obbligatorie

- il cittadino crea titolo, descrizione, categoria, coordinate, consenso e immagini;
- un cittadino non vede ticket di altri cittadini;
- la PA filtra, apre, assegna, aggiorna, commenta ed esporta;
- le transizioni non autorizzate sono rifiutate;
- il cambio stato registra motivazione, attore e timestamp;
- il cittadino riceve la notifica e vede la timeline aggiornata;
- `resolved/closed` abilita il rating solo al proprietario;
- gli errori Geo/Media/Notify hanno fallback e non perdono il ticket.

## Non fare

Non usare controller HTTP o Services layer, non cambiare `phpstan.neon`, non modificare credenziali in modo ad hoc e non dichiarare completata una story senza evidenza.

## Artefatti non conformi rimossi

Sono stati rimossi due artefatti non referenziati che introducevano un'architettura non ammessa: `app/Http/Controllers/Citizen/CitizenAuthController.php` e `app/Actions/Wizard/FormValidationWizardAction.php`. L'autenticazione cittadino deve usare le pagine Folio/Actions già previste dai moduli User e Cms; la validazione wizard deve appartenere all'Action reale che persiste il ticket.

È stato rimosso anche `app/Http/Controllers/Wizard/WizardController.php` e l'omonima policy duplicata in `app/Policies/`: erano artefatti non referenziati che usavano namespace/classi inesistenti o colonne (`user_id`) non presenti nel modello Ticket. La policy canonica resta `app/Models/Policies/TicketPolicy.php`.

La policy applicativa corretta è invece `app/Policies/TicketPolicy.php` con namespace `Modules\\Fixcity\\Policies`, coerente con il `composer.json` del modulo. Gli artefatti duplicati sotto `App\\Actions`, `App\\Http\\Controllers` e le notifiche che usavano proprietà inesistenti del modello sono stati rimossi.

## UI/UX e convenzioni Filament

- [x] le pagine Resource Fixcity usano `XotBaseCreateRecord`, `XotBaseEditRecord`, `XotBaseViewRecord` e `XotBaseListRecords`;
- [x] la Dashboard Fixcity usa `XotBaseDashboard`;
- [ ] verificare in browser mobile il wizard completo, errori inline, focus, contrasto e stato di caricamento;
- [ ] verificare in browser desktop la tabella PA, assegnazione, cambio stato e feedback toast;
- [ ] registrare screenshot/report prima della promozione a release candidate.

## Migrazioni

Le migration nuove usano `foreignIdFor(Model::class, 'column')`; per User usano `XotData::make()->getUserClass()`. Le migration storiche con `foreignId('ticket_id')` non vengono riscritte: la regola forward-only richiede una migration evolutiva separata, se l'audit dello schema pilota la renderà necessaria.
