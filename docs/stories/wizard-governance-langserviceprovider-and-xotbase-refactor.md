# story: wizard governance langserviceprovider and xotbase refactor

## contesto

Il widget `CreateTicketWizardWidget` e' il punto di ingresso frontoffice per la creazione segnalazione.
Negli ultimi cicli e' emersa una regola chiara di progetto:

- niente `->label()` / `->tooltip()` hardcoded nel widget dominio;
- niente `Log::error()` locale nel flusso wizard frontoffice;
- spostare nella base `XotBaseWizardWidget` tutto cio' che e' cross-cutting e non business-specific.

## problema

Quando il widget dominio contiene logica di presentazione/localizzazione e behavior comune wizard:

- cresce il rischio di drift tra moduli;
- si duplicano regole gia' risolte in Xot;
- si rompe la filosofia DRY + KISS + module boundaries.

## obiettivo

Consolidare la governance del wizard in modo che:

1. il widget Fixcity resti focalizzato su schema step e payload ticket;
2. i testi action/field seguano auto-configurazione via LangServiceProvider o default localizzati;
3. il feedback errore frontoffice sia UX-first (`addError` + `Notification`) senza logging locale rumoroso;
4. la base Xot contenga il comportamento wizard riusabile tra moduli.
5. mantenere il submit action nel layer base (`XotBaseWizardWidget`) senza override nel widget dominio.

## file coinvolti

| file | responsabilita |
|---|---|
| `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` | widget dominio ticket |
| `Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php` | base wizard condivisa |
| `Modules/Fixcity/docs/rules/filament-wizard-rules.md` | regole operative modulo |
| `Modules/Fixcity/docs/CreateTicketWizardWidget.md` | doc tecnica widget |
| `Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md` | integrazione frontoffice tema |

## acceptance criteria

1. Nessun `->label(` e nessun `->tooltip(` nel widget `CreateTicketWizardWidget`.
2. Nessun `Log::error(` nel submit handler del widget frontoffice.
3. `XotBaseWizardWidget` non forza `->label()` nel fallback submit action.
4. Regole e rationale documentati in `Fixcity/docs/rules/filament-wizard-rules.md`.
5. Indici docs aggiornati con link relativi bidirezionali.
6. `getWizardSubmitAction()` resta in `XotBaseWizardWidget`; il widget Fixcity non lo overridea.
7. `submit()` del widget resta un orchestrator corto; payload/build/error handling sono estratti in metodi dedicati.
8. Nessun metodo pass-through "middle man" senza valore (es. wrapper one-liner eliminati).

## visione (zen)

- **module=body, theme=dress**: il dominio ticket non si occupa di policy UI globale.
- **base class over copy-paste**: comportamento comune wizard vive in Xot.
- **user-facing over noisy logs**: errore noto al cittadino tramite UX coerente, non spam log locale.
- **traduzione by convention**: auto-label elimina hardcode e rende il sistema piu' consistente.

## stato

- [x] refactor widget su no-label/no-tooltip/no-log
- [x] aggiornata base xot submit fallback senza `label()`
- [x] centralizzato in XotBaseWizardWidget il submit action wizard
- [x] rimosso override `getWizardSubmitAction()` dal widget Fixcity (DRY+KISS)
- [x] submit riallineato al flusso Filament-first (`form->getState()` + mutate minime + `Ticket::create`)
- [x] rimosso wrapper pass-through `createTicketFromFormData()` (middle man smell)
- [x] aggiornata documentazione modulo/tema
- [x] aggiornate regole wizard modulo

## collegamenti bidirezionali

- [Stories index](./index.md)
- [CreateTicketWizardWidget doc](../CreateTicketWizardWidget.md)
- [Filament wizard rules](../rules/filament-wizard-rules.md)
- [Fixcity docs index](../INDEX.md)
- [Theme wizard report](../../../../Themes/Sixteen/docs/design-comuni/TICKET-CREATION-WIZARD.md)
