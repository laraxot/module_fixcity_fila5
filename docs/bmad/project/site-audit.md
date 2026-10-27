---
title: "FixCity site audit — remaining gaps"
type: bmad-audit
status: active
updated: 2026-10-14
tags: [bmad, audit, release, sixteeen, localization, accessibility]
qmd: "scope fixcity"
---

# Scope

Read-only audit of the Laravel application, Sixteen public theme, route inventory,
BMAD release documents and tests. Existing dirty worktree changes were preserved.
The audit assumes the public site is served by the Sixteen theme for multiple
municipal tenants and locales `it`, `en` and `de`.

# Findings

## Must fix

### A-01 — Blade view cache is not green

`php artisan view:cache` stops at
`Themes/Sixteen/resources/views/components/blocks/tests/segnalazione-crea.blade.php`
because `<x-geo.mappa-static>` cannot be resolved, even though the implementation
exists under `components/blocks/geo/mappa-static.blade.php`. Any release pipeline
that compiles all views can fail before deployment.

Planned story: **1.4 — view-cache-component-integrity**. The component alias was
aligned to `x-blocks.geo.mappa-static` and an extra closing parenthesis in the
component was removed. `php artisan view:cache` now completes successfully.

### A-02 — Legal route infrastructure exists, but tenant legal content is incomplete

The initial route-only check was incomplete. The Fixcity CMS/Folio content files and
existing feature tests prove localized `/privacy`, `/cookie-policy` and
`/accessibilita` pages, plus the `note-legali` and media-policy content contracts.
The real gap is publication readiness: inspected EU/local privacy and terms files
are templates or short incomplete notices, legal-notes/media-policy files are
missing, and the accessibility declaration contains null/empty owner data.

Several guest/auth/footer templates still link to inconsistent slugs or `#` anchors.
Story **6.10 — localized-legal-route-inventory** remains useful for link
normalization; stories **6.12–6.15** cover document contract, owner facts,
versioned acceptance and publication gating.

## Should fix

### A-03 — The no-Bootstrap boundary is not true across Sixteen

The repository still contains a Bootstrap import in
`resources/js/components/bootstrap-italia.js`, a Vite entry referencing
`bootstrap-italia.css`, direct `window.bootstrap` usage, and 217 view files with
`data-bs-*` references. This is broader than the already-correct cookiebar and
must be separated into an explicit runtime boundary: either migrate active public
components to Alpine/Lit/Tailwind/Flowbite/DaisyUI or classify legacy/showcase
assets as excluded from the production bundle.

Planned story: **6.11 — no-bootstrap-runtime-boundary**.

### A-04 — Legal links are inconsistent and partially hard-coded

Templates use `/privacy`, `/accessibilita`, `/note-legali`, `legal-notice`,
`legal-notes`, `#`, and fixed Italian labels in parallel. Some links use
`LaravelLocalization`, others use `url()` or `route()` names that are absent in
the current table. This can produce dead links or wrong-language navigation.
This is included in story 6.10 rather than split into another story.

# Already covered by existing backlog

G-01/G-04 are covered by 2.3 and 5.1; G-02/G-03/G-05/G-06/G-07/G-10 are covered
by 2.1, 2.2, 3.1–3.3, 4.1–4.2 and 5.1; G-08/G-09 remain explicit product
decisions; G-11–G-14 are covered by 1.2–1.3, 6.1–6.2 and 7.1–7.2. They remain
backlog/blockers until candidate evidence or owner decisions exist.

# Correction to the earlier route finding

The earlier statement that legal routes were absent is superseded by the source
inspection and feature tests. The route/content system is present; the open issue is
that its tenant inputs and several links are not production-ready.

# Verification limits

No candidate environment, browser session, screen reader, SMTP sandbox, backup
restore or legal-owner approval was available. Static/local evidence cannot certify
legal compliance or release readiness.
