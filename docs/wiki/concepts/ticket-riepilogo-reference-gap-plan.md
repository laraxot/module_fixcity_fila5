# Segnalazione Riepilogo Reference Gap Plan

## Runtime

URL locale verificato:

`/it/tests/segnalazione-crea?step=form.riepilogo%3A%3Adata%3A%3Awizard-step`

Risultato HTTP: `200`.

## Submit

Nel markup locale il submit esiste:

- footer Filament nativo: `Invia`;
- nav custom Design Comuni: `Conferma e invia`.

Il problema non e' assenza PHP dell'azione, ma ownership visuale: il footer Filament nativo viene nascosto per evitare doppia nav, quindi il submit visibile deve essere governato dalla nav Design Comuni.

## Gaps funzionali/UX

- manca alert `Attenzione` con testo dichiarativo;
- manca struttura `Segnalazione` > `Disservizio`;
- manca azione `Modifica` per tornare agli step precedenti;
- manca sezione `Dati Generali` con card autore/contatti in stile reference;
- manca eventuale modale `Termini e condizioni` prima della submit finale.

## Regola

`getSummarySchema()` resta basato su Infolist entries (`TextEntry`, `ImageEntry`) per i dati read-only, ma la gerarchia visuale deve essere allineata alla reference Design Comuni.
