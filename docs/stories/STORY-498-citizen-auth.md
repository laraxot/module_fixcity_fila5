---
created: 2026-09-26
updated: 2026-09-26
qmd: "STORY 498 citizen auth"
issues: []
discussions: []
title: "STORY-498: Citizen Authentication"
type: story
status: open
priority: Must
tags: [auth, citizen, livewire, folio, wizard]
issue_link: "https://github.com/laraxot/fixcity/issues/498"
discussion_link: "https://github.com/laraxot/fixcity/discussions/498"
created_at: "2026-09-26T10:00:00Z"
---

# STORY-498 — Citizen Authentication

## Obiettivo
Implementare autenticazione e registrazione per cittadini (front-office) usando Livewire components dentro Folio pages.

## Requisiti

### 1. Livewire Components
- `app/Http/Livewire/Citizen/Auth/Login.php` + `login.blade.php`
- `app/Http/Livewire/Citizen/Auth/Register.php` + `register.blade.php`

### 2. Folio Pages
- `resources/views/pages/citizen/auth/login.blade.php` — include `<livewire:citizen.auth.login />`
- `resources/views/pages/citizen/auth/register.blade.php` — include `<livewire:citizen.auth.register />`

### 3. Actions
- `app/Actions/Citizen/Auth/AuthenticateCitizenAction.php` — `execute(array $credentials): bool`
- `app/Actions/Citizen/Auth/RegisterCitizenAction.php` — `execute(array $data): User`

### 4. Policy
- `TicketPolicy::viewAny()` — accesso solo se autenticato
- `TicketPolicy::view()` — solo own ticket o public

## Checklist
- [ ] Login Livewire creato
- [ ] Register Livewire creato
- [ ] Folio login page creato
- [ ] Folio register page creato
- [ ] Auth Actions creati
- [ ] Test Pest per login/register
