# Coordinate Picker Lit Component

> Componente mappa per il wizard ticket basato su Lit.dev + Leaflet

---

## Architettura

### Stack Tecnologico
- **Lit.dev** - Web Components framework
- **Leaflet** - Open source map library
- **Light DOM** - `createRenderRoot() { return this; }`
- **Filament/Livewire** - Backend integration

---

## File Structure

```
laravel/Modules/Fixcity/resources/js/components/
├── coordinate-picker-lit.js      # Componente principale
├── map-picker-styles.js          # Stili CSS e icon SVG
└── map-picker-marker-config.js   # Configurazione marker
```

---

## Dipendenze Cross-Module

I file `map-picker-styles.js` e `map-picker-marker-config.js` sono copiati da:
```
laravel/Modules/Geo/resources/js/components/
```

**Rationale**: KISS - Evitare symlink complessi per Vite build.

---

## Build Process

Il componente viene buildato nel modulo Geo:

```bash
cd laravel/Modules/Geo
npm install
npm run build
npm run copy
```

Questo genera:
```
public_html/assets/geo/
├── coordinate-picker-lit-*.js
├── map-picker-marker-config-*.js
└── manifest.json
```

---

## Regola Critica: wire:ignore

⚠️ **OBBLIGATORIO** per Light DOM + Leaflet:

```blade
{{-- coordinate-picker.blade.php --}}
<div wire:ignore class="map-container-wrapper">
    <coordinate-picker-lit
        :state="state"
        zoom="{{ $field->getZoom() }}"
    ></coordinate-picker-lit>
</div>
```

**Perché**: Livewire non deve toccare il DOM creato da Leaflet.

---

## Problemi Conosciuti

### 1. Icona Marker Gigante
**Causa**: URL Leaflet marker non risolto correttamente
**Fix**: Usare `map-picker-marker-config.js` con SVG fallback

### 2. Lat/Lng Non Aggiornati
**Causa**: Evento `coords-changed` non propagato
**Fix**: Aggiungere listener in Blade:
```blade
@coords-changed="$wire.set($statePath + '.latitude', $event.detail.latitude)"
```

### 3. Tasti Controllo Mancanti
**Causa**: CSS `layer-controls-overlay` non visibile
**Fix**: Verificare z-index e visibility in `map-picker-styles.js`

---

## API Componente

### Properties
```javascript
static properties = {
    state: { type: Object },           // {latitude, longitude}
    zoom: { type: Number },            // Default: 13
    height: { type: String },          // CSS height
    geolocateWhenEmpty: { type: Boolean },
    labels: { type: Object },          // Traduzioni
    showSearch: { type: Boolean },     // Mostra ricerca
}
```

### Eventi
```javascript
// coords-changed
this.dispatchEvent(new CustomEvent('coords-changed', {
    detail: { latitude, longitude, source },
    bubbles: true,
    composed: true,
}));
```

---

## Metodi

| Metodo | Descrizione |
|--------|-------------|
| `setCoordinates(lat, lng)` | Setta coordinate programmaticamente |
| `_requestGeolocation()` | Richiede geolocalizzazione utente |
| `_toggleFullscreen()` | Toggle fullscreen mode |
| `_switchLayer()` | Cambia layer (street/satellite/topo) |
| `_zoomIn() / _zoomOut()` | Zoom in/out |

---

## Wizard Integration

```php
// TicketForm.php
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;

public static function getDataSchema(): array
{
    return [
        CoordinatePicker::make('coordinates')
            ->zoom(15)
            ->height(400)
            ->geolocateWhenEmpty(),
    ];
}
```

---

## References

- [Lit.dev Docs](https://lit.dev/docs/)
- [Leaflet Docs](https://leafletjs.com/)
- [Story 8-41](../../../.planning/stories/8-41-coordinate-picker-lit-map-issues.story.md)
- [Geo Module Vite Config](../../Geo/docs/vite-build-configuration.md)

---

**Data**: 2026-04-27
**Stato**: In Progress
**Issues**: #8-41
