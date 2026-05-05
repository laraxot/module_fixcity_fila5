# TicketInfolist Pattern Reference

**Date:** 2026-05-05  
**Updated:** 2026-05-05 (Filament v5 Hybrid Pattern)  
**Type:** architecture-reference  
**Sources:** TicketInfolist.php, XotBaseResourceInfolist.php, ProjectInfolist.php (Filament demo)  
**Confidence:** verified  
**Tags:** filament5, ticketinfolist, pattern, reference, fixcity, infolist, hybrid-pattern, configure  
**Related:** ticketform-pattern-reference.md, why-xotbaseresourceform-superior.md, filament-v5-hybrid-pattern.md

## Overview

TicketInfolist.php in Fixcity module is the **reference implementation** for all Infolist classes in our project. It demonstrates the **Filament v5 Hybrid Pattern** - combining modern Filament v5's `configure(Schema $schema): Schema` with Laraxot's `XotBaseResourceInfolist` foundation:

- ✅ **NEW**: `configure(Schema $schema): Schema` - Filament v5 fluent API
- ✅ **LEGACY**: `getInfolistSchema(): array` - Backward compatibility
- ✅ XotBaseResourceInfolist extension
- ✅ LangServiceProvider for translations (no `->label()`)
- ✅ Filament 5 Infolist entries (TextEntry, ImageEntry, Tabs, Tab, Section)
- ✅ Dynamic data display (no form inputs in infolists)

## Key Characteristics

### 1. Extends XotBaseResourceInfolist (Hybrid Pattern)

```php
class TicketInfolist extends XotBaseResourceInfolist {
    // NEW: Filament v5 style - fluent Schema configuration
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                Tabs::make('ticket')
                    ->schema([
                        static::getTabByName('overview', ...),
                        static::getTabByName('location', ...),
                    ])
                    ->columnSpanFull(),
            ]);
    }
    
    // LEGACY: Array API - backward compatibility
    public static function getInfolistSchema(): array {
        return [
            Tabs::make('ticket')
                ->schema([
                    static::getTabByName('overview', ...),
                    static::getTabByName('location', ...),
                ])
                ->columnSpanFull(),
        ];
    }
}
```

**Why Hybrid?**
- `configure()` enables Filament v5 fluent API with full Schema control
- `getInfolistSchema()` preserves backward compatibility with existing Resources
- Both methods share the same schema building logic (DRY)
- XotBaseResourceInfolist provides auto-label via LangServiceProvider

### 2. No `->label()` or `->tooltip()` Calls

```php
// ✅ CORRECT - No label, LangServiceProvider owns it
TextEntry::make('name')
    ->columnSpanFull();

// ❌ WRONG - Filament demo pattern (DO NOT USE)
TextEntry::make('name')
    ->label('Name')  // Breaks i18n
    ->columnSpanFull();
```

### 3. Uses Infolist Entries (Not Form Fields)

```php
// ✅ CORRECT - Infolist entries for read-only display
TextEntry::make('id'),
TextEntry::make('status')
    ->badge(),
ImageEntry::make('images')
    ->disk('uploads'),

// ❌ WRONG - Form fields in infolist
TextInput::make('name')  // This is for forms, not infolists
```

### 4. Uses Tabs and Sections for Organization

```php
Tabs::make('ticket')
    ->columnSpanFull()
    ->schema([
        Tab::make('overview')
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id'),
                        TextEntry::make('status')->badge(),
                        // ...
                    ]),
            ]),
        Tab::make('location')
            ->schema([
                // Location data display
            ]),
    ]),
```

### 5. Relationship Entries with Placeholders

```php
TextEntry::make('owner.name')
    ->placeholder('-'),  // ✅ Show dash if null

TextEntry::make('assignee.name')
    ->placeholder('-'),
```

### 6. Date/Time Entries

```php
TextEntry::make('created_at')
    ->dateTime(),  // ✅ Proper date/time formatting

TextEntry::make('updated_at')
    ->dateTime(),
```

### 7. Prose Content with Placeholder

```php
TextEntry::make('content')
    ->prose()          // ✅ Rich text rendering
    ->columnSpanFull()
    ->placeholder('-'),  // ✅ Fallback if empty
```

## Filament Demo Pattern vs Our Pattern

### Filament Demo (ProjectInfolist.php)

```php
// Demo uses pure static class
class ProjectInfolist {
    public static function configure(Schema $schema): Schema {
        return $schema->components([
            Tabs::make('Project')
                ->label('Project')  // ❌ Hardcoded
                ->schema([...])
        ]);
    }
}
```

**Problems:**
- ❌ Hardcoded labels: `->label('Overview')`
- ❌ No LangServiceProvider integration
- ❌ No wizard support
- ❌ No shared base logic
- ❌ Single API (only configure())

### Our Hybrid Pattern (TicketInfolist.php)

```php
// We extend XotBaseResourceInfolist with DUAL API
class TicketInfolist extends XotBaseResourceInfolist {
    // NEW: Filament v5 style
    public static function configure(Schema $schema): Schema {
        return $schema->components([
            Tabs::make('ticket')->schema([...])  // ✅ No label, auto-resolved
        ]);
    }
    
    // LEGACY: Array style for compatibility
    public static function getInfolistSchema(): array {
        return [
            Tabs::make('ticket')->schema([...])
        ];
    }
}
```

**Advantages:**
- ✅ **Dual API**: Both `configure()` and `getInfolistSchema()` work
- ✅ **Filament v5 Ready**: Full fluent Schema API support
- ✅ **Backward Compatible**: Existing code continues to work
- ✅ **LangServiceProvider**: No hardcoded `->label()`
- ✅ **XotBase**: Shared logic via inheritance
- ✅ **Consistent**: Same pattern as TicketForm

## Usage Guidelines

### For Module Developers

1. **Always extend XotBaseResourceInfolist**
2. **Never use `->label()` or `->tooltip()`**
3. **Use Infolist entries only** (TextEntry, ImageEntry, etc.)
4. **No form fields** (TextInput, Select, etc.) in infolists
5. **Use placeholders** for nullable relationships
6. **Use Tabs/Sections** for organization
7. **Use `->prose()`** for rich text content

### Creating New Infolist Class (Hybrid Pattern)

```php
<?php
declare(strict_types=1);

namespace Modules\YourModule\Filament\Resources\YourResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Modules\Xot\Filament\Resources\Schemas\XotBaseResourceInfolist;

class YourInfolist extends XotBaseResourceInfolist {
    /**
     * NEW: Filament v5 style - fluent Schema configuration.
     * Primary method for modern Filament v5 resources.
     */
    public static function configure(Schema $schema): Schema {
        return $schema
            ->components([
                Tabs::make('resource')
                    ->columnSpanFull()
                    ->schema([
                        Tab::make('overview')
                            ->schema([
                                Section::make()
                                    ->columns(2)
                                    ->schema([
                                        TextEntry::make('id'),
                                        TextEntry::make('name')
                                            ->columnSpanFull(),
                                        // Add more entries...
                                    ]),
                            ]),
                    ]),
            ]);
    }
    
    /**
     * LEGACY: Array API - backward compatibility.
     * Can be removed in v6.0 when all resources use configure().
     * 
     * @return array<int, \Filament\Schemas\Components\Component>
     */
    public static function getInfolistSchema(): array {
        // Delegate to configure() to avoid duplication
        $schema = app(Schema::class);
        return static::configure($schema)->getComponents();
    }
}
```

## Anti-Patterns to Avoid

❌ **Filament Demo Pure-Static Classes**
```php
// WRONG - Don't use this pattern
class ProjectInfolist {
    public static function configure(Schema $schema): Schema {
        return $schema->components([...]);
    }
}
```

❌ **Form Fields in Infolist**
```php
// WRONG
TextInput::make('name')  // This is a form field!
```

❌ **Hardcoded Labels**
```php
// WRONG
TextEntry::make('status')
    ->label('Status')  // Breaks i18n
```

❌ **SchemaView for Summaries**
```php
// WRONG
SchemaView::make('summary-view');
```

## Files to Study

- `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php` ✅ (reference)
- `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php` ✅ (reference)
- `laravel/Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceInfolist.php` ✅ (base class)
- `laravel/Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceForm.php` ✅ (base class)

## Documentation Contract

When implementing new Infolist classes:
1. Update module `docs/wiki/concepts/` with pattern usage
2. Add entry to module `docs/wiki/index.md`
3. Update `docs/wiki/log.md`
4. Run QMD ingest
