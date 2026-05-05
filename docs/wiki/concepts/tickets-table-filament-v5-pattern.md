# TicketsTable - Filament v5 Hybrid Pattern

**Status**: ✅ Implemented  
**Module**: Fixcity  
**Resource**: Ticket  
**Location**: `Tables/TicketsTable.php`  
**Pattern**: Filament v5 Hybrid (configure + legacy)  
**Last Updated**: 2026-05-05

## Overview

`TicketsTable` implements the **Filament v5 Hybrid Pattern** for table configuration. It provides a modern fluent API while maintaining backward compatibility with the legacy `table()` method.

## File Location

```
Modules/Fixcity/app/Filament/Resources/TicketResource/Tables/TicketsTable.php
```

## Architecture

### Hybrid Pattern Implementation

```php
class TicketsTable extends XotBaseResourceTable
{
    // NEW: Filament v5 style - fluent Table configuration
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([...])
            ->filters([...])
            ->defaultSort('created_at', 'desc');
    }
    
    // LEGACY: Backward compatibility
    public static function table(Table $table): Table
    {
        return static::configure($table);
    }
}
```

## Table Structure

### Columns

| Column | Type | Features |
|--------|------|----------|
| `id` | TextColumn | Sortable |
| `name` | TextColumn | Searchable, sortable, limited to 50 chars |
| `status` | TextColumn | Badge format, sortable |
| `priority` | TextColumn | Badge format, sortable |
| `type.name` | TextColumn | Placeholder '-' if null |
| `owner.name` | TextColumn | Placeholder '-' if null |
| `assignee.name` | TextColumn | Placeholder '-' if null |
| `created_at` | TextColumn | DateTime format, sortable |
| `updated_at` | TextColumn | DateTime format, sortable, toggleable (hidden by default) |

### Filters

| Filter | Type | Options |
|--------|------|---------|
| `status` | SelectFilter | `TicketStatusEnum` |
| `priority` | SelectFilter | `TicketPriorityEnum` |
| `type_id` | SelectFilter | `TicketTypeEnum` |

### Features

- **Default Sort**: `created_at` descending
- **Striped**: Alternating row colors
- **Searchable**: `name` column
- **Toggleable Columns**: `updated_at` hidden by default

## Translation Keys

All labels auto-resolved from `Modules/Fixcity/lang/{locale}/ticket.php`:

```php
// Modules/Fixcity/lang/it/ticket.php
return [
    'table' => [
        'columns' => [
            'id' => ['label' => 'ID'],
            'name' => ['label' => 'Titolo'],
            'status' => ['label' => 'Stato'],
            'priority' => ['label' => 'Priorità'],
            'type' => ['label' => 'Tipo'],
            'owner' => ['label' => 'Creatore'],
            'assignee' => ['label' => 'Assegnato a'],
            'created_at' => ['label' => 'Data creazione'],
            'updated_at' => ['label' => 'Ultima modifica'],
        ],
        'filters' => [
            'status' => ['label' => 'Filtra per stato'],
            'priority' => ['label' => 'Filtra per priorità'],
            'type_id' => ['label' => 'Filtra per tipo'],
        ],
    ],
];
```

## Critical Rules

### ✅ DO
- Extend `XotBaseResourceTable`
- Use `configure(Table $table): Table` for new code
- Use `->badge()` for status/priority columns
- Use `->placeholder('-')` for nullable relationships
- Use `->toggleable()` for less important columns
- Use `->sortable()` on frequently sorted columns
- Use `->searchable()` on name/title columns

### ❌ DON'T
- Never use `->label()` explicit calls
- Never use `->tooltip()` explicit calls
- Never hardcode column widths (use `->limit()` for text)

## Resource Integration

XotBaseResource auto-resolves the Table class:

```php
// In TicketResource.php - NO method needed!
// XotBaseResource automatically resolves:
// TicketResource\Tables\TicketsTable::configure($table)

// If you need to override:
public static function table(Table $table): Table
{
    return TicketsTable::configure($table)
        ->columns([...]) // Additional customization
        ->defaultSort('priority', 'desc');
}
```

## References

### External
- **Filament v5 Demo**: https://github.com/filamentphp/demo/blob/5.x/app/Filament/Resources/HR/Departments/Tables/DepartmentsTable.php
- **Filament Docs**: https://filamentphp.com/docs/5.x/schemas/tables

### Internal
- **Parent Class**: `Modules/Xot/app/Filament/Resources/Schemas/XotBaseResourceTable.php`
- **Pattern Guide**: `Modules/Xot/docs/wiki/concepts/filament-v5-hybrid-pattern.md`
- **Story 8-91**: `.planning/stories/8-91-filament-v5-schemas-structure-refactor.story.md`

## Testing

```bash
# PHPStan check
cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity/app/Filament/Resources/TicketResource/Tables/TicketsTable.php --level=5

# PHPMD check
./vendor/bin/phpmd.phar Modules/Fixcity/app/Filament/Resources/TicketResource/Tables/TicketsTable.php text cleancode,codesize,controversial,design,naming,unusedcode
```

## Complete File Structure

```
Modules/Fixcity/app/Filament/Resources/TicketResource/
├── Schemas/
│   ├── TicketForm.php          # Form schema (wizard-based)
│   ├── TicketInfolist.php      # Infolist (Tabs: Overview + Location)
│   └── TicketWizard.php         # (optional) Wizard configuration
├── Tables/
│   └── TicketsTable.php         # ✅ Table columns/filters
├── Pages/
│   ├── ListTickets.php          # Uses TicketsTable
│   ├── ViewTicket.php           # Uses TicketInfolist
│   ├── EditTicket.php           # Uses TicketForm
│   └── CreateTicket.php         # Uses TicketForm
└── TicketResource.php           # Resource configuration
```

---

*Part of Filament v5 Hybrid Pattern implementation. See Story 8-91 for multi-agent rollout plan.*
