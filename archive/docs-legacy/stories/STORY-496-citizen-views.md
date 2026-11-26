---
created: 2026-09-26
updated: 2026-09-26
qmd: "STORY 496 citizen views"
issues: []
discussions: []
title: "STORY-496: Citizen Views Implementation"
type: story
status: open
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/496"
discussion_link: "https://github.com/laraxot/fixcity/discussions/496"
tags: [citizen, views, ticket, folio]
---

# STORY-496 — Citizen Views Implementation

## Goal
Implementa le viste per i cittadini usando Folio pages: dashboard, elenco ticket, dettaglio ticket.

## Required Implementation

### 1. Folio Pages (resources/views/pages/citizen/)
- `dashboard.blade.php` - Citizen dashboard with stats
- `tickets/index.blade.php` - Elenco ticket cittadino
- `tickets/[ticket].blade.php` - Dettaglio singolo ticket
- `profile/edit.blade.php` - Modifica profilo cittadino

### 2. Actions (app/Actions/Citizen/)
- `GetCitizenTicketsAction.php` - Recupera ticket del cittadino
- `GetCitizenStatsAction.php` - Statistiche per dashboard
- `UpdateCitizenProfileAction.php` - Aggiorna profilo cittadino

### 3. Data Fetching
- Use existing Actions like `BuildPublicTicketsQueryAction`
- Use existing Actions like `GetCitizenRatingAggregateAction`

## Issue Link
- GitHub Issue #496
- Discussion #496

## Evidence Post-Fix Checklist
- [ ] Citizen dashboard Folio page creato
- [ ] Citizen tickets index Folio page creato
- [ ] Citizen ticket show Folio page creato
- [ ] Citizen profile edit Folio page creato
- [ ] Integration con existing Actions
- [ ] Responsive design mobile-friendly
