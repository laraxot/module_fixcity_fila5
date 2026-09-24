---
title: frontoffice — no livewire standalone
type: concept
module: fixcity
updated: 2026-05-29
related:
  - ../../../../../../docs/wiki/concepts/no-pure-livewire-outside-filament-widgets.md
  - ../../../../../../docs/wiki/concepts/filament-widget-vs-livewire-philosophy.md
  - ./implementation-guide-ticket-infolist.md
---

# Fixcity — frontoffice senza Livewire standalone

## Scopo

L’elenco segnalazioni su `/it` è **presentazione CMS + Blade**, non un componente `app/Livewire/*`. Lo stato interattivo (wizard, tabelle admin) resta nei **widget Filament**.

## Rimosso (non reintrodurre)

| Asset | Motivo |
|-------|--------|
| `app/Livewire/TicketList.php` | Volt + `$tickets` non inizializzata → 500; duplicava layout CMS |
| `View/Components/Blocks/TicketList*` | Orfano; sostituito da `pub_theme::components.blocks.ticket.layout` |
| `@livewire(Modules\Fixcity\Livewire\TicketList::class)` | Vietato su Folio/pubblico |

## Percorso canonico `/it`

1. Folio: `Themes/Sixteen/resources/views/pages/index.blade.php` → `<x-page slug="home" />`
2. CMS: `config/local/fixcity/database/content/pages/1.json` → `type: ticket-layout`, `view: pub_theme::components.blocks.ticket.layout`
3. View tema: `resources/views/components/blocks/ticket/layout.blade.php`
4. Dati: query/actions nel blocco o JSON demo; filtri: `BuildSegnalazioniFilterAggregateAction`
5. Mappa: `map-lit` (modulo Geo), non Volt `ticket_list`

## Naming

- Codice / blocchi / view: **`ticket`**
- Label italiana “segnalazioni”: solo in `lang/*/ticket.php` (`fixcity::ticket.*`)

## Widget Filament (consentiti)

- `CreateTicketWizardWidget` — creazione segnalazione
- `TicketListWidget` — solo contesti Filament/panel admin, non homepage pub

## Story

- [STORY-059](../../../../../../docs/stories/STORY-059-it-ticketlist-500-uninitialized-fix.md)
- [STORY-060](../../../../../../docs/stories/STORY-060-no-standalone-livewire-frontoffice.md)
