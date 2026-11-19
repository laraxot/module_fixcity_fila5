---
title: "FixCity user experience plan"
type: ux-experience
status: draft-for-validation
updated: 2026-10-07
tags: [bmad, ux, journeys, fixcity, sixteeen]
qmd: "FixCity citizen operator supervisor admin user journeys screen states"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Journeys and states

## Citizen report journey

Home → public list/map → sign-in gate → privacy acknowledgement → report data →
location → media → review → confirmation/code. Loading, empty, validation, upload,
auth, network and success states must each tell the user what to do next.

## Tracking journey

Tracking form → code validation → safe public status/timeline. Owner ID lookup may
show owner-only details; guest and other-user responses must not reveal private data.
Rate limit and not-found states preserve locale and offer recovery.

## PA journey

Authenticated panel → queue/filter → inspect → assign → transition with reason →
comment/attach/export → resolve/close → notification/rating eligibility. Every
denial is explicit and every mutation has feedback plus audit evidence.

## Supervisor/admin journey

Dashboard → SLA/workload → drill-down → policy/configuration → audit/export. Empty
and delayed data states must distinguish “no data” from “not authorized”.

## Release screen inventory

Home, public map/list, public detail, auth, report wizard (all steps), confirmation,
tracking, personal tickets, PA queue/detail, assignment/status dialogs, privacy,
notification preferences and error/rate-limit pages. Verify each default/loading/
empty/error/success/disabled state in the candidate environment.

## Shared shell states

Every screen inherits and verifies: header default, language menu open/closed,
mobile navigation open/closed, search activation, guest personal-area CTA focus,
authenticated account state, footer default, footer link focus and external social
link behavior. The language menu closes with Escape and outside click and preserves
the current locale. The shell is checked at 320, 390, 768 and 1440px with keyboard
navigation and reduced motion.
