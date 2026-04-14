# wizard translation prototype ticket context

## contesto

Il testo immagini del summary era stato modellato con chiavi widget-first o con nodi finali ambigui.

La semantica corretta e' dominio-first: `fixcity::ticket.messages.images_uploaded.text`.

## decisione

1. nel widget: usata `fixcity::ticket.messages.images_uploaded.text` con `trans_choice()`;
2. riallineate le chiavi `messages.*` in `lang/it/ticket.php`;
3. riallineato `lang/en/ticket.php` con la stessa grammatica.

## perche' (visione)

- domain-first naming: la chiave descrive il dominio (`ticket.messages`), non il punto di rendering;
- type-last grammar: il nodo finale deve essere un tipo (`text`, `label`, `body`, `title`);
- riduce duplicazione tra UI diverse (wizard, resource, api feedback);
- migliora discoverability e manutenzione i18n.

## criteri di accettazione

- [x] runtime usa `fixcity::ticket.messages.images_uploaded.text`;
- [x] chiave presente in it + en;
- [x] docs rules aggiornate con regola context-first.

## collegamenti

- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
