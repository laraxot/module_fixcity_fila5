# Wizard Runtime Lessons Learned

## Data: 2026-04-23

Lezioni operative dal wizard CreateTicketWizardWidget e dai suoi componenti Geo.

---

## 1. EnumSelect Signature Mismatch — FatalError

### Problema

Il componente `EnumSelect::make(string $name)` aveva una signature incompatibile con `Filament\Forms\Components\Field::make(?string $name = null)`.

```
Declaration of Modules\UI\Filament\Forms\Components\EnumSelect::make(string $name)
must be compatible with Filament\Forms\Components\Field::make(?string $name = null)
```

### Fix

```php
public static function make(?string $name = null): static
```

### Lezione

Ogni override di `make()` su un componente Filament custom DEVE rispettare la signature del parent. PHPStan level max lo rileva — MA solo se il bootstrap non fallisce prima per altri errori.

### Regola

SEMPRE verificare le signature dei metodi overridden contro la classe parent quando si creano componenti custom che estendono Filament.

---

## 2. Merge Conflict Markers = Fatal Error in Bootstrap

### Problema

Marker di merge conflict (`<<<<<<< HEAD`, `=======`, `>>>>>>>`) in `CreateTicketWizardWidget.php` alla riga 28 bloccavano l'intera applicazione.

### Anti-pattern

Modificare codice e considerare "fatto" senza grep per merge conflict markers.

### Best Practice

PRIMA di qualsiasi verifica (PHPStan, browser test):

```bash
grep -rl "<<<<<<" laravel/Modules/ --include="*.php" --include="*.blade.php"
```

---

## 3. CoordinatePicker Namespace — Il Doppio Esistente

Nel modulo Geo esistono DUE CoordinatePicker:

| Namespace | Base Class | Uso |
|-----------|------------|-----|
| `Modules\Geo\Filament\Forms\Components\CoordinatePicker` | `XotBaseField` | **Da usare** — integrato con Filament Form |
| `Modules\Geo\Forms\Components\CoordinatePicker` | `Field` | Legacy / alternativa — NON usare nel wizard |

### Perché è una false friend

`Modules\Geo\Forms\Components\CoordinatePicker` sembra la path "standard" di un componente Geo, ma nel contesto del wizard Filament va usato il namespace `Filament\Forms\Components`.

---

## 4. GeopointPicker Import — Aggiungere, non sostituire

Nel conflict resolution di `CreateTicketWizardWidget`, l'import corretto era:

```php
use Modules\Geo\Filament\Forms\Components\CoordinatePicker;
use Modules\Geo\Filament\Forms\Components\GeopointPicker;
use Modules\UI\Filament\Forms\Components\EnumSelect;
```

HEAD aveva solo `CoordinatePicker`. L'altro branch aggiungeva `GeopointPicker` e `EnumSelect` — entrambi necessari per il wizard completo.

### Lezione

Nei merge conflict di import, SEMPRE controllare se il branch alternativo aggiunge funzionalità necessarie, non solo se HEAD è corretto.

---

## False Friends Riepilogo

| False Friend | Perché sembra giusto | Perché è sbagliato |
|---|---|---|
| `make(string $name)` obbligatorio | Il nome è sempre richiesto nel mio uso | Parent signature: `?string $name = null` |
| Merge conflict in un solo file | "Non tocca il mio codice" | Bootstrap failure → TUTTO l'app fallisce |
| `Geo\Forms\Components\CoordinatePicker` | Namespace più pulito | NON è il Filament-integrated version |
| Risolvere conflict scegliendo HEAD | HEAD = più recente | L'altro branch può contenere import necessari |

---

## Best Practices per il Wizard

1. **defaultFormData()** — Tutti i campi devono essere inizializzati, anche se null. Livewire/Alpine errorano su proprietà Entangle mancanti.
2. **Wizard step schema** — I componenti mappa devono essere nel place section con `CoordinatePicker`, non `GeopointPicker` (quest'ultimo è per deidratazione database).
3. **EnumSelect per backed enum** — Usare `EnumSelect::make('type_id')->enum(TicketTypeEnum::class)` per select con icone e label HTML.
4. **Location structured data** — Il campo `location` nel form state deve contenere `latitude`, `longitude`, `address`, e tutti gli address details per reverse geocoding.
