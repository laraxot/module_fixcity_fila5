# wizard infolist replaces placeholders

## obiettivo

Consolidare il wizard `tests.segnalazione-crea` eliminando l'ultimo `Filament\Forms\Components\Placeholder` rimasto, adottando componenti Infolist per tutta la parte read-only.

## decisione

Nel widget `CreateTicketWizardWidget` la regola diventa:

- dati read-only -> `TextEntry` / `ImageEntry`
- dati input -> componenti Forms (`TextInput`, `Select`, `Textarea`, `FileUpload`, `Checkbox`)

## implementazione

- step privacy:
  - `privacy_notice` migrato da `Placeholder` a `TextEntry` con `->html()`
- step summary:
  - gia' su `TextEntry`/`ImageEntry`, nessuna regressione introdotta
- import `Placeholder` rimosso dal widget

## perche'

- allineamento con la semantica Filament Infolists (visualizzazione);
- coerenza strutturale del wizard: nessun componente ibrido legacy;
- parity piu' stabile lato UX e manutenzione.

## verifica

- `php -l Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` -> OK
- `curl -I --max-time 20 http://127.0.0.1:8000/it/tests/segnalazione-crea` -> `200`
- `curl -I --max-time 20 "http://127.0.0.1:8000/it/tests/segnalazione-crea?step=form.dati-della-segnalazione%3A%3Adata%3A%3Awizard-step"` -> `200`

## collegamenti

- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [CreateTicketWizardWidget](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
