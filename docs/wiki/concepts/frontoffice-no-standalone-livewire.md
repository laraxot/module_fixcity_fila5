---
title: frontoffice — no livewire standalone
type: concept
module: fixcity
updated: 2026-05-29
related:
  - ../../../../../../docs/wiki/concepts/no-pure-livewire-outside-filament-widgets.md
  - ../../../app/Filament/Docs/frontoffice-no-standalone-livewire.md
---

# Frontoffice Fixcity — no Livewire standalone

Canon operativo (SSoT dettagliato): [app/Filament/Docs/frontoffice-no-standalone-livewire.md](../../../app/Filament/Docs/frontoffice-no-standalone-livewire.md).

## Regola

- **Vietato**: `app/Livewire/TicketList.php`, `@livewire` su Folio/CMS pub
- **Consentito**: widget `Modules/Fixcity/Filament/Widgets/*`, Blade `ticket.layout`

## `/it`

Folio `pages/index` → CMS `ticket-layout` → `pub_theme::components.blocks.ticket.layout`.

`Livewire\TicketList` **rimosso** (2026-05-29).
