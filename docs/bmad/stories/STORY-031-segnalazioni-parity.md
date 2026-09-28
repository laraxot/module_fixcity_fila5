---
title: Design Comuni Parity - Segnalazioni Elenco Page
status: to_do
priority: must

## Expected (Design Comuni Italia)

### 1. /segnalazioni-elenco (Public Report List)
**Structure:**
- Header: Main navigation (Amministrazione, Novità, Servizi, Vivere il Comune)
- Breadcrumbs: Home > Elenco segnalazioni
- Title: "Elenco segnalazioni" with subtitle showing resolved count
- Search bar with filters (Categoria, Stato, Data)
- View toggles: Mappa (Map) | Elenco (List)
- Cards grid: title, category badge, status badge, data, address
- Pagination
- CTA: "Segnala un disservizio"

### 2. Accessibility & UX
- WCAG 2.1 AA compliant
- Keyboard navigation
- Screen reader support
- Mobile responsive (grid on desktop, stacked on mobile)
- Search with autocomplete
- Filter chips that can be removed
- Clear visual hierarchy

## Current State (ITITA in blades)

### Italian Phrases Found:
1. **Header:** "Nome della Regione", "Accedi all'area personale"
2. **Navigation:** "Iscrizioni", "Estate in città", "Polizia locale", "Tutti gli argomenti >"
3. **Status labels:** "Aperta", "In corso", "Risolta" (should use __() translations)
4. **UI elements:** "Rimuovi tutti i filtri", "Mappa", "Elenco", "Nessuna segnalazione trovata"

### Gaps:
1. **Missing search functionality** - hardcoded placeholder "Cerca..."
2. **Missing real map** - placeholder with "Mappa interattiva delle segnalazioni"
3. **Category names in Italian** - should use translations
4. **Status labels in Italian** - should use __() translations
5. **Missing API integration** - hardcoded local data
6. **Missing pagination** - only shows first few items
7. **No "Segnala un disservizio" CTA** - missing main action button
8. **Italian header strings** - "Il mio Comune", "Un comune da vivere"

## BMAD Fix Plan

### Phase 1: Translations Setup
- Ensure all Italian strings have translations in `Modules/Fixcity/lang/it/`
- Category names: map `acqua`, `ambiente`, `arredo` to English translations
- Status labels: `open` → "Segnalazione aperta", `in_progress` → "In corso", `resolved` → "Risolta"
- Navigation items: "Iscrizioni" → "Subscriptions", "Polizia locale" → "Local Police"

### Phase 2: Remove Italian Hardcoded Strings
- Replace all Italian strings with `__()` translations
- Update wire:model and other HTML attributes for proper localization

### Phase 3: Improve Structure
- Add missing "Segnala un disservizio" CTA button
- Implement real map using existing GeoJSON API
- Add search functionality
- Add pagination
- Fix category structure (convert hardcoded categories to data)

### Phase 4: Styling
- Ensure Design Comuni compliant styling
- Update header with proper common logo
- Improve card layout with proper shadows and borders
- Make mobile responsive

## Implementation Details

### Files to Modify:
1. `Themes/Sixteen/Sixteen/resources/views/pages/segnalazioni.blade.php`
   - Replace Italian strings with `__()`
   - Fix category structure
   - Add missing CTA button
   - Implement real search
   - Fix map integration

2. `Modules/Fixcity/lang/it/homepage.php` - ensure translations exist
3. Add necessary CSS for Design Comuni compliance

### Quality Gates:
- [ ] All strings traduibili tramite `__()` (no Italian hardcoded)
- [ ] Search functionality with autocomplete
- [ ] Filter chips with remove capability
- [ ] Real map integration using `/api/tickets/geojson`
- [ ] Pagination
- [ ] Responsive design
- [ ] WCAG 2.1 AA accessibility
- [ ] Design Comuni parity verified