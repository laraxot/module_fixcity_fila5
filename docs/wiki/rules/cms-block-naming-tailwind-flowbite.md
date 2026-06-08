---
title: "CMS Block naming — Tailwind UI / Flowbite (Fixcity)"
type: rule
confidence: high
created: 2026-06-01
updated: 2026-06-01
tags: [cms, blocks, naming-convention, tailwind, flowbite, views]
related:
  - rules/frontend-stack-canonical.md
---

# CMS Block naming — Tailwind UI / Flowbite

## Regola

> Le sottocartelle di `resources/views/components/blocks/` **devono** prendere i nomi da:
> - https://tailwindcss.com/plus/ui-blocks
> - https://flowbite.com/blocks/

## Mapping principale

| Sottocartella | Reference |
|---------------|-----------|
| `hero/` | [Tailwind — Hero sections](https://tailwindcss.com/plus/ui-blocks/marketing/sections/heroes) |
| `grid/` | [Tailwind — Grids](https://tailwindcss.com/plus/ui-blocks/application-ui/layout/panels) |
| `cta/` | [Tailwind — CTA sections](https://tailwindcss.com/plus/ui-blocks/marketing/sections/cta-sections) |
| `rating/` | [Flowbite — Rating](https://flowbite.com/docs/components/rating/) |
| `vertical-navigation/` | [Tailwind — Vertical navigation](https://tailwindcss.com/plus/ui-blocks/application-ui/navigation/vertical-navigation) |
| `card/` | [Flowbite — Card](https://flowbite.com/docs/components/card/) |
| `tabs/` | [Flowbite — Tabs](https://flowbite.com/docs/components/tabs/) |
| `modal/` | [Flowbite — Modal](https://flowbite.com/docs/components/modal/) |

## Anti-pattern

```
// ❌ SBAGLIATO
blocks/ticket-layout/
blocks/segnalazioni-elenco/

// ✅ CORRETTO
blocks/hero/
blocks/grid/
blocks/vertical-navigation/
```

## Story di riferimento

STORY-112: `docs/stories/STORY-112-frontend-stack-canonical-rule.md`

## Architettura Actions

> **Regola LARAXOT**: Useremo Actions di Spatie (`laravel-queueable-action`) invece di Services.
> Ogni logica di business va in `app/Actions/` con metodo `execute()`.

Esempio:
```
// ❌ SBAGLIATO
app/Services/TicketCategoryService.php

// ✅ CORRETTO
app/Actions/TicketCategoryAction.php
    public function execute()
```

Reference: [[../../docs/wiki/rules/laraxot-actions-over-services.md]]
