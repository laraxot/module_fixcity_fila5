# wizard step 2 high html visual parity

## obiettivo

Portare lo step `dati-della-segnalazione` del wizard `segnalazione-crea` a parity alta con il reference Design Comuni, sia lato struttura percepita sia lato resa visiva.

## perche

- coerenza con standard istituzionale;
- riduzione attrito in compilazione;
- prevenzione regressioni UI dovute a mix tra markup legacy e classi Filament `fi-*`.

## regola

- `Schemas` nel form wizard;
- CSS page-scoped su `data-slug` della pagina test;
- nessuna duplicazione di heading/sezione;
- testi utente sempre da i18n, mai chiavi tecniche visibili.

## output atteso

- blocchi `Luogo`, `Disservizio`, `Autore` leggibili e stabili;
- legenda campi obbligatori in posizione coerente;
- azioni finali allineate al reference;
- colonna sinistra e colonna centrale con proporzioni leggibili;
- ritmo verticale piu denso e meno dispersivo;
- header della pagina non fuori tono rispetto al reference;
- documentazione modulo + tema sincronizzata.

## collegamenti

- [story implementation 7-50](../../../../_bmad-output/implementation-artifacts/7-50-segnalazione-crea-step2-high-html-visual-parity.md)
- [ticket wizard frontoffice](../ticket-wizard-frontoffice.md)
- [theme ticket creation wizard](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
