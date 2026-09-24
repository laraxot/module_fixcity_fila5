# wizard step 2 visual cleanup and schema layout

## contesto

Lo step `dati-della-segnalazione` risultava visivamente degradato: gerarchia poco leggibile, blocchi percepiti come compressi e presenza di una sezione contatti annidata che rompeva il ritmo visivo rispetto al riferimento Design Comuni.

## causa radice

- mix tra layout Filament Schema (`fi-section`, `fi-sc-section`) e selettori CSS legacy disegnati su markup precedente;
- duplicazione dei label wrapper delle sezioni (`fi-sc-section-label-ctn`) oltre all'header reale della `Section`;
- blocco `Contatti` annidato in una seconda `Section` con azione, eccessivo per il caso d'uso.

## decisione

- mantenere il paradigma `Schemas` nel form wizard (coerenza architetturale);
- semplificare la sezione autore: dati read-only + campo email diretto, senza sotto-sezione collassabile;
- riallineare CSS su classi Filament reali dello step 2.

## implementazione

- `CreateTicketWizardWidget`:
  - rimossa la `Section` annidata `Contatti` con `headerActions`;
  - mantenuto `TextInput::make('email')` direttamente nella sezione autore.
- `segnalazione-parity.css`:
  - regole page-scoped su `div.page-content[data-slug="tests.segnalazione-crea"]`;
  - override su `fi-section`, `fi-section-content-ctn`, `fi-section-header-*`, `fi-sc-text`;
  - hidden del wrapper duplicato `fi-sc-section-label-ctn`.

## regola anti-regressione

Quando il wizard usa `Filament\Schemas\Components\Section`, i fix visuali devono targettare le classi `fi-*` prodotte da Filament e non markup storici non piu presenti.

## collegamenti

- [stories index](./index.md)
- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [theme ticket creation wizard](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
