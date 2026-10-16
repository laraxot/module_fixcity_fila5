---
type: investigation
issue: livewire-schema-method-not-found
date: 2026-10-13
status: resolved-pending-browser-gate
---

# Investigation — Livewire schema method not found

## Symptom

An authenticated visitor opening `/it/tickets/create` could trigger
`Livewire\Exceptions\MethodNotFoundException` for
`callSchemaComponentMethod` while interacting with the Filament wizard.

## Evidence

- **A** — The reported POST fails at Livewire's public-method whitelist in
  `HandleComponents.php:579`.
- **A** — The rendered component is
  `Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget` and Filament's
  wizard/Geo controls emit `callSchemaComponentMethod` calls.
- **A** — Reflection before the fix resolved the method through
  `XotBaseSchemaWidget`, while Livewire discovers public methods by reflection
  on the component hierarchy.
- **A** — The method is implemented by Filament's
  `InteractsWithSchemas`; it must not be reimplemented or security-bypassed.
- **B** — The Sixteen wrapper used legacy Bootstrap-like utility names, which
  increased parity drift but was independent of the Livewire exception.

## Ranked hypotheses

1. **High** — The schema bridge was not visible at the Xot wizard boundary used
   by Livewire's method discovery. Fix: redeclare the public bridge in
   `XotBaseWizardWidget` and delegate to Filament.
2. **Low** — A page-specific Blade navigation implementation bypassed Filament.
   Refuted by the rendered `wizardSchemaComponent` and the existing shared
   `Wizard` schema.

## Applied resolution

`XotBaseWizardWidget::callSchemaComponentMethod()` now delegates to the parent
Filament implementation. The Fixcity widget remains schema-driven and the
Sixteen wrapper remains markup-only. A unit regression assertion verifies the
method is public and declared by the shared wizard base.

## Verification gate

The focused Pest test passes. Final release verification still requires a
browser with Chromium/Playwright: open the page, toggle privacy, advance to
step two, inspect console errors, and capture 375/768/1440 screenshots before
claiming visual parity.

## Related artifacts

- `Modules/Fixcity/docs/CreateTicketWizardWidget.md`
- `Themes/Sixteen/docs/decision-log/D11-filament-wizard-tickets-create.md`
- `Themes/Sixteen/docs/bmad/stories/dcp-22-tickets-create.story.md`
