# wizard navigation confusion and header color parity

## Problem Statement

Users identified a confusing redundancy in the `segnalazione-crea` wizard:
1. Double navigation buttons: Filament's native "Successivo"/"Precedente" footer was visible alongside the custom "Avanti"/"Indietro" buttons designed for Design Comuni parity.
2. Cognitive ambiguity: Different terminology ("Successivo" vs "Avanti") and duplicate CTAs.
3. Visual Parity Gap: The center header background was set to Green (#007A52) instead of the institutional Blue (#0066CC) shown in the reference [Design Comuni - Segnalazione 01 Privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html).
4. Alignment issue: The "Avanti" button on step 1 was left-aligned, whereas it should be right-aligned.

## KPI & Success Metrics

- **CTA Singularity**: Only one primary action button visible per step.
- **Visual Parity**: Layout and colors matching the official Design Comuni static pages to 95%+.
- **Terminology Consistency**: Use of "Avanti" and "Indietro" across all layers (PHP, Blade, Lang).

## Priority: P0 (Critical)

## Functional Requirements

- [x] Hide Filament native wizard navigation via PHP schema override.
- [x] Consolidate translations in `fixcity::create_ticket_wizard` and `fixcity::segnalazione`.
- [x] Refactor `ticket-create-wizard.blade.php` navigation wrapper for correct alignment (Right-aligned primary CTA).
- [x] Switch Sixteen theme header center background from Green to Blue.

## Technical Specifications

### 1. PHP/Filament Logic
In `CreateTicketWizardWidget.php`, we override `configureWizardNextAction` and `configureWizardPreviousAction` to return `->hidden()`. This prevents Filament from rendering its own footer, leaving our custom Blade navigation as the sole source of control.

### 2. Blade Layer
The `steppers-nav` container uses a **Mobile-First vertical stack** (`flex-column align-items-stretch w-100`). The "Avanti" button is positioned directly under the form content (checkboxes) and occupies the full width on mobile (min-height: 48px) and a fixed width (348px-428px) on larger screens. This choice prioritizes accessibility and clear call-to-action flow over the horizontal reference.

### 3. CSS/Theme Layer (Green Logo Strategy)
The theme was refactored to use **Green (#007A52)** and **Dark Green (#00402B)** globally for all header components (Slim, Center, Navbar, Personal Area Access). The `--dc-blue` tokens were partially remapped or overridden to ensure a coherent green branding that matches the Comune's logo, avoiding any blue/green color clashing.

### 4. Database & Infrastructure
Resolved a critical `QueryException` regarding the missing `cache` table. This table was required by Livewire's `RateLimiter` and checksum mechanisms. A migration was executed to provide the necessary persistence layer for backend cache operations.

## References

- [segnalazione-01-privacy reference](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html)
- [wizard-single-next-cta-rule wiki](../wiki/concepts/wizard-single-next-cta-rule.md)
- [header-green-branding-rule wiki](../wiki/concepts/header-green-branding-rule.md)
- [livewire-cache-table-rule wiki](../wiki/concepts/livewire-cache-table-rate-limiter.md)
