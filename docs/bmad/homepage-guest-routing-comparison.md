---
title: "homepage guest routing comparison"
type: bmad-comparison
status: done
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, homepage, folio, homecontroller, loginurl]
qmd: "homepage guest routing comparison homecontroller loginurl folio"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
related:
  - ./homepage-guest-routing-expected.md
  - ./homepage-guest-routing-correction-plan.md
  - ../wiki/concepts/no-controllers-folio-volt-filament.md
---

# Homepage guest — confronto routing atteso vs incidente

## Metodo

- Log `storage/logs/laravel.log` (09:26–09:29 Europe/Rome)
- Disk: `Modules/Fixcity/app/Http/Controllers/` (vuota al momento del confronto)
- HTTP kernel su `127.0.0.1:8001/it` (Host `localhost:8001`)
- Stack Ignition utente: `HomeController@homepage` + `Undefined variable $loginUrl`

## Tabella

| Condizione attesa | Misura / evidenza | Esito |
|-------------------|-------------------|-------|
| Action Folio su `/it` | kernel: `FolioManager@handle`, name `home`, HTTP 200 | PASS (ora) |
| Nessun HomeController | cartella Controllers vuota; log 09:29 class missing | GAP intermittente |
| Nessuna rotta `it.home` Controller | Ignition utente riporta `it.home` → HomeController | GAP (rotta reintrodotta da agente parallelo / cache) |
| Blade autosufficiente | `@php $loginUrl=…` + uso in slot; log 09:27–09:28 `$loginUrl` undefined su `home`/`index` | GAP |
| CTA elenco → segnalazioni | blade punta a `/tickets` | GAP minore |
| web.php senza FO closure | file solo commenti ownership | PASS |

## Analisi causa (perché)

1. **Anti-pattern Controller**: un `HomeController@homepage` faceva `view(pub_theme::pages.home)` passando solo `recentTickets`/`stats`. La Blade tema assume URL locali; se il blocco `@php` non gira nello stesso scope del render (o la view compilata è stale), → `$loginUrl` undefined.
2. **Religione violata**: FO deve essere Folio; il Controller ombreggia `/{locale}` con nome `it.home`.
3. **Race multi-agente**: Controllers cancellati e `web.php` svuotato, ma Ignition/utente e log mostrano finestre in cui il Controller e/o la Blade fragile erano ancora attivi.

## Verdetto

**PASS** (2026-09-27 post-fix): `/it` → FolioManager `home` 200; CTA con
`localizeURL` inline (niente `$loginUrl`); Controllers Fixcity vuota; `web.php`
senza FO. Residuo: `/it/home` può risolvere a CMS `container0` (fuori scope home
root); non reintrodurre `HomeController`.
