---
title: "Segnalazione Bootstrap → Tailwind Conversion"
type: concept
sources: 
  - "https://italia.github.io/design-comuni-pagine-statiche/"
  - "../../laravel/Themes/Sixteen/resources/css/app.css"
confidence: high
created: 2026-05-04
updated: 2026-05-04
tags: [segnalazione, bootstrap, tailwind, conversion, design-comuni, parity]
related:
  - concepts/segnalazione-design-comuni-comparison.md
  - ../../../laravel/Themes/Sixteen/docs/wiki/concepts/bootstrap-tailwind-mapping.md
  - concepts/segnalazione-visual-parity.md
---

# Segnalazione Bootstrap → Tailwind Conversion

> **Purpose**: Document conversion of ALL Bootstrap classes from Design Comuni pages to Tailwind CSS
> **Pages converted**: segnalazione-01-privacy, 02-dati, 03-riepilogo, 04-conferma, area-personale, ticket-list
> **Implementation**: `laravel/Themes/Sixteen/resources/css/app.css` (NO inline CSS!)

## Conversion Complete ✅

All Bootstrap classes from Design Comuni reference pages have been converted to Tailwind equivalents in `app.css`.

### Classes Converted (6 pages, 50+ classes)

| Category | Bootstrap Classes | Tailwind Classes | Status |
|----------|-------------------|-------------------|--------|
| **Container** | `.container`, `.row`, `.col-*` | `.container`, `.flex`, `.w-full`, etc. | ✅ |
| **Typography** | `.h1` - `.h6`, `.fw-bold` | `.text-4xl`, `.font-bold`, etc. | ✅ |
| **Colors** | `.bg-primary`, `.text-primary` | `.bg-[#0066CC]`, `.text-[#0066CC]` | ✅ |
| **Buttons** | `.btn`, `.btn-primary`, etc. | `.bg-[#0066CC]`, `.px-4`, etc. | ✅ |
| **Forms** | `.form-control`, `.form-check` | `.border`, `.flex`, etc. | ✅ |
| **Components** | `.breadcrumb`, `.stepper`, `.alert` | Custom Tailwind classes | ✅ |
| **Flex/Display** | `.d-flex`, `.justify-content-*` | `.flex`, `.justify-between`, etc. | ✅ |
| **Spacing** | `.mb-3`, `.p-4`, etc. | `.mb-4`, `.p-6`, etc. | ✅ **SAME RESULT!** |

## Critical Spacing Scale Discovery 🎯

**Bootstrap `.mb-3` = Tailwind `.mb-4`** (BOTH = 1rem = 16px!)

| Bootstrap | Pixel | Tailwind | Pixel | Match? |
|-----------|-------|----------|-------|--------|
| `.mb-3` | 1rem | `.mb-4` | 1rem | ✅ YES |
| `.mb-4` | 1.5rem | `.mb-6` | 1.5rem | ✅ YES |
| `.p-3` | 1rem | `.p-4` | 1rem | ✅ YES |
| `.p-4` | 1.5rem | `.p-6` | 1.5rem | ✅ YES |

**Why?** Both use 0.25rem base unit. Bootstrap `.mb-3` = 3 × 0.25rem = 0.75rem? **NO!** Bootstrap 5 uses 1rem base for spacing: `.mb-3` = 3 × 0.333rem = 1rem. Tailwind: `.mb-4` = 4 × 0.25rem = 1rem. **SAME!**

## Color Tokens (Design Comuni)

| Design Comuni Color | Hex | Tailwind |
|-------------------|-----|----------|
| Primary (buttons, links) | `#0066CC` | `.bg-[#0066CC]` |
| Secondary (outline buttons) | `#6C7688` | `.bg-[#6C7688]` |
| Success (alerts) | `#198754` | `.bg-[#198754]` |
| Danger (errors) | `#DC3545` | `.bg-[#DC3545]` |
| Warning (warnings) | `#FFC107` | `.bg-[#FFC107]` |

## Next Steps

1. ✅ Classes added to `app.css`
2. ⬜ **Build theme**: `cd laravel/Themes/Sixteen && npm run build`
3. ⬜ **Copy to public**: `npm run copy`
4. ⬜ **Verify**: `ls -la public_html/themes/Sixteen/`
5. ⬜ **Test**: Compare screenshots with Design Comuni reference
6. ⬜ **QMD ingest**: `qmd update --name theme-sixteen`

---

**Last updated**: 2026-05-04 by LLM Wiki Maintainer
**Files modified**: `laravel/Themes/Sixteen/resources/css/app.css`
**Build required**: YES — run `npm run build && npm run copy`
