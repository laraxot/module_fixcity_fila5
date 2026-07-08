---
title: "Technical Debt: Livewire Removal"
type: technical-debt
confidence: high
created: 2026-05-29
updated: 2026-05-29
tags: [technical-debt, livewire, filament, refactoring]
related:
  - ../../../../../docs/wiki/rules/no-pure-livewire-outside-filament-widgets.md
  - ../../../../../docs/wiki/concepts/filament-widget-vs-livewire-philosophy.md
---

# Debito Tecnico: Rimozione Livewire

## Stato

| Componente | Stato | Data |
|------------|-------|------|
| `TicketList.php` | ✅ ELIMINATO | 2026-05-29 |
| `Auth/` | 🔄 Legacy | Da migrare |

## Componente Eliminato

### TicketList.php

**File:** `laravel/Modules/Fixcity/app/Livewire/TicketList.php`

**Problemi:**
1. ❌ Usa Livewire Volt invece di Filament Widget
2. ❌ Categorie hardcoded in italiano
3. ❌ Numeri hardcoded `(21)`, `(14)`, etc.
4. ❌ Duplica logica Filament
5. ❌ No i18n
6. ❌ No separation of concerns

**Sostituito da:**
- `pub_theme::components.blocks.ticket.layout` (Blade + Filament Widget)
- `Modules/Fixcity/Filament/Widgets/` (per logica tabellare)

**Verifica eliminazione:**
```bash
test -f laravel/Modules/Fixcity/app/Livewire/TicketList.php && echo "❌ ESISTE" || echo "✅ ELIMINATO"
```

## Componente Legacy

### Auth/

**Cartella:** `laravel/Modules/Fixcity/app/Livewire/Auth/`

**Stato:** In uso ma deprecato

**Piano di migrazione:**
1. Valutare `module_user_fila5` per autenticazione
2. Migrare a Filament Auth pages
3. Eliminare `Auth/` dopo migrazione

## Altri Moduli con Livewire

### Geo Module

| File | Stato | Problema |
|------|-------|----------|
| `app/Http/Livewire/FormSearchAddressCategories.php` | ❌ Da rimuovere | `extends Component` |
| `app/Http/Livewire/Test.php` | ❌ Da rimuovere | `extends Component` (test) |

### Blog Module

| File | Stato | Note |
|------|-------|------|
| `app/Http/Livewire/Profile.php` | ✅ Legittimo | Estende `Filament\Pages\Page`, namespace legacy |

## Regole Stabilite

### Nuova Regola Critica

Vedi: `docs/wiki/rules/no-pure-livewire-outside-filament-widgets.md`

> **"No Livewire puro nel frontoffice. Solo Filament Widgets."**

## Script di Verifica

```bash
# Verifica che non ci siano nuovi file Livewire
bash bashscripts/ai/check-livewire-pure.sh
```

## Changelog

- **2026-05-29**: `TicketList.php` eliminato
- **2026-05-29**: Regola `no-pure-livewire-outside-filament-widgets.md` creata
- **2026-05-29**: Filosofia `filament-widget-vs-livewire-philosophy.md` creata
