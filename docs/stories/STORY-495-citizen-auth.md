---
created: 2026-09-26
updated: 2026-09-26
qmd: "STORY 495 citizen auth"
issues: []
discussions: []
title: "STORY-495: Citizen Authentication"
type: story
status: open
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/495"
discussion_link: "https://github.com/laraxot/fixcity/discussions/495"
tags: [auth, citizen, livewire, folio]
---

# STORY-495 — Citizen Authentication

## Goal
Implementa autenticazione FO per cittadini usando Livewire components in Folio pages.

## Required Implementation

### 1. Livewire Components (app/Http/Livewire/Citizen/Auth/)
- `Login.php` + `login.blade.php` - Login form
- `Register.php` + `register.blade.php` - Registration form

### 2. Folio Pages (resources/views/pages/citizen/auth/)
- `login.blade.php` - Uses Livewire login component
- `register.blade.php` - Uses Livewire register component

### 3. Actions (app/Actions/Citizen/Auth/)
- `AuthenticateCitizenAction.php` - Handles login logic
- `RegisterCitizenAction.php` - Handles registration logic

### 4. Middleware (optional)
- Protection for citizen-only routes

## Issue Link
- GitHub Issue #495
- Discussion #495

## Evidence Post-Fix Checklist
- [ ] Livewire login component creato
- [ ] Livewire register component creato
- [ ] Folio login page implementato
- [ ] Folio register page implementato
- [ ] Auth Actions creati
- [ ] Integration con existing auth system
