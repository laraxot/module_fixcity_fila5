---
title: "Frontoffice Fixcity — no Livewire standalone"
type: concept
status: active
created: 2026-05-28
updated: 2026-05-28
tags: [fixcity, livewire, filament, segnalazioni]
related:
  - ../../../../../../docs/wiki/concepts/no-pure-livewire-outside-filament-widgets.md
  - ./segnalazioni-elenco-map-architecture.md
  - ./ticket-wizard-frontoffice.md
---

# Frontoffice Fixcity — no Livewire standalone

Canon progetto: [no-pure-livewire-outside-filament-widgets.md](../../../../../../docs/wiki/concepts/no-pure-livewire-outside-filament-widgets.md).

## Scopo modulo

- **Segnalazioni pubbliche:** blocchi Blade + CMS (`segnalazioni/layout.blade.php`), dati da modelli/JSON/`SegnalazioniFilterViewModel`.
- **Wizard crea segnalazione:** solo `Modules\Fixcity\Filament\Widgets\*` (es. `CreateTicketWizardWidget`).
- **Vietato:** `Modules\Fixcity\app\Livewire\TicketList` e mount `@livewire(TicketList::class)` su Folio.

## `/it` (home)

- JSON `1.json`, `slug: home`, tipo `segnalazioni-layout`.
- Non duplicare markup Folio: niente secondo breadcrumb/heading fuori dal blocco.

## Backlink

- [segnalazioni-elenco-map-architecture.md](./segnalazioni-elenco-map-architecture.md)
- [docs/stories/STORY-058](../../../../../../docs/stories/STORY-058-it-segnalazioni-elenco-html-visual-parity.md)
