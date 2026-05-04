---
title: "Wizard Fix Summary - May 2026"
type: troubleshooting
sources:
  - laravel/Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php
  - laravel/Themes/Sixteen/resources/views/components/wizard.blade.php
confidence: high
created: 2026-05-04
updated: 2026-05-04
tags: [wizard, filament, fix, iconposition]
---

# Wizard Fix Summary

## Issues Fixed

### 1. IconPosition Error
**Problem**: `IconPosition::tryFrom()` was receiving a Closure instead of string/int.

**Root Cause**: `XotBaseWizardWidget` was passing string values `'after'` and `'before'` to `iconPosition()`, which Filament's `HasIconPosition` trait handles by calling `IconPosition::tryFrom()`. When a Closure was passed, it failed.

**Fix**: Added `use Filament\Support\Enums\IconPosition;` and changed to use enum constants:
- `->iconPosition(IconPosition::After)`
- `->iconPosition(IconPosition::Before)`

**File**: `laravel/Modules/Xot/app/Filament/Widgets/XotBaseWizardWidget.php`

### 2. Duplicate Content in wizard.blade.php
**Problem**: The wizard view had duplicate `@foreach` and footer sections, causing rendering issues.

**Fix**: Rewrote `laravel/Themes/Sixteen/resources/views/components/wizard.blade.php` to match Filament's default structure exactly, ensuring:
- Stepper renders correctly
- Step content renders once
- Actions footer renders once

### 3. Action Rendering Syntax
**Problem**: The actions were using `{!! $action->render() !!}` instead of `{{ $action }}`.

**Fix**: Changed to use Filament's standard `{{ $previousAction }}`, `{{ $nextAction }}`, `{{ $getCancelAction() }}`, `{{ $getSubmitAction() }}` syntax which handles rendering internally.

## Current Architecture

```
┌─────────────────────────────────────────────────────────────┐
│  THEME (pub_theme) = VESTITO (Presentation Layer)            │
├─────────────────────────────────────────────────────────────┤
│  pub_theme::components.wizard                                │
│  ├── Stepper: <x-pub_theme::wizard.stepper />               │
│  ├── Content: @foreach($steps) {{ $step }} @endforeach      │
│  └── Actions: {{ $previousAction }}, {{ $nextAction }},    │
│               {{ $getSubmitAction() }}                      │
└─────────────────────────────────────────────────────────────┘
```

## Next Steps

1. Verify visual parity between `/it/tests/segnalazione-crea` and `/fixcity/admin/tickets/create`
2. Test "Avanti" button visibility on public URL
3. Check skip link accessibility ("vai al contenuto principale")