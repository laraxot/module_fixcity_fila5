---
title: Design Comuni Complete Parity — BMAD Master Plan
status: to_do
priority: must
date: 2026-09-27

## References
- https://italia.github.io/design-comuni-pagine-statiche/index.html — Homepage template index
- https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html — Services templates index

## Design Comuni Section Structure (Master Template)
1. **Generali** — General info, municipality data
2. **Amministrazione** — Administration, transparency, organigrams
3. **Novità** — News, announcements
4. **Servizi** — Service catalog, how to request
5. **Vivere il Comune** — Events, sports, culture
6. **Prenotazione Appuntamento** — Appointment booking
7. **Richiesta Assistenza** — Help/contact forms
8. **Segnalazione Disservizio** — ⭐ OUR DOMAIN (Fixcity)
9. **Other services** — Graduatoria, Permessi, Vantaggi economici, Pagamenti (pagoPA)

## Current Fixcity State vs Design Comuni Expected

### 1. Homepage (`/` or `/it`)
**Design Comuni Expected:**
- Navigation bar with "Indice templates" collapsible menu
- Tab navigation: Sito | Flussi di servizio
- Hero section with municipality name
- "Segnalazione Disservizio" prominently in navigation
- Sections: Generale, Amministrazione, Novità, Servizi, Eventi, Appuntamento, Assistenza, Segnalazione
- Footer with links, social, credits

**Current Fixcity:**
- ✅ Header exists with FixCity branding
- ✅ Navigation exists (Home, Segnalazioni, etc.)
- ❌ Missing "Indice templates" collapsible menu
- ❌ Missing tab navigation
- ❌ Missing multiple sections (Amministrazione, Novità, Servizi, etc.)
- ❌ Missing proper Design Comuni footer
- ❌ Missing hero section styling
- ❌ Missing "Prenotazione Appuntamento" section
- ❌ Missing "Richiesta Assistenza" section

### 2. Services Page (`/servizi`)
**Design Comuni Expected:**
- Card grid of service categories:
  - Graduatoria (ranking/elections)
  - Permessi e autorizzazioni (permits)
  - Vantaggi economici (economic benefits)
  - Pagamenti dovuti (pagoPA)
  - Servizi di pagamento (payment services)
  - E-RATA, SUAP, Sportelli, etc.
- Each card has icon, title, description, CTA

**Current Fixcity:**
- ❌ No services listing page exists
- ❌ No service cards
- ❌ No category structure for services

### 3. Segnalazione Disservizio (Our Domain)
**Design Comuni Expected:**
- Search bar with filters
- Map view / List view toggle
- Cards with: title, category badge, status badge, date
- CTA: "Segnala un disservizio"
- Pagination
- "Area Personale" link

**Current Fixcity:**
- ✅ Has `segnalazioni.blade.php` with search/filter
- ✅ Has map toggle
- ✅ Has cards
- ✅ Has CTA
- ⚠️ Needs Design Comuni styling polish
- ⚠️ Missing proper breadcrumbs
- ⚠️ Missing status filters
- ⚠️ Missing category icons

## BMAD Fix Plan — Master Implementation

### Phase 1: Homepage Complete Redesign
1. Create folio page `/` with Design Comuni structure
2. Add collapsible navigation menu ("Indice templates")
3. Add tab navigation (Sito | Flussi di servizio)
4. Add hero section with municipality name + badge
5. Add all sections: Generale, Amministrazione, Novità, Servizi, Eventi, Appuntamento, Assistenza, Segnalazione
6. Add proper Design Comuni footer
7. Add social links, credits

### Phase 2: Services Page
1. Create `/servizi` folio page
2. Add service card grid
3. Categories: Graduatoria, Permessi, Vantaggi economici, Pagamenti
4. Each card: icon, title, description, CTA
5. Responsive grid layout

### Phase 3: Segnalazioni Enhancement (Our Domain)
1. Improve `segnalazioni.blade.php` with Design Comuni styling
2. Add proper breadcrumbs
3. Add status filters (Aperta, In corso, Risolta)
4. Add category icons
5. Add "Area Personale" link
6. Add statistics sidebar (total, resolved, in-progress)
7. Add map integration (already has Leaflet)

### Phase 4: Additional Pages
1. Create `/appuntamento` (Appointment booking)
2. Create `/assistenza` (Help/contact)
3. Create `/amministrazione` (Transparency)
4. Create `/novita` (News)
5. Create `/eventi` (Events)

### Phase 5: Folio Pages for All Sections
Each section must have:
- Proper blade.php with name() directive
- Folio routing
- __() translations for all strings
- Design Comuni CSS classes
- Responsive layout
- No Italian hardcoded strings

### Files to Create/Modify
1. `Themes/Sixteen/Sixteen/resources/views/pages/index.blade.php` — New homepage
2. `Themes/Sixteen/Sixteen/resources/views/pages/servizi.blade.php` — New services page
3. `Themes/Sixteen/Sixteen/resources/views/pages/segnalazioni.blade.php` — Enhanced
4. `Themes/Sixteen/Sixteen/resources/views/pages/appuntamento.blade.php` — New
5. `Themes/Sixteen/Sixteen/resources/views/pages/assistenza.blade.php` — New
6. `Themes/Sixteen/Sixteen/resources/views/pages/amministrazione.blade.php` — New
7. `Themes/Sixteen/Sixteen/resources/views/pages/novita.blade.php` — New
8. `Themes/Sixteen/Sixteen/resources/views/pages/eventi.blade.php` — New
9. `Themes/Sixteen/Sixteen/resources/css/app/design-comuni-parity.css` — New CSS
10. `Modules/Fixcity/lang/it/homepage.php` — Add new translations

### Quality Gate
- All strings via __() (no Italian hardcoded)
- Design Comuni CSS classes applied
- Responsive layout (mobile-first)
- WCAG 2.1 AA accessibility
- All routes registered in Folio
- PHPStan 0 errors
- Test pass

## Second Brain Integration
- `docs/bmad/stories/STORY-033-design-comuni-master.md`
- `docs/bmad/stories/STORY-034-services-page.md`
- `docs/bmad/stories/STORY-035-segnalazioni-enhanced.md`
- `docs/chat/design-comuni-complete.md`
- `docs/wiki/log.md` updated