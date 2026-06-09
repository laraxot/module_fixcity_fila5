---
title: "Frontend Stack Canonico — Tailwind + Alpine + Lit + DaisyUI + Flowbite + Filament"
type: rule
confidence: high
created: 2026-06-01
updated: 2026-06-01
tags: [frontend, tailwind, alpine, lit, daisyui, flowbite, filament, no-bootstrap, stack]
related:
  - rules/cms-block-naming-tailwind-flowbite.md
---

# Frontend Stack Canonico (Fixcity)

## Mantra (SSoT)

```
Tailwind v4 + Alpine.js v3 + Lit v3 + DaisyUI v5 + Flowbite + Filament v5 + Vite
```

## Stack (NO Bootstrap)

| Layer | Tecnologia | Scopo |
|-------|-----------|-------|
| CSS | **Tailwind CSS v4** | Utility-first |
| Componenti | **DaisyUI v5** | Componenti Tailwind-ready |
| Componenti extra | **Flowbite** | Dropdown, datepicker, ecc. |
| Interattività | **Alpine.js v3** | Toggle, modal, tabs, form |
| Web Components | **Lit v3** | Mappa (Leaflet) |
| Admin | **Filament 5.x** | Backoffice |

## Naming blocchi CMS

Le sottocartelle di `resources/views/components/blocks/` prendono i nomi da:
- https://tailwindcss.com/plus/ui-blocks
- https://flowbite.com/blocks/

## Regola

> MAI classi Bootstrap nei Blade del modulo Fixcity.
> Usare Tailwind/DaisyUI/Flowbite.

## Story di riferimento

STORY-112: `docs/stories/STORY-112-frontend-stack-canonical-rule.md`
