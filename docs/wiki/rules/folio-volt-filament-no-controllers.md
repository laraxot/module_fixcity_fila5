---
title: "folio volt filament no controllers"
type: rule
status: canonical
created: 2026-09-26
updated: 2026-09-27
qmd: "folio volt filament no controllers front office back office"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
tags: [fixcity, folio, volt, filament, architecture]
related:
  - ../concepts/no-controllers-folio-volt-filament.md
  - ../concepts/folio-api-no-controllers.md
  - ../../bmad/README.md
---

# Folio + Volt + Filament — NO Controllers

## Split FO / BO (KISS)

| Dove | Stack | Vietato |
|------|-------|---------|
| Front office | Folio + Volt + Filament-in-page + Actions | `Http\Controllers`, view da `Route::` |
| Back office | Filament `XotBase*` | Controller MVC paralleli al pannello |
| API pubbliche | Folio `pages/api/` + Actions | Controller API “di comodo” |

Canone esteso: [no-controllers-folio-volt-filament](../concepts/no-controllers-folio-volt-filament.md).

## Struttura

```
Modules/Fixcity/
├── routes/web.php          # vuoto (solo commento ownership)
├── resources/views/pages/  # Folio FO (+ api/)
├── app/Actions/            # business logic
└── app/Filament/           # BO
Themes/Sixteen/resources/views/pages/  # Folio chrome / home / segnalazioni
```

## Nomi route FO

Preferire path localizzati (`localizeURL`). Se serve un nome stabile
(`tickets.list`), dichiararlo nel blade Folio (`name('…')`) e mantenere
`bootstrap/cache/folio-routes.php` dopo `optimize:clear` (hit `/it` o persist Folio).
