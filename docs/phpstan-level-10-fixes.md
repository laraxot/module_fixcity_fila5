# PHPStan Level 10 Fixes - Modulo Fixcity

## Panoramica
Documentazione degli errori PHPStan Level 10 identificati nel modulo Fixcity e delle soluzioni consigliate.

## Errori Identificati (2026-03-02)

### 1. Missing Model Methods (35 errori)

#### Ticket::setStatus()
**File:** `Fixcity/app/Actions/ChangeStatus.php:22`  
**Problema:** Metodo `setStatus()` non trovato su modello Ticket  
**Soluzione:**
```php
/**
 * Imposta lo stato del ticket
 *
 * @param string $status Nuovo stato
 * @return bool
 */
public function setStatus(string $status): bool {
    $this->status = $status;
    return $this->save();
}
```

#### Ticket::comments()
**File:** `Fixcity/app/Services/TicketService.php:171`  
**Problema:** Relazione `comments()` non definita  
**Soluzione:**
```php
/**
 * @return HasMany<Comment>
 */
public function comments(): HasMany {
    return $this->hasMany(Comment::class);
}
```

#### Ticket::activities()
**File:** `Fixcity/app/Services/WorkflowService.php:220`  
**Problema:** Relazione `activities()` non definita  
**Soluzione:**
```php
/**
 * @return HasMany<TicketActivity>
 */
public function activities(): HasMany {
    return $this->hasMany(TicketActivity::class);
}
```

### 2. Undefined Properties (8 errori)

#### Ticket::$assignee
**File:** `Fixcity/app/Services/NotificationService.php:209`  
**Problema:** Proprietà `$assignee` non documentata  
**Soluzione:**
```php
/**
 * @property User|null $assignee
 * @property-read User|null $assigneeRelation
 */
class Ticket extends BaseModel {
    public function assignee(): BelongsTo {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
```

### 3. Service Class Type Issues (12 errori)

#### NotificationService Collection Handling
**File:** `Fixcity/app/Services/NotificationService.php`  
**Problema:** Collection type mismatches  
**Soluzione:**
```php
/**
 * @return Collection<int, User>
 */
public function getUsersToNotify(): Collection {
    return User::where('notify', true)->get();
}
```

### 4. Factory Issues (5 errori)

#### ReportFactory - Missing Model
**File:** `Fixcity/database/factories/ReportFactory.php:13`  
**Problema:** Modello `Report` non trovato  
**Soluzione:**
```php
// Creare Modules/Fixcity/Models/Report.php
class Report extends BaseModel {
    protected $fillable = [
        'title',
        'description',
        'status',
    ];
}
```

#### TicketFactory - Missing Category Model
**File:** `Fixcity/database/factories/TicketFactory.php:21`  
**Problema:** Classe `Modules\Category\Models\Category` non trovata  
**Soluzione:**
```php
// Verificare namespace corretto e importazione
use Modules\Category\Models\Category;

'category_id' => Category::factory(),
```

### 5. Seeder Issues (8 errori)

#### Array Access on Mixed Type
**File:** `Fixcity/database/seeders/ReportContentSeeder.php:67-72`  
**Problema:** Accesso a array su tipo mixed  
**Soluzione:**
```php
/** @var array<string, mixed> $data */
$data = json_decode(file_get_contents($file), true);

if (is_array($data)) {
    $title = $data['title'] ?? null;
    $description = $data['description'] ?? null;
}
```

## Risultati Attuali
- ⚠️ **35 errori** PHPStan livello 10 (Fixcity module)
- 🔧 **In Progress** - Implementazione metodi mancanti
- 📝 **Planned** - Creazione modelli mancanti

## Prossimi Passi
1. Implementare metodi mancanti su Ticket model
2. Creare modello Report
3. Aggiungere PHPDoc completo a tutte le proprietà
4. Aggiornare Service classes con proper typing
5. Correggere Seeder per array access safety

## Pattern Identificati

### Pattern 1: Missing Relationships
Molti modelli mancano di relazioni esplicite. Soluzione:
```php
/**
 * @return HasMany<RelatedModel>
 */
public function relatedModels(): HasMany {
    return $this->hasMany(RelatedModel::class);
}
```

### Pattern 2: Undefined Properties
Proprietà non documentate causano errori. Soluzione:
```php
/**
 * @property int $id
 * @property string $name
 * @property-read Collection<int, RelatedModel> $related
 */
class Model extends BaseModel {
    // ...
}
```

### Pattern 3: Service Type Mismatches
Service classes non hanno type hints. Soluzione:
```php
/**
 * @return Collection<int, User>
 */
public function getUsers(): Collection {
    return User::all();
}
```

## Collegamenti
- [Report Completo PHPStan 2026-03-02](../../../docs/phpstan-analysis-2026-03-02.md)
- [PHPStan Level 10 Guide](../../../docs/phpstan-level-10-guide.md)
- [Ticket Model Documentation](./models/ticket.md)
