---
title: "FixCity UX design contract"
type: ux-design
status: draft-for-validation
updated: 2026-10-07
tags: [bmad, ux, accessibility, design-comuni, sixteeen]
qmd: "FixCity Sixteen Design Comuni WCAG accessibility design contract"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Visual contract

## Principles

Clear, trustworthy and civic. Reuse Sixteen/Design Comuni patterns, make status and
next action explicit, preserve keyboard focus and never rely on color alone.

## Tokens

Use the existing Sixteen/Bootstrap Italia-aligned tokens as the source of truth; do
not invent a second palette. Body text is at least 16px on mobile, spacing follows an
8px rhythm, focus is a visible 2px outline, and touch targets are at least 44×44px.
Validate every text pair at 4.5:1 and UI pair at 3:1.

## Critical components

Buttons, form fields, map/list tabs, ticket status badge, timeline, media upload,
validation summary, notification feedback, navigation and PA table must define
default/hover/focus/disabled/loading/error/success states. The report wizard must
retain data after validation or upload failure and announce errors via `aria-live`/
`aria-describedby`.

## Responsive contract

Test 320, 390, 768 and 1440px. At mobile, wizard fields and primary actions stack;
map/list remains usable without horizontal scrolling; reduced-motion users receive
no essential information only through animation.

## Acceptance

Keyboard-only completion, screen-reader labels, focus restoration, contrast, 320px
no-overflow and localized copy are release gates for the citizen flow and tracking.

## Shared shell contract (2026-10-12)

Header and footer are CMS sections rendered by Sixteen and populated by JSON. The
header uses a two-level civic green shell; the footer uses a dark institutional shell.
No second Design Comuni layout or runtime Bootstrap Italia asset is allowed.

The language selector is an Alpine button/menu pair with `aria-expanded`, `aria-controls`,
`role=menu`, `aria-current` and a visible focus ring. Icon links and icon-only controls
have `aria-label`, no decorative default underline and at least 44x44px hit area.
Footer headings and links are explicitly light on the dark background so later global
CSS cannot silently create a low-contrast pair. These rules are locked by
`laravel/Themes/Sixteen/docs/bmad/wcag/header-footer-audit.md`.
