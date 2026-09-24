# 🎯 Ticket Infolist Implementation Guide

## 📚 Overview
- **Purpose**: Create `TicketInfolist` extending `XotBaseResourceInfolist`
- **Location**: `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php`
- **Role**: Contextual summary in Ticket wizard via Infolist `Tabs`
- **Schema Structure**: 
  - Tabs wrapper (`'ticket'`) → Multiple sections with consistent column layout
  - Tab components: icon-based navigation, full width, schema-schema composition

## 🏗️ Architecture Compliance
- **Base Class**: Extends `Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist`
- **Inherited Contracts**:
  - Must implement `static function getInfolistSchema(): array`
  - Must use `static::getTabByName()` for consistent tab label resolution
  - Must respect `getTabByName()` labeling convention: `{module_low}::{group}.tabs.{name}.label`
  - Preserve functional dependencies on `XotBaseResourceInfolist` methods (`getInfolistSchema`, `getTabByName`)
- **Critical Guardrails**:
  - Core functions MUST NOT be overwritten
  - Signature declarations MUST remain static (type-hint preserved)
  - NEVER remove or rename `configure()` method
  - Tab components MUST use `Tab::make('ticket')` structure

## 🧩 Filament 5.x Patterns
- **Component Stack**:
  - `Tabs` wrapper for segmented context separation
  - `static::getTabByName()` integration ensures label localization
  - `Heroicon-o-*` icons for visual consistency
  - Column spanning pattern (`->columns(2)`) for responsive field arrangement
- **Schema Composition Rules**:
  - Schema components accept Closure callbacks for dynamic property resolution
  - `Section::make()` requires explicit `columns()` declaration
  - All non-static method calls MUST transit through `static` context
  - Internal schema schema entries MUST use numeric keys only

## 🔐 Security Patterns
- **Static Method Enforcement**: All overridden methods remain static type-safe
- **Label Resolution**: Labels pulled via `__($labelKey)` with key following naming convention
- **HTML Sanitization**: All output via `TextEntry` automatically escapes via Laravel's default casting
- **PHP Stan Protection**: 
  - Maintain sealed class interface
  - Preserve `__invoke` closure expectations
  - No hidden dependencies injected via constructor

## 📂 Documentation Integration
- **Root Path**: `/laravel/Modules/Fixcity/app/Filament/Docs/implementation-guide-ticket-infolist.md`
- **Ingestion Protocol**: 
  - Add to `docs/wiki/instances/ticket-infolist.md` 
  - Reference in `docs/wiki/concepts/fixcity-schemas.md`
  - Update module `CHANGELOG.md` with version notes
- **Linking Convention**:
  - Use relative module roots in documentation links
  - Paths MUST start with `../../` prefix when referencing up-context
  - Example: `../../../docs/wiki/concepts/schema-architecture`

## 🆚 Comparison with Filament Demo Patterns
| Demo Source                         | Fixcity Implementation | Key Differences |
|-------------------------------------|------------------------|-----------------|
| DepartmentForm.php                  | TicketForm.php         | Ticket extends XotBase* vs pure Filament      |
| DepartmentsTable.php                | TicketInfolist.php     | Ticket uses `XotBaseResourceInfolist` directly |
| ProjectInfolist.php                 | — | Shared base class used, but TicketInfolist adds location-specific fields |
| Filament Official Wizard Component  | `XotBaseWizardWidget`  | Uses Filament's internal wizard engine via `makeWizard()` |

## ✅ Implementation Checklist
- [ ] Extend `XotBaseResourceInfolist` exactly
- [ ] Implement `getInfolistSchema()` returning `Tabs` wrapper schema
- [ ] Use `static::getTabByName()` for all tab definitions
- [ ] Follow column span pattern (`->columns(2)`)
- [ ] Preserve icon naming convention (`heroicon-o-*`)
- [ ] Maintain closure-based schema components
- [ ] Audit schema composition for numeric key usage
- [ ] Add TransformCAST actions for dynamic field rendering

## 🔄 Migration Path
1. Clone existing TicketInfolist implementation
2. Verify inheritance chain matches `XotBaseResourceInfolist`
3. Confirm all schema component methods are static and type-safe
4. Run phpstan analysis on the module directory
5. Test visual rendering on `/fixcity/admin/tickets/create`
6. Validate persistence behavior for `?step=` navigation

## 🔧 Dev Notes
- **Important Directories**:
  - Module Schemas: `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/`
  - Infolist Definitions: `TicketInfolist.php` + `TicketForm.php` (parent)
  - Wizard Widgets: `laravel/Modules/Fixcity/app/Filament/Widgets/`
- **API Contracts**:
  - Must preserve `getInfolistSchema()` return type array
  - Must maintain `getTabByName()` label key pattern
  - Must NOT override core `XotBaseResourceInfolist` methods
- **Testing Coverage**:
  - Visual regression on ticket creation wizard flow
  - Query string step navigation (`?step=2`)
  - Icon rendering consistency across tabs

> **Under Development**: Documentation ingest pending. Next task: validate against `phpmd` and `phpstan` quality gates.