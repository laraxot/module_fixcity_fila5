# Select con Enum in Filament - Best Practices

## Problema
Quando si usa `Select::make('field')->options(YourEnum::class)` in Filament, a volte non funzionano correttamente le etichette tradotte.

## Soluzione Corretta (Filament Native)
Filament gestisce automaticamente gli enum che implementano `HasLabel`. Usa semplicemente:

```php
Select::make('type_id')
    ->options(TicketTypeEnum::class)  // NON usare TicketTypeEnum::cases()
    ->required()
    ->native(false),
```

## Perché Non Funzionava il Codice Complesso
Il codice con `reduce()` o `collect()` era:
1. **Complesso** inutile: Filament già fa questa conversione
2. ** fragile**: Rompe se cambia l'API di Filament
3. **contro le best practices**: Filament ha un sistema integrato per questo

## Come Funziona Internamente
1. Filament vede che è una stringa con un enum
2. Chiama automaticamente `->enum(TicketTypeEnum::class)`
3. Nel metodo `getOptions()`, Filament:
   - Controlla se l'enum implementa `HasLabel`
   - Se sì, usa `$case->getLabel()` per le opzioni
   - Se no, usa `$case->name`

## Requisiti
L'enum deve implementare `HasLabel` (tramite `EnumTrait` nel nostro caso):

```php
enum TicketTypeEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;
    
    // ... casi enum
}
```

## Traduzioni
Le traduzioni devono essere nel file `lang/{locale}/enums.php`:

```php
'road_maintenance' => [
    'label' => 'Manutenzione Stradale',
    'color' => '#ff9800',
],
```

## Esempio Funzionante
Vedi `Modules/Fixcity/app/Filament/Resources/TicketResource.php`:

```php
Select::make('type')
    ->hiddenLabel()
    ->placeholder(__('fixcity::fixcity.ticket.type.placeholder').'*')
    ->searchable()
    ->options(TicketTypeEnum::class)  // Così si fa!
    ->columnSpanFull(),
```

## Regola Aurea
**MAI** usare `Enum::cases()` come opzioni in un Select di Filament. Usa sempre `Enum::class`.