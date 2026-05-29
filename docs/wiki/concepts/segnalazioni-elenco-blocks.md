# Segnalazioni elenco — composizione blocchi CMS

## Scopo

La pagina canonica **Elenco segnalazioni** (`/it`, slug CMS `home`) deve replicare il [reference Design Comuni](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html) con **blocchi piccoli e riusabili**, non un unico `segnalazioni-layout`.

Story owner: [STORY-062](../../../../docs/stories/STORY-062-segnalazioni-elenco-cms-blocks-decomposition.md).

## Cosa non va nel monolite attuale

- Breadcrumb nel JSON → duplicato su ogni pagina (il breadcrumb è **globale** per tutte le pagine).
- Filtri sidebar condizionati a `tickets.json` non vuoto → su desktop **sparisce** la colonna sinistra.
- CTA, rating e contatti accorpati → difficile riusare su altre pagine Design Comuni.

## Stack blocchi target

| Ordine | Tipo | Componente tema |
|--------|------|-----------------|
| *(runtime)* | breadcrumb | Layer `<x-page>` — non in `content_blocks` |
| 1 | `hero` | `blocks/hero/*` — titolo + sottotitolo |
| 2 | `layout-grid` (universale) | `layout/grid` + figli: `filters/sidebar` + `map-lit`/tab — **no** blocco `segnalazioni/*` page-specific (#77) |
| 3 | `cta` | blocco testo+bottone «Segnala disservizio» |
| 4 | `rating` | `rating/default` |
| 5 | `contacts` | `contacts/default` |

## Desktop: griglia filtri + mappa

Reference: sidebar **sempre** a sinistra; contenuto (tab + mappa/lista) a destra.

```
row
├── aside.col-lg-3     → map-filter-lit / category list
└── div.col-lg-8       → toolbar risultati + nav-tabs + tab-content
```

Fix P0: non omettere `aside` se facet vuoti — mostrare shell UI; popolare `public_html/data/tickets.json` (vedi [segnalazioni-elenco-map-architecture.md](./segnalazioni-elenco-map-architecture.md)).

## Collegamenti

- [segnalazioni-elenco-map-architecture.md](./segnalazioni-elenco-map-architecture.md)
- [it-vs-segnalazioni-elenco.md](../../../../../Themes/Sixteen/docs/design-comuni/visual-comparison/it-vs-segnalazioni-elenco.md)

### GitHub (discussion + issue — aggiornare a ogni PR)

| Repo | Discussion | Issue |
|------|------------|-------|
| base_fixcity_fila5 | [#133](https://github.com/laraxot/base_fixcity_fila5/discussions/133) (canon), [#36](https://github.com/laraxot/base_fixcity_fila5/discussions/36), [#45](https://github.com/laraxot/base_fixcity_fila5/discussions/45) | [#91](https://github.com/laraxot/base_fixcity_fila5/issues/91), [#77](https://github.com/laraxot/base_fixcity_fila5/issues/77) |
| theme_sixteen_fila5 | [#13](https://github.com/laraxot/theme_sixteen_fila5/discussions/13) | [#12](https://github.com/laraxot/theme_sixteen_fila5/issues/12) |
| module_geo_fila5 | [#5](https://github.com/laraxot/module_geo_fila5/discussions/5) | [#4](https://github.com/laraxot/module_geo_fila5/issues/4) |
| module_cms_fila5 | [#16](https://github.com/laraxot/module_cms_fila5/discussions/16) | — |
| module_fixcity_fila5 | *(discussions disabilitate)* | issue modulo quando aperta |
