---
title: "STORY-515 — Design-Comuni service directory alignment"
story_id: STORY-515
status: completed
owner: Modules/Fixcity + Themes/Sixteen
created: 2026-09-27
updated: 2026-09-27
issue: "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussion: "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Context

The official Design-Comuni catalogue defines a service landing page, category
directory and detail page. The FixCity public page previously exposed only a
featured-card grid and its reusable service card still contained Italian
fallback copy.

Reference study:

- [Design-Comuni catalogue](https://italia.github.io/design-comuni-pagine-statiche/index.html)
- [Official services page](https://italia.github.io/design-comuni-pagine-statiche/sito/servizi.html)
- [Official service category page](https://italia.github.io/design-comuni-pagine-statiche/sito/servizi-categoria.html)
- [Official service detail page](https://italia.github.io/design-comuni-pagine-statiche/sito/servizio-dettaglio.html)
- [Official reports listing](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html)

# Acceptance criteria

- The public services page has a labelled search control and a usable
  client-side filter for featured services.
- The page exposes a visible category directory with a clear hierarchy and
  keyboard-focusable links.
- Service-card statuses, featured labels and CTA aria labels come from the
  theme translation catalogue in `it`, `en`, `de` and `es`.
- No new HTTP controller or service layer is introduced: the page remains a
  Folio view and reusable Blade component.
- Desktop and mobile rendering has no horizontal overflow, raw translation
  keys or empty action links.
- The design rationale is captured in
  `design-comuni-design-dna-2026-09-27.json` and this story.

# Implementation

Implemented the category directory, Alpine-powered search state and localized
service/task copy. Chromium verification passed for `it/en/de/es` at 390px and
1440px: HTTP 200, no JavaScript errors, no failed requests, no raw translation
keys and no horizontal overflow. View cache, PHPStan (9183 files, zero
errors), Pint and the wiki quality gate also pass.
