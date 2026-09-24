# wizard translation key five elements image rule

## contesto

Nel summary wizard era usata la chiave:

- `fixcity::ticket.messages.no_images`

La chiave e' valida tecnicamente ma non segue il prototipo semantico richiesto (5 elementi):  
`namespace::context.collection.element.type`.

## decisione

Riallineata la semantica sul dominio ticket con tipo finale esplicito:

- `fixcity::ticket.messages.images_uploaded.text`

## perche' (regola / visione)

- business-first: il testo appartiene al dominio ticket, non al widget che lo mostra;
- grammar-first: il quinto elemento deve essere un tipo atomico (`text`), non un composto come `empty_message`;
- DRY: un unico namespace di dominio evita duplicazioni tra wizard, resource, view;
- KISS: naming prevedibile e ricercabile.

## criteri di accettazione

- [x] widget usa chiave `ticket.messages.images_uploaded.text`;
- [x] chiavi presenti in `lang/it/ticket.php` e `lang/en/ticket.php`;
- [x] docs regole aggiornate con prototipo a 5 elementi.

## collegamenti

- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
