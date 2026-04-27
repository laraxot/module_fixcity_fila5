---
name: segnalazione-crea-url-routing-chain
description: "URL /it/tests/segnalazione-crea è gestito dal CMS JSON block chain, NON dal Folio page segnalaizione-crea.blade.php"
type: discovery
---

# Segnalazione-Crea URL Routing Chain

## URL in questione

```
/it/tests/segnalazione-crea
```

## Catena reale (CMS-driven)

### Step 1 — Folio route `tests/[slug].blade.php`

File: `laravel/Themes/Sixteen/resources/views/pages/tests/[slug].blade.php`

```php
name('tests.view');
// $pageSlug = 'tests.'.$slug = 'tests.segnalazione-crea'
// $blocks = Page::getBlocksBySlug('tests.segnalazione-crea', 'content')
```

### Step 2 — CMS JSON block definition

File: `laravel/config/local/fixcity/database/content/pages/tests.segnalazione-crea.json`

3 blocchi `content_blocks`:
1. **breadcrumb** → `pub_theme::components.blocks.breadcrumb.default`
2. **segnalazione-crea** → `pub_theme::components.blocks.tests.segnalazione-crea`
3. **contacts-card** → `pub_theme::components.blocks.design-comuni.contacts-card`

### Step 3 — Block view → Widget mount

File: `laravel/Themes/Sixteen/resources/views/components/blocks/tests/segnalazione-crea.blade.php`

```blade
@props(['data' => []])
@livewire(\Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget::class, ['blockData' => $data])
```

### Step 4 — Widget → Template vestito

Widget: `laravel/Modules/Fixcity/app/Filament/Widgets/CreateTicketWizardWidget.php`
- `$view = 'fixcity::filament.widgets.ticket-create-wizard'`
- Estende `XotBaseWizardWidget`
- 3 step: privacy → data → summary

Template: `laravel/Modules/Fixcity/resources/views/filament/widgets/ticket-create-wizard.blade.php`

## Architettura: Due percorsi separati

| Feature | Percorso CMS | Percorso Folio diretto |
|---------|-------------|----------------------|
| URL | `/it/tests/segnalazione-crea` | `/it/segnalazione-crea` (se esistesse) |
| Route name | `tests.view` | `segnalazione.crea` |
| Page | `tests/[slug].blade.php` | `segnalazione-crea.blade.php` |
| Content | CMS JSON block | Hardcoded `@livewire` |
| Blocks | breadcrumb → wizard → contacts | wizard only |
| Data passing | `blockData` da JSON | vuoti |
| Customizzazione | via JSON config | via codice Blade |

Il percorso CMS è il **canale produttivo**: il contenuto è configurabile tramite JSON, non hardcoded.

## Come `Page::getBlocksBySlug` risolve il JSON

Il modello `Page` legge il file JSON nella cartella `config/local/fixcity/database/content/pages/` e ne parse i blocchi. Il nome del file DEVE corrispondere al `$pageSlug` calcolato dalla route.

Pattern naming: `{prefix}.{slug}.json` → `tests.segnalazione-crea.json`

## CMS block data → widget props

Ogni blocco nel JSON ha:
```json
{
    "type": "segnalazione-crea",
    "data": {
        "view": "pub_theme::components.blocks.tests.segnalazione-crea",
        "title": "...",
        "confirmation_slug": "...",
        "privacy_link": "#",
        ...
    }
}
```

Il `$data` viene passato come `$data` al `@include`, quindi come `blockData` al Livewire widget. Il widget usa questi valori per redirect, privacy link, titoli, ecc.
