# TicketForm Pattern Reference

**Date:** 2026-05-05
**Type:** architecture-reference
**Sources:** TicketForm.php, XotBaseResourceForm.php
**Confidence:** verified
**Tags:** filament5, ticketform, pattern, reference, fixcity
**Related:** why-xotbaseresourceform-superior.md, xotbase-resource-form-architecture.md

## Overview

TicketForm.php in Fixcity module is the **reference implementation** for all Form classes in our project. It demonstrates the correct integration of:
- XotBaseResourceForm extension
- LangServiceProvider for translations
- Wizard multi-step support
- Infolist entries for summaries

## Key Characteristics

### 1. Extends XotBaseResourceForm

```php
class TicketForm extends XotBaseResourceForm {
    use HasTicketAuthorData;
    // ...
}
```

### 2. No `->label()` or `->tooltip()` Calls

```php
// ✅ CORRECT - No label, LangServiceProvider owns it
TextInput::make('name')
    ->required()
    ->maxLength(255);

// ❌ WRONG - Filament demo pattern (DO NOT USE)
TextInput::make('name')
    ->label('Name')  // Breaks i18n
    ->required();
```

### 3. Wizard Integration

```php
public static function getFormSchema(): array {
    $steps = static::getWizardSteps();
    $wizard = Wizard::make($steps)
        ->skippable()
        ->persistStepInQueryString();
    return [$wizard];
}

public static function getWizardSteps(): array {
    return [
        static::getStepByName('privacy'),
        static::getStepByName('data'),
        static::getStepByName('summary'),
    ];
}
```

### 4. Summary Schema Uses Infolist Entries

```php
public static function getSummarySchema(): array {
    return [
        Section::make(...)
            ->schema([
                Grid::make(['default' => 1, 'lg' => 2])
                    ->schema([
                        TextEntry::make('review_type')  // ✅ Infolist entry
                            ->state(static fn (Get $get): string => ...),
                        ImageEntry::make('review_images')  // ✅ Not SchemaView
                            ->disk('public'),
                    ]),
            ]),
    ];
}
```

### 5. SafeStringCastAction for Translation Casting

```php
Section::make(SafeStringCastAction::cast(__('fixcity::segnalazione.fields.place.section.label')))
    ->description(SafeStringCastAction::cast(__('fixcity::segnalazione.sections.place.description')))
```

### 6. Dynamic Values with Get $get

```php
TextEntry::make('review_location')
    ->state(static fn (Get $get): string => static::formatLocationSummary($get('location')))
```

## Usage Guidelines

### For Module Developers

1. **Always extend XotBaseResourceForm**
2. **Never use `->label()` or `->tooltip()`**
3. **For wizards: implement `getWizardSteps()` and `getStepByName()`**
4. **For summaries: use Infolist entries (TextEntry, ImageEntry, Grid)**
5. **Use SafeStringCastAction for translation casting**
6. **Use Get $get and Set $set for dynamic values**

### For New Modules

Copy TicketForm.php structure:

```php
<?php
declare(strict_types=1);
namespace Modules\YourModule\Filament\Resources\YourResource\Schemas;

use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceForm;

class YourForm extends XotBaseResourceForm {
    public static function getFormSchema(): array {
        // Your schema here (NO ->label(), use LangServiceProvider)
    }
    
    public static function getWizardSteps(): array {
        return [
            static::getStepByName('first_step'),
            static::getStepByName('second_step'),
        ];
    }
}
```

## Anti-Patterns to Avoid

❌ **Filament Demo Pure-Static Classes**
```php
// WRONG - Don't use this pattern
class DepartmentForm {
    public static function configure(Schema $schema): Schema {
        return $schema->components([...]);
    }
}
```

❌ **Hardcoded Labels**
```php
// WRONG
TextInput::make('name')->label('Name');
```

❌ **SchemaView for Summaries**
```php
// WRONG
SchemaView::make('ticket-summary');
```

## Files to Study

- `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `laravel/Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceForm.php`

## Documentation Contract

When implementing new Form classes:
1. Update module `docs/wiki/concepts/` with pattern usage
2. Add entry to module `docs/wiki/index.md`
3. Update `docs/wiki/log.md`
4. Run QMD ingest
