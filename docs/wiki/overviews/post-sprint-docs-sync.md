---
title: Post-sprint — sincronizzazione docs modulo e tema
type: overview
created: 2026-05-29
---

# Post-sprint — sync documentazione

Eseguire a fine sprint (o dopo `/bmad/dev-story` architetturale).

## Checklist

1. Codice implementato → concept in `docs/wiki/concepts/` (nome minuscolo).
2. Backlink in [wiki/index.md](../index.md) tabella «Pagine Compilate» o sezione sprint.
3. Tema Sixteen: se consumer (map-lit, tab) → file in `Themes/Sixteen/docs/` + voce in [wiki/index.md](../../../../Themes/Sixteen/docs/wiki/index.md).
4. Wiki root: `docs/wiki/concepts/` o `guidelines/` + voce in [trigger map](../../../../docs/wiki/rules/00-TRIGGER_MAP.md) se regola nuova.
5. Story BMAD: `docs/stories/story-NNN-*.md` con sezione GitHub (issues + discussions).
6. `bashscripts/docs/llm-wiki-qmd.sh update`
7. `docs/wiki/log.md` se decisione stabile.

## Sprint 6–7 — riferimenti

- [folio-api-no-controllers.md](../concepts/folio-api-no-controllers.md)
- [ticket-citizen-rating-via-rating-module.md](../concepts/ticket-citizen-rating-via-rating-module.md)
- [module-basemodel-rule.md](../concepts/module-basemodel-rule.md)
- Root: [module-boundaries-rating.md](../../../../docs/wiki/concepts/module-boundaries-rating.md)
