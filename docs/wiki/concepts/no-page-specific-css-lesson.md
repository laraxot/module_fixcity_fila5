# No Page-Specific CSS - Lesson Learned

## Problem
Using CSS selectors like `.ticket-wizard-root` or `[data-slug="tests.segnalazione-crea"]` caused:

1. **Fragility**: Styles break when page structure changes
2. **Non-portability**: Styles only work on specific pages
3. **Violation of Design Comuni principles**: Component styles should be self-contained

## Root Cause
Trying to fix visual inconsistencies by scoping CSS to specific pages instead of fixing the underlying component issues.

## Solution

### 1. Fix the Component First (HTML Parity)
- Ensure semantic HTML structure follows Design Comuni guidelines
- Use proper component variants via props/attributes when needed
- Example: `<coordinate-picker-lit size="compact" />` instead of `.ticket-wizard-root coordinate-picker-lit`

### 2. Use Global Component CSS
Style components to work everywhere they're used:
```css
/* ✅ CORRECT: Global component styling */
.filament-wizard-step { ... }
.filament-wizard-step.active { ... }
coordinate-picker-lit { display: block; width: 100%; }
.it-page-sections-container .section-muted { ... }
```

### 3. Container Queries for Context-Aware Styling
When component needs to adapt to container size:
```css
@container (min-width: 400px) {
    .component { /* styles for wider containers */ }
}
```

### 4. Component Variants via Props
Pass context through component properties:
```blade
<!-- Instead of page-specific CSS -->
<x-coordinate-picker variant="wizard-summary" />

<!-- In component -->
<div class="coordinate-picker {{ $variant }}">
    <!-- Styles work everywhere -->
</div>
```

## Best Practices

- CSS should be written for components, not pages
- Components must look correct in all contexts where they're used
- Use Design Comuni classes (`.it-*`) for layout and spacing
- Test components in isolation and in various contexts

## False Friends

- ❌ `[data-slug="tests.segnalazione-crea"] .map-container` - breaks if slug changes
- ❌ `.ticket-wizard-root` - creates unnecessary coupling to page structure
- ✅ `.map-container` - works everywhere the component is used

## Related Files

- `laravel/Themes/Sixteen/resources/css/app.css` - source CSS
- Design Comuni reference: https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html