---
title: "Ownership map — sostenibilità"
type: architecture
status: proposed
updated: 2026-10-15
tags: [docs, fixcity, bmad]
qmd: "mappa di ownership fixcity"
---

# Mappa di ownership

Principio: **un pezzo di business logic → un modulo (o tema)**. Il dossier BMAD
coordina; non sostituisce i docs di dominio.

```text
Missione + impatto ticket     → Fixcity
Contenuto pagine/sezioni      → Cms
Isolation / opt-in per host   → Tenant
Ruoli approvazione / membership → User
Privacy CTA / pagamenti       → Gdpr
Asset loghi partner           → Media
Classi base / no dominio      → Xot (solo stewardship)
Vestito FO + WCAG             → Themes/Sixteen
Inbound editoriale            → Blog (+ inbound-marketing dossier)
Newsletter (solo con consenso)→ Notify
Eventi contestuali            → Themes/Meetup
Indice second brain           → docs/wiki
Coordinamento BMAD            → laravel/Modules/Fixcity/docs/bmad/project-sustainability
```

## Confini vietati

- Sixteen non inventa partner o prezzi
- Cms non gestisce contratti commerciali
- Fixcity non diventa ad server
- Tenant non ospita HTML sponsor di terzi
- Xot non accumula logica di fundraising

## Flusso futuro (post-GO)

`contratto/policy → Cms JSON (proiezione) → Sixteen Blade (vestito) → cittadino`

Audit e scadenze vivono fuori dalla sola proiezione JSON (vedi
[../footer-sponsorship/architecture.md](../footer-sponsorship/architecture.md)).
