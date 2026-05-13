# TicketForm Schema Guidelines

## Overview
This document outlines the proper usage patterns for the TicketForm schema in the Fixcity module, compliant with the project's on-demand rules and Filament best practices.

## Label and Placeholder Usage

### Prohibited
- Explicit use of `->label()` on form components
- Explicit use of `->placeholder()` on form components
- Hardcoded strings for labels or placeholders

### Required
- Use `->hiddenLabel()` for all form components
- Rely on LangServiceProvider for automatic label generation via translation keys
- Use translation keys following the pattern: `{namespace}::{resource}.{field}.{property}`

### Example (Correct)
```php
TextInput::make('name')
    ->hiddenLabel()
    ->columnSpanFull()
    ->required()
    ->maxLength(255),
```

### Translation Key Format
Labels and placeholders are automatically generated using keys like:
- `fixcity::fixcity.ticket.name.label`
- `fixcity::fixcity.ticket.name.placeholder`

## Wizard Schema Implementation

### getSteps() Method
The TicketForm implements `getSteps()` to define wizard steps:
1. Privacy step (accept terms)
2. Data step (main form fields)
3. Summary step (read-only review)

### getSummarySchema() Pattern
The summary schema must use Infolist components (`TextEntry`) rather than disabled form inputs:
- Use `TextEntry::make()` for read-only display
- Use `->state(fn(Get $get))` to read values from wizard state
- Format enum values using dedicated formatter methods
- Group related fields using Grid and Section components

### Example (Correct Summary Schema)
```php
public static function getSummarySchema(): array
{
    return [
        Section::make()
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])
                    ->schema([
                        TextEntry::make('type_id')
                            ->state(static fn (Get $get): string => static::formatTicketType($get('type_id'))),
                        TextEntry::make('priority')
                            ->state(static fn (Get $get): string => static::formatTicketPriority($get('priority'))),
                        // ... other fields
                    ]),
            ]),
    ];
}
```

## Form Schema Composition
- `getFormSchema()` should return `static::getDataSchema()` for consistency
- Avoid commented-out code in production files
- Ensure all form fields correspond to actual model attributes

## Related Documentation
- [Filament Forms Documentation](https://filamentphp.com/docs/forms)
- [Project On-Demand Rules](../rules/00-TRIGGER_MAP.md)
- [Translation Guidelines](../lang/README.md)