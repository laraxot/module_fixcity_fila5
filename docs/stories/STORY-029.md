# STORY-029: Pagina ticket-list map-lit

**Epic:** EPIC-005  
**Priority:** Should  
**Story Points:** 8  
**Status:** Completed  
**Assigned To:** Kilo  
**Created:** 2026-06-01  
**Sprint:** Sprint 6

---

## User Story

As a **citizen**  
I want to **view and filter service reports on a map with sidebar filters**  
So that **I can find nearby issues and understand problem types in my area**

---

## Description

### Background
La pagina ticket-list deve mostrare una mappa interattiva con segnalazioni puntuali e un filtro laterale per tipologie. HTML parity con Design Comuni richiede filtri funzionanti su desktop/mobile e tab mappa/lista operativi.

### Scope
**In scope:**
- Filtri sidebar con colori e icone per ogni tipologia
- Toggle desktop ↔ mobile filter sync
- Pulsante filtro mobile (it-funnel icon)
- Tab mappa/lista con `x-data` Alpine wrapper
- Dati filtri da JSON (home.json, tests.ticket-list.json)
- Fallback a dati vivo dal DB via SegnalazioniFilterViewModel

**Out of scope:**
- Marker personalizzati con icone inline
- Geolocalizzazione automatica
- Filtro per intervallo data

### User Flow
1. Cittadino naviga su `/it` o `/it/tests/ticket-list`
2. Vede heading con breadcrumb, tabs mappa/lista
3. Desktop: filtri visibili in sidebar con checkbox
4. Mobile: icona funnel apre modale filtri
5. Cittadino seleziona tipologia filtro
6. Evento `filter-type-changed` propaga selezione su mappa
7. Mappa GeoJSON si aggiorna con ticket filtrati
8. Tab "Mappa" è preselezionato di default

---

## Acceptance Criteria

- [x] Homepage `/it` mostra layout ticket con filtri sidebar
- [x] Filtri sidebar hanno icone e colori per tipologia
- [x] Checkbox filtri sincronizzate desktop ↔ mobile
- [x] Evento `filter-type-changed` dispatcha su cambio filtro
- [x] Tab "Mappa" preselezionato per default
- [x] Pulsante mobile funnel apre modale filtri
- [x] Dati filtri da JSON (`color`, `icon`) in `main_content.filters.items`
- [x] Fallback a DB `SegnalazioniFilterViewModel` quando JSON vuoto
- [x] `view()->exists($block->view)` con fallback errore

---

## Technical Notes

### Components
- **Blade:** `pub_theme::components.blocks.ticket.layout` (wrapper Alpine)
- **Blade:** `pub_theme::components.blocks.ticket.filters-sidebar`
- **Blade:** `pub_theme::components.blocks.ticket.tabs`
- **VM:** `Modules/Fixcity/app/ViewModels/TicketLayoutViewModel.php`
- **VM:** `Modules/Fixcity/app/ViewModels/SegnalazioniFilterViewModel.php`
- **JSON:** `laravel/config/local/fixcity/database/content/pages/home.json`
- **JSON:** `laravel/config/local/fixcity/database/content/pages/tests.ticket-list.json`

### Data Flow
```
page.blade.php → $blocks = Page::getBlocksBySlug() → 
  layout.blade.php → $vm = app(TicketLayoutViewModel::class, ['data' => $data]) →
  filterItems(): JSON filters → DB fallback
```

### Changes Made
1. `filters-sidebar.blade.php` - Fixato typo `categoy-list` → `category-list`
2. `home.json` - Aggiunto block ticket-layout con filtri (color/icon)
3. `tests.ticket-list.json` - Aggiunti `color` e `icon` ai filtri
4. `x-page.blade.php` - Modificato per `$block->data` diretto (tests/[slug])
5. `page.blade.php` - Già corretto con merge dati
6. SVG icon - Rimosse dimensioni width/height per rendering corretto

### Properties Schema
Ogni filtro richiede in JSON:
```json
{
  "id": "emergency",
  "label": "Emergenza",
  "count": 25,
  "value": "emergenza",
  "color": "#d00000",
  "icon": "it-alert-circle"
}
```

---

## Dependencies

**Prerequisite Stories:**
- STORY-009 (Elenco segnalazioni cittadino)

**External Dependencies:**
- sprites.svg con icon `it-alert-circle`, `it-car`, `it-leaf`

---

## Definition of Done

- [x] Code implemented in blade/json/view-model files
- [x] Filri visibili nella sidebar
- [x] Colori/icone alignment a Design Comuni
- [x] Dati JSON funzionanti
- [x] Mobile modal trigger funzionante

---

## Progress Tracking

**Status History:**
- 2026-06-01: Completed - Filtri sidebar operativi, JSON aggiornato

**Actual Effort:** ~4 ore

---

## GitHub (tracciamento)

| Repo | Type | Link |
|------|------|------|
| base_fixcity_fila5 | Issue | https://github.com/laraxot/base_fixcity_fila5/issues/179 |
| module_fixcity_fila5 | Issue | https://github.com/laraxot/module_fixcity_fila5/issues/4 |
| theme_sixteen_fila5 | Issue | https://github.com/laraxot/theme_sixteen_fila5/issues/15 |

---

**BMAD Method v6 — Fase 4 (Implementation Planning)**