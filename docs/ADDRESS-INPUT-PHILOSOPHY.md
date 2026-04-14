# AddressInput Component — Geo Module

> **Date:** 2026-04-13
> **Related:** [Module Boundary Philosophy](./MODULE-BOUNDARY-PHILOSOPHY.md) · [XotBaseWizard Philosophy](./XOTBASE-WIZARD-PHILOSOPHY.md)

## The Rule

**Geographic input belongs in Geo module.**
For address-based geolocation with reverse geocoding: use `AddressInput`.
For direct coordinate selection with map interface: use `LeafletMarkerMapInput`.
Other modules consume these components, NEVER duplicate geographic functionality.

```php
// ✅ CORRECT — Fixcity CONSUMES Geo's AddressInput
use Modules\Geo\Filament\Forms\Components\AddressInput;

AddressInput::make('address')
    ->label(__('fixcity::segnalazione.fields.address.label'))
    ->placeholder(__('fixcity::segnalazione.create.address.placeholder'))
    ->required()

// ❌ WRONG — Fixcity reinvents address input
TextInput::make('address')
    ->suffixAction(Action::make('locate')->action(fn() => /* geolocation logic */))
```

## Perché (Visione / Filosofia / Zen)

### 1. Domain Ownership

| Module | Owns | Provides |
|---|---|---|
| **Geo** | Address input, geolocation, autocomplete | `AddressInput` component |
| **Fixcity** | Ticket workflow, status tracking | Consumes `AddressInput` |

### 2. DRY — One Address Input

If every module implements its own address field:
- Geolocation logic duplicated N times
- Autocomplete provider configured N different ways
- UI inconsistencies across modules
- Bug fixes must be applied N times

### 3. Single Source of Truth

```
Modules/Geo/app/Filament/Forms/Components/AddressInput.php
  ├── geolocation logic (navigator.geolocation)
  ├── reverse geocoding (Nominatim)
  ├── autocomplete (configurable provider)
  └── Design Comuni UI patterns

Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php
  └── uses AddressInput::make('address')
```

### 4. Zen del Componente

> "Geo sa COME catturare un indirizzo.
> Fixcity sa COSA farne.
> I confini sono chiari: nessuno invade il dominio dell'altro."

### 5. Configuration Over Implementation

Fixcity configures, Geo implements:

```php
// Fixcity: CONFIGURA
AddressInput::make('address')
    ->label('Indirizzo del disservizio')
    ->placeholder('Via, numero, città')
    ->required()
    ->geolocationEnabled(true)

// Geo: IMPLEMENTA
class AddressInput extends TextInput
{
    protected function setUp(): void {
        // Geolocation button
        // Nominatim reverse geocoding
        // Design Comuni UI
    }
}
```

## Component API

```php
AddressInput::make('address')
    ->label(string|Closure $label)
    ->placeholder(string $placeholder)
    ->required()
    ->geolocationEnabled(bool $enabled = true)
    ->geolocationLabel(string|Closure|null $label)
    ->autocompleteProvider(string $provider) // 'nominatim', 'google', 'mapbox'
    ->spriteUrl(string|Closure|null $url)
```

## Coordinate Input Options

Geo module provides two approaches for geographic input:

### 1. Address-based Geolocation (AddressInput)
For traditional address search with reverse geocoding:
```php
use Modules\Geo\Filament\Forms\Components\AddressInput;

AddressInput::make('address')
    ->label(__('fixcity::segnalazione.fields.address.label'))
    ->placeholder(__('fixcity::segnalazione.create.address.placeholder'))
    ->required()
    ->geolocationEnabled(true)
```

### 2. Direct Coordinate Selection (LeafletMarkerMapInput)
For map-based coordinate selection with draggable marker:
```php
use Modules\Geo\Filament\Forms\Components\LeafletMarkerMapInput;

LeafletMarkerMapInput::make('location')
    ->label(__('fixcity::segnalazione.fields.place.section.label'))
    ->defaultCenter(41.9028, 12.4964) // Roma di default
    ->defaultZoom(13)
    ->mapHeight('340px')
    ->showMap(true)
```

**Note**: The `LeafletMarkerMapInput` component automatically updates sibling `latitude` and `longitude` fields in the same form scope when the marker is moved, eliminating the need for separate coordinate fields.

### 3. Manual Coordinate Input (LatitudeLongitudeInput)
For direct numeric coordinate entry:
```php
use Modules\Geo\Filament\Forms\Components\LatitudeLongitudeInput;

LatitudeLongitudeInput::make('coordinates')
    ->label(__('fixcity::segnalazione.fields.coordinates.label'))
```

This component provides:
- Two numeric fields for latitude (-90 to 90) and longitude (-180 to 180)
- Range validation with real-time feedback
- Design Comuni UI styling
- Optional map display integration point (`->showMap()`)

## Files

### Geo (Owner)
- `Modules/Geo/app/Filament/Forms/Components/AddressInput.php` — Address geolocation component
- `Modules/Geo/resources/views/filament/forms/components/address-input.blade.php` — Address input view
- `Modules/Geo/lang/{locale}/address.php` — Address translations
- `Modules/Geo/app/Filament/Forms/Components/LeafletMarkerMapInput.php` — Map-based coordinate component
- `Modules/Geo/resources/views/filament/forms/components/leaflet-marker-map-input.blade.php` — Map input view
- `Modules/Geo/lang/{locale}/leaflet-map.php` — Map component translations
- `Modules/Geo/app/Filament/Forms/Components/LatitudeLongitudeInput.php` — Coordinate input class
- `Modules/Geo/resources/views/filament/forms/components/latitude-longitude-input.blade.php` — Coordinate view
- `Modules/Geo/lang/{locale}/coordinates.php` — Coordinate translations

### Fixcity (Consumer)
- `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` — Uses AddressInput or LeafletMarkerMapInput based on requirements

## Docs Links
- [Module Boundary Philosophy](./MODULE-BOUNDARY-PHILOSOPHY.md)
- [XotBaseWizard Philosophy](./XOTBASE-WIZARD-PHILOSOPHY.md)
- [CMS-Driven Blocks](../../../Themes/Sixteen/docs/architecture/CMS-DRIVEN-BLOCKS.md)
