---
title: "FixCity citizen notification preferences — UI comparison"
type: bmad-comparison
status: verified-baseline
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, citizen, preferences, i18n]
qmd: "FixCity preferences current locale files missing German Spanish compare expected"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ./notification-preferences-ui-expected-2026-09-27.md
  - ./notification-preferences-ui-correction-plan-2026-09-27.md
  - ./stories/STORY-013-ticket-notification-preferences.md
---

# Actual versus expected

## Source audit — 2026-09-27

The Folio page at `Themes/Sixteen/resources/views/pages/area-personale/impostazioni.blade.php`
mounts the FixCity Livewire widget. The widget has a labelled checkbox, helper
copy, save button and polite status region. Feature tests cover guest
protection, persistence and links from the personal area.

The dedicated `ticket_notification_preferences.php` catalogue exists only in
`Modules/Fixcity/lang/it` and `Modules/Fixcity/lang/en`; the module has no
dedicated German or Spanish catalogue. Consequently those citizens receive
fallback copy rather than the locale promised by the product.

An authenticated Chromium baseline on 2026-09-27 confirmed the visual impact:
all four locales return HTTP 200 and have no overflow or page errors, but DE/ES
show Italian page title, section, checkbox and help copy. At 390 and 1440 px,
the page description is also the internal metadata string “Voce menu dropdown
area personale” (EN uses the analogous dropdown metadata) instead of useful
account guidance. The form remains readable, but its user-facing hierarchy is
wrong and the locale parity is incomplete.

| Requirement | Current result | Gap |
| --- | --- | --- |
| Authenticated settings page | Folio route and Feature coverage exist | Browser responsive evidence missing |
| Labeled choice and helper copy | Accessible markup exists | Labels depend on locale catalogue |
| Page heading and description | Generic menu label/metadata rendered as content | Replace with feature-specific title and guidance |
| IT and EN | Dedicated widget catalogs present | Widget copy is localized; heading description is not useful |
| DE and ES | No dedicated widget catalog | Italian fallback; fails four-locale parity |
| Save feedback | `role=status`, `aria-live=polite` | Browser announcement and layout not checked |
| Shared-account visual QA | No screenshot evidence | Pending read-only browser pass |

## Decision

Add the missing DE/ES strings, use the existing feature-specific title and add
a feature-owned page description instead of rendering menu metadata. Add a
read-only authenticated Playwright check. Preserve preference semantics and
do not click Save in the browser test.
