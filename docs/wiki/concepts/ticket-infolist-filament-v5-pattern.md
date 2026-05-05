# TicketInfolist - Filament v5 Hybrid Pattern

**Status**: ✅ Implemented  
**Module**: Fixcity  
**Resource**: Ticket  
**Pattern**: Filament v5 Hybrid (configure + legacy)  
**Last Updated**: 2026-05-05

## Overview

`TicketInfolist` implements the **Filament v5 Hybrid Pattern** - combining modern Filament v5's `configure(Schema $schema): Schema` approach with Laraxot's `XotBaseResourceInfolist` foundation for auto-label functionality.

## File Location

```
Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php
```

## Architecture

### Hybrid Pattern Implementation

```php
class TicketInfolist extends XotBaseResourceInfolist
{
    /**
     * NEW: Filament v5 fluent API
     */
    public static function configure(Schema $schema): Schema
    {
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
    
    /**
     * LEGACY: Array API (backward compatibility)
     */
    public static function getInfolistSchema(): array
    {
        return [
            Tabs::make('ticket')
                ->schema([...])
                ->columnSpanFull(),
        ];
    }
}
```

### Key Features

1. **Dual API Support**: Both `configure()` and `getInfolistSchema()` work
2. **XotBase Extension**: Inherits auto-label functionality via `TransTrait`
3. **Tabs Layout**: Overview + Location tabs
4. **Zero Hardcoded Labels**: All labels resolved via LangServiceProvider

## Infolist Structure

### Tab: Overview (`heroicon-o-information-circle`)

**Section**: Basic Info (2 columns)
- `id` - Ticket ID
- `slug` - URL slug
- `name` - Ticket title (full width)
- `status` - Status badge
- `priority` - Priority badge
- `type_id` - Type badge
- `owner.name` - Creator (with placeholder '-')
- `assignee.name` - Assigned to (with placeholder '-')
- `created_at` - Creation date/time
- `updated_at` - Last update date/time
- `content` - Description (full width, prose format)

### Tab: Location (`heroicon-o-map-pin`)

**Section**: Geographic Data (2 columns)
- `location.address` - Full address (full width)
- `location.lat` - Latitude
- `location.lng` - Longitude
- `images` - Spatie Media Library images (full width)

## Translation Keys

All labels auto-resolved from `Modules/Fixcity/lang/{locale}/ticket.php`:

```php
// Modules/Fixcity/lang/it/ticket.php
return [
    'infolist' => [
        'tabs' => [
            'overview' => [
                'label' => 'Panoramica',
            ],
            'location' => [
                'label' => 'Posizione',
            ],
        ],
        'sections' => [
            // Section labels auto-resolved
        ],
        'fields' => [
            'id' => ['label' => 'ID'],
            'name' => ['label' => 'Titolo'],
            'status' => ['label' => 'Stato'],
            'priority' => ['label' => 'Priorità'],
            'content' => ['label' => 'Descrizione'],
            'location' => [
                'address' => ['label' => 'Indirizzo'],
                'lat' => ['label' => 'Latitudine'],
                'lng' => ['label' => 'Longitudine'],
            ],
            'images' => ['label' => 'Immagini'],
        ],
    ],
];
```

## Critical Rules

### ✅ DO
- Extend `XotBaseResourceInfolist`
- Use `configure(Schema $schema): Schema` for new code
- Keep `getInfolistSchema()` for backward compatibility
- Let LangServiceProvider handle all labels
- Use `->placeholder('-')` for optional fields
- Use `->badge()` for status/priority fields
- Use `->prose()` for long text content

### ❌ DON'T
- Never use `->label()` explicit calls
- Never use `->helperText()` explicit calls
- Never use `->placeholder()` with hardcoded strings (use translation keys)

## Related Files

- **Parent Class**: `Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceInfolist.php`
- **Form Schema**: `TicketForm.php` (wizard-based)
- **Table Schema**: `TicketTable.php`
- **Resource**: `TicketResource.php`
- **Model**: `Modules/Fixcity/app/Models/Ticket.php`

## References

- **Filament v5 Demo**: https://github.com/filamentphp/demo/blob/5.x/app/Filament/Resources/HR/Projects/Schemas/ProjectInfolist.php
- **Pattern Guide**: `Modules/Xot/docs/wiki/concepts/filament-v5-hybrid-pattern.md`
- **Story 8-91**: `.planning/stories/8-91-filament-v5-schemas-structure-refactor.story.md`

## Usage in Resource

```php
// In TicketResource.php:
public static function infolist(Infolist $infolist): Infolist
{
    // Option A: Using configure() (Filament v5 style)
    return TicketInfolist::configure($infolist->getSchema());
    
    // Option B: Using getInfolistSchema() (Legacy style)
    return $infolist->schema(TicketInfolist::getInfolistSchema());
}
```

## Testing

```bash
# PHPStan check
cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php --level=5

# PHPMD check
./vendor/bin/phpmd.phar Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php text cleancode,codesize,controversial,design,naming,unusedcode
```
