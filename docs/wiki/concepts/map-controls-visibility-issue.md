# Map Controls Visibility Issue on Ticket Creation Page

## Story: BMAD-2026-04-28-001

**Target**: Identify why map controls (fullscreen, zoom, current position) are missing on admin ticket creation page (`http://127.0.0.1:8001/fixcity/admin/tickets/create`) but present on CMS-driven test page (`http://127.0.0.1:8001/it/tests/segnalazione-crea`).

## Artifacts Involved

- Folio admin page: `laravel/Modules/Fixcity/resources/views/admin/tickets/create.blade.php`
- Folio test page: `laravel/Modules/Fixcity/resources/views/pages/segnalazione-crea.blade.php`
- Map component: `laravel/Modules/Geo/resources/js/components/map-picker-lit.js`
- Coordinate field: `laravel/Modules/Geo/resources/js/components/coordinate-picker-field.js`
- CSS: `laravel/Themes/Sixteen/resources/css/app.css`
- Database: `laravel/database/fixcity_data.sqlite` (tickets table)

## Architecture Analysis

### Page Type Differences

**Admin Ticket Creation (Folio)**:
- Route: `/fixcity/admin/tickets/create`
- Uses Livewire component wrapper around Folio page
- May inherit admin theme/layout (not Sixteen theme)
- Different CSS cascade rules

**Test Page (CMS-driven)**:
- Route: `/it/tests/segnalazione-crea`
- Uses Sixteen theme (`laravel/Themes/Sixteen/`)
- CMS block view via JSON (`tests.segnalazione-crea.json`)
- Full Sixteen theme CSS/JS pipeline

### Z-Index Hierarchy (Issue Root Cause)

**Header dropdown z-index**: 1050 (from Bootstrap Italia `.dropdown-menu`)
**Map wrapper z-index**: 1000 (from `.bg-gray-900` or similar)

Result: Map controls rendered under header dropdown layer → invisible to pointer events.

**Additional CSS Issue**: `.latitude-longitude-map-shell` or parent wrapper inheriting `opacity` or `background-color` rules that obscure controls.

### Leaflet Initialization Timing

Filament 5 wizard steps use Alpine.js `x-show` which toggles `display: none`. When map initializes:
1. Container is hidden (`display: none`)
2. Leaflet calculates dimensions as 0×0
3. Tiles never load

**Solution Required**: MutationObserver on ancestor nodes watching for `class`/`style`/`hidden` attribute changes. When wrapper becomes visible (`offsetParent !== null`), call `map.invalidateSize()`.

**Critical Detail** (from `leaflet-wizard-invalidate-size.md`): 
- Depth must be ≥ 12 in Filament 5 (not 6)
- DOM path: component → field wrapper → grid → section → wizard step content → wizard step → wizard (7-8 levels to reach `x-show`)
- Delay array: `[0, 80, 180, 350, 700, 1200]` ms (Alpine tick can be > 500ms)

## Database Schema Issue

**Current Error**: `SQLSTATE[HY000]: General error: 1 table tickets has no column named address`

**Root Cause**: Code attempting to insert into `address` column, but architecture requires:
- `location` column (JSON/geometry type) stores lat/lng + address components
- Address should be parsed from geocoding service into: street, street_number, zip, city, province, region, country

**Correct Pattern**: 
```php
// Parse from Google/OSM geocoding response
$location = [
    'lat' => $latitude,
    'lng' => $longitude,
    'address_components' => [
        'street' => $street,
        'street_number' => $number,
        'zip' => $zip,
        'city' => $city,
        // ...
    ]
];
```

## Verification Requirements

**Browser Testing** (per `post-modifica-verifica-obbligatoria.md` and `visual-parity-verification.md`):

1. Open `http://127.0.0.1:8001/fixcity/admin/tickets/create?step=form.data::data::wizard-step`
2. Login: marco.sottana@gmail.com / prova123
3. Verify map controls visible:
   - Fullscreen button (top-right)
   - Zoom in (+) / zoom out (-) (bottom-right or top-right)
   - Current position button (bottom-right or top-right)
4. Test functionality: click fullscreen, zoom, locate
5. Compare with test URL: `http://127.0.0.1:8001/it/tests/segnalazione-crea`
6. Screenshot both states (guest view would show different UI)

## Fixes Required

### 1. CSS Fix (z-index and opacity)

File: `laravel/Themes/Sixteen/resources/css/app.css` or new `map-visual-fix.css`

```css
/* Ensure map wrapper appears above header dropdowns */
.map-wrapper {
    position: relative;
    z-index: 1060 !important; /* > .dropdown-menu z-index 1050 */
    pointer-events: auto !important;
}

/* Prevent opacity inheritance that hides controls */
.latitude-longitude-map-shell,
.map-wrapper,
.map-wrapper * {
    opacity: 1 !important;
    background-color: transparent !important;
}

/* Admin page specific fix */
[data-page*="tickets.create"] .map-wrapper,
body[data-page*="tickets"] .map-wrapper {
    z-index: 1060 !important;
}
```

### 2. JavaScript Fix (invalidateSize)

File: `laravel/Modules/Geo/resources/js/components/map-picker-lit.js`

```javascript
// In firstUpdated() or connectedCallback():
this._mutationObserver = new MutationObserver(() => {
    if (this.offsetParent !== null && this._map) {
        setTimeout(() => this._map.invalidateSize(), 150);
    }
});

let parent = this.parentElement;
// CRITICAL: depth >= 12 for Filament 5 wizard
for (let i = 0; i < 12 && parent; i++) {
    this._mutationObserver.observe(parent, {
        attributes: true,
        attributeFilter: ['class', 'style', 'hidden']
    });
    parent = parent.parentElement;
}

// In disconnectedCallback():
this._mutationObserver?.disconnect();
```

Also apply similar pattern to `coordinate-picker-lit.js` using `this._refreshMapSize()`.

### 3. Database Fix (location column)

**Current tickets table** (needs inspection):
- Has: latitude, longitude, address (type_id, name, content, email, slug, status)
- Missing: location (JSON/geometry)

**Migration approach** (per `one-migration-per-model.md`):
1. Locate existing migration: `..._create_tickets_table.php`
2. Add `tableUpdate()` method:
```php
public function up(): void {
    $this->tableCreate(function (Blueprint $table) {
        // existing columns
    });
    
    $this->tableUpdate(function (Blueprint $table) {
        if (! $this->hasColumn('location')) {
            $table->json('location')->nullable()->after('longitude');
        }
        // Optionally: $table->dropColumn('address');
    });
}
```
3. Bump timestamp in filename for reinstall

4. Update Eloquent model to handle location as array/JSON:
```php
// Modules/Fixcity/app/Models/Ticket.php
protected $casts = [
    'location' => 'array', // or 'json'
];

public function setLocationFromGeocode($lat, $lng, $addressComponents)
{
    $this->location = [
        'lat' => $lat,
        'lng' => $lng,
        'address' => $addressComponents
    ];
}
```

5. Update Form Schema (Filament 5):
```php
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;

public function getFormSchema(): array
{
    return [
        Section::make('Posizione')
            ->schema([
                Grid::make(2)->schema([
                    Text::make('latitude')->hidden(), // or coordinate picker field
                    Text::make('longitude')->hidden(),
                    // Use custom CoordinatePickerField component
                ]),
            ]),
        // Store parsed address in location, not separate address column
        Section::make('Indirizzo')
            ->schema([
                Grid::make(2)->schema([
                    Text::make('street')->label('Via'),
                    Text::make('street_number')->label('Civico'),
                    Text::make('zip')->label('CAP'),
                    Text::make('city')->label('Città'),
                ]),
            ]),
    ];
}
```

## Quality Gates (post-modifica-verifica)

**Must verify** after implementing fixes:

1. **PHPStan** (static analysis):
   ```bash
   phpstan analyse laravel/Modules/Fixcity/app/Models/Ticket.php --level=max
   phpstan analyse laravel/Modules/Fixcity/app/Filament/Resources/TicketResource.php --level=max
   ```

2. **PHPMD** (mess detection):
   ```bash
   phpmd laravel/Modules/Fixcity/app/Models/Ticket.php text unusedcode,design,codesize
   ```

3. **PHPInsights** (code quality):
   ```bash
   cd laravel && php artisan insights Modules\Fixcity
   ```

4. **Pest** (tests):
   ```bash
   cd laravel && php artisan test Modules/Fixcity
   ```
   - Check `TicketPagesTest.php` for coordinate/location tests
   - Add tests for location JSON handling

5. **Playwright** (visual parity):
   - Screenshot map on admin ticket page
   - Verify controls visible and functional
   - Compare with test page

## Files to Create/Modify

### Documentation
- [x] This file: `laravel/Modules/Fixcity/docs/wiki/concepts/map-controls-visibility-issue.md`
- [ ] Update `laravel/Modules/Fixcity/docs/wiki/index.md`
- [ ] Add log entry: `laravel/Modules/Fixcity/docs/wiki/log.md`

### CSS
- [ ] `laravel/Themes/Sixteen/resources/css/app.css` — z-index, opacity fixes
- [ ] `laravel/Themes/Sixteen/resources/css/map-visual-fix.css` — if separate file

### JavaScript
- [ ] `laravel/Modules/Geo/resources/js/components/map-picker-lit.js` — MutationObserver depth=12
- [ ] `laravel/Modules/Geo/resources/js/components/coordinate-picker-lit.js` — same pattern
- [ ] `laravel/Modules/Geo/resources/js/components/coordinate-picker-field.js` — if applicable

### Database
- [ ] Migration file: update existing tickets migration with location column
- [ ] Model: `laravel/Modules/Fixcity/app/Models/Ticket.php` — casts, accessor/mutator

### Form Schemas
- [ ] `.../Filament/Resources/TicketResource/Pages/CreateTicket.php` — form schema
- [ ] `.../Filament/Resources/TicketResource/Pages/EditTicket.php` — same
- [ ] `.../Filament/Resources/TicketResource/Pages/ViewTicket.php` — summary schema

### Tests
- [ ] `laravel/Modules/Fixcity/tests/Feature/Pages/TicketPagesTest.php` — verify map loads
- [ ] Add geocoding address parsing tests

## Next Steps

Priority 1: CSS fix (quick win, immediately visible)
Priority 2: JS fix (ensure Leaflet works in wizard)
Priority 3: Database refactor (location column, address parsing)

All changes must pass the full quality gate suite before considering task complete.

## References

- `docs/wiki/concepts/leaflet-wizard-invalidate-size.md`
- `docs/wiki/concepts/map-interaction-transparency-rule.md`
- `docs/wiki/concepts/no-page-specific-css.md`
- `docs/wiki/concepts/post-modifica-verifica-obbligatoria.md`
- `laravel/Modules/Geo/docs/wiki/concepts/lit-icons-filament-way.md`
- `laravel/Modules/Geo/docs/wiki/concepts/svg-asset-location.md`
- `laravel/Modules/Xot/docs/wiki/concepts/xotbase-blade-icons-auto-registration.md`

---
**Created**: 2026-04-28  
**Author**: Claude (XotBase AI)  
**Status**: Draft → Implementation Ready