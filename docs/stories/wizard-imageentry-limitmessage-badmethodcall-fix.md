# wizard imageentry limitmessage badmethodcall fix

## contesto

Su `GET /it/tests/segnalazione-crea` il widget wizard andava in errore:

- `BadMethodCallException`
- `Method Filament\Infolists\Components\ImageEntry::limitMessage does not exist`

Root cause: uso di un metodo non presente nell'API Filament Infolists v5.

## decisione

Allineare lo step summary immagini al contratto reale di `ImageEntry`:

- `->limit(5)`
- `->limitedRemainingText()`

ed eliminare completamente ogni uso di `->limitMessage()` su `ImageEntry`.

## perche' (regola / visione)

- KISS: usare solo API ufficiali del componente.
- DRY: regola documentata una volta nei docs modulo e riusata.
- anti-regression: chiarire che `limitMessage()` non appartiene a `ImageEntry` in questa versione.

## criteri di accettazione

- [x] nessuna occorrenza di `limitMessage(` nel modulo Fixcity;
- [x] route `segnalazione-crea` non rompe piu' per `BadMethodCallException`;
- [x] documentazione widget/rules aggiornata con API corretta.

## collegamenti

- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
