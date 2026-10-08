---
title: "create record guidelines"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "create record guidelines"
issues: []
discussions: []
---

# XotBase `CreateRecord` – Guideline & Architecture Overview

Le pagine create del progetto estendono `Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord`; questa guida descrive i relativi hook, non autorizza l’estensione diretta della classe Filament.

## What is `CreateRecord`
`XotBaseCreateRecord` è la base di progetto per le pagine create delle Resource e fornisce il lifecycle del record. I suoi hook permettono di aggiungere comportamento specifico:

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
| **Hooks, not overrides** – `beforeValidate`, `mutateFormDataBeforeCreate()` e gli altri hook permettono di aggiungere comportamento senza cambiare il core flow. | Mantiene stabile la base Xot. |
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
| `handleRecordCreation(array $data)` | Sostituire la persistenza predefinita solo se necessario, delegando la logica a una Action. |
| `getRedirectUrl()` / `getRedirectUrlParameters()` | Custom post‑create routing, e.g. sending the user to a thank‑you page. |
| `getCreatedNotification()` | Override the toast message or change its style. |

## Best‑Practice Checklist (DRY + KISS)
1. **Never duplicate transaction code** – rely on the `beginDatabaseTransaction` / `commitDatabaseTransaction` flow.
2. **Prefer hooks over overriding `create()`** – only override if you need to change the entire flow.
3. **Keep form schemas declarative** – define all fields in the resource’s `form()` method; avoid mutating the schema in the page class.
4. **Use `mutateFormDataBeforeCreate` for page-specific preparation**. Per normalizzazioni riusabili, chiamare direttamente l’Action proprietaria dal hook; non aggiungere metodi statici pass-through al Resource e non duplicare la logica nel Resource.
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
    return app(GetTicketFormDataForPersistAction::class)->execute($data);
}
```
La Page adatta il lifecycle XotBase, l’Action possiede la trasformazione e il Resource configura schema e route. Il frontoffice non dipende dal Resource Filament.

---
title: "create record guidelines"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-26
qmd: "create record guidelines"
issues: []
discussions: []
**Where this lives**
- Core class: `Modules/Xot/app/Filament/Resources/Pages/XotBaseCreateRecord.php`
- Extension example: `Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- Guidelines added here: `Modules/Fixcity/docs/create-record-guidelines.md`

---
**Next steps**
- Add this file to the module’s `docs` index.
- Create a memory entry (`memory/create-record-guidelines.md`) summarising the key rules for quick reference.
- Optionally write a test case in `Modules/Fixcity/tests/Feature/CreateRecordTest.php`.
