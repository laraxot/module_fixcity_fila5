---
title: "Fixcity — boundary UI Filament-first"
type: concept
status: active
created: 2026-05-28
tags: [filament, fixcity, segnalazioni, frontoffice]
related:
  - ../../../../../../docs/wiki/rules/filament-first-rule.md
  - ./frontoffice-no-standalone-livewire.md
  - ./segnalazioni-elenco-map-architecture.md
  - ../../../../Themes/Sixteen/docs/wiki/concepts/filament-first-frontoffice.md
---

# Fixcity — boundary UI Filament-first

## Perché

Il dominio **segnalazione** (FO) e **ticket** (admin Filament) condividono UX (tab, badge stato, azioni) ma boundary diversi. La regola **Filament-first** evita due implementazioni parallele (Bootstrap tab + widget admin).

## Dove si applica in Fixcity

| Superficie | Pattern Filament | Note |
|------------|------------------|------|
| Pannello admin ticket | `Filament\Forms`, `Tables`, wizard widget | SSoT form/schema |
| `/it` elenco segnalazioni | `<x-filament::tabs>` (story 065) | Pannelli: `map-lit` + Blade lista |
| Widget creazione | `TicketCreateWizard` (Filament) | No `app/Livewire` FO |

## Dati vs UI

- **GeoJSON / filtri:** `tickets.json` + `map-lit` (non è un componente Filament — OK).
- **Tab Mappa/Elenco:** UI → Filament tabs; contenuto → Lit + Blade.

## Story e GitHub

- [STORY-065](../../../../../../docs/stories/STORY-065-it-segnalazioni-filament-tabs.md)
- Issue owner: vedi sezione GitHub nella story (aggiornata con discussion governance)

## Canon esterno

- https://filamentphp.com/docs/5.x/components/overview
