# wizard privacy notice parity step 1

## obiettivo

Allineare lo step 1 (`autorizzazioni e condizioni`) del wizard `tests.segnalazione-crea` al reference Design Comuni:

- `https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html`

con focus sul testo legale GDPR mancante prima del checkbox.

## perche'

Il checkbox da solo non copre il requisito informativo: l'utente deve leggere il contesto privacy prima dell'accettazione.
La parity visuale e semantica richiede:

- blocco informativo read-only;
- link esplicito all'informativa;
- conferma esplicita tramite checkbox.

## implementazione

Nel widget `CreateTicketWizardWidget` lo step privacy ora include:

1. `Placeholder` read-only `privacy_notice` con:
   - testo `fixcity::segnalazione.privacy.intro.text`
   - prefisso `fixcity::segnalazione.privacy.detail_prefix.text`
   - link `fixcity::segnalazione.privacy.link.label`
2. `Checkbox::make('privacyAccepted')` come acceptance finale.

La URL del link privacy usa `blockData['privacy_link']` con fallback `#`.

## regola

- contenuto legale in traduzioni modulo (`lang/{locale}`), non hardcoded in runtime;
- step 1 sempre composto da:
  - contenuto informativo read-only
  - azione esplicita di consenso.

## verifica

- smoke step privacy:
  - `http://127.0.0.1:8000/it/tests/segnalazione-crea?step=form.privacy-e-condizioni%3A%3Adata%3A%3Awizard-step`
- presenza stringa:
  - `Il Comune di Firenze gestisce i dati personali forniti e liberamente comunicati`
- lint PHP:
  - `php -l Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`

## collegamenti

- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [CreateTicketWizardWidget](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
