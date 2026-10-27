---
id: IM-FIXCITY-004
title: "Information architecture Design Comuni per blog e eventi"
type: story
module: Fixcity
epic: "III Authoring e presentazione"
status: blocked
priority: P0
owner: module:Fixcity
scope: "laravel/Modules/Fixcity/docs/bmad/, laravel/Themes/Sixteen/docs/bmad/, laravel/**/sections/header.json, laravel/**/sections/footer.json"
created: 2026-10-15
updated: 2026-10-15
tags: [bmad, design-comuni, information-architecture, blog, event, json]
qmd: "Design Comuni argomenti novità risorse eventi header footer JSON"
related: [IM-BLOG-002, IM-CMS-001, IM-SIXTEEN-001, IM-BLOG-003]
issues: []
discussions: []
---

# Information architecture Design Comuni per blog e eventi

## Richiesta / scopo
Come cittadino voglio trovare contenuti per tema e bisogno (come nel modello
Design Comuni), poi approfondire una novità o un evento, con Novità ed Eventi
raggiungibili da header e footer senza link hardcoded.

## Perché (prima di abilitare)
Le otto pagine statiche Design Comuni definiscono il contratto cognitivo:
argomenti → hub tema → liste risorse/novità/eventi → dettaglio con indice,
«a cura di», feedback e (per eventi) date/luogo/patrocinio. Abilitare Blog
senza questo contratto spezza la navigazione comunale. Studio:
[design-comuni-static-pages-study.md](../design-comuni-static-pages-study.md).

## Acceptance Criteria
- [x] Contratto documentato contro le otto pagine ufficiali.
- [x] Header/footer JSON espongono Novità→`/blog` e Eventi→`/events` in IT/EN/DE/ES
      (copie local + Fixcity module + eu); Blade senza link hardcoded.
- [x] Route FO `/blog` risolve lista + dettaglio (`content_type=post`).
- [x] Route FO `/events` risolve lista + dettaglio (`content_type=event`).
- [ ] Liste/dettagli rispettano breadcrumb, indice, empty state, a11y.
- [ ] Sponsor e patrocinio distinti; nessun banner commerciale.

## Tasks / Subtasks
- [x] Studio otto URL Design Comuni + mapping Article blog|event.
- [x] Aggiornare header.json / footer.json (etichette Design Comuni).
- [x] Folio/Sixteen listing+detail Novità (IM-SIXTEEN-001).
- [x] Folio/Sixteen listing+detail Eventi (IM-SIXTEEN-002).
- [ ] Collegare Blueprint form/table type+date (IM-BLOG-002/003).
- [ ] Hub argomenti/risorse Cms (IM-CMS-001).

## Owned File / Module Scope
Docs Fixcity/Sixteen; JSON sections Fixcity; implementazione pagine = Sixteen/Blog.

## Testing / Verifica
JSON parse; smoke route quando esistono; WCAG; nessun `#` in nav.

## Dipendenze / blocchi
**Blocked** finché `/blog` e `/events` FO non esistono. JSON è pronto; abilitazione no.

## Dev Notes / Ponytail
Existing Assets First: `Article`, section JSON, renderer header/footer.
Nessun modulo Eventi. Filament Blueprint = wiring campi esistenti.

## Verifica JSON 2026-10-15

Header/footer in `Modules/Fixcity/config/.../sections/` + `config/local` +
`config/eu`: Novità→`/blog`, Eventi→`/events` presenti in IT/EN/DE/ES.
Footer groups `news` + `living` («Vivere il comune») allineati al modello.
**Non abilitare** finché le route FO non esistono; aggiornare solo JSON se
cambiano etichette/path.
