# TicketForm Schema Guidelines

## Overview

This document outlines the proper usage patterns for the TicketForm schema in the Fixcity module.

## Schema Structure

```php
class TicketForm extends XotBaseResourceForm
{
    public static function getSteps(): array
    {
        return [
            static::getStepByName('privacy'),
            static::getStepByName('data'),
            static::getStepByName('summary'),
        ];
    }

    // Wrapper for full form schema (required by XotBaseResourceForm)
    public static function getFormSchema(): array
    {
        return array_merge(
            static::getPrivacySchema(),
            static::getDataSchema(),
            static::getSummarySchema(),
        );
    }

    public static function getPrivacySchema(): array { /* ... */ }
    public static function getDataSchema(): array { /* ... */ }
    public static function getSummarySchema(): array { /* ... */ }
}
```

## Schema Return Types

**ALL schema methods must return `array<string, SchemaComponent>`**

```php
/**
 * @return array<string, SchemaComponent>
 */
public static function getSummarySchema(): array
{
    return [
        'summaryGrid' => Grid::make(2)->schema([
            TextEntry::make('name')
                ->label(__('fixcity::segnalazione.fields.name.label')),
            // ...
        ]),
    ];
}
```

**Key must be a string**, not an integer.

## Label and Placeholder Usage

### Prohibited
- Explicit use of `->label()` on form components (except summary infolist)
- Explicit use of `->placeholder()` on form components
- Hardcoded strings for labels or placeholders

### Required
- Use `->hiddenLabel()` for all form components
- Rely on LangServiceProvider for automatic label generation via translation keys
- Use translation keys following the pattern: `fixcity::segnalazione.fields.{field}.label`

### Example (Correct)
```php
TextInput::make('name')
    ->hiddenLabel()
    ->columnSpanFull()
    ->required()
    ->maxLength(255),
```

## CoordinatePicker Rules

- **NO** `geolocateWhenEmpty()` - component auto-geolocates when no state data
- **YES** `->reverseGeocoding()` for address lookup

```php
'location' => CoordinatePicker::make('location')
    ->columnSpanFull()
    ->reverseGeocoding(),
```

## GDPR Text (Privacy Step)

Uses `getGdprHtml()` which returns `Illuminate\Support\HtmlString`:

```php
public static function getGdprHtml(): HtmlString
{
    $municipality = (string) config('app.name', 'Il Comune');
    $gdprText = (string) __('fixcity::segnalazione.gdpr_notice.text', [
        'municipality' => $municipality,
    ]);
    $privacyLinkText = (string) __('fixcity::segnalazione.gdpr_notice.privacy_link');
    $privacyUrl = '#';

    return new HtmlString($gdprText.' <a href="'.$privacyUrl.'" class="t-primary">'.$privacyLinkText.'</a>');
}
```

### Usage in Schema
```php
'gdprSection' => Section::make()
    ->schema([
        TextEntry::make('gdpr_text')
            ->html(fn (): HtmlString => self::getGdprHtml()),
    ])
    ->columnSpanFull(),
```

## Translations

Key structure: `fixcity::segnalazione.gdpr_notice.*`

```php
// lang/it/segnalazione.php
'gdpr_notice' => [
    'text' => 'Il Comune di :municipality gestisce i dati personali forniti e liberamente comunicati sulla base dell\'articolo 13 del Regolamento (UE) 2016/679 General Data Protection Regulation (GDPR)...',
    'privacy_link' => 'informativa sulla privacy.',
],

// lang/en/segnalazione.php
'gdpr_notice' => [
    'text' => 'The Municipality of :municipality manages the personal data provided and freely communicated on the basis of Article 13 of Regulation (EU) 2016/679 General Data Protection Regulation (GDPR)...',
    'privacy_link' => 'privacy policy.',
],
```

## PHPStan Compliance

```bash
./vendor/bin/phpstan analyse Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php
# [OK] No errors
```

## Related Documentation
- [Filament Forms Documentation](https://filamentphp.com/docs/forms)
- [Wizard Architecture](wizard-architecture.md)
- [Translation Guidelines](../lang/README.md)