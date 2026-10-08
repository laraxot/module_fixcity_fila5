---
updated: 2026-09-27
qmd: "no controllers folio volt filament front office back office"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
title: "No Controllers — Solo Folio + Volt + Filament"
type: concept
confidence: high
created: 2026-05-29
tags: [fixcity, architecture, controllers, folio, volt, filament, bmad, second-brain]
related:
  - concepts/fixcity-best-practices.md
  - concepts/fixcity-architecture-contract-2026-09-26.md
  - concepts/folio-api-no-controllers.md
  - ../rules/folio-volt-filament-no-controllers.md
  - ../../bmad/homepage-guest-expected-visual.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/no-controllers-folio-volt-filament.md
  - ../../../../../docs/wiki/rules/no-controllers-rule.md
  - ../../../../../docs/second-brain/process/folio-volt-app-pages.md
---

# No Controllers — Solo Folio + Volt + Filament

## Perché (religione del prodotto)

FixCity separa i canali per responsabilità, non per gusto stilistico:

| Canale | Stack | Perché |
|--------|-------|--------|
| **Front office** (cittadino / guest) | **Folio + Volt + Filament** (widget in pagina) + **Actions** | pagina = file, interattività senza Controller, logica in Actions |
| **Back office** (PA / admin) | **Filament** (`XotBase*` Resources / Pages / Widgets) | pannello unico, policy, form e tabelle già risolti |
| **Business logic** | `app/Actions/*Action.php` | testabile, riusabile da FO e BO |

**Mai** `Modules\Fixcity\Http\Controllers\*` né `Route::get(..., [HomeController::class, ...])`.
Un Controller FO (es. `HomeController@homepage` + `route('tickets.list')`) è debito: ombra Folio, 500 su nomi non Illuminate, query fuori Action.

## Regola permanente

```
MAI:  Modules/Fixcity/app/Http/Controllers/**
MAI:  Route web che puntano a Controller o che restituiscono view
MAI:  services layer al posto di Actions
```

`routes/web.php` del modulo resta **senza** closure FO: solo commento di ownership.
Le pagine vivono in Folio (tema Sixteen e/o `Modules/Fixcity/resources/views/pages`).

## Cosa fare

### FO — pagina pubblica (tema o modulo)

```blade
{{-- Themes/Sixteen/resources/views/pages/index.blade.php --}}
@php
use function Laravel\Folio\name;
name('home'); // o nome dedicato; non rubare nomi altrui senza bisogno
@endphp
<x-pub_theme::layouts.app>...</x-pub_theme::layouts.app>
```

Link FO: `LaravelLocalization::localizeURL('/segnalazioni')` — preferito a
`route()` quando il nome è solo Folio (resolveMissing). Nomi Folio stabili
(es. `tickets.list`) vivono nel blade Folio + cache `bootstrap/cache/folio-routes.php`.

### FO — interattività

```blade
@volt('fixcity.ticket-filters')
{{-- stato Livewire, niente Controller --}}
@endvolt
```

### FO — API JSON

Folio sotto `resources/views/pages/api/` + Action. Vedi [folio-api-no-controllers](./folio-api-no-controllers.md).

### BO — admin

Solo Filament `app/Filament/**` che estende `XotBase*`. Nessun Controller admin custom.

### Logica

```php
final class BuildPublicTicketsQueryAction { public function execute(...): Builder { ... } }
```

## Anti-pattern (incidenti reali)

| Sintomo | Causa | Correzione |
|---------|-------|------------|
| `Route [tickets.list] not defined` su `/it` | `HomeController` + Blade con `route('tickets.list')` senza nome Illuminate | Folio tema per `/it`; nome `tickets.list` su Folio `tickets/index`; niente Controller |
| Loop `/segnalazioni` ↔ `/tickets` | closure in `routes/web.php` che ombreggia Folio | svuotare web.php FO; SSoT elenco = Folio `segnalazioni` |
| `Target class HomeController does not exist` | rotta web rimasta dopo delete Controller | rimuovere rotta; non ricreare il Controller |

## Storico

- Maggio 2026: API ticket migrate a Folio
- 2026-09-27: home guest — rimosso percorso `HomeController`; trilogy BMAD homepage; regola riaffermata in second brain
