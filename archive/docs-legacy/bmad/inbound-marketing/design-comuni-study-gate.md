---
title: "Gate Design Comuni — studio novità/eventi/argomenti prima dell’abilitazione"
type: research-report
status: gate-passed
module: Fixcity
created: 2026-10-16
updated: 2026-10-16
tags: [bmad, design-comuni, novita, eventi, argomenti, gate]
qmd: "Design Comuni gate novità eventi argomenti lista risorse header footer"
related:
  - ../../../../../Themes/Sixteen/docs/design-comuni-novita-eventi-fo.md
  - ./brainstorming-event-module-vs-article-type.md
  - ./brainstorming-demo-blog-eventi.md
  - ../../../../Comment/docs/bmad/fo-comments/README.md
issues: []
discussions: []
---

# Gate — studio Design Comuni (2026-10-16)

## Pagine studiate

| URL | Pattern FO FixCity |
|---|---|
| argomenti.html | Hub temi (Cms + categorie) |
| argomento.html | Aggregatore tema (novità+eventi+servizi) |
| lista-risorse.html | Lista + evidenza |
| lista-risorse-categorie.html | Lista + evidenza + categorie |
| novita.html | `/blog` — `content_type=post` |
| novita-dettaglio.html | `/blog/{slug}` |
| eventi.html («Vivere il comune») | `/events` — `content_type=event` |
| evento-dettaglio.html | `/events/{slug}` |

## Scopo (filosofia)

Il cittadino si orienta per **temi** (argomenti) e consuma **aggiornamenti**
(novità) e **occasioni** (eventi). Non è un CMS marketing B2B: è informazione
istituzionale + partecipazione. Header/footer espongono i path; Blog possiede
i contenuti; Sixteen veste; Comment abilita dialogo autenticato.

## Decisione Ponytail (multi-agente)

**KEEP** `Article` + `content_type ∈ {post,event}` — **NO** `Modules/Event`
finché non servono RSVP/capienza/ICS/pagamenti (≥2 criteri).

Vedi [brainstorming-event-module-vs-article-type.md](./brainstorming-event-module-vs-article-type.md).

## Nav

Header/footer JSON già contengono Novità→`/blog` e Eventi→`/events`
(SSoT Fixcity + local + eu). Abilitazione = seed + FO parity, non nuova nav.

## Demo

`DemoContentSeeder`: 8 novità + 5 eventi, foto Design Comuni, commenti morph
Comment (approvati/pending/reply). Seed:

```bash
cd laravel && php artisan db:seed --class="Modules\\Blog\\Database\\Seeders\\DemoContentSeeder"
```

## Commenti autenticati

[fo-comments](../../../../Comment/docs/bmad/fo-comments) — Filament
`CommentsWidget` only.
