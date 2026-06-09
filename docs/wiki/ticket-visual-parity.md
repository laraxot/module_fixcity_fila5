# Segnalazione — Visual parity & rules (module-level)

Purpose: document module-level responsibilities and rules for achieving visual parity with Design-Comuni for the Segnalazione create wizard.

Findings:
- Duplicate CTAs observed (Filament wizard footer + theme nav). Module should expose a flag to suppress Filament footer or theme wrapper should hide it.
- Language keys: confirm `pagination.next` / `components.next` values point to 'Avanti' in italian lang files.

Developer rules:
- Module must not output its own wizard footer when theme provides navigation; add `suppress_wizard_footer` config to module view data.
- Use `trans('fixcity::pagination.next')` for CTA labels to ensure multilanguage.
- Document where the stepper logic lives (Theme: resources/views/components/utilities/stepper.blade.php).

Next steps:
1. Implement visual hide of Filament footer in theme wrapper.
2. Run theme build and validate on mobile/tablet.
3. Add screenshots to both theme and module docs.

