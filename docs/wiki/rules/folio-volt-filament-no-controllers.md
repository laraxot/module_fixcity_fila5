---
name: folio-volt-filament-no-controllers
description: FixCity usa Folio + Volt + Filament, mai controller MVC per pagine pubbliche
metadata:
  type: reference
  canonical: true
---

# Folio + Volt + Filament — NO Controllers

## Regola

**Mai usare controller MVC per pagine pubbliche.** FixCity utilizza una stack moderna:

- **Folio** — Routing e pagina basato su file (pagina = file)
- **Volt** — Componenti Blade interattivi con stato reattivo (Alpine.js)
- **Filament** — Widget e pannelli admin

## Struttura corretta

```
laravel/Modules/Fixcity/
├── routes/                 # Solo route file per API (se necessario)
│   └── api.php            # API endpoints solo, mai web.php
├── resources/views/pages/  # Folio: pagina = blade
│   └── ticket-list.blade.php
├── resources/views/components/blocks/  # Blade components riusabili
└── app/Filament/           # Widget admin Filament
```

## Per le API

Se servono endpoint API, vanno in `app/Http/Controllers/Api/` SOLO per:
- GeoJSON
- JSON response per mappe
- Integrare con sistemi esterni

**Mai** per:
- Rendering view
- Logic di business frontoffice
- Gestione form pubblici (usare Volt)

## Per le pagine

Usare **Folio**:
- Route automatiche da `resources/views/pages/**`
- Nessun `web.php` necessario
- Blade + Volt per interattività

## Per l'admin

Usare **Filament**:
- Widget in `app/Filament/Widgets/`
- Resource in `app/Filament/Resources/`
- Nessun controller necessario

## Vedi anche

- [[folio-routing-pattern]]
- [[volt-component-pattern]]
- [[filament-widget-pattern]]