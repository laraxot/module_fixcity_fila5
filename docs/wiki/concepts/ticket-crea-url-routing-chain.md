---
title: "ticket create URL — routing chain CMS + PageSlugMiddleware"
type: concept
tags: [fixcity, tickets, create, folio, cms, pageslugmiddleware]
created: 2026-05-04
updated: 2026-07-13
qmd: "tickets create URL routing PageSlugMiddleware auth JSON tickets.create wizard"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/362"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/363"
related:
  - tickets-view-cms-folio-page.md
  - ../../../../Modules/Cms/docs/wiki/concepts/cms-page-middleware-json-ssot.md
  - ../../../../Themes/Sixteen/docs/wiki/concepts/folio-no-semantic-pages-tickets.md
---

# Ticket create — catena URL e auth

## URL produzione

```
/it/tickets/create
```

| Layer | Valore |
|-------|--------|
| Folio owner | `Modules/Fixcity/resources/views/pages/tickets/create.blade.php` |
| `name()` | `tickets.create` |
| Middleware Folio | `PageSlugMiddleware` (solo questo — **no** `middleware(['auth'])` hardcoded) |
| CMS JSON | `config/local/fixcity/database/content/pages/tickets.create.json` |
| Auth SSoT | `"middleware": ["auth"]` nel JSON (`web` già nello stack Folio) |
| Widget | `CreateTicketWizardWidget` via blocco CMS `type: widget` |
| Post-submit | redirect `/it/tickets/confirmation` |

Guest → `PageSlugMiddleware` legge JSON → redirect login localizzato (`/it/auth/login*`).

## Catena render (autenticato)

1. Folio `tickets/create.blade.php` → `<x-page slug="tickets.create">`
2. `Page::getBlocksBySlug('tickets.create')` da Sushi/JSON tenant `local/fixcity`
3. Blocchi: breadcrumb + widget wizard
4. `CreateTicketWizardWidget` → `TicketForm::getSteps()` (privacy → data → summary)

## Sandbox parity (non produzione)

```
/it/tests/segnalazione-crea
```

Stesso widget, canale `tests/[slug].blade.php` + `tests.segnalazione-crea.json` — vedi sezione sotto.

## Prerequisito tenant

`Page::findUniqueBySlug('tickets.create')` richiede tenant `local/fixcity` (da `APP_URL=http://fixcity.local`).  
Se `GetTenantNameAction` fallisce → 0 pagine Sushi → middleware JSON ignorato.

---

# Segnalazione-Crea URL Routing Chain (sandbox)

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
- Estende `XotBaseWizardWidget`
- Non dichiara `$view`: `XotBaseWidget` calcola `pub_theme::filament.widgets.create-ticket-wizard`, poi `fixcity::filament.widgets.create-ticket-wizard`
- 3 step: privacy → data → summary

Template: `laravel/Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php` con fallback `laravel/Modules/Fixcity/resources/views/filament/widgets/create-ticket-wizard.blade.php`

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
