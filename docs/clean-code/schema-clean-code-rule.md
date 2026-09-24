# Schema Clean Code Rule

**Rule**: `->schema()` must always call a method that returns an array with string keys

## Definition

All methods that call `->schema()` must receive as parameter an associative array where keys are strings.

## Correct Pattern

```php
// Schema defined in a method that returns array<string, mixed>
public static function getFormSchema(): array
{
    return [
        'title' => TextInput::make('title'),
        'description' => Textarea::make('description'),
        'status' -> Select::make('status'),
    ];
}

// Correct usage
public static function form(Schema $schema): Schema
{
    return $schema->schema(static::getFormSchema());
}
```

## Incorrect Pattern

```php
// Inline schema (not reusable)
public static function form(Schema $schema): Schema
{
    return $schema->schema([
        TextInput::make('title'),
        Textarea::make('description'),
        Select::make('status'),
    ]);
}
```

## Benefits

1. **Reusability**: Methods like `getFormSchema()` can be reused in different contexts
2. **Testability**: Each schema can be tested separately
3. **Maintainability**: Schema changes can be made in one place
4. **Readability**: Code is more organized and readable

## Application

This rule applies to:
- `Filament\Resources\Resources::schema()`
- `Filament\Resources\Pages\Page::schema()`
- `Filament\Widgets\Widget::schema()`
- `Filament\Forms\Form::schema()`
- `Filament\Infolists\Infolist::schema()`

## Exceptions

Does not apply to:
- Indexed arrays for simple layout (e.g., grid)
- Temporary arrays for internal configuration

## Implementation

Methods following this rule must:

1. Define a `getSchemaName()` method that returns array<string, mixed>
2. Use that method in `->schema(static::getSchemaName())`
3. Keep the `getSchemaName()` method without side effects

## Practical Examples

### Resource Schema

```php
// Correct
class UserResource extends Resource
{
    public static function schema(Schema $schema): Schema
    {
        return $schema->schema(static::getUserSchema());
    }

    public static function getUserSchema(): array
    {
        return [
            'personal_info' => Section::make('Personal Information')
                ->schema([
                    TextInput::make('name'),
                    TextInput::make('email'),
                ]),
            'profile' => Section::make('Profile')
                ->schema([
                    Select::make('role'),
                    Toggle::make('active'),
                ]),
        ];
    }
}
```

### Wizard Schema

```php
// Correct
class CreateTicketWizardWidget extends XotBaseWizardWidget
{
    public function getSteps(): array
    {
        return [
            Step::make('Personal Data')
                ->schema(static::getPersonalDataSchema()),
            Step::make('Ticket Details')
                ->schema(static::getTicketDetailsSchema()),
        ];
    }

    public static function getPersonalDataSchema(): array
    {
        return [
            'contact_info' -> Section::make('Contact')
                ->schema([
                    TextInput::make('name'),
                    TextInput::make('email'),
                ]),
            'address' -> Section::make('Address')
                ->schema([
                    TextInput::make('address'),
                    TextInput::make('city'),
                ]),
        ];
    }
}
```

## Verification

This rule can be verified with:
- PHPStan custom rule for return type
- Code analysis to identify non-compliant patterns
- Manual review during code review

**Date**: 2026-05-14  
**Status**: Implemented  
**Module**: All Laravel Modules