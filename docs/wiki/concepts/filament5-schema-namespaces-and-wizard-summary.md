# Filament 5.x — Namespace Schema e Pattern Wizard Summary

## Namespace corretti (fonte: vendor/filament/schemas/src/)

| Componente | Namespace corretto |
|---|---|
| `Grid` | `Filament\Schemas\Components\Grid` |
| `Section` | `Filament\Schemas\Components\Section` |
| `Text` | `Filament\Schemas\Components\Text` |
| `View` | `Filament\Schemas\Components\View` |
| `Wizard` | `Filament\Schemas\Components\Wizard` |
| `Step` | `Filament\Schemas\Components\Wizard\Step` |
| `TextEntry` | `Filament\Infolists\Components\TextEntry` |
| `ImageEntry` | `Filament\Infolists\Components\ImageEntry` |

### VIETATO

- `Filament\Infolists\Components\Infolist` — non esiste come componente schema
- `SchemaView` — non esiste in Filament 5.x; usare `Filament\Schemas\Components\View`
- `Livewire\Forms\Form` — NON importare nel widget; Filament usa `Filament\Schemas\Schema`

## Errore PHP: "name is already in use"

**Causa**: doppio blocco `use` con lo stesso class alias nello stesso file.

```php
// SBAGLIATO — TextEntry dichiarato due volte
use Filament\Infolists\Components\TextEntry; // riga 14
// ... altre righe ...
use Filament\Infolists\Components\TextEntry; // riga 35 — PHP FatalError!
```

**Regola**: ogni `use` compare UNA sola volta per file.

## Pattern Wizard Summary Step

Il summary step mostra i dati del form in sola lettura.  
Il widget è Livewire: `$this` è accessibile nel Blade, quindi `$this->form->getState()` è disponibile nella view.

### Approccio corretto: TextEntry + Get (pattern Infolist ufficiale)

Ref: https://filamentphp.com/docs/5.x/infolists/overview

```php
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

public function getSummarySchema(): array
{
    return [
        Section::make()->schema([
            Grid::make(2)->schema([
                TextEntry::make('review_type')
                    ->state(fn (Get $get): string => (string) ($get('type_id') ?? '—')),
                TextEntry::make('review_name')
                    ->state(fn (Get $get): string => (string) ($get('name') ?? '—')),
            ]),
        ]),
    ];
}
```

`TextEntry::make()->state(fn(Get $get) => ...)` legge lo stato del form tramite la utility `Get`
di Filament 5.x — NON richiede un record Eloquent.

### VIETATO

- `SchemaView::make(...)` — non è il pattern Infolist
- `Infolist::make('name')` — non è un componente Schema
- `TextEntry` senza `->state()` quando manca il record

## Riferimento codice

- Pattern ViewRecord corretto: `laravel/Modules/Xot/app/Filament/Resources/LogResource/Pages/ViewLog.php`
- Base wizard: `laravel/Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php`
- Widget segnalazione: `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`

## Anti-pattern linter (Pint/PHP-CS-Fixer)

Se il linter trova più `Section` da namespace diversi, li aliasa tutti e tre — `Section::make()` non risolve più:

```php
// ❌ Prodotto dal linter — SBAGLIATO
use Filament\Forms\Components\Section as FormSection;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Schemas\Components\Section as SchemaSection;
```

**Fix**: rimuovere tutti gli alias errati, tenere SOLO `use Filament\Schemas\Components\Section;`.

## Storia

- Story 8-41: refactor getSummarySchema da SchemaView a View::make()
- Data: 2026-04-22
