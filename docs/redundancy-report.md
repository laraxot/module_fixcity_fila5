- Inventario [ridondanze cross-modulo](../docs/redundancy-report.md)
- Concetti [ridondanze cross-cutting](../Xot/docs/wiki/concepts/ridondanze-cross-cutting-codebase.md)

# Redundancy Report — Modulo Fixcity

> Generato: 2026-05-21 | Analisi automatica deep-scan

## Problemi Trovati

### 1. 🔴 BaseModel NON estende XotBaseModel

**File**: `app/Models/BaseModel.php`

```php
// ATTUALE (NON conforme)
abstract class BaseModel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Updater;
}

// CORRETTO (conforme Laraxot)
abstract class BaseModel extends XotBaseModel
{
    use SoftDeletes; // se necessario
}
```

`XotBaseModel` include già `HasFactory`, `Updater` e la logica factory tramite `GetFactoryAction`. Estendere `Model` direttamente duplica funzionalità e viola la regola Laraxot.

### 2. 🔴 BasePivot NON estende XotBasePivot

**File**: `app/Models/BasePivot.php`

```php
// ATTUALE
abstract class BasePivot extends Pivot
{
    use HasFactory;
    use Updater;
}

// CORRETTO
abstract class BasePivot extends XotBasePivot {}
```

### 3. 🟠 CommentsRelationManager — 2 copie identiche

| File | Namespace |
|------|-----------|
| `app/Filament/Resources/RelationManagers/CommentsRelationManager.php` | `Modules\Fixcity\Filament\Resources\RelationManagers` |
| `app/Filament/Resources/TicketResource/RelationManagers/CommentsRelationManager.php` | `Modules\Fixcity\Filament\Resources\TicketResource\RelationManagers` |

Entrambe hanno import identici, stessa logica, estendono `RelationManager`. Solo il namespace differisce.

**Azione**: Eliminare `Resources/RelationManagers/CommentsRelationManager.php` e usare solo quella in `TicketResource/RelationManagers/`.

### 4. 🟡 TicketForm.php — Import duplicato (RISOLTO 2026-05-21)

**File**: `app/Filament/Resources/TicketResource/Schemas/TicketForm.php`

Aveva un import duplicato di `XotBaseResourceForm` (una dal namespace locale e una da Xot) che causava un fatal error PHP. Rimossa la riga ridondante.

### 5. 🟡 ChangeStatus — 2 versioni

| File | Tipo |
|------|------|
| `app/Actions/ChangeStatus.php` | Action class |
| `app/Filament/Actions/ChangeStatus.php` | Filament Action |

Potenziale confusione. Verificare se entrambe sono necessarie o se l'Action Filament dovrebbe usare l'Action class internamente.

## Riepilogo

| Priorità | Problema | Stato |
|----------|----------|-------|
| 🔴 | BaseModel non conforme | Da risolvere |
| 🔴 | BasePivot non conforme | Da risolvere |
| 🟠 | CommentsRelationManager duplicato | Da eliminare copia |
| 🟡 | TicketForm import duplicato | ✅ Risolto |
| 🟡 | ChangeStatus 2 versioni | Da verificare |
