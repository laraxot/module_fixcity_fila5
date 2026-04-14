# Module Boundary Philosophy — AddressInput belongs to Geo

**Status**: Active
**Created**: 2026-04-12
**Last Updated**: 2026-04-13
**Category**: Architecture / Module Boundaries / DDD / Filament Components

## The Zen of Module Boundaries

> "Every concern has a home. Cross-cutting concerns live in their own module. Domain modules consume, never own."

> "Un campo indirizzo è un campo geografico. Non esistono 'campi indirizzo di Fixcity'. Esiste UN campo indirizzo, fornito da Geo, usato da tutti."

## Core Principle: Dependency Direction

```
┌──────────────────────────────────────┐
│       Geo Module (Generic)           │
│                                      │
│  Ubiquitous Language:                │
│  "address", "coordinates",           │
│  "geocode", "location", "bounds"     │
│                                      │
│  ┌────────────────────────────┐      │
│  │ AddressInput (Field)        │      │
│  │ - Geolocation button        │      │
│  │ - Reverse geocoding         │      │
│  │ - Nominatim integration     │      │
│  │ - Filament state binding    │      │
│  └────────────────────────────┘      │
└──────────────────────────────────────┘
         ↑ consumed by ↑
┌──────────────────────────────────────┐
│    Fixcity Module (Core Domain)       │
│                                      │
│  Ubiquitous Language:                │
│  "ticket", "segnalazione",            │
│  "issue", "resolution", "status"     │
│                                      │
│  ┌────────────────────────────┐      │
│  │ CreateTicketWizardWidget    │      │
│  │ AddressInput::make('addr')  │ ← CONSUMES
│  └────────────────────────────┘      │
└──────────────────────────────────────┘
```

```
Fixcity ──→ Geo  ✅  (specific domain consumes cross-cutting concern)
Geo     ──→ Fixcity  ❌  (geo must NEVER depend on domain modules)
```

**Geo** owns ALL geolocation concerns:
- Address lookup & validation
- Reverse geocoding
- Map rendering
- Coordinate calculation
- Distance computation
- Timezone resolution

**Fixcity** (and every other domain module) **consumes** Geo components:
- `Modules\Geo\Filament\Forms\Components\AddressInput` ← **THE component for single address + geolocation**
- `Modules\Geo\Filament\Forms\Components\AddressesField`
- `Modules\Geo\Actions\*` (geocoding actions)
- `Modules\Geo\Models\Address`, `Locality`, etc.

## The Rule: Use AddressInput, NOT Blade::render Hacks

### Wrong (Blade::render hack) — Anti-Pattern

```php
// ❌ WRONG: Fixcity creates a Blade::render workaround
protected function getAddressComponent(): Component
{
    return Placeholder::make('address_section')
        ->label('')
        ->content(new HtmlString(
            \Blade::render('geo::filament.components.address-field', [
                'sprite' => '/themes/Sixteen/...',
            ])
        ));
}
```

**Why it's wrong:**
1. `Placeholder` is NOT a form input — it doesn't participate in Filament's form state management
2. `Blade::render` creates an isolated rendering context — no access to `$this->form`, no validation pipeline
3. `@this.set()` → `$_instance` **UNDEFINED** error (rendered outside Livewire context)
4. It's a workaround, not a composition — it shows Fixcity doesn't trust Geo to provide a proper component
5. It can't be unit-tested properly (Blade::render bypasses Filament's component lifecycle)

### Right (Proper Filament component) — The Pattern

```php
// ✅ RIGHT: Use the proper Geo component
use Modules\Geo\Filament\Forms\Components\AddressInput;

protected function getAddressComponent(): Component
{
    return AddressInput::make('address')
        ->required()
        ->spritePath('/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg');
}
```

**Why it's right:**
1. `AddressInput` extends `Field` — full Filament form integration
2. `@this.set()` works correctly — rendered WITHIN the Livewire component context
3. Composable — any module, wizard, resource can use it declaratively
4. Testable — Pest feature tests can assert on the component class
5. DRY — single source of truth for address input UX

## Why `@this` Works with AddressInput (and NOT with Blade::render)

| Aspetto | AddressInput (Field) | Blade::render() + Placeholder |
|---|---|---|
| **Contesto Livewire** | ✅ Renderizzato dentro il componente | ❌ Contesto isolato |
| **`@this.set()`** | ✅ Funziona | ❌ `$_instance` undefined |
| **Validazione Filament** | ✅ Integrata | ❌ Nessuna |
| **Errori campo** | ✅ Automatici | ❌ Manuali |
| **State binding** | ✅ `wire:model` nativo | ❌ Hack `window.Livewire` |
| **Configurazione** | ✅ Fluent API | ❌ Hardcoded |
| **Testabilità** | ✅ Unit test | ❌ Integration test |

## The Philosophy Table

| Dimension | Blade::render Hack | Proper Component |
|-----------|-------------------|------------------|
| **State management** | Manual `wire:model` string | Filament form lifecycle |
| **Validation** | Must be done manually in `submit()` | Participates in Filament validation |
| **Reusability** | Tied to one module's workaround | Available to ALL modules |
| **Testability** | Requires full HTTP test | Unit-testable component class |
| **Maintainability** | Two places to fix bugs | One source of truth |
| **Philosophy** | "Get it working" | "Get it right" |

## The XotBase Pattern

Laraxot uses XotBase wrapper classes throughout:

| Concept | Wrong | Right |
|---------|-------|-------|
| Resource | `extends Resource` | `extends XotBaseResource` |
| Widget | `extends Widget` | `extends XotBaseWidget` |
| Wizard Widget | `extends XotBaseWidget` | `extends XotBaseWizardWidget` |
| Model | `extends Model` | `extends XotBaseModel` |
| Migration | `return new class extends Migration` | `return new class extends XotBaseMigration` |
| Service Provider | `extends ServiceProvider` | `extends XotBaseServiceProvider` |
| Form Component | Custom class | Extends proper Filament component |

**The rule**: If an XotBase wrapper exists, USE IT. It centralizes:
- Navigation groups
- Localization
- Authorization
- State normalization
- Security policies

## Actions over Services

Laraxot enforces **Actions over Services**:

```php
// ❌ WRONG: Service class
class GeocodingService {
    public function geocode(string $address): array { ... }
}

// ✅ RIGHT: Action class (invokable, queueable)
class GetCoordinatesFromAddressAction {
    public function handle(string $address): CoordinatesData { ... }
}

// Usage
$coords = app(GetCoordinatesFromAddressAction::class)->handle('Via Roma 1, Milano');
```

**Why Actions:**
1. Invokable — single responsibility, single entry point
2. Queueable — can be dispatched to background jobs (`spatie/laravel-queueable-action`)
3. Testable — one `handle()` method to test
4. Composable — chain actions together
5. No state — actions are stateless, unlike services

## Module Structure Convention

```
Modules/Geo/
├── app/
│   ├── Actions/                  ← Geocoding actions (invokable)
│   ├── Filament/
│   │   ├── Forms/Components/
│   │   │   ├── AddressInput.php          ← PROPER form component (Field)
│   │   │   ├── AddressesField.php        ← Repeater for multiple addresses
│   │   │   ├── AddressField.php          ← Section for structured address
│   │   │   └── AddressSection.php        ← Enum-driven flat fields
│   │   ├── Resources/
│   │   │   └── AddressResource.php       ← CRUD for addresses
│   │   └── ...
│   ├── Models/
│   │   ├── Address.php
│   │   └── ...
│   └── ...
├── resources/views/
│   └── filament/
│       └── forms/components/
│           └── address-input.blade.php   ← View for AddressInput Field
├── lang/
│   └── {locale}/address.php              ← Translations (geo::address.*)
└── docs/
    ├── address-input-component.md        ← Component documentation
    ├── philosophy.md                     ← Geo philosophy (Schema.org, DDD)
    └── MODULE-BOUNDARY-PHILOSOPHY.md     ← This file
```

## Cross-Module Usage Pattern

When Fixcity (or any module) needs Geo functionality:

```php
// 1. Import the Geo component
use Modules\Geo\Filament\Forms\Components\AddressInput;

// 2. Use it in your form schema
public function getFormSchema(): array
{
    return [
        AddressInput::make('address')
            ->label(__('fixcity::segnalazione.fields.address.label'))
            ->placeholder(__('fixcity::segnalazione.create.address.placeholder'))
            ->required()
            ->spritePath('/themes/Sixteen/design-comuni/assets/bootstrap-italia/dist/svg/sprites.svg'),
        // ... other fields
    ];
}
```

**NOT:**
- ❌ Copy-pasting the Blade template into Fixcity
- ❌ Using `Blade::render('geo::...')` as a workaround
- ❌ Creating a duplicate `Fixcity\Filament\AddressInput`

## When to Use Which Geo Component

| Scenario | Component | Type |
|---|---|---|
| "Dove è successo?" (singolo indirizzo + geolocalizzazione) | `AddressInput` | `Field` |
| "Qual è l'indirizzo completo?" (via, città, provincia, CAP) | `AddressField` | `Section` |
| "Tutti i contatti" (telefono, email, PEC, indirizzo, lat/lng) | `AddressSection` | `XotBaseSection` |
| "Quali sono le sedi?" (più indirizzi con tipo) | `AddressesField` | `Repeater` |
| "Mostra indirizzo in tabella" | `AddressColumn` | `ViewColumn` |

## The Test

Before writing code, ask:

1. **"Does this concern belong in Geo?"** → If yes, put it there.
2. **"Am I creating a workaround for something Geo should provide?"** → Stop. Add it to Geo.
3. **"Is there an XotBase wrapper?"** → If yes, extend it.
4. **"Should this be an Action instead of a Service?"** → If yes, make it invokable.

## Summary

- **Geo OWNS geolocation**. Every module consumes Geo.
- **Proper Filament components** (`AddressInput::make()`), not `Blade::render()` hacks.
- **XotBase wrappers** everywhere they exist.
- **Actions over Services** — invokable, queueable, testable.
- **One source of truth** per concern. DRY is the law.
- **Documentation lives in the module** that owns the concern.
- **Component location**: `Modules/Geo/resources/views/filament/components/address-field.blade.php`
- **Class location**: `Modules/Geo/app/Filament/Forms/Components/AddressInput.php`
- **Usage example**: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php` (line 183)

## Current Status (2026-04-13)

- ✅ AddressInput class lives in Geo module
- ✅ address-field.blade.php lives in Geo module  
- ✅ Fixcity widget imports via namespace: `use Modules\Geo\Filament\Forms\Components\AddressInput;`
- ✅ Fixcity widget uses proper API: `AddressInput::make('address')`
- ✅ NO duplicate component in Fixcity
- ✅ NO Blade::render workarounds
- ✅ Documentation is comprehensive and up-to-date

## Riferimenti

- **AddressInput Component**: `../../Geo/app/Filament/Forms/Components/AddressInput.php`
- **AddressInput Docs**: `../../Geo/docs/address-input-component.md`
- **Geo Philosophy**: `../../Geo/docs/philosophy.md`
- **CreateTicketWizardWidget**: `../app/Filament/Widgets/CreateTicketWizardWidget.php`
- **Storie BMad**:
  - `../../../.planning/stories/1-10-extract-address-input-to-geo-module.md`
  - `../../../.planning/stories/2-1-geo-module-address-input.md`
  - `../../../_bmad-output/implementation-artifacts/7-30-refactor-ticket-wizard-to-filament-pure.md`
