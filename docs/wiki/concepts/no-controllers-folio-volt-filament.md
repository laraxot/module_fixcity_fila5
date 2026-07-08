---
title: "No Controllers — Solo Folio + Volt + Filament"
type: concept
confidence: high
created: 2026-05-29
tags: [fixcity, architecture, controllers, folio, volt]
related:
  - concepts/fixcity-best-practices.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/no-controllers-folio-volt-filament.md
  - ../../../../../docs/wiki/concepts/stack-folio-volt-filament.md
---

# No Controllers — Solo Folio + Volt + Filament

## Regola Permanente

**NON usare `app/Http/Controllers/` in nessun modulo o tema.**

Lo stack ufficiale per il dominio applicativo è:

- **Folio** — file-based routing per pagine pubbliche e API JSON
- **Volt** — Livewire components per interattività
- **Filament** — admin panel
- **Actions** — logica di business riutilizzabile

## Cosa Fare

### Per pagine pubbliche
```blade
{{-- resources/views/pages/tickets/create.blade.php --}}
@php
use function Laravel\Folio\{name, render};
name('tickets.create');
render(fn () => view('...')->with([...]));
@endphp
```

### Per API JSON
```blade
{{-- resources/views/pages/api/tickets/geojson.blade.php --}}
@php
use function Laravel\Folio\name;
name('api.tickets.geojson');
$payload = app(BuildTicketsGeoJsonAction::class)->execute(...);
echo json_encode($payload, JSON_UNESCAPED_UNICODE);
@endphp
```

### Per interattività
```blade
@volt('component-name')
<div>
    <button wire:click="action">Click</button>
</div>
@endvolt
```

### Per logica di business
```php
// app/Actions/BuildTicketsGeoJsonAction.php
final class BuildTicketsGeoJsonAction
{
    public function execute(...): array { ... }
}
```

## Cosa NON Fare

```php
// ❌ VIETATO: Controller classico
namespace Modules\Fixcity\Http\Controllers\Api;
final class TicketsGeoJsonController { ... }
```

```php
// ❌ VIETATO: Route con controller
Route::get('tickets/geojson', [TicketsGeoJsonController::class, '__invoke']);
```

## Perché

1. **Folio** mappa automaticamente file → route, zero boilerplate
2. **Volt** integra stato Livewire senza controller intermedi
3. **Filament** gestisce tutto l'admin via pannello
4. **Actions** sono testabili, riutilizzabili, senza dipendenza da Request/Response
5. **Uniformità** — tutto lo stack segue lo stesso pattern

## Storico

- Maggio 2026: rimosso `TicketsGeoJsonController` e `TicketDetailsApiController` da Fixcity, migrati a Folio
- Maggio 2026: rimosso `routes/api.php` (dead code, zero consumatori)
- Maggio 2026: rinominato `api_ticket.php` → `.old` (deprecato)
