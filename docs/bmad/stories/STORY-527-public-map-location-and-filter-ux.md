---
id: STORY-527
title: "Public tickets map location control and filter contrast"
type: story
status: done
module: Fixcity
created: 2026-10-07
updated: 2026-10-07
priority: Must
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/54"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/54"
---

# Story

As a citizen, I want the public tickets map to locate me and the filter reset
control to remain readable, so I can explore nearby reports without confusion.

## Acceptance criteria

- [x] `/it/tickets` exposes the active `ticket-list` page scope so the existing
      Design Comuni reset-button rules apply to the real Folio page.
- [x] “Rimuovi filtri” has white text and a visible keyboard focus state on the
      green primary button.
- [x] “Usa la mia posizione” retries after an error and reports insecure-context,
      denied-permission, timeout and unsupported-browser states accessibly.
- [x] The map build is published through `public_html/themes/Sixteen`.
- [x] Translation smoke checks continue to resolve `user::login.no_account` as
      a string; no controller or service layer is added.
- [x] Local development exposes the same application at
      `https://192.168.1.40:8080/it/tickets`, allowing browser geolocation after
      accepting/trusting the local certificate.

## Root-cause evidence

- The tickets Folio page rendered `<body>` without `data-page="ticket-list"`,
  so the already-present scoped CSS never matched `#block-clear-filters`.
- The location control relies on the browser Geolocation API. Chromium blocks
  it on `http://192.168.1.40`; production must use HTTPS (localhost is the
  development exception). The control now explains that state instead of
  failing silently and can be retried after permission errors.
- `public_html/themes/Sixteen` is intentionally a symlink to
  `../../laravel/Themes/Sixteen/public/`, keeping Vite output and the web root
  on the same asset tree.
- The browser cannot show the geolocation prompt on the original LAN HTTP URL;
  `laravel/scripts/dev-https-proxy.mjs` keeps Laravel on `127.0.0.1:8081` and
  terminates TLS on port `8080` with an IP-SAN certificate.

## Local HTTPS runbook

From `laravel/`, run:

```bash
php artisan serve --host=127.0.0.1 --port=8081
node scripts/dev-https-proxy.mjs 192.168.1.40:8080 127.0.0.1:8081
```

Then open:

`https://192.168.1.40:8080/it/tickets`

The certificate is intentionally local and expires after 30 days. The browser
will show a certificate warning because it is not signed by a public CA; accept
the warning for this development machine before retrying “Usa la mia posizione”.

## Verification record — HTTPS follow-up — 2026-10-07

- `laravel/scripts/dev-https-proxy.mjs` is active: PHP upstream on
  `127.0.0.1:8081`, TLS proxy on `192.168.1.40:8080`.
- Certificate SAN covers `192.168.1.40` and `localhost`.

## Verification record — 2026-10-07

- `npm run build` passed in `laravel/Themes/Sixteen` and refreshed the symlinked
  production assets.
- `/it/tickets` returned HTTP 200; its HTML contains `data-page="ticket-list"`
  after the view change and the reset label remains localized.
- `php -l` passed for the touched Italian translation files.
- `php artisan tinker` resolved `__('user::login.no_account')` to the expected
  Italian string.
- `bash bashscripts/quality-gates/verify-llm-wiki.sh` remains red on pre-existing
  repository-wide frontmatter and merge-marker debt; no unrelated docs were
  changed to conceal that baseline.
