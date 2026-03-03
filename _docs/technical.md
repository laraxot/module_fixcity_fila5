# Documentazione Tecnica FixCity

## Architettura del Sistema

### Overview
FixCity è costruito seguendo i principi dell'architettura esagonale (ports and adapters), separando chiaramente:
- Domain Layer (logica di business)
- Application Layer (casi d'uso)
- Infrastructure Layer (implementazioni tecniche)

### Domain Layer
```
app/
└── Models/
    ├── Profile.php        # Gestione profili utente
    ├── User.php          # Gestione utenti
    └── Ticket.php        # Gestione segnalazioni
```

#### Modello Profile
- Gestisce i dati del profilo utente
- Relazioni con User e Ticket
- Attributi personalizzati

#### Modello User
- Estende il modello base di Laravel
- Implementa autenticazione e autorizzazione
- Gestisce ruoli e permessi

#### Modello Ticket
- Core della gestione segnalazioni
- Sistema di stati e transizioni
- Tracciamento modifiche

### Application Layer
```
app/
├── Services/
│   ├── TicketService.php
│   └── NotificationService.php
├── Events/
│   └── TicketEvents/
└── Listeners/
    └── TicketListeners/
```

### Infrastructure Layer
```
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Resources/
└── Providers/
    └── FixcityServiceProvider.php
```

## Filament Integration

### Resources
```php
use Filament\Resources\Resource;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form fields
            ]);
    }
}
```

### Widgets
```php
use Filament\Widgets\Widget;

class TicketStatsWidget extends Widget
{
    protected static string $view = 'fixcity::widgets.ticket-stats';
}
```

## Database Schema

### Tabelle Principali
```sql
CREATE TABLE profiles (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    name VARCHAR(255),
    address TEXT,
    phone VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

CREATE TABLE tickets (
    id BIGINT UNSIGNED PRIMARY KEY,
    title VARCHAR(255),
    description TEXT,
    status VARCHAR(50),
    priority VARCHAR(20),
    reporter_id BIGINT UNSIGNED,
    assignee_id BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## API Endpoints

### Autenticazione
Tutte le richieste API richiedono un token Bearer JWT.

### Formato Risposte
```json
{
    "success": true,
    "data": {},
    "message": "Operation successful",
    "errors": []
}
```

### Gestione Errori
```php
return response()->json([
    'success' => false,
    'message' => 'Error message',
    'errors' => ['field' => ['error details']]
], 400);
```

## Eventi e Listeners

### Eventi Disponibili
```php
namespace Modules\Fixcity\Events;

class TicketCreated
{
    public function __construct(public Ticket $ticket)
    {}
}
```

### Listeners
```php
namespace Modules\Fixcity\Listeners;

class SendTicketNotification
{
    public function handle(TicketCreated $event)
    {
        // Logica notifica
    }
}
```

## Configurazione

### File di Configurazione
```php
// config/fixcity.php
return [
    'notifications' => [
        'channels' => ['mail', 'database'],
        'templates' => [
            'ticket_created' => 'fixcity::emails.ticket.created',
            'ticket_updated' => 'fixcity::emails.ticket.updated'
        ]
    ],
    'uploads' => [
        'disk' => 'public',
        'directory' => 'tickets'
    ]
];
```

## Testing

### Unit Tests
```php
namespace Modules\Fixcity\Tests\Unit;

class TicketTest extends TestCase
{
    /** @test */
    public function it_can_create_ticket()
    {
        // Test logic
    }
}
```

### Feature Tests
```php
namespace Modules\Fixcity\Tests\Feature;

class TicketApiTest extends TestCase
{
    /** @test */
    public function it_can_list_tickets()
    {
        // Test logic
    }
}
```

## Performance

### Ottimizzazioni
1. Eager Loading delle relazioni
2. Cache delle query frequenti
3. Indici database ottimizzati
4. Queue per operazioni pesanti

### Caching
```php
use Illuminate\Support\Facades\Cache;

Cache::remember('ticket.stats', 3600, function () {
    // Calcolo statistiche
});
```

## Sicurezza

### Autorizzazione
```php
Gate::define('update-ticket', function (User $user, Ticket $ticket) {
    return $user->id === $ticket->reporter_id || $user->hasRole('admin');
});
```

### Validazione
```php
$request->validate([
    'title' => 'required|string|max:255',
    'description' => 'required|string',
    'priority' => 'required|in:low,medium,high,critical'
]);
```

## Deployment

### Requisiti
- PHP 8.2+
- MySQL 8.0+
- Redis (opzionale)
- Node.js 18+ (per assets)

### Comandi
```bash
# Installazione
composer install --no-dev
php artisan migrate --force
php artisan module:seed Fixcity

# Cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Assets
npm install
npm run build
``` 