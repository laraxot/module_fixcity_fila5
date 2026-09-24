# Decision: Wizard Submission States (Draft vs Pending)

**Status**: accepted
**Date**: 2026-04-22
**Context**: Wizard Final Step (Summary)
**Tags**: UX, Workflow, Ticket

---

## Context

Il wizard di segnalazione citizen-frontoffice richiede una distinzione chiara tra l'intento di "completare il lavoro" e l'intento di "notificare l'ente". 

In molti sistemi, il salvataggio è automatico o implicito. In Laraxot/Fixcity, seguiamo il pattern del **Design Comuni (Bootstrap Italia 2.x)** che prevede bottoni espliciti nell'area delle azioni finali.

## Decisione: La Strategia dei Due Stati

Abbiamo deciso di implementare due canali di persistenza finale nello step di Riepilogo:

1.  **Invia (Submit)**:
    - **Azione**: Scatena l'invio ufficiale.
    - **Stato Modello**: `pending`.
    - **Evento**: `TicketCreatedEvent` (che invia mail al Comune e al cittadino).
    - **Zen**: La chiusura del cerchio. Il cittadino ha finito il suo compito, la palla passa all'istituzione.

2.  **Salva richiesta (Save Draft)**:
    - **Azione**: Salva i dati correnti senza chiudere il wizard o notificare l'ente.
    - **Stato Modello**: `draft` ( TicketStatusEnum::DRAFT )
    - **Evento**: Nessuno (solo save su DB)
    - **Zen**: Rispetto per il tempo dell'utente. Permette di raccogliere dati e decidere in un secondo momento se l'istituzione debba essere coinvolta.

## Logica e Visione

- **Filosofia (Body/Dress)**: 
    - Il **Body** (`Modules/Fixcity`) gestisce gli stati e la validazione.
    - Il **Dress** (`Themes/Sixteen`) gestisce l'allineamento visuale ai bottoni grandi (428px su desktop) e le icone tematiche.
- **Politica**: Un ticket non inviato NON deve comparire nelle code di lavoro degli istruttori comunali, ma solo nel dashboard "Le mie segnalazioni" del cittadino.
- **Religione**: L'autenticazione è pre-requisito per il salvataggio bozza. Non è possibile salvare una "richiesta anonima" come bozza persistente.
- **Zen (Visual Parity)**: 
    - Niente bottoni duplicati. Niente "Avanti" se sono già all'ultimo passo.
    - Solo **Indietro**, **Salva richiesta**, **Invia**.

## Impatto Tecnico

- Il metodo `submit()` in `CreateTicketWizardWidget` deve essere affiancato da un metodo `draft()`.
- Il wrapper Blade `ticket-create-wizard.blade.php` deve renderizzare condizionalmente questi bottoni basandosi sull'indice dello step.
- Il CSS deve garantire `display: none !important` su ogni elemento nativo di Filament che possa rompere la parity visuale del tema.

## Riferimenti

- [CreateTicketWizardWidget.md](../CreateTicketWizardWidget.md)
- [Wizard Governance Philosophy](../wizard-governance-philosophy.md)
- [Design Comuni - Flusso Segnalazione](https://designers.italia.it/modelli/comuni/segnalazione-disservizio/)
