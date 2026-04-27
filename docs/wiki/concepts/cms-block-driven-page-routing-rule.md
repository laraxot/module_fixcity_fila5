---
name: cms-block-driven-page-routing-rule
description: "REGOLA: quando un URL usa tests/[slug].blade.php, la pagina è CMS-driven (JSON blocks), NON hardcoded. Cercare PRIMA il JSON block, poi il Folio page."
type: concept
---

# CMS Block-Driven Page Routing Rule

## REGOLA PERMANENTE: URL `/it/tests/{slug}` = CMS JSON blocks, NON Folio hardcoded

### Vincolo assoluto

```
SE l'URL matcha /it/tests/*:
  1. Cercare: config/local/fixcity/database/content/pages/tests.{slug}.json
  2. Il JSON definisce content_blocks → view → @include
  3. Il block view monta il componente/Livewire
  
NON guardare prima il Folio page hardcoded — il JSON è la source of truth.
```

### Perché

L'architettura CMS usa un pattern a due livelli:
- **Folio page**: `tests/[slug].blade.php` — shell generica per TUTTE le pagine test
- **CMS JSON**: definisce i blocchi (breadcrumb, wizard, contacts-card, ecc.)
- **Block view**: thin wrapper che renderizza il componente appropriato

Se esiste un file Folio hardcoded (`segnalazione-crea.blade.php` con route `segnalazione.crea`), questo è un PERCORSO SEPARATO — non il canale CMS.

### Come trovare la source of truth per una pagina

```
1. URL → /it/tests/segnalazione-crea
2. Route name → tests.view
3. Route handler → tests/[slug].blade.php
4. pageSlug → "tests.segnalazione-crea"
5. JSON → config/local/fixcity/database/content/pages/tests.segnalazione-crea.json
6. Block → @include($block->view) → block blade → @livewire(...)
```

### Anti-pattern da evitare

- Assumere che `/it/tests/segnalazione-crea` corrisponda a `pages/segnalazione-crea.blade.php` ❌
- Ignorare il JSON di configurazione ❌
- Cercare il widget SOLO nei Filato pages hardcoded ❌

### Documentazione

- `docs/wiki/concepts/segnalazione-crea-url-routing-chain.md` — Fixcity wiki
- `laravel/Themes/Sixteen/docs/wiki/concepts/theme-cms-block-architecture-segnalazione-crea.md` — Theme wiki
