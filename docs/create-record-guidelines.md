# Filament `CreateRecord` – Guideline & Architecture Overview

## What is `CreateRecord`
`CreateRecord` (\Filament\Resources\Pages\CreateRecord) is the **base page class** that powers every *resource create* page in Filament.  It implements the full lifecycle of a record creation form:

1. **Authorization** – `authorizeAccess()` ensures the current user can create the resource.
2. **Form hydration** – `fillForm()` calls the resource's `form()` definition and populates default values.
3. **Validation & mutation** – Hooks `beforeValidate`, `afterValidate`, and `mutateFormDataBeforeCreate()` give you fine‑grained control over the payload.
4. **Persistence** – `handleRecordCreation()` creates the Eloquent model, optionally linking it to a parent via `associateRecordWithParent()`.
5. **Relationship saving** – `saveRelationships()` persists any `HasOne/HasMany` relations defined in the form schema.
6. **Events & notifications** – Fires `RecordCreated` / `RecordSaved` events and optionally shows a success toast.
7. **Redirect handling** – `getRedirectUrl()` determines whether the user is sent to the `view`, `edit` or back to the resource index, respecting the global Filament redirect configuration.

## Design Philosophy (the “zen”)
| Principle | Why it matters |
|-----------|----------------|
| **Single source of truth** – All wizard‑style logic (step handling, DB transaction scope, form actions) lives here. Sub‑classes only supply **what** fields exist, never **how** the form works. | Guarantees consistency across every resource page and prevents accidental duplication of transaction handling. |
| **Hooks, not overrides** – `callHook('beforeValidate')`, `mutateFormDataBeforeCreate()`, etc. allow you to inject behaviour without touching the core flow. | Keeps the base class stable; future Filament upgrades rarely break custom pages that rely on hooks. |
| **Safety first** – Uses `CanUseDatabaseTransactions` and rolls back on any exception. The `$isCreating` flag (locked by `#[Locked]`) prevents double submissions. | Protects data integrity and avoids race conditions in a Livewire environment. |
| **Internationalisation** – All UI strings are pulled from Filament language files (`__('filament‑panels::resources/pages/create‑record…')`). | Guarantees a multilingual UI without hard‑coded text. |
| **Extensibility** – Methods like `preserveFormDataWhenCreatingAnother()` and `mutateFormDataBeforeCreate()` are *intended* extension points. | Enables “Create + New” workflows without custom copy‑pasting of form state. |

## Core Extension Points (hooks you can use)
| Hook / method | Typical use‑case |
|---------------|-----------------|
| `beforeFill` / `afterFill` | Populate additional hidden fields (e.g. current user ID) before the form renders. |
| `beforeValidate` / `afterValidate` | Add custom Livewire/JS validation or manipulate the raw state before Filament validates it. |
| `mutateFormDataBeforeCreate(array $data)` | Convert UI‑friendly values to DB‑ready values (e.g. explode a comma‑separated list, map a human‑readable enum to its key). |
| `preserveFormDataWhenCreatingAnother(array $data)` | Keep certain fields (like `category_id`) when the user clicks **Create Another**. |
| `handleRecordCreation(array $data)` | Replace the default `new Model($data)` with a factory or a service‑layer call. |
| `getRedirectUrl()` / `getRedirectUrlParameters()` | Custom post‑create routing, e.g. sending the user to a thank‑you page. |
| `getCreatedNotification()` | Override the toast message or change its style. |

## Best‑Practice Checklist (DRY + KISS)
1. **Never duplicate transaction code** – rely on the `beginDatabaseTransaction` / `commitDatabaseTransaction` flow.
2. **Prefer hooks over overriding `create()`** – only override if you need to change the entire flow.
3. **Keep form schemas declarative** – define all fields in the resource’s `form()` method; avoid mutating the schema in the page class.
4. **Use `mutateFormDataBeforeCreate` for data massaging** – e.g. converting a `type_id` enum to the enum class, or normalising phone numbers.
5. **Leverage the notifications system** – calling `$this->getCreatedNotification()?->send()` gives a consistent UI experience.
6. **Add phpdoc for generic `TModel`** – improves IDE support and static analysis.
7. **Write a feature test** that exercises the whole lifecycle (`Livewire::test(...)->set(...)->call('create')`).

## Example: Adding a custom field without breaking the flow
```php
protected function beforeFill(): void
{
    // Add the current authenticated user's ID automatically.
    $this->form->fill(['owner_id' => auth()->id()]);
}

protected function mutateFormDataBeforeCreate(array $data): array
{
    // Cast the incoming enum string to the actual enum instance.
    if (isset($data['type_id'])) {
        $data['type_id'] = \Modules\Fixcity\Enums\TicketTypeEnum::tryFrom($data['type_id']) ?? \Modules\Fixcity\Enums\TicketTypeEnum::default();
    }
    return $data;
}
```
The above follows the **DRY** rule: we do not touch `handleRecordCreation` or the transaction logic.

---
**Where this lives**
- Core class: `vendor/filament/filament/src/Resources/Pages/CreateRecord.php`
- Extension example: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- Guidelines added here: `Modules/Fixcity/docs/create-record-guidelines.md`

---
**Next steps**
- Add this file to the module’s `docs` index.
- Create a memory entry (`memory/create-record-guidelines.md`) summarising the key rules for quick reference.
- Optionally write a test case in `Modules/Fixcity/tests/Feature/CreateRecordTest.php`.
