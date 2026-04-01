# Fixcity Module - PHPStan Fix Plan

## Current Status

**Errors**: Multiple errors across several files
- Actions: 2 errors
- Livewire components: 8 errors
- Models: 4 errors
- Services: 6 errors (to be replaced with Actions)
- View Components: 2 errors

## Issues Analysis

### 1. Service Classes (6 errors) - ARCHITECTURAL CHANGE REQUIRED

**Files Affected**:
- `app/Services/NotificationService.php`
- `app/Services/TicketService.php`
- `app/Services/WorkflowService.php`

**Architectural Rule**: **NEVER create Service classes - use Spatie Queueable Actions**

**Fix Strategy**:
1. **Replace NotificationService with Actions**:
   - Create `NotifyStakeholdersAction`
   - Create `SendEmailNotificationAction`
   - Create `SendSmsNotificationAction`

2. **Replace TicketService with Actions**:
   - Create `AddCommentAction`
   - Create `CreateTicketAction`
   - Create `UpdateTicketAction`

3. **Replace WorkflowService with Actions**:
   - Create `ChangeStatusAction` (already exists)
   - Create `AssignToAgentAction`
   - Create `EscalateTicketAction`

**Example Implementation**:
```php
// app/Actions/AddCommentAction.php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;
use Spatie\QueueableAction\QueueableAction;

class AddCommentAction
{
    use QueueableAction;

    public function execute(Ticket $ticket, string $content, string $userId): void
    {
        $ticket->comments()->create([
            'content' => $content,
            'user_id' => $userId,
        ]);
    }
}
```

### 2. Ticket Model Missing Methods (3 errors)

**File**: `app/Models/Ticket.php`

**Missing Methods**:
```php
public function setStatus(string $status): self
public function comments(): MorphMany
public function activities(): MorphMany
```

**Fix Plan**:
1. Implement `setStatus()` method
2. Define `comments()` relationship (morphMany)
3. Define `activities()` relationship (morphMany)

**Implementation**:
```php
// app/Models/Ticket.php

public function setStatus(string $status): self
{
    $this->status = $status;
    $this->save();
    return $this;
}

public function comments(): MorphMany
{
    return $this->morphMany(Comment::class, 'commentable');
}

public function activities(): MorphMany
{
    return $this->morphMany(Activity::class, 'subject');
}
```

### 3. Livewire Components Type Hints (8 errors)

**Files Affected**:
- `app/Livewire/Auth/Login.php` (3 errors)
- `app/Livewire/TicketList.php` (5 errors)

**Fix Plan**:
1. Add return types to methods
2. Add type hints to properties
3. Fix undefined property access

**Implementation**:
```php
// app/Livewire/Auth/Login.php
class Login extends Component
{
    public string $email = '';
    public string $password = '';

    public function authenticate(): RedirectResponse
    {
        // Implementation
    }

    public function render(): View
    {
        return view('fixcity::livewire.auth.login');
    }
}

// app/Livewire/TicketList.php
class TicketList extends Component
{
    public string $search = '';
    public ?string $selectedCategory = null;
    public ?string $selectedStatus = null;

    /**
     * @return Collection<int, Ticket>
     */
    public function getTicketsProperty(): Collection
    {
        return Ticket::query()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->get();
    }
}
```

### 4. GenerateTicketsAction Type Hints (2 errors)

**File**: `app/Actions/GenerateTicketsAction.php`

**Fix Plan**:
1. Add type hint to `$faker` property
2. Cast `$faker` to proper type

**Implementation**:
```php
class GenerateTicketsAction
{
    /** @var \Faker\Generator */
    private $faker;

    public function __construct()
    {
        $this->faker = fake();
    }

    public function execute(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            Ticket::create([
                // ... use $this->faker
            ]);
        }
    }
}
```

### 5. CreateTicketWidget Schema Type (2 errors)

**File**: `app/Filament/Widgets/CreateTicketWidget.php`

**Fix Plan**:
1. Adjust schema() method to return correct type
2. Ensure components() receives proper array type

**Implementation**:
```php
protected function getFormSchema(): array
{
    return [
        TextInput::make('title'),
        Textarea::make('description'),
        // ... other fields
    ];
}
```

### 6. Ticket Policy Can Method (2 errors)

**File**: `app/Models/Policies/TicketPolicy.php`

**Fix Plan**:
1. Fix `can()` method call on User
2. Add proper return type

**Implementation**:
```php
public function create(User $user): bool|Response
{
    return $user->can('create', Ticket::class);
}
```

### 7. Ticket Model Relationships (2 errors)

**File**: `app/Models/Ticket.php`

**Fix Plan**:
1. Fix `belongsTo()` type hints
2. Fix `belongsToMany()` type hints

**Implementation**:
```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class, 'category_id');
}

public function tags(): BelongsToMany
{
    return $this->belongsToMany(Tag::class, 'ticket_tag');
}
```

### 8. TicketActivity Relationship (1 error)

**File**: `app/Models/TicketActivity.php`

**Fix Plan**:
1. Fix `withTrashed()` call on relationship

**Implementation**:
```php
public function ticket(): BelongsTo
{
    return $this->belongsTo(Ticket::class)->withTrashed();
}
```

### 9. FilterCoordinatesInRadius Rule (1 error)

**File**: `app/Rules/FilterCoordinatesInRadius.php`

**Fix Plan**:
1. Add type cast for coordinates parameter

**Implementation**:
```php
public function validate(string $attribute, mixed $value, Closure $fail): void
{
    /** @var array<array{latitude: string, longitude: string}> $coordinates */
    $typedValue = (array) $value;
    // ... validation logic
}
```

## Migration Strategy

1. **Phase 1**: Fix type hints and missing methods (Ticket model, Livewire)
2. **Phase 2**: Replace Service classes with Actions
3. **Phase 3**: Fix relationship definitions
4. **Phase 4**: Update Filament widgets and policies

## Dependencies

- Spatie/laravel-queueable-action package
- Laravel 12 type system
- Filament v5 component types

## Testing Requirements

1. Unit tests for all new Actions
2. Feature tests for Livewire components
3. Policy tests for authorization
4. Relationship tests for models

## Timeline

- Priority: HIGH
- Estimated effort: 8-12 hours
- Dependencies: Service replacement requires architectural review

## Success Criteria

- All Service classes replaced with Actions
- All type hints added
- All relationships properly defined
- PHPStan level 10 passes
- All tests pass