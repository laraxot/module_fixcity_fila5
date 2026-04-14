# Filament Components — Guida Corretta

## Verifica Fatto (2026-04-14)

`Filament\Schemas\Components\Text` **NON ESISTE**. Tentare di usarlo causa errore `Class not found`.

## Componenti Esistenti

| Component | Namespace | Uso |
|-----------|-----------|-----|
| `Placeholder` | `Filament\Forms\Components` | Contenuto statico HTML (non-dehydrated) |
| `TextEntry` | `Filament\Infolists\Components` | Dati read-only dal form state |
| `TextInput` | `Filament\Forms\Components` | Input testo |
| `Select` | `Filament\Forms\Components` | Select/dropdown |
| `Textarea` | `Filament\Forms\Components` | Textarea multi-riga |
| `Checkbox` | `Filament\Forms\Components` | Checkbox |
| `FileUpload` | `Filament\Forms\Components` | Upload file |

## Pattern Corretti

### HTML Statico (privacy notice, help text)
```php
Placeholder::make('privacy_notice')
    ->content(fn (): HtmlString => $this->getPrivacyNoticeHtml())
    ->dehydrated(false)
    ->columnSpanFull(),
```

### Dati Read-Only (author info, summary)
```php
TextEntry::make('author_name')
    ->state(fn (): string => $this->getAuthUserName())
    ->icon('heroicon-o-user'),
```

### Input Form
```php
TextInput::make('name')
    ->required()
    ->maxLength(255),
```

## Imports da Usare

```php
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;  // Read-only data
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
```

## MAI Usare

- ❌ `Filament\Schemas\Components\Text` — non esiste
- ❌ `TextEntry` dentro Infolist schema per HTML statico — usa `Placeholder`
- ❌ `Placeholder` per dati del form — usa `TextEntry`

---
*Ultimo aggiornamento: 2026-04-14*
*Verificato: Text::make → Class not found error*
