---
title: "STORY-494: Wizard Implementation"
created_at: "2026-09-26T10:00:00Z"
type: story
status: open
priority: Must
issue_link: "https://github.com/laraxot/fixcity/issues/494"
discussion_link: "https://github.com/laraxot/fixcity/discussions/494"
tags: [wizard, citizen, ticket, folio]
---

# STORY-494 — Wizard Implementation

## Goal
Implementa il wizard completo "nuova segnalazione" usando Folio pages + Actions pattern.

## Required Implementation

### 1. Folio Pages (resources/views/pages/tickets/wizard/)
- `privacy.blade.php` - Privacy consent step
- `data.blade.php` - Ticket data input step  
- `position.blade.php` - Location selection step
- `review.blade.php` - Review and submit step

### 2. Actions (app/Actions/Wizard/)
- `PrivacyConsentWizardAction.php` - Validates privacy consent
- `FormDataWizardAction.php` - Validates and stores form data
- `GeocodeWizardAction.php` - Handles geocoding (optional)
- `CreateTicketFromWizardAction.php` - Creates ticket from wizard data

### 3. Page Controllers (embedded in Folio pages using use statement)
Each Folio page can have PHP logic in the blade file or use Actions.

## Issue Link
- GitHub Issue #494
- Discussion #494

## Evidence Post-Fix Checklist
- [ ] Wizard privacy step implemented
- [ ] Wizard data step implemented
- [ ] Wizard position step implemented  
- [ ] Wizard review step implemented
- [ ] Wizard Actions created and tested
- [ ] Integration with existing Ticket creation flow
