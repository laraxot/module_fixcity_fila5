# Story: segnalazione-crea map fullscreen refinement

## Contesto

Target runtime:

`http://127.0.0.1:8000/it/tests/segnalazione-crea?step=form.dati-della-segnalazione%3A%3Adata%3A%3Awizard-step`

Il modulo Fixcity possiede il flusso wizard e la verifica utente. La mappa e' pero' un componente Geo (`CoordinatePicker` / `coordinate-picker-lit`), mentre il tema Sixteen possiede il CSS pubblico e la parity Design Comuni.

## Obiettivo

Migliorare il fullscreen della mappa nello step dati:

- fullscreen realmente full viewport;
- nessuna scrollbar verticale durante fullscreen;
- nessun box laterale o blocco wizard sopra la mappa;
- controlli mappa sempre visibili e cliccabili;
- tile Leaflet completi dopo enter/exit fullscreen;
- uscita nativa via Esc sincronizzata con stato Lit/Alpine.

## Story BMAD

La story pronta per sviluppo e':

`_bmad-output/implementation-artifacts/8-74-segnalazione-crea-map-fullscreen-refinement.md`

Mirror planning:

`.planning/stories/8-74-segnalazione-crea-map-fullscreen-refinement.story.md`

## Regole operative

- Non spostare logica fullscreen dentro `CreateTicketWizardWidget`.
- Non usare selettori page-scoped come `.page-content[data-slug="tests.segnalazione-crea"]`.
- Non nascondere `Informazioni richieste` come fix primario: il layer mappa deve stare sopra.
- Non toccare la persistenza `location` JSON, gia' trattata da story separata.

## Verifica

La story e' verificabile solo sull'URL esatto con `step=`. La verifica deve includere screenshot prima del fullscreen, durante fullscreen e dopo uscita, almeno desktop e mobile.
