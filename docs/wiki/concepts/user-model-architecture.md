---
title: "Fixcity User Model Architecture"
type: concept
tags: [architecture, user, comment, baseuser, cancomment, contract]
created: 2026-06-18
updated: 2026-06-18
qmd: "fixcity user model architecture baseuser cancomment contract extends implements"
related:
  - laravel/Modules/User/docs/wiki/concepts/baseuser-hierarchy.md
  - laravel/Modules/Comment/docs/wiki/concepts/can-comment-contract-owner.md
---

# Fixcity User Model Architecture

## Overview

The Fixcity module extends the User domain with civic reporting capabilities while maintaining clean separation of concerns through contract-based composition.

## Architecture Pattern

### Class Hierarchy

```
Illuminate\Foundation\Auth\User (Laravel)
    ↑
Modules\User\Models\BaseUser (abstract - core auth)
    ↑
Modules\Fixcity\Models\User (concrete - civic + comments)
```

### Contract Implementation

```php
namespace Modules\Fixcity\Models;

use Modules\User\Models\BaseUser;
use Modules\Comment\Models\Contracts\CanComment;
use Modules\Comment\Models\Concerns\InteractsWithComments;

class User extends BaseUser implements CanComment
{
    use InteractsWithComments;
    
    // Additional Fixcity-specific functionality
}
```

## Design Principles

### 1. Clean Upstream (User Module)

The User module remains **decoupled** from the Comment module:

```bash
# Verification - User module has NO Comment dependencies
grep -r "use Modules\\Comment" laravel/Modules/User/app/
# Result: No matches (correct)
```

### 2. Downstream Composition (Fixcity Module)

Fixcity composes functionality from multiple upstream modules:

| Capability | Source | Integration |
|------------|--------|-------------|
| Authentication | User (BaseUser) | Extension |
| Authorization | User (Roles/Permissions) | Inherited |
| Comments | Comment (CanComment) | Contract implementation |
| Civic Reporting | Fixcity (Ticket) | Domain logic |

### 3. Contract-Based Architecture

- **CanComment** = interface defining comment capabilities
- **InteractsWithComments** = trait providing default implementation
- **Fixcity\Models\User** = concrete model composing both

## Benefits

1. **No circular dependencies** - User → (no deps), Fixcity → User + Comment
2. **Testable in isolation** - BaseUser can be tested without Comment module
3. **Extensible** - Other modules can create their own User variants
4. **DRY** - Common auth logic in BaseUser, specialized logic in concrete classes

## Related Patterns

- `docs/wiki/concepts/baseuser-hierarchy.md` - User module documentation
- `docs/wiki/concepts/can-comment-contract-owner.md` - Comment module documentation
