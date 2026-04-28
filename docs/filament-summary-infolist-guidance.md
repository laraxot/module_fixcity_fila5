# Filament Infolist Guidance for Wizard Summary (Fixcity)

Overview

In Filament 5, the wizard summary step must expose read-only structured data. Use `Filament\Infolists\Components` entries (`TextEntry`, `ImageEntry`, etc.) inside schema layout components, mapped to wizard state via `Filament\Schemas\Components\Utilities\Get`. Do **not** use `SchemaView`, `View::make()` or `Placeholder` for primary structured summary data.

References

- Official Filament docs: https://filamentphp.com/docs/5.x/infolists/overview

Recommended pattern (example)

```php
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

public function getSummarySchema(): array
{
    return [
        Section::make((string) __('fixcity::create_ticket_wizard.summary.section.label'))
            ->schema([
                Grid::make(['default' => 1, 'md' => 2])->schema([
                    TextEntry::make('review_name')
                        ->state(fn (Get $get): string => (string) ($get('name') ?? '')),
                    TextEntry::make('review_content')
                        ->state(fn (Get $get): string => (string) ($get('content') ?? '')),
                ]),
            ]),
    ];
}
```

Implementation notes

- Import entry classes from `Filament\Infolists\Components` and layout classes from `Filament\Schemas\Components`.
- There is no `Filament\Infolists\Components\Infolist` component to put inside `Step::schema()`.
- Map state using `Get $get` in closures for robust server-driven state resolution.
- Do not call `->label()` or `->placeholder()` in this project; translation/autolabel rules own labels.
- For purely static HTML content (privacy notices, disclaimers), keep using Filament\Schemas prime components (Text) or dedicated theme views.

Acceptance criteria

- Summary step uses Infolist components with proper state mapping.
- No `SchemaView`, `View::make()`, Placeholder, or disabled input fields used as primary summary content.
- Docs updated and indexed in LLM wiki.
