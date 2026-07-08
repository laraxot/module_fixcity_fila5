---
title: PHPStan Standards & Model Documentation
type: technical
tags: [phpstan, models, ticket, type-safety]
created: 2026-06-10
updated: 2026-06-10
qmd: docs/wiki/phpstan-fixcity-module.md
---

# PHPStan Level 10 Standards - Fixcity Module

## Ticket Model - Complete PHPDoc Reference

The `Ticket` model includes all required methods with proper PHPDoc annotations.

### Key Methods

| Method | Location | Return Type | Purpose |
|--------|----------|-------------|---------|
| `setStatus()` | Line 604 | `void` | Update ticket status with enum conversion |
| `activities()` | Line 413 | `HasMany<TicketActivity>` | Activity history relationship |
| `comments()` | Via HasComments trait | `MorphMany<Comment>` | Comments relationship |
| `ticketComments()` | Line 596 | `HasMany<TicketComment>` | Legacy admin comments |
| `assignee()` | Line 583 | `BelongsTo<User>` | Assigned user relationship |

### Model Properties (PHPDoc)

```php
/**
 * @property string $name
 * @property string $slug
 * @property int $id
 * @property string $content
 * @property int $owner_id
 * @property int|null $responsible_id
 * @property int|null $assignee_id
 * @property Collection<int, TicketActivity> $activities
 * @property Collection<int, Comment> $comments
 * @property User|null $assignee
 * @property TicketStatusEnum|null $status
 * @property TicketTypeEnum|null $type
 * @property TicketPriorityEnum|null $priority
 */
```

### Enum Integration

```php
public function casts(): array
{
    return [
        'status' => TicketStatusEnum::class,
        'type' => TicketTypeEnum::class,
        'type_id' => TicketTypeEnum::class,
    ];
}
```

## Actions with Type Safety

### ChangeStatus Action

```php
class ChangeStatus
{
    public function execute(Ticket $ticket, string $status, string $reason): void
    {
        $ticket->setStatus($status); // Type-safe
    }
}
```

## PHPStan Compliance Status

| Category | Status | Notes |
|----------|--------|-------|
| Model PHPDoc | ✅ Complete | All properties documented |
| Method Signatures | ✅ Complete | All methods typed |
| Relationships | ✅ Complete | All return types declared |
| Enums | ✅ Complete | Full enum integration |

## Related Models

- `TicketActivity` - Activity log entries
- `TicketComment` - Admin comments
- `TicketHour` - Time tracking

## Compliance

Last PHPStan Check: 2026-06-10
Status: ✅ All methods verified and documented
