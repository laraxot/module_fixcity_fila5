# Fixcity Module - Immediate PHPStan Fixes

## Analysis Date: 2026-03-02
**Remaining Errors**: 30+ errors
**Priority**: HIGH

## Critical Fixes Required

### 1. Missing Methods in Ticket Model (3 errors)

#### Fix 1.1: Add setStatus() Method
**File**: `app/Models/Ticket.php`
**Error**: Call to undefined method `Ticket::setStatus()`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

/**
 * @property string $status
 */
class Ticket extends BaseModel
{
    // ... existing code ...

    /**
     * Set the ticket status.
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }
}
```

**Affected File**: `app/Actions/ChangeStatus.php:22`

#### Fix 1.2: Add comments() Relationship
**File**: `app/Models/Ticket.php`
**Error**: Call to undefined method `Ticket::comments()`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Comment\Models\Comment;

class Ticket extends BaseModel
{
    // ... existing code ...

    /**
     * Get all comments for the ticket.
     *
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
```

**Affected File**: `app/Services/TicketService.php:171`

#### Fix 1.3: Add activities() Relationship
**File**: `app/Models/Ticket.php`
**Error**: Call to undefined method `Ticket::activities()`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends BaseModel
{
    // ... existing code ...

    /**
     * Get all activities for the ticket.
     *
     * @return HasMany<TicketActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class);
    }
}
```

**Affected File**: `app/Services/WorkflowService.php:220`

### 2. Missing Models (2 errors)

#### Fix 2.1: Create Report Model
**Files**:
- `app/Models/Report.php` (NEW)
- `database/factories/ReportFactory.php` (UPDATE)

**Solution**:

Create `app/Models/Report.php`:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Modules\Fixcity\Database\Factories\ReportFactory;
use Modules\User\Models\BaseModel;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $location
 * @property string $address
 * @property string $category
 * @property string $status
 * @property string $priority
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Modules\User\Models\User $user
 * @method static \Modules\Fixcity\Database\Factories\ReportFactory factory()
 */
class Report extends BaseModel
{
    protected $fillable = [
        'title',
        'description',
        'location',
        'address',
        'category',
        'status',
        'priority',
        'user_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\Modules\User\Models\User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\Modules\User\Models\User::class);
    }

    /**
     * @return \Modules\Fixcity\Database\Factories\ReportFactory
     */
    protected static function newFactory(): \Modules\Fixcity\Database\Factories\ReportFactory
    {
        return \Modules\Fixcity\Database\Factories\ReportFactory::new();
    }
}
```

Update `database/factories/ReportFactory.php`:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\Report;
use Modules\User\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<Report>
 */
class ReportFactory extends Factory
{
    protected $model = Report::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'location' => fake()->address(),
            'address' => fake()->streetAddress(),
            'category' => fake()->word(),
            'status' => fake()->randomElement(['open', 'in_progress', 'closed']),
            'priority' => fake()->randomElement(['low', 'medium', 'high']),
            'user_id' => User::factory(),
        ];
    }
}
```

#### Fix 2.2: Fix Category Model Reference
**File**: `database/factories/TicketFactory.php:21`
**Error**: Unknown class `Modules\Category\Models\Category`

**Solution**:
```php
// Check if Category exists in Fixcity module
// If yes, use correct namespace:
\Modules\Fixcity\Models\Category::factory()->create()

// If no, create Category model or remove this line
```

### 3. Type Safety Issues (15+ errors)

#### Fix 3.1: Add Return Types to Anonymous Functions
**File**: `app/Actions/GenerateTicketsAction.php:30-34`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Models\Ticket;

class GenerateTicketsAction
{
    public function execute(): array
    {
        $tickets = [
            'open' => static fn (): Ticket => Ticket::factory()->open()->create(),
            'urgent' => static fn (): Ticket => Ticket::factory()->urgent()->create(),
            'resolved' => static fn (): Ticket => Ticket::factory()->resolved()->create(),
        ];

        return array_map(fn ($callback) => $callback(), $tickets);
    }
}
```

#### Fix 3.2: Add Type Assertions in Seeders
**Files**:
- `database/seeders/ReportContentSeeder.php:67-74`
- `database/seeders/TicketDatabaseSeeder.php:51-56`

**Solution for ReportContentSeeder.php**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Models\Report;

class ReportContentSeeder extends Seeder
{
    public function run(): void
    {
        $data = $this->getData();

        foreach ($data as $item) {
            if (!is_array($item)) {
                continue;
            }

            $title = $item['title'] ?? throw new \InvalidArgumentException('Missing title');
            $description = $item['description'] ?? '';
            $location = $item['location'] ?? '';
            $address = $item['address'] ?? '';
            $category = $item['category'] ?? '';
            $status = $item['status'] ?? 'open';
            $priority = $item['priority'] ?? 'medium';

            Report::create([
                'title' => $title,
                'description' => $description,
                'location' => $location,
                'address' => $address,
                'category' => $category,
                'status' => $status,
                'priority' => $priority,
                'user_id' => 1,
            ]);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getData(): array
    {
        return [
            // ... data ...
        ];
    }
}
```

**Solution for TicketDatabaseSeeder.php**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Models\Ticket;

class TicketDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $tickets = [
            ['name' => 'Ticket 1', 'content' => 'Content 1', 'status' => 'open', 'priority' => 'high'],
            ['name' => 'Ticket 2', 'content' => 'Content 2', 'status' => 'in_progress', 'priority' => 'medium'],
        ];

        foreach ($tickets as $ticketData) {
            if (!is_array($ticketData)) {
                continue;
            }

            $name = $ticketData['name'] ?? throw new \InvalidArgumentException('Missing name');
            $content = $ticketData['content'] ?? '';
            $status = $ticketData['status'] ?? 'open';
            $priority = $ticketData['priority'] ?? 'medium';

            Ticket::create([
                'name' => $name,
                'content' => $content,
                'status' => $status,
                'priority' => $priority,
                'user_id' => 1,
            ]);
        }
    }
}
```

#### Fix 3.3: Fix Relationship Type Annotations
**File**: `app/Models/Ticket.php:481, 491`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Modules\User\Models\User;
use Modules\Tag\Models\Tag;

class Ticket extends BaseModel
{
    // ... existing code ...

    /**
     * Get the user that owns the ticket.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the tags for the ticket.
     *
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(
            Tag::class,
            'ticket_tag',
            'ticket_id',
            'tag_id'
        );
    }
}
```

#### Fix 3.4: Fix Builder Class Reference
**Files**:
- `app/Livewire/TicketList.php:39`
- `app/View/Components/Blocks/TicketList.php:30`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Livewire;

use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Modules\Fixcity\Models\Ticket;

class TicketList extends Livewire\Component
{
    /** @var array<int, Ticket> */
    public array $tickets = [];

    public function mount(): void
    {
        /** @var EloquentBuilder<Ticket> $query */
        $query = Ticket::query();

        $this->tickets = $query
            ->when($this->status, fn (EloquentBuilder $q) => $q->where('status', $this->status))
            ->latest()
            ->get()
            ->all();
    }
}
```

#### Fix 3.5: Fix Property Type Annotation
**File**: `app/View/Components/Blocks/TicketList.php:12`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks;

use Illuminate\View\Component;
use Modules\Fixcity\Models\Ticket;

class TicketList extends Component
{
    /** @var array<int, Ticket> */
    public array $tickets = [];

    public function render(): \Illuminate\View\View
    {
        return view('fixcity::components.blocks.ticket-list');
    }
}
```

#### Fix 3.6: Fix Array Key Type
**File**: `app/View/Components/Blocks/TicketList/Agid.php:59`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\View\Components\Blocks\TicketList;

use Illuminate\View\Component;

class Agid extends Component
{
    public function render(): \Illuminate\View\View
    {
        $data = $this->getData();
        $result = [];

        foreach ($data as $key => $value) {
            if (is_string($key)) {
                $result[$key] = $this->processValue($value);
            }
        }

        return view('fixcity::components.blocks.ticket-list.agid', [
            'result' => $result,
        ]);
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    private function processValue(mixed $value): mixed
    {
        return $value;
    }
}
```

### 4. Property and Service Issues (3 errors)

#### Fix 4.1: Add assignee Property to Ticket
**File**: `app/Models/Ticket.php`
**Error**: Access to undefined property `Ticket::$assignee`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;

/**
 * @property int|null $assignee_id
 * @property-read User|null $assignee
 */
class Ticket extends BaseModel
{
    protected $fillable = [
        // ... existing fillable ...
        'assignee_id',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }
}
```

#### Fix 4.2: Fix Collection Type in NotificationService
**File**: `app/Services/NotificationService.php:215, 219`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Services;

use Illuminate\Support\Collection;
use Modules\User\Models\User;

class NotificationService
{
    /**
     * Get users to notify.
     *
     * @return Collection<int, User>
     */
    public function getUsersToNotify(): Collection
    {
        /** @var Collection<int, User> $users */
        $users = collect();

        $assignees = $this->getAssignees();

        $users = $users->merge($assignees);

        return $users;
    }
}
```

### 5. Other Issues (5 errors)

#### Fix 5.1: Fix Auth Login Credentials
**File**: `app/Livewire/Auth/Login.php:43`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Livewire\Auth;

use Livewire\Component;

class Login extends Component
{
    public array $form = [
        'email' => '',
        'password' => '',
    ];

    public function authenticate(): void
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!auth()->attempt($credentials)) {
            $this->addError('email', __('auth.failed'));
            return;
        }

        redirect()->intended(route('dashboard'));
    }
}
```

#### Fix 5.2: Fix CreateTicketWidget Schema
**File**: `app/Filament/Widgets/CreateTicketWidget.php:81, 114`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Forms\Components\TextInput;
use Filament\Widgets\Widget;

class CreateTicketWidget extends Widget
{
    protected static string $view = 'fixcity::widgets.create-ticket';

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('title')
                ->required()
                ->maxLength(255),
            // ... other fields ...
        ];
    }
}
```

#### Fix 5.3: Fix FilterCoordinatesInRadius Rule
**File**: `app/Rules/FilterCoordinatesInRadius.php:31`

**Solution**:
```php
<?php

declare(strict_types=1);

namespace Modules\Fixcity\Rules;

use Modules\Geo\Actions\FilterCoordinatesInRadiusAction;

class FilterCoordinatesInRadius implements \Illuminate\Contracts\Validation\Rule
{
    public function passes($attribute, $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        /** @var array<int, array{latitude: string, longitude: string}> $coordinates */
        $coordinates = $value;

        $action = new FilterCoordinatesInRadiusAction();
        $result = $action->execute($this->center, $this->radius, $coordinates);

        return count($result) > 0;
    }
}
```

## Implementation Order

### Day 1: Critical Methods
1. Add setStatus() to Ticket model
2. Add comments() relationship to Ticket model
3. Add activities() relationship to Ticket model

### Day 2: Missing Models
4. Create Report model and factory
5. Fix Category model reference

### Day 3: Type Safety
6. Fix anonymous function return types
7. Add type assertions in seeders
8. Fix relationship type annotations

### Day 4: Component Fixes
9. Fix Builder class references
10. Fix property type annotations
11. Fix array key types

### Day 5: Final Polish
12. Fix service issues
13. Fix Filament widgets
14. Run PHPStan validation

## Testing Strategy

```php
test('ticket can set status', function () {
    $ticket = Ticket::factory()->create(['status' => 'open']);

    $ticket->setStatus('in_progress');
    $ticket->save();

    expect($ticket->status)->toBe('in_progress');
});

test('ticket has comments', function () {
    $ticket = Ticket::factory()->create();
    $comment = Comment::factory()->for($ticket)->create();

    expect($ticket->comments)->toHaveCount(1);
});

test('report factory creates valid report', function () {
    $report = Report::factory()->create();

    expect($report)->toBeInstanceOf(Report::class);
});
```

## Success Criteria

✅ All 30+ PHPStan errors resolved
✅ All tests pass
✅ No regressions in other modules
✅ Documentation updated

---

**Status**: Ready for Implementation
**Estimated Time**: 5 days
**Priority**: HIGH