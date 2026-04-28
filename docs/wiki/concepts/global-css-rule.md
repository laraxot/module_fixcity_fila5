---
title: Global CSS Rule - Avoid Page-Specific Selectors
type: concept
tags: [css, design-comuni, dry-kiss, architecture]
sources: [theme-css-build-workflow.md, ticket-wizard-submit-button-fix.md]
created: 2026-04-22
updated: 2026-04-22
related:
  - theme-css-build-workflow.md
  - design-comuni-compliance.md
---

# Global CSS Rule - Avoid Page-Specific Selectors

## Rule

**VIETATO**: CSS selectors using page-specific attributes or classes:
- `.page-content[data-slug="tests.segnalazione-crea"]`
- `.ticket-wizard-root` (page-specific)
- Any selector tied to a specific page URL

**OBBLIGATORIO**: Global CSS selectors applied component-wide:
- `.segnalazione-wizard-root .container`
- `.segnalazione-wizard-root .form-group`
- Component-based class names, not page-specific

## Why

Following Design Comuni pattern where CSS is site-wide, not page-specific:
- Static pages don't have CSS per-page but for the entire site
- Components should be reusable and not tied to specific pages
- DRY principle: avoid duplicating CSS for similar components

## Implementation

### Correct Pattern (Design Comuni compliance)
```css
/* Fix excessive vertical spacing in ticket wizard sections */
.segnalazione-wizard-root .container {
  margin-bottom: 8px;
}
.segnalazione-wizard-root .form-group {
  margin-bottom: 0;
}
```

### Incorrect Pattern (to avoid)
```css
/* VIETATO - Page-specific selector */
.page-content[data-slug="tests.segnalazione-crea"] .container {
  margin-bottom: 8px;
}

/* VIETATO - Page-specific class */
.ticket-wizard-root .container {
  margin-bottom: 8px;
}
```

## Examples

### Submit Button in Wizard Summary
The submit button should be rendered via:
```blade
@if($isSummaryStep)
    <div class="d-flex gap-2 w-100">
        <button type="button" class="btn btn-outline-primary btn-sm fw-bold flex-fill" wire:click="saveDraft">
            {{ __('fixcity::segnalazione.actions.save.label') }}
        </button>
        <button type="button" class="btn btn-primary btn-sm fw-bold flex-fill" wire:click="submit">
            {{ __('fixcity::segnalazione.actions.submit.label') }}
        </button>
    </div>
@endif
```

Not via page-specific CSS that hides/shows elements based on URL.

## Validation

To test compliance:
1. Check that CSS selectors don't contain page-specific attributes
2. Verify that components work across multiple pages without modification
3. Follow the Design Comuni reference pages for styling patterns
4. Use the theme CSS build workflow: HTML parity → CSS → build → copy