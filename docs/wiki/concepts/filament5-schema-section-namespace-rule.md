---
title: Filament 5 Schema Section Namespace Rule For Fixcity Wizard
type: concept
tags: [fixcity, filament, wizard, schemas, sections, livewire]
created: 2026-04-22
updated: 2026-04-22
sources:
  - ../../../../../../docs/wiki/concepts/filament5-schema-section-namespace-rule.md
  - https://filamentphp.com/docs/5.x/schemas/overview
  - https://filamentphp.com/docs/5.x/schemas/sections
---

# Filament 5 Schema Section Namespace Rule For Fixcity Wizard

## Regola

Nel widget `Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget`, i blocchi `place`, `inefficiency`, `author` e `summary` devono usare:

```php
use Filament\Schemas\Components\Section;
```

`Filament\Infolists\Components\Section` non e' disponibile nel vendor Filament 5 installato.

## Contratto Del Wizard

- Il widget usa il form standard `form` ereditato da `XotBaseWidget::form(Schema $schema)`.
- Lo stato resta sotto `public ?array $data` tramite `statePath('data')`.
- La Blade tema renderizza `{{ $this->form }}`.
- Non introdurre multiple forms se non esiste un requisito reale di state isolation.

## Composizione Schema

- Layout: `Filament\Schemas\Components\Section`, `Grid`, `Wizard\Step`.
- Input cittadino: `Filament\Forms\Components\TextInput`, `Select`, `Textarea`, `Checkbox`, `FileUpload`.
- Riepilogo read-only: `Filament\Infolists\Components\TextEntry`, `ImageEntry`.

Questa separazione evita errori runtime di classe mancante e mantiene DRY/KISS il widget.
