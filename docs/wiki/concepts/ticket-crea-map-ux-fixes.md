---
title: "Segnalazione Crea Map UX Fixes"
description: "Map centering, search UX, and geolocation fixes for segnalazione-crea page"
type: concept
sources: []
confidence: high
created: 2026-05-13
updated: 2026-05-13
tags: [fixcity, map, geolocation, search, coordinate-picker]
related:
  - ../concepts/location-capture-map-wizard.md
  - ../concepts/segnalazione-crea-geolocate-when-empty.md
  - ../concepts/ticket-list-map-architecture.md
---

# Segnalazione Crea Map UX Fixes (2026-05-13)

## Overview

Multiple map UX issues on `/it/tests/segnalazione-crea` were identified and fixed:

1. **Map not centered on current location** → Now auto-geolocates
2. **Duplicate search controls** → Single search revealed on demand
3. **Search always visible** → Hidden by default, shows on magnifier click

## Problemi Risolti

### 1. Geolocalizzazione Automatica

**Sintomo**: La mappa mostrava sempre Roma come centro anche quando nessuna coordinata era selezionata.

**Soluzione**: `coordinate-picker-lit` ora ha `geolocate-when-empty` che triggera la geolocalizzazione del browser.

**Implementazione** in `coordinate-picker.blade.php`:
```blade
<coordinate-picker-lit
    geolocate-when-empty
    show-search
    ...
></coordinate-picker-lit>
```

**Flusso**:
1. Wizard step "data" renderizza `CoordinatePicker`
2. `TicketForm::getDataSchema()` configura `->geolocateWhenEmpty()` + `->reverseGeocoding()`
3. Lit component detecta coordinate null
4. `requestGeolocation()` centra sulla posizione utente

### 2. Search UX Semplificato

**Sintomo**: Due controlli di ricerca ("cerca un luogo" + "cerca indirizzo") visibili contemporaneamente.

**Soluzione**: Un solo campo di ricerca, visibile **solo** dopo click sulla lente.

**Comportamento**:
- Apertura mappa → solo controlli standard (fullscreen, posizione, layer, zoom)
- Click lente → campo "Cerca indirizzo..." appare
- ESC / selezione risultato / click X → campo nascosto

**Responsabilità**:
- `coordinate-picker-lit.js` gestisce `_searchOpen` state
- `map-picker-controls.js` renderizza la lente in `renderControls()`
- `map-picker-search.js` contiene la logica di ricerca

### 3. Controlli Posizionati Correttamente

**Prima**: Controlli sovrapposti al campo ricerca
**Dopo**: Controlli in alto a sinistra, ricerca in alto a destra

## File Coinvolti

### Modulo Geo (Owner Runtime)

| File | Ruolo |
|------|-------|
| `resources/views/filament/forms/components/coordinate-picker.blade.php` | View che renderizza il Lit component |
| `resources/js/components/coordinate-picker-lit.js` | Lit Web Component principale |
| `resources/js/components/map-picker-controls.js` | Controlli mappa (lente, fullscreen, posizione, layer, zoom) |
| `resources/js/components/map-picker-search.js` | Logica ricerca indirizzo |

### Modulo Fixcity (Owner Dominio)

| File | Ruolo |
|------|-------|
| `app/Filament/Resources/TicketResource/Schemas/TicketForm.php` | Configura `CoordinatePicker` con `geolocateWhenEmpty()` |
| `app/Filament/Widgets/CreateTicketWizardWidget.php` | Widget wizard che contiene il form |

### Tema Sixteen (Owner Visual Parity)

| File | Ruolo |
|------|-------|
| `resources/views/components/blocks/tests/segnalazione-crea.blade.php` | Block view per la pagina CMS |
| `resources/css/` | CSS per visual parity |

## Testing Contract

```bash
# Test 1: Geolocalizzazione
# 1. Apri http://127.0.0.1:8000/it/tests/segnalazione-crea
# 2. Accetta permesso geolocalizzazione se richiesto
# 3. Verifica che la mappa centri sulla tua posizione corrente
# 4. Se negato: fallback a Roma

# Test 2: Search UX
# 1. Apri lo step "data" del wizard
# 2. Verifica che il campo ricerca NON sia visibile
# 3. Clicca sulla lente di ingrandimento (🔍)
# 4. Verifica che il campo "Cerca indirizzo..." appaia
# 5. Digita un indirizzo → risultati → seleziona
# 6. Mappa centratata sull'indirizzo

# Test 3: Marker
# 1. Click sulla mappa → marker piazzato
# 2. Drag marker → coords updated
# 3. Verifica coords nel riepilogo
```

## Quality Gates

Dopo le modifiche, eseguire:

```bash
cd laravel
./vendor/bin/phpstan analyse Modules/Geo --level=max
./vendor/bin/phpinsights analyse Modules/Geo
php artisan test Modules/Geo
```

## Riferimenti

- [[../Geo/docs/wiki/concepts/coordinate-picker-map-ux-fixes]] - Documentazione Geo module
- [[location-capture-map-wizard]] - Workflow completo della mappa
- [[segnalazione-crea-geolocate-when-empty]] - Contract precedente
- [[ticket-list-map-architecture]] - Architettura per ticket-list