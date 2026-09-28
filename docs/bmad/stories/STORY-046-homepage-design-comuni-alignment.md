---
title: STORY-046 — Homepage Design Comuni Alignment
state: in-progress
authors: [developer-agent]
priority: critical
github_issue: https://github.com/laraxot/fixcity_fila5/issues/46
discussion: https://github.com/laraxot/fixcity_fila5/discussions/46
---

## Problem
Homepage (`/it`) non segue pattern Design Comuni standard. L'utente deve poter:
- Creare segnalazione (wizard)
- Tracciare segnalazione
- Vedere elenco segnalazioni
- Accedere al proprio profilo

## Design Comuni Reference Structure

### Pagina principale (segnalazione-elenco.html)
1. **Hero Section**:
   - Title: "Segnalazioni e pratiche"
   - Description: "Il Comune ti aiuta a segnalare"
   - Primary CTA: "Segnala" → `/it/segnalazione-crea`
   - Secondary CTA: "Traccia" → `/it/segnalazione-traccia`

2. **How it works (3 steps)**:
   - "Segnala": descrivi il problema
   - "Ti risponde il Comune": ricevi aggiornamenti
   - "Tutto completato"

3. **List/Detail area**:
   - Search/filter
   - Results list with cards
   - Map view toggle

### Pagina dettaglio (segnalazione-dettaglio.html)
- Ticket ID / Code
- Status badge
- Category
- Location
- Timeline/activities
- Comments section
- CTA "Chiudi" se risolto

### Pagina tracciamento (segnalazione-traccia.html)
- Solo codice di tracciamento
- Form di ricerca codice
- Titolo, stato, categoria, descrizione breve
- Timeline degli aggiornamenti

## Implementation Plan

### Step 1: Fix Routes
- `/it/segnalazioni` → alias `/it/tickets` (maintain both)
- `/it/segnalazione-crea` → `/it/tickets/create`
- `/it/segnalazione-traccia` → nuova route `/it/tickets/track`

### Step 2: Create Public Listing Page
- New Folio page: `/it/segnalazioni`
- Use Design Comuni components
- Integrate with GeoJSON map

### Step 3: Update Homepage
- Change CTA to point to correct routes
- Add tracking CTA
- Simplify layout per Design

### Step 4: Translation Keys
Add all missing keys for:
- `fixcity::home.hero.*`
- `fixcity::track.*`
- `fixcity::list.*`

## Quality Gate
- PHPStan: 0 errors
- Translation keys present (IT/EN)
- Responsive design
- Accessibility (WCAG 2.1 AA)
- Design Comuni parity

## Second Brain References
- `docs/wiki/rules/design-comuni-parity.md`
- `../wiki/concepts/ux-patterns-design-comuni.md`
- `../wiki/layouts/hero-section-patterns.md`