---
title: "Fixcity Module Best Practices"
type: concept
sources: ["../../Modules/Fixcity/app/Filament/Resources/TicketResource/"]
confidence: high
created: 2026-04-28
updated: 2026-04-28
tags: [fixcity, best-practices, ticket-system, filament-5, wizard]
related:
  - concepts/admin-ticket-create-map-visual-contract.md
  - concepts/location-capture-map-wizard.md
  - concepts/wizard-summary-infolist-rule.md
---

# Fixcity Module Best Practices

## Overview

Best practices per lo sviluppo del modulo Fixcity (ticket system, wizard, segnalazioni).

## ✅ Best Practices

### 1. Widget per liste, Blade per contenuto editoriale
```php
// ✅ SI - per liste navigabili
class RecentTicketsWidget extends XotBaseWidget { }

// ✅ SI - per contenuto CMS
<x-section slug="segnalazione-crea" />

// ❌ NO - Blade per liste complesse
```

### 2. Summary wizard con Infolist (non SchemaView)
```php
// ✅ SI - in Wizard schema
public function getSummarySchema(): array {
    return [
        TextEntry::make('title'),
        IconEntry::make('status'),
    ];
}

// ❌ NO
public function getSummarySchema(): array {
    return [View::make('path.to.view')];
}
```

### 3. CSS Design Comuni nel tema, non nei moduli
```css
/* ❌ NO in Modules/Fixcity/resources/views/ */
.ticket-wizard-root { color: green; }

/* ✅ SI in Themes/Sixteen/resources/css/ */
[data-slug="segnalazione-crea"] { color: var(--dc-green); }
```

### 4. NO Controller — Folio + Volt + Filament
```php
// ❌ VIETATO: Http/Controllers/
final class TicketsGeoJsonController { }

// ✅ SI - Folio page per API JSON
// resources/views/pages/api/tickets/geojson.blade.php
@php
use function Laravel\Folio\name;
name('api.tickets.geojson');
echo json_encode($payload);
@endphp

// ✅ SI - Action per logica di business
final class BuildTicketsGeoJsonAction { }
```

### 5. Verifica sempre l'URL finale dopo il fix

## ❌ Bad Practices

### 1. `<style>` inline nei Blade del modulo
```blade
{{-- ❌ NO --}}
<style>
.ticket-wizard { background: red; }
</style>

{{-- ✅ SI --}}
{{-- CSS in Themes/Sixteen/resources/css/app.css --}}
```

### 2. `SchemaView` per summary wizard
Vedi Best Practice #2.

### 3. CDN per asset nel wizard
```html
{{-- ❌ NO --}}
<script src="https://cdn.jsdelivr.net/..."></script>
```

### 4. Dimenticare `dehydrated(false)` nei trait
Vedi Geo best practices - `dehydrated(false)` rompe il salvataggio.

## 🔗 False Friends

### `getSummarySchema()` vs `getFormSchema()`
- **False Friend**: Pensare che restituiscano la stessa cosa
- **Realtà**: `getSummarySchema()` usa **Infolist** entries, `getFormSchema()` usa **Form** components
- **Soluzione**: In summary usa `TextEntry::make()`, non `TextInput::make()`

### `Section` in Form vs `Section` in Infolist
- **False Friend**: Pensare che `Filament\Infolists\Components\Section` sia la stessa di `Filament\Schemas\Components\Section`
- **Realtà**: In Filament 5.x, `Section` per layout viene da `Filament\Schemas\Components\Section`
- **Soluzione**: Usare `Schemas\Components\Section` per layout, `Infolists\Components\Section` per display read-only

### Blade `x-section` vs Folio page
- **False Friend**: Pensare che `x-section slug="..."` crei una nuova pagina
- **Realtà**: `x-section` renderizza un componente Blade esistente, NON una route
- **Soluzione**: Per CMS-driven pages, usare JSON config → block view → widget

## Related

- [[admin-ticket-create-map-visual-contract]]
- [[location-capture-map-wizard]]
- [[wizard-summary-infolist-rule]]
- [[theme-owned-wizard-css-parity-rule]]
- [[design-comuni-theme-css-only-rule]]
