# wizard schemas overview alignment

## obiettivo

Allineare il widget `CreateTicketWizardWidget` alla regola Filament v5: in un `Form Schema` usare componenti `Filament\Schemas\Components\*`, evitando mix improprio con componenti Infolists.

## root cause

Nel wizard erano presenti componenti Infolists (`TextEntry`/`ImageEntry`) dentro metodi schema del form (`getPrivacySchema`, `getDataSchema`, `getSummarySchema`), causando incoerenza architetturale e rischio di errori runtime/namespace.

## soluzione

- sostituiti i blocchi read-only con `Text` (`Filament\Schemas\Components\Text`);
- mantenute `Section` e `Grid` come struttura semantica;
- mantenuto `Get` per leggere lo stato del wizard in review;
- corretto import `Action` su namespace Filament corretto.

## verifica

- `php -l Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` -> OK
- `curl -I --max-time 20 http://127.0.0.1:8000/it/tests/segnalazione-crea` -> `200`
- `curl -I --max-time 20 "...step=form.dati-della-segnalazione::data::wizard-step"` -> `200`

## riferimento

- [Filament Schemas overview](https://filamentphp.com/docs/5.x/schemas/overview)
- [Filament Infolists overview](https://filamentphp.com/docs/5.x/infolists/overview)

## collegamenti

- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [CreateTicketWizardWidget](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
