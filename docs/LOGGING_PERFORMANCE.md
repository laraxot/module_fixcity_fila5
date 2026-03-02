# Logging Performance Optimization

## Rule: NEVER USE Log::info()

### Impact
- Slows down requests by 30-50%
- Wastes disk space with useless information
- Makes debugging harder with noise
- Each call blocks execution with disk I/O

### Correct Usage

#### For Errors Only
```php
Log::error('Ticket creation failed', [
    'title' => $title,
    'error' => $e->getMessage(),
]);
```

#### For Warnings Only
```php
Log::warning('API rate limit', [
    'user_id' => $user->id,
    'attempts' => $attempts,
]);
```

#### For Audit Trails (NOT logs)
```php
activity()
    ->causedBy(auth()->user())
    ->performedOn($ticket)
    ->log('Ticket created');
```

### WRONG Usage (NEVER do this)
```php
Log::info('Ticket created', ['ticket_id' => $id]);
Log::info('User logged in', ['user_id' => $id]);
Log::info('Notification sent', ['email' => $email]);
Log::info('Request received', ['url' => $url]);
```

### CORRECT Usage (DO this instead)
```php
// Only log when something goes wrong
Log::error('Ticket creation failed', ['error' => $e->getMessage()]);

// Use activity for audit trails
activity()->causedBy($user)->log('Ticket created');

// Use metrics for tracking
Metrics::increment('tickets.created');
```