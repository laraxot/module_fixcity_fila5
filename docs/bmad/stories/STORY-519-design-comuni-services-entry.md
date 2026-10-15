---
title: "Fixcity services entry aligned with Design Comuni"
type: story
status: in-progress
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, design-comuni, services, folio, accessibility]
qmd: "Fixcity service catalogue Design Comuni localized citizen journeys search accessible"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../services-catalog-gap-analysis-2026-09-27.md
  - ../../../../Themes/Sixteen/docs/bmad/services-catalog-expected-2026-09-27.md
  - ../../../../Themes/Sixteen/docs/bmad/services-catalog-correction-plan-2026-09-27.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/comuni-design-system-guidance.md
---

# Story

As a resident, I want the services page to show real Fixcity tasks so I can
report, browse or track a civic issue without following fake service links.

## Acceptance criteria

- [x] The public entry point is a Folio page and lists only verified Fixcity tasks.
- [x] The shared public layout adds skip links, site navigation and honest
      institutional footer content.
- [x] The report service detail offers a localized link to the public reports
      list alongside its primary report-submission action.
- [x] All actions have real locale-aware links to create, list and tracking flows.
- [x] Search updates visible tasks, provides a localized count and an empty state.
- [x] IT, EN, DE and ES have localized title, search label, task copy and feedback.
- [x] Placeholder phone/email contacts and unsupported active municipal services are absent.
- [x] The report card explains that guests must sign in before submitting;
      browser navigation confirms the guest redirect to the localized login.
- [x] The services entry and category directory expose only the supported
      public-reports group; its link reaches the real reports list.
- [x] The service category page no longer claims registry, tax, planning,
      social, culture or appointment services without verified content.
- [x] The rendered service hero heading and description pass WCAG AA text
      contrast (4.5:1) at all supported locales and responsive widths.
- [x] Chromium checks all four locales at 320, 390, 768 and 1440 px with no
      overflow or page errors; task filtering and destinations are verified.
- [x] Mobile task links use separate full-width 44px-minimum targets; browser
      coverage checks label fit at every supported locale and viewport.
- [ ] Pest feature contract passes once the dedicated test DB credentials are
      provisioned; the present MariaDB placeholder fails before assertions.
- [x] Production Vite build and repository wiki quality gate pass.
- [ ] Verify keyboard/screen-reader announcements manually.

## Design decision

Design Comuni v2.4.0 presents a broad municipal service catalogue and a rich
service detail. Fixcity does not contain verified data or integrations for
registry certificates, taxes, SUAP, appointments or social support. This slice
therefore exposes the real report/list/track journeys and records the full
municipal catalogue and the tenant-owned portions of the service detail
(approved terms, contacts, related services) as future content/integration
gaps. The report service sheet itself is implemented. It does not invent tenant
contacts or operational availability.

## Follow-up from full catalogue audit

The prior entry still rendered six generic municipal categories even though the
implementation plan said to remove unsupported service claims. The official
service index confirms that categories are navigational pathways into actual
service records; a link to an unfiltered report list does not implement those
services. The six claims were removed from the FixCity catalogue. It now links
to the public reports group, whose destination is the real list/map. Browser
regression coverage is in `Themes/Sixteen/tests/browser/services-catalogue.spec.mjs`.
The theme Feature suite is still blocked at bootstrap by placeholder MariaDB
credentials; its results are not represented as passing.

The final browser regression run passes **3 tests**: all 4 locales at 320, 390,
768 and 1440 px; working category/list and service-detail/guest-login journeys;
search empty/clear states; zero page errors and at least 4.5:1 contrast for the
rendered service hero. `npm run build` in `Themes/Sixteen` passes and publishes
the verified CSS. A legacy unresolved `/themes/Sixteen/images/logo.svg` Vite
warning remains unrelated to these flows.

The Design Comuni report-detail comparison also added the missing “all reports”
path beside report creation. The service-detail browser journey verifies
that action in IT/EN/DE/ES and confirms it reaches the canonical localized
`/tickets` list. The official index's sample URP contact and terms remain
excluded pending approved tenant content.

### Mobile quick-task navigation follow-up

The fresh 390 px screenshot exposed cramped first-row task links that the
overflow-only assertion did not catch. They now stack full width below 640 px;
the browser contract checks each label fits, each tap target is at least 44 px,
and larger viewports keep the compact layout. Keyboard and screen-reader
announcements still need manual review; tenant/legal approvals and test-database
credentials remain separate release gates.
