---
title: Design Comuni Study Complete — Findings & Remainings
status: done
priority: must
date: 2026-09-27
author: BMAD + Second Brain

## Study Completed

### URLs Studied
1. ✅ https://italia.github.io/design-comuni-pagine-statiche/index.html — Homepage master template
2. ✅ https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html — Services template
3. ✅ https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html
4. ✅ All 7 segnalazione pages from user request
5. ✅ All navigation sections (Generali, Amministrazione, Novità, Servizi, Vivere il Comune, Appuntamento, Assistenza, Segnalazione)

### Structure Found (Design Comuni Index)

**Top Navigation (Fixed):**
- Logo + Comune name
- Nav links: Amministrazione | Novità | Servizi | Vivere il Comune | Prenotazione Appuntamento | Richiesta Assistenza | Segnalazione Disservizio
- Social links (X, FB, YT, IG, IN)
- Search bar
- Dark mode toggle
- Login / Area personale
- Language selector

**Main Sections (Tab Navigation):**
- Tab 1: Sito (Generali, Amministrazione, Novità, Servizi, Eventi, Appuntamento, Assistenza, Segnalazione)
- Tab 2: Flussi di servizio (Graduatoria, Permessi, Vantaggi economici, Pagamenti, etc.)

**Hero:**
- Large title (title-xxxlarge)
- Subtitle / description
- CTAs: primary + secondary
- Image / illustration
- Breadcrumbs

**Footer:**
- Multi-column layout
- Links to sections
- Credits / copyright
- Social links
- Cookie consent link

### What We Have in Fixcity (Current)
- ✅ /it — Homepage (with hero, some sections)
- ✅ /it/tickets — Ticket list (with filter/search)
- ✅ /it/tickets/create — Wizard
- ✅ /it/tickets/{id} — Ticket detail (Folio)
- ✅ /segnalazioni — Redirect to /tickets (fixed)
- ✅ Map (Leaflet) with markers
- ✅ Design Comuni footer with links
- ⚠️ Missing: Full Design Comuni navigation
- ⚠️ Missing: Services page (servizi/index.html equivalent)
- ⚠️ Missing: Appointment page
- ⚠️ Missing: Assistance page
- ⚠️ Missing: Admin/transparency page
- ⚠️ Missing: News page
- ⚠️ Missing: Events page
- ⚠️ Missing: Category icons in filters
- ⚠️ Missing: Full Breadcrumbs structure
- ⚠️ CSS not fully rebuilt with new styles

### Fix Applied (Already Done)
- ✅ Route redirect /segnalazioni → /tickets
- ✅ Map popup card CSS (STORY-027)
- ✅ BMAD docs (STORY-030 through STORY-033)
- ✅ Second Brain docs
- ✅ No controllers (Folio architecture)

### Remaining (Priority Order)
1. **Services page** `/servizi` — Create with service cards matching Design Comuni
2. **Homepage refinement** — Add collapsible nav menu, hero styling, full sections
3. **Ticket list polish** — Status filters, category icons, pagination
4. **Additional pages** — Appuntamento, Assistenza, Amministrazione, Novità, Eventi
5. **CSS rebuild** — Compile with new design-comuni-parity styles

### BMAD + Second Brain Summary
- **All rules followed**: No controllers, Folio pages + Actions, proper translations, architecture compliant
- **Quality gate passed**: PHPStan 0 errors, no Italian hardcoded
- **Documentation complete**: All stories in module docs folder, Second Brain updated
- **Methodology followed**: Analyze → Plan → Implement → Verify (every step documented)

The project is architecturally correct with BMAD + Second Brain. The Design Comuni visual parity requires the remaining pages to be created (services page is the most important next step).
