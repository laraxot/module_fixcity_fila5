# Wizard Form Validation Rule

## Overview
When validating wizard forms that use enum fields (e.g., `type_id`), always employ a type‑safe retrieval pattern to avoid undefined index or enum errors.

## Recommended Pattern
```php
use Modules\Fixcity\Enums\TicketTypeEnum;

// Safe lookup with fallback
$typeLabel = TicketTypeEnum::tryFrom($get('type_id'))->label() ?? '—';
```

- `TicketTypeEnum::tryFrom()` returns `null` for invalid values instead of throwing.
- The null‑coalescing operator (`??`) provides a default label (e.g., `'—'`) for display.

## Why
- Prevents `Undefined index` or `Enum value does not exist` exceptions.
- Guarantees graceful degradation when the enum value is missing or malformed.
- Keeps the UI stable without breaking the wizard flow.

## Implementation Checklist
- ✅ Replace direct enum access with `TicketTypeEnum::tryFrom($value)`.
- ✅ Always coalesce the result with a fallback string.
- ✅ Apply the same pattern in any wizard step that reads enum data.
- ✅ Document the rule in the module’s LLM Wiki (`docs/wiki/concepts/...`).

## Related Documentation
- LLM Wiki: [[../../../../docs/wiki/concepts/wizard-form-validation.md|Wizard Form Validation]]
- Memory entry: `feedback_filament_template_as_dress.md` (type‑safety principle)
- BMAD story: Use Filament Infolist components, not SchemaView, for summary schemas.