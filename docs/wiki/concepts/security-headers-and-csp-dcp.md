---
title: "Intestazioni di sicurezza HTTP e CSP (DCP-139)"
type: concept
tags: [security, headers, csp, hsts, geolocation, permissions-policy, livewire, alpine, bmad]
created: 2026-10-13
updated: 2026-10-13
story: dcp-139
related:
  - ../../stories/dcp-139-intestazioni-di-sicurezza-http-e-hardening-per-la-produzione.story.md
  - ../../../../../../bashscripts/docs/visual-comparison/net-hosts-probe.md
  - ../../../../../../bashscripts/docs/tools/production-checklist.md
qmd: "security headers csp content security policy report-only enforce hsts permissions-policy geolocation self x-powered-by securityheaders middleware net-hosts-probe unsafe-eval alpine livewire dcp-139"
status: draft
---

# Intestazioni di sicurezza HTTP e CSP

**Dove:** `laravel/app/Http/Middleware/SecurityHeaders.php`, registrato con `$middleware->append()` in `laravel/bootstrap/app.php`; valori in `laravel/config/security.php`, sezione `headers`. Un solo middleware, globale (vale per Filament e Livewire).

## Regole da non rompere
- `Permissions-Policy` deve avere `geolocation=(self)`: con `geolocation=()` il pulsante «Usa la tua posizione» del passo 2 non funziona. Lo verifica `Modules/Fixcity/tests/Feature/Http/SecurityHeadersTest.php`.
- La CSP completa e' in `Content-Security-Policy-Report-Only` (default `SECURITY_CSP_MODE=report-only`): nulla si blocca, le violazioni stanno nella console. Solo `frame-ancestors 'self'` e' applicata. Prima di `enforce`: `node bashscripts/tools/visual/net-hosts-probe.mjs ...` deve dare zero violazioni sulle pagine reali.
- La lista dei domini viene dalla misura, non dall'intuito: Google Fonts (`fonts.googleapis.com`, `fonts.gstatic.com`), tessere `*.tile.openstreetmap.org`, `nominatim.openstreetmap.org`. Una dipendenza nuova che carica da un host terzo la rompe in `enforce`.
- `'unsafe-inline'` e `'unsafe-eval'` negli script servono davvero (Alpine/Livewire usano `eval`, Blade ha script inline): provato togliendoli, 304 violazioni `eval` e 28 script inline. Toglierli e' un progetto (build CSP di Alpine, nonce).
- HSTS solo su richieste HTTPS (`$request->isSecure()`, dietro proxy conta `trustProxies`); senza `includeSubDomains` e `preload`.
- `X-Powered-By`: `header_remove()` nel middleware (il server `php artisan serve` lo mette perche' `expose_php=On` nella CLI; FPM e Apache l'hanno `Off`).

## Trappola: config in cache
`bootstrap/cache/config.php` esiste: dopo aver cambiato `config/security.php` serve `php artisan config:cache` e, subito, le curl di sentinella (`/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale` a 200). Con la cache vecchia il middleware dava solo `nosniff`, `X-Frame-Options`, `Referrer-Policy` (valori di ripiego nel codice).

## Verifica
`bash bashscripts/tools/security/production-checklist.sh <https://origine> --env-file <.env>`: 19/19 sull'ambiente di produzione simulato con TLS, 13/19 sul server di sviluppo (atteso).
