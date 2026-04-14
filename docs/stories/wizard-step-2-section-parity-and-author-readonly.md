# wizard step 2 section parity and author readonly

## obiettivo

Aumentare la parity visuale dello step 2 (`dati di segnalazione`) del wizard `tests.segnalazione-crea` rispetto al riferimento Design Comuni:

- [segnalazione-02-dati](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html)

## problema

Lo step 2 era piatto (campi in sequenza) e non rifletteva chiaramente la gerarchia attesa:

- Luogo
- Disservizio
- Autore della segnalazione

Inoltre il blocco autore non comunicava la logica read-only in modo coerente con il pattern Infolist/reference.

## soluzione adottata

Nel widget `CreateTicketWizardWidget::getDataSchema()`:

1. introdotte tre `Section` esplicite:
   - `Luogo`
   - `Disservizio`
   - `Autore della segnalazione`
2. nel blocco autore aggiunta griglia read-only (infolist-like) con:
   - nome
   - codice fiscale
   - telefono
3. mantenuto input email come contatto modificabile.

## note architetturali

- scelta `Section + Placeholder` nello step form per stabilita' del render path;
- pattern infolist usato come semantica di presentazione read-only, senza forzare un Infolist separato dentro schema form;
- stringhe UI risolte via traduzioni (`fixcity::create_ticket_wizard.fields.*`).

## verifica

- smoke URL step 2:
  - `http://127.0.0.1:8000/it/tests/segnalazione-crea?step=form.dati-della-segnalazione%3A%3Adata%3A%3Awizard-step`
- presenza in HTML di:
  - `Luogo`
  - `Disservizio`
  - `Autore della segnalazione`
  - `Informazione su di te`

## collegamenti

- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [CreateTicketWizardWidget](../CreateTicketWizardWidget.md)
- [stories index](./index.md)
