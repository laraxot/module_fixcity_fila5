# wizard submit action centralization

## contesto

Nel widget `CreateTicketWizardWidget` era presente un override locale di `getWizardSubmitAction()`.
Questa scelta duplicava una responsabilita cross-cutting gia propria di `XotBaseWizardWidget`.

## problema

- policy submit frammentata tra modulo dominio e layer base;
- maggiore rischio drift tra wizard di moduli diversi;
- riduzione della coerenza architetturale DRY/KISS.

## obiettivo

Portare e mantenere `getWizardSubmitAction()` in `XotBaseWizardWidget`, lasciando il widget Fixcity focalizzato su business logic (`step`, `payload`, `submit` orchestration).

## decisione

1. rimosso override di `getWizardSubmitAction()` da `CreateTicketWizardWidget`;
2. mantenuta la definizione canonica in `XotBaseWizardWidget`;
3. aggiornata documentazione modulo, tema e base per riflettere la regola.

## criteri di accettazione

- [x] `CreateTicketWizardWidget` non dichiara `getWizardSubmitAction()`;
- [x] `XotBaseWizardWidget` resta unica sorgente della submit action wizard;
- [x] nessun errore lint sui file modificati;
- [x] documentazione aggiornata con regola esplicita "submit action in base".

## collegamenti

- [CreateTicketWizardWidget docs](../CreateTicketWizardWidget.md)
- [Wizard rules](../rules/filament-wizard-rules.md)
- [Xot base wizard](../../../../Xot/docs/filament/widgets/xot-base-wizard-widget.md)
- [Theme wizard report](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
