---
title: "ticket create URL — routing chain CMS + PageSlugMiddleware"
type: concept
tags: [fixcity, tickets, create, folio, cms, pageslugmiddleware]
created: 2026-05-04
updated: 2026-10-13
qmd: "tickets create URL routing PageSlugMiddleware auth JSON tickets.create wizard"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/362"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/363"
related:
  - tickets-view-cms-folio-page.md
  - ../../../../Cms/docs/wiki/concepts/cms-page-middleware-json-ssot.md
  - ../../../../../Themes/Sixteen/docs/wiki/concepts/folio-no-semantic-pages-tickets.md
---

# Ticket create — catena URL e auth

## URL produzione

```
/it/tickets/create
```

Verificato il 2026-10-13 dalla risposta reale (`data-slug="tickets.create"`, `wire:name` dello snapshot Livewire) e da `bashscripts/tools/cms/page-json-for-url.sh`.

| Layer | Valore |
|-------|--------|
| Pagina che risponde | `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php` (Folio `container0.view`, componente Volt) |
| Folio del modulo | `Modules/Fixcity/resources/views/pages/tickets/create.blade.php` (`tickets.create`, `middleware('auth')`): registrata in `folio:list` ma NON e' quella che risponde con il tema Sixteen. Perche' vinca la generica non e' stato indagato |
| Slug | `<container0>.<slug0>` dai segmenti dell'URL = `tickets.create` |
| CMS JSON | `laravel/config/local/fixcity/database/content/pages/tickets.create.json` (identico in `Modules/Fixcity/config/...`; anche `config/eu/fixcity/...` (identiche alle 14:10 UTC)) |
| Blocco | `type: ticket_create`, `data.view = pub_theme::components.blocks.tests.ticket-crea`. La cartella `tests` e' di view, NON di slug: non esiste e non deve esistere `tests.tickets-create.json` |
| Widget | `CreateTicketWizardWidget` via `@livewire` in `Themes/Sixteen/resources/views/components/blocks/tests/ticket-crea.blade.php` |
| Auth | `"middleware": ["auth"]` nel JSON (ripristinato il 2026-10-13, D17, DCP-42). Solo `auth`: `web` e' un gruppo gia' nello stack Folio (prima dava 500, `Target class [web] does not exist`; dalla DCP-50 e' ignorato ma non va scritto) e un nome non risolvibile lancia un errore chiaro. Ospite: 302 `/<lingua>/auth/login` con `url.intended` |
| Post-submit | redirect `/it/tickets/confirmation` |

## Catena render

1. Folio generica `[container0]/[slug0]` -> `ResolvePageAction` -> `@volt('container0.view')` con `div.page-content` come radice (vedi `Themes/Sixteen/docs/decision-log/D15-volt-fragment-explicit-root.md`)
2. `<x-page slug="tickets.create">` -> `Page::getBlocksBySlug('tickets.create')`
3. Blocco `ticket-crea` -> `CreateTicketWizardWidget` -> `TicketForm::getSteps()` (privacy -> data -> summary)

## Sandbox parity (non produzione)

```
/it/tests/segnalazione-crea
```

Stesso widget, ma canale `pages/tests/[slug].blade.php` (compone `tests.<slug>`) e `tests.segnalazione-crea.json`. Le sezioni sotto descrivono SOLO questa sandbox, non `/it/tickets/create`. Correzione verificata 2026-10-13: oggi `tests.segnalazione-crea.json` non esiste in nessun tenant (`page-json-for-url.sh` esce con 2) e la risposta di `/it/tests/segnalazione-crea` (200) non contiene il wizard (0 occorrenze di `fi-sc-wizard` e di `$wire.save()`, contro 5 e 3 su `/it/tickets/create`): le sezioni «Catena reale» e «Architettura» qui sotto sono storiche e non descrivono lo stato attuale.

## Prerequisito tenant

`Page::findUniqueBySlug('tickets.create')` richiede tenant `local/fixcity` (da `APP_URL=http://fixcity.local`).  
Se `GetTenantNameAction` fallisce → 0 pagine Sushi → middleware JSON ignorato.

---

## URL sandbox in questione

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
