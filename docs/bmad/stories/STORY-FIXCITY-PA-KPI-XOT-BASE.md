---
title: "FixCity Filament widgets use Xot base"
type: story
status: done
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, filament, xot, dashboard]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# STORY — Widget FixCity sulle basi Xot

## Contesto

I riepiloghi volumi ticket e SLA estendevano direttamente
`Filament\Widgets\StatsOverviewWidget`; il widget legacy `CreateTicketWidget`
estendeva direttamente `Filament\Widgets\Widget` e duplicava i trait form/action.
Entrambi i casi bypassavano il livello condiviso del progetto.

## Criteri di accettazione

- [x] I widget KPI e SLA estendono
      `Modules\Xot\Filament\Widgets\XotBaseStatsOverviewWidget`.
- [x] `CreateTicketWidget` estende `XotBaseWidget` e riusa i trait/contratti
      forniti dalla base condivisa.
- [x] KPI, traduzioni, descrizioni e colori esistenti restano disponibili.
- [x] I widget vengono renderizzati da Livewire nel pannello PA.
- [x] PHPStan Modules e quality gate wiki passano.

## Implementazione e verifica

I due riepiloghi usano ora `XotBaseStatsOverviewWidget`; il widget form legacy
usa `XotBaseWidget`. La regressione è coperta
da `tests/Feature/Filament/TicketOverviewWidgetsTest.php`, che verifica la base
architetturale, il render dei dati KPI e lo stato vuoto SLA. Pest: 4 test / 19
asserzioni con database SQLite effimero. I test Livewire di render/validazione
del form legacy: 8 test / 33 asserzioni. Suite FixCity completa: 374 test / 1.594
asserzioni. PHPStan Modules senza errori e gate wiki conforme. La verifica visuale
browser richiede un runtime Chromium disponibile; non è stata dichiarata sulla
base del solo render Livewire.
