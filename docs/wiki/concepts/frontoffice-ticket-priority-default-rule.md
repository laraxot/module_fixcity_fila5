---
title: "Frontoffice Ticket Priority Default Rule"
type: concept
confidence: high
created: 2026-05-26
updated: 2026-05-26
tags: [fixcity, ticket, wizard, priority, frontoffice, filament]
related:
  - ./ticket-wizard-steps-in-form-rule.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/frontoffice-ticket-priority-theme-boundary.md
---

# Frontoffice Ticket Priority Default Rule

Nel wizard pubblico `segnalazione-crea` la priorita' del ticket non e' una scelta del cittadino.

## Regola

- Lo step dati mostra la tipologia (`type`) come `Select` per classificare la segnalazione.
- La priorita' (`priority`) resta un valore interno con default di dominio (`TicketPriorityEnum::default()`).
- In `TicketForm::getDataSchema()` il campo `priority` deve essere `Hidden`, non `Select`.
- Il pannello admin puo' continuare a usare un select di priorita' nella resource dedicata, perche' quello e' un workflow operativo diverso.

## Perche'

La priorita' e' triage/backoffice: mostrarla nel wizard pubblico aggiunge rumore, crea un controllo visuale non previsto dalla parity Design Comuni e spinge il cittadino a fare una scelta che dovrebbe derivare dalla lavorazione interna.

## File owner

- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `Themes/Sixteen/docs/wiki/concepts/frontoffice-ticket-priority-theme-boundary.md`
