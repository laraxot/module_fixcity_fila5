# Filament Infolist Guidance for Wizard Summary (Fixcity)

## Regole

- Nel wizard summary step usare `Filament\Infolists\Components\TextEntry` con `->state(fn(Get $get))`.
- **MAI** usare `TextInput`/`Textarea` disabilitati come riepilogo (causano errori di cast enum→string).
- **MAI** usare `->label()`, `->placeholder()`, `->helperText()` — gestiti da `LangServiceProvider`.
- I nomi degli entry DEVONO avere prefisso `review_` per evitare conflitti con i field del form nello stesso wizard state.
- Usare helper statici `formatTicketType()` / `formatTicketPriority()` per il display delle enum (gestiscono sia `TicketTypeEnum|string|int`).

## Implementazione attuale

File: `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm::getSummarySchema()`

```php
TextEntry::make('review_type')
    ->state(static fn (Get $get): string => static::formatTicketType($get('type_id'))),
TextEntry::make('review_priority')
    ->state(static fn (Get $get): string => static::formatTicketPriority($get('priority'))),
TextEntry::make('review_name')
    ->columnSpanFull()
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
        $lat = $location['latitude'] ?? $location['lat'] ?? null;
        $lng = $location['longitude'] ?? $location['lng'] ?? null;
        if (null !== $lat && null !== $lng) {
            return (string) $lat.', '.(string) $lng;
        }
        return '';
    }),
```

## Note sui campi Ticket

| Campo | DB | Modello cast | Form field |
|---|---|---|---|
| `type_id` | `integer` nullable | `TicketTypeEnum::class` | `Select::make('type_id')->options(TicketTypeEnum::class)` |
| `priority` | `string` nullable | nessun cast (stringa raw) | `Select::make('priority')->options(TicketPriorityEnum::class)` |
| `location` | `json` nullable | `'array'` | `CoordinatePicker::make('location')` — chiavi: `latitude`, `longitude`, `address` |
| `images` | — | Spatie MediaLibrary | `SpatieMediaLibraryFileUpload::make('images')->collection('attachments')` |

## Riferimenti

- `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php`
- `Modules/Fixcity/app/Models/Ticket.php` (casts, fillable)
- `Modules/Fixcity/database/migrations/2026_04_29_110000_create_tickets_table.php`
- https://filamentphp.com/docs/5.x/infolists/overview
