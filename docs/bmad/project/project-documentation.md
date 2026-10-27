---
title: "FixCity brownfield project documentation"
type: bmad-project-documentation
status: current-baseline
updated: 2026-10-13
tags: [bmad, brownfield, fixcity, architecture, second-brain]
qmd: "FixCity brownfield Laravel modular monolith modules ticket civic reporting architecture"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---

# Brownfield baseline

## Product purpose

FixCity turns fragmented municipal channels into a transparent civic workflow:
discover a report, submit a geolocated ticket, receive a tracking credential, follow
public status, and let authorized PA staff assign, work, resolve and audit the case.

## Stack and entry points

- PHP 8.4 / Laravel 13 / Nwidart modules / Filament 5.
- Front office: Folio pages, Volt/Filament widgets, Actions, theme Sixteen.
- Back office: Filament resources built on XotBase classes.
- Root app: `laravel/`; module code: `laravel/Modules/*`; theme: `laravel/Themes/Sixteen`.
- Runtime routes: `laravel/routes/web.php`, `console.php`, `ai.php`; module Folio
  pages under `Modules/*/resources/views/pages/`.
- Package ownership: root Composer is minimal; module Composer manifests are merged.

## Domain topology

`Fixcity` owns Ticket, Category, activities, assignment, tracking, ratings and
subscriptions. It composes `User`, `Geo`, `Media`, `Comment`, `Activity`, `Rating`,
`Tenant`, `Notify` and `Xot`. `Cms` owns pages/menus/content; `Lang` localization;
`Gdpr` consent; `Mobile` mobile workflows; `Job` background work; `AI`, `Blog`, `Seo`
and `UI` provide supporting platform capabilities.

## Primary flows

1. Guest discovers public tickets/map and starts a localized report flow.
2. Citizen authenticates, accepts the approved privacy semantics, enters data/location,
   attaches media, submits and receives confirmation/tracking.
3. Citizen tracks status, public timeline and follows the ticket; owner-only data is
   never disclosed through guest or other-user lookup.
4. Operator/supervisor triages, assigns, changes status with reason, comments and
   exports; activity records provide audit evidence.
5. Resolution triggers owner notification and rating eligibility.

## Current evidence and limits

PHPStan on `Modules` is green. Local SQLite/browser checks cover many public pages and
selected ownership/privacy cases. The release is still incomplete because tenant
legal sign-off, isolated staging, worker/SMTP/failed-jobs monitoring, candidate
backup/restore, full PA/citizen smoke, screen-reader review and release sign-off are
not all evidenced together. See `completion-gap-analysis.md` and `release-plan.md`.

## Documentation architecture

La documentazione è distribuita per ownership: i moduli descrivono contratti dati
e comportamento applicativo, i temi descrivono markup/layout/asset e
`bmad-output/` descrive iniziative cross-project. Gli ingressi locali sono
`README.md` e `00-INDEX.md`; le directory `wiki/`, `bmad/`, `stories/`,
`investigation/` e `visual-parity/` hanno ruoli distinti. La tassonomia completa,
il ciclo di vita e il piano di consolidamento sono in
[`docs-architecture/`](docs-architecture/README.md).

La fotografia del 2026-10-13 rileva duplicati e varianti storiche in molti
perimetri. Non sono stati cancellati o rinominati: ogni consolidamento richiede
prima una migration map, una verifica dei link e una story BMAD. QMD/Graphify
servono per trovare e collegare documenti; Second Brain conserva il perché e le
correzioni, ma non sostituisce la verifica del codice.

## Conventions

Actions contain domain operations; contracts use `*Contract`; migrations are
forward-only; module providers are manifest-declared; docs are module/theme-owned;
quality checks include PHPStan max, Pest, Pint, browser checks and wiki validation.
