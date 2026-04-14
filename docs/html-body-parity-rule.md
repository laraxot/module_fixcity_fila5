# HTML Body Parity Rule

Related:
- [Fixcity README](README.md)
- [Theme policy](../../Themes/Sixteen/docs/html-parity-body-policy.md)
- [Theme CSS scoping rule](../../Themes/Sixteen/docs/architecture/CSS-SCOPING-RULE.md)
- [Agent parity rule](../../../bashscripts/ai/.agents/docs/rules/html-parity.md)

## Permanent rule

> Il tag `<body>` deve restare plain e lo scoping parity deve usare il wrapper canonico `page-content[data-slug]`.

## Correct pattern

```blade
<div class="page-content content" data-slug="{{ $pageSlug }}" data-side="content">
  ...
</div>
```

```css
.page-content[data-slug="tests.segnalazione-02-dati"] .steppers-header {
  ...
}
```

## Wrong pattern

```blade
<body class="page-tests-segnalazione-02-dati">
<div class="tests-view-wrapper">
```

## Why

- `page-content[data-slug]` e un contratto del progetto.
- `tests-view-wrapper` e un dettaglio tecnico del runtime e non va promosso a regola.
- I fix di parity devono agganciarsi a markup stabile e documentato.

See also:
- [Fixcity README](README.md)
- [Theme policy](../../Themes/Sixteen/docs/html-parity-body-policy.md)
- [Theme CSS scoping rule](../../Themes/Sixteen/docs/architecture/CSS-SCOPING-RULE.md)

## Stories correlate

- **Story 1-3**: [Stepper Responsive + No Italian + Body Plain](../../.planning/stories/1-3-segnalazione-02-dati-stepper-responsive-no-italian-body-plain.md) — segnalazione-02-dati stepper CSS responsive, no hardcoded Italian, body plain
- **Story 1-4**: [segnalazione-crea Header Parity + Stepper Responsive](../../.planning/stories/1-4-segnalazione-crea-header-parity-stepper-responsive.md) — hamburger centering, "Cerca" visibility, language dropdown, stepper wizard responsive
- **Story 1-5**: [Geolocalizzazione + Step Navigation](../../.planning/stories/1-5-geolocation-step-navigation-segnalazione-crea.md) — "Usa la tua posizione" geolocation, Nominatim reverse geocoding, ?step=N URL navigation
- **Story 1-6**: [Refactor Wizard — NO Filament Schemas, NO hardcoded blade](../../.planning/stories/1-6-refactor-wizard-no-filament-schemas-no-hardcoded-blade.md) — Remove Wizard::make(), pure Livewire $currentStep, Design Comuni HTML parity, blade conditional rendering
- **Story 1-7**: [Token-Efficient Agent Setup](../../.planning/stories/1-7-token-efficient-agent-setup.md) — QWEN.md as single source of truth, deduplicate docs, token reduction strategies
- **Story 1-8**: [Wizard Filament Schemas + XotBaseWidget + HTML Parity](../../.planning/stories/1-8-wizard-filament-schemas-xotbasewidget-html-parity.md) — Wizard via Filament 5.x Schemas, extends XotBaseWidget, outer wrapper HTML parity, contacts section parity
- **Story 1-9**: [Segnalazione-Crea Visual Parity — Filament Wizard](../../.planning/stories/1-9-segnalazione-crea-visual-parity-filament-wizard.md) — Breadcrumbs, title, stepper CSS overrides, form fields CSS, responsive verification
- **Story 1-10**: [Extract AddressInput to Geo Module — DDD Bounded Context](../../.planning/stories/1-10-extract-address-input-to-geo-module.md) — Proper Filament component in Geo, Fixcity consumes via AddressInput::make(), NO Blade::render hacks, SRP/DRY/reusability
- [Master Index](../../../docs/MODULE_DOCS_INDEX.md)
