---
title: Design Comuni Parity - Segnalazioni Flow
status: to_do
priority: must

## Reference: Design Comuni Italia
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html
- https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-area-personale.html

## Expected (Design Comuni Spec)

### 1. /segnalazioni-elenco (Public List)
- Search bar with filters (categoria, stato, data)
- Cards grid with: title, categoria badge, stato badge, data, indirizzo
- Pagination
- "Nuova segnalazione" CTA prominent
- Map integration (sidebar or inline)

### 2. /segnalazione-dettaglio (Public Detail)
- Breadcrumbs: Home > Segnalazioni > Dettaglio
- Status badge + type badge + priority badge
- Title (large)
- Description (pre-formatted)
- Address + map embed (OpenStreetMap)
- Date created
- "Torna all'elenco" button
- "Apri in mappe esterne" button (if geo)

### 3. /segnalazione-01-privacy (Step 1: Privacy)
- Privacy policy modal/step
- Checkbox consenso privacy obbligatorio
- Checkbox consenso dati facoltativo
- "Avanti" button disabled until required checked
- Link to privacy policy page

### 4. /segnalazione-02-dati (Step 2: Dati)
- Form fields:
  - Tipologia (select: buche, illuminazione, rifiuti, verde, altro)
  - Titolo (text, required)
  - Descrizione (textarea, required, min chars)
  - Indirizzo (autocomplete Nominatim)
  - Allegati (file upload, max 5, images/pdf)
- Validation inline
- "Indietro" / "Avanti" navigation

### 5. /segnalazione-03-riepilogo (Step 3: Riepilogo)
- Read-only summary of all data
- Edit links per section
- "Conferma e invia" button

### 6. /segnalazione-04-conferma (Step 4: Conferma)
- Success message with codice pratica
- "Torna all'elenco" button
- "Crea nuova segnalazione" button
- Email confirmation info

### 7. /segnalazione-area-personale (My Reports)
- List of user's tickets with status
- Filter by status
- Actions: view detail, add comment, rate (if resolved)
- Pagination

## Current State (Gap Analysis)
- /it shows generic homepage, not segnalazioni list
- /it/tickets exists but not styled as Design Comuni
- Wizard exists but not 4-step Design Comuni flow
- No privacy step
- Detail page exists but missing Design Comuni styling
- Map popup improved but detail page needs work

## BMAD Fix Plan
1. Create folio pages matching Design Comuni URLs
2. Style list page as card grid with filters
3. Style detail page with breadcrumbs, badges, map embed
4. Implement 4-step wizard (privacy → dati → riepilogo → conferma)
5. Create area personale page
6. All strings via __() translations
7. No Italian hardcoded in blades

## Second Brain
- docs/bmad/stories/STORY-030-design-comuni-parity.md
- docs/chat/design-comuni-parity.md
- docs/wiki/log.md