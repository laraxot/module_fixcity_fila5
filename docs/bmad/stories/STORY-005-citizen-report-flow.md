---
title: CitizenReportFlow
id: STORY-005
author: BMAD
status: done
priority: must
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 005 citizen report flow FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Flusso del cittadino (Citizen → PA → System)

1. **Accesso** → Cittadino apre il sito pubblico (`pub_theme::filament.widgets.create-ticket-wizard`)
2. **Insert** → Inserisce nome, descrizione, tipo (opzionale)
3. **Validazione** → `GetTicketFormDataForPersistAction` normalizza
4. **Salvataggio** → `CreateTicketAction::execute()` crea il ticket
5. **Event** → `TicketCreatedEvent` dispatch
6. **Notifica** → PA Operator riceve notifica
7. **Conferma** → Cittadino riceve conferma tramite redirect

## Attori
- **Citizen** (reporter)
- **PA Operator** (receiver/assignee)
- **System** (notification handler)

## Regole architetturali
- `CreateTicket` Page non deve delegare alla Resource
- `mutateFormDataBeforeCreate` usa `GetTicketFormDataForPersistAction`
- `XotBaseWizardWidget` estende, non `Filament\Widgets\Widget`
- `foreignIdFor()` per le relazioni
- `UserContract` per le dichiarazioni utente

## Second Brain
- `docs/chat/citizen-report-flow.md`
- `docs/wiki/log.md` aggiornato
- Link a Issue/Discussion su GitHub

## Quality Gate
- PHPStan 0 errori su `Modules/Fixcity`
- Test `tests/Unit/CreateTicketWizardWidgetTest.php` passa
- `tests/Feature/Filament/CreateTicketWizardWidgetTest.php` passa
