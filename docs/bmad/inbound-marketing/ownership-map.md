---
title: "Ownership — inbound blog ed eventi"
type: architecture
status: proposed
updated: 2026-10-15
---

# Ownership

```text
Post + content_type post|event → Blog (Article, ArticleResource)
Nav / landing hub risorse      → Cms (pages/sections JSON)
Presentazione FO Design Comuni → Sixteen (liste/dettaglio eventi, card blog)
Presentazione eventi Meetup    → Meetup (tema; no regole dominio)
Notifiche iscrizione (futuro)  → Notify (+ Gdpr consenso)
Metatag / schema / RSS         → Seo (con Cms/Blog)
Media cover/allegati           → Media
Isolation host                 → Tenant
Missione/KPI nei contenuti     → Fixcity (input storytelling)
Sostenibilità economica        → project-sustainability (non inbound)
```

## Flusso

`editor Blog → publish → Cms/nav opzionale → Sixteen/Meetup vestito → lettore`

## Vietato

- Sixteen non inventa articoli
- Meetup non decide pricing eventi
- Blog non gestisce pagamenti sponsor
- Footer globale ≠ calendario eventi

## Story ownership

- Blog: IM-BLOG-001..006
- Cms: IM-CMS-001
- Gdpr: IM-GDPR-001
- Notify: IM-NOTIFY-001 (deferred)
- Seo: IM-SEO-001
- UI/Media/Lang/Tenant: IM-UI-001, IM-MEDIA-001, IM-LANG-001, IM-TENANT-001
- Sixteen/Meetup: IM-SIXTEEN-001, IM-MEETUP-001
- Fixcity: IM-FIXCITY-001..005 (002 trust, 003 adozione, 004 Design Comuni IA, 005 demo seed)
