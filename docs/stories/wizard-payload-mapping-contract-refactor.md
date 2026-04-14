# wizard submit filament first refactor

## contesto

La creazione ticket nel wizard era stata portata verso un layer payload dedicato.

Per un create wizard semplice questo introduce complessita' non necessaria.

## decisione

Refactor "Filament-first":

- uso diretto di `$this->form->getState()`;
- mutazioni minime in `submit()` per allineare i nomi campo al model;
- `Ticket::query()->create($data)` senza payload layer separato.

## perche' (regola / visione)

- KISS: meno layer, meno metodi, meno superficie di errore;
- Filament way: form state come sorgente unica del create flow;
- DRY locale: una sola orchestrazione nel submit del wizard.

## criteri di accettazione

- [x] create da form state diretto;
- [x] mutazioni minime e leggibili nel submit;
- [x] nessun metodo payload dedicato;
- [x] docs regole/widget aggiornate.

## collegamenti

- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [stories index](./index.md)
