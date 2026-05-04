---
title: "Header Green Branding Rule"
type: concept
confidence: high
created: 2026-04-20
updated: 2026-05-04
tags: [header, branding, colori, design-comuni, slim-bar]
related:
  - ../../../Sixteen/docs/wiki/concepts/header-color-parity.md
  - ../../../Sixteen/docs/wiki/concepts/header-slim-dropdown-behavior.md
  - ../../../Sixteen/docs/wiki/concepts/design-comuni-header-green-navbar-rule.md
---

# header green branding rule

## Regola di Branding Locale

Nonostante i riferimenti standard di Design Comuni (Bootstrap Italia) utilizzino il Blu (#0066CC), il modulo Fixcity adotta per questa istanza una **Branding basata sul Logo del Comune** (verde).

### Definizione Colori

| Elemento | Colore | Token CSS | File sorgente |
|---------|--------|-----------|---------------|
| **Slim Header** | Verde Scuro `#00402B` | `--color-italia-dark` | `header-footer-colors.css` |
| **Center Header** | Verde `#007A52` | `--color-italia` | `header-footer-colors.css` |
| **Navbar** | Verde `#007A52` | `--color-italia` | `header-footer-colors.css` |
| **CTA "Accedi"** | Verde `#007A52` inline | — | `personal-area-guest-cta.blade.php` |

### Applicazione Obbligatoria

Tutti i componenti dell'header devono seguire questa scala cromatica:

- **Slim Header Wrapper**: Verde Scuro `#00402B`
- **Center Header Wrapper**: Verde `#007A52`
- **Navbar Header Wrapper**: Verde `#007A52`
- **CTA "Accedi all'area personale"**: Verde `#007A52` — per non creare "Blue Spots" incongruenti
- **Link Secondari**: Sfondo Verde `#007A52`

## Rationale

La parità visuale non è solo copiare i token di un framework, ma rispettare la **gerarchia cromatica dell'ente locale** all'interno della struttura architettonica del framework.

## Stato File Verificato (2026-05-04)

Analisi codice condotta su:
- `laravel/Themes/Sixteen/resources/css/components/header-footer-colors.css` — imposta tutti i token verdi con `!important`
- `laravel/Themes/Sixteen/resources/css/design-comuni-header-fix.css` — conferma `#00402b` slim e `#007a52` center/navbar
- `laravel/Themes/Sixteen/resources/views/components/sections/header/partials/personal-area-guest-cta.blade.php` — `style="background-color: #007A52; border-color: #007A52;"` inline deliberato

**Conclusione**: I colori verdi sono **corretti e deliberati**. Non modificare per renderli blu.

## Story di riferimento

- `8-103`: fix dropdown Livewire + conferma branding verde — non cambia i colori

## Backlink

- [header-color-parity Sixteen](../../../Sixteen/docs/wiki/concepts/header-color-parity.md)
- [header-slim-dropdown-behavior Sixteen](../../../Sixteen/docs/wiki/concepts/header-slim-dropdown-behavior.md)
