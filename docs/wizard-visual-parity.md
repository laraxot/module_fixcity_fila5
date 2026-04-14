# Filament Wizard → Bootstrap Italia Visual Parity

## Filosofia
Il markup è generato da Filament (Livewire) — NON possiamo cambiarlo senza rompere il framework.
La soluzione: **CSS scoped** che forza il look & feel Bootstrap Italia sopra il markup Filament.

## Come Funziona

### 1. Entry Point CSS
`app-test.css` include `components/filament-wizard-parity.css`
→ Vite builda `app-test-CoVhwqrJ.css` (841KB con tutte le parity rules)

### 2. Layout Test
`layouts/test.blade.php` usa `@vite(['resources/css/app-test.css'])`
→ Le pagine test caricano il CSS con parity rules

### 3. CSS Scoped
Tutte le rules sono sotto `.ticket-wizard-root` → non inquinano altri componenti

## Mappatura Componenti

| Bootstrap Italia | Filament Class | CSS Override |
|-----------------|----------------|-------------|
| `.form-section` | `.fi-fo-section` | `@apply mb-4` |
| `h2.h4` heading | `.fi-fo-section-header-heading` | `text-xl font-bold uppercase` |
| `.text-muted` desc | `.fi-fo-section-header-description` | `text-base text-gray-600` |
| `.form-group` | `.fi-fo-field-wrp` | `@apply mb-4` |
| `label.active` | `.fi-fo-field-wrp-label` | `text-sm font-semibold` |
| `.form-control` | `input, select, textarea` | `w-full px-3 py-2 border rounded` |
| `.note-asterisk` | `.wizard-required-note` | custom class |
| `.it-btn-row` | `.fi-fo-wizard-actions` | `flex gap-3 mt-6` |

## File Coinvolti

```
Themes/Sixteen/
├── resources/css/
│   ├── app-test.css                    ← Entry point test pages
│   └── components/
│       └── filament-wizard-parity.css  ← Override rules
├── vite.config.js                      ← input: app-test.css
└── resources/views/layouts/
    └── test.blade.php                  ← usa app-test.css
```

## Manutenzione

Quando Filament cambia le classi runtime:
1. Inspect nel browser per trovare le nuove classi `.fi-*`
2. Aggiungere al safelist in `tailwind.config.js`
3. Aggiungere le regole in `filament-wizard-parity.css`
4. `npm run build`

---
*Ultimo aggiornamento: 2026-04-14*
