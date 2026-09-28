---
title: "STORY-513 — Demo visuale guest riproducibile"
type: story
status: done
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, demo, guest, ui, ux, puppeteer, playwright, i18n, seeder]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
---

# User story

Come visitatore voglio aprire FixCity in una demo pulita e vedere una pagina
completa, leggibile e tradotta, così posso capire il servizio senza conoscere
l'architettura interna.

## Acceptance criteria

- [x] Seed demo: `FixcityDatabaseSeeder` → utenti + profili + categorie + 20 ticket `DEMO-*`
- [x] `/it` e `/en` 200 con copy da `pub_theme::home.*` (niente italiano hardcoded in Blade FO)
- [x] `/it/segnalazioni` e `/en/segnalazioni` 200 (lista FO, non CMS `[container0]`)
- [x] `/it/tickets/create` redirect login (middleware CMS `auth`) — flusso guest → login
- [x] `php artisan serve` + `npm run dev` (vite watch senza ELOOP symlink `.claude`)
- [x] Smoke HTTP: `bash scripts/guest-fo-http-smoke.sh` → 8/8 PASS
- [x] Screenshot Puppeteer: `/tmp/fixcity-visual/it-home-desktop-v3.png` (+ mobile); CTA contrast OK; eyebrow FixCity
- [x] Home Design Comuni (no hero Tailwind custom); bug box bianco CTA risolto
- [x] Seed ripetibile verificato su database demo: 3 utenti, 13 categorie, 20 ticket
      `DEMO-*`, attività/commenti e GeoJSON aggiornato, exit code 0
- [x] Crawler Chromium verificato su IT/EN/DE/ES: rotte canoniche 200, redirect guest
      locali, zero overflow, chiavi raw, errori JS o asset falliti
- [x] `auth`, categorie, privacy, servizio report e alias legacy risultano localizzati
      o intenzionalmente bloccati con 404

## Note di tooling

- Playwright MCP e Puppeteer MCP sono configurati in [`.mcp.json`](../../../../../../.mcp.json).
- Chromium host: libs via `apt-get download` + `LD_LIBRARY_PATH=/tmp/chromium-libs/root/usr/lib/x86_64-linux-gnu` (workaround senza sudo).
- Script: `laravel/scripts/guest-fo-http-smoke.sh`, `laravel/scripts/guest-fo-smoke.mjs`.
- Fix visuale: [homepage-guest-visual-fix](../../../../Themes/Sixteen/docs/wiki/concepts/homepage-guest-visual-fix.md).
- Audit finale: [design-comuni-guest-crawl-2026-09-27](../design-comuni-guest-crawl-2026-09-27.md).

## Collegamenti

- [demo-tickets-presentation-seed](../../wiki/concepts/demo-tickets-presentation-seed.md)
- [actor-flow-map](../../wiki/concepts/actor-flow-map.md)
- [STORY-508](./STORY-508-confirmation-tracking-fo.md)
