---
title: "FixCity citizen notification preferences — expected UI"
type: bmad-ux-contract
status: approved
module: Fixcity
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, citizen, preferences, i18n, accessibility]
qmd: "FixCity citizen email notification preferences localized responsive expected UI"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ./stories/STORY-013-ticket-notification-preferences.md
  - ./notification-preferences-ui-comparison-2026-09-27.md
  - ./notification-preferences-ui-correction-plan-2026-09-27.md
---

# Expected experience

## Actor and route

An authenticated citizen opens `/{locale}/area-personale/impostazioni` from
their personal area. Guests are redirected to the login page in the same
locale. This page manages only FixCity email updates; in-app notifications stay
available.

## Content and behavior

- A localized, feature-specific page heading explains that this is notification
  settings, followed by a useful sentence about the email choice.
- A named section contains one labelled checkbox for ticket-update email,
  concise helper copy that in-app notifications remain enabled, and a save
  button.
- The current saved choice is checked on entry. Saving gives an accessible,
  localized status announcement.
- The user can operate the checkbox and save button by keyboard and touch.
- No UI implies email delivery when the address is unverified; sending policy
  remains governed by the verified-email rule in STORY-013.
- IT, EN, DE and ES have complete page and widget copy with no raw keys or
  fallback-language fragments or internal translation metadata shown as
  user-facing copy.
- At 320, 390, 768 and 1440 px, the page has no horizontal overflow; controls
  remain visible and usable.

## Boundaries

Visual checks must not submit the form or mutate a shared citizen account.
Provider delivery, SMTP, legal approval, and screen-reader sign-off remain
separate release gates.

## Definition of done

- [ ] All supported locales render complete localized copy.
- [ ] Authenticated page renders and guest access remains protected.
- [ ] Responsive and keyboard checks pass at the specified widths.
- [ ] Browser verification is read-only and records screenshots for mobile and
  desktop.
