# Filament Infolist Guidance for Wizard Summary (Fixcity)

## Regola

Nel wizard summary step usare `Filament\Infolists\Components\TextEntry` con `->state(fn(Get $get))`.
**MAI** usare `TextInput`/`Textarea` disabilitati come riepilogo.
**MAI** usare `->label()`, `->placeholder()`, `->helperText()` — gestiti da `LangServiceProvider`.

## Implementazione attuale

File: `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm::getSummarySchema()`

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

public static function getSummarySchema(): array
{
    return [
        Section::make()
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])
                    ->schema([
                        TextEntry::make('review_type')
                            ->state(static function (Get $get): string {
                                $type = $get('type');
                                if ($type instanceof \BackedEnum) {
                                    return (string) $type->value;
                                }
                                return (string) ($type ?? '');
                            }),
                        TextEntry::make('review_name')
                            ->state(static fn (Get $get): string => (string) ($get('name') ?? '')),
                        TextEntry::make('review_content')
                            ->columnSpanFull()
                            ->state(static fn (Get $get): string => (string) ($get('content') ?? '')),
                        TextEntry::make('review_location')
                            ->columnSpanFull()
                            ->state(static function (Get $get): string {
                                $location = $get('location');
                                if (! is_array($location)) { return ''; }
                                if (isset($location['address']) && is_string($location['address']) && '' !== $location['address']) {
                                    return $location['address'];
                                }
                                $lat = $location['lat'] ?? $location['latitude'] ?? null;
                                $lng = $location['lng'] ?? $location['longitude'] ?? null;
                                if (null !== $lat && null !== $lng) {
                                    return (string) $lat.', '.(string) $lng;
                                }
                                return '';
                            }),
                    ]),
            ]),
    ];
}
```

## Note

- Import da `Filament\Infolists\Components` (entries) e `Filament\Schemas\Components` (layout).
- `BackedEnum` → usare `->value` (non `(string)` diretto).
- Per contenuto HTML statico (privacy, disclaimer) usare blade view dedicata.

## Riferimenti

- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketInfolist.php`
- https://filamentphp.com/docs/5.x/infolists/overview
