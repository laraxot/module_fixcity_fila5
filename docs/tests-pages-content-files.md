---
id: fixcity-tests-pages-content-files
title: "File di contenuto delle pagine /it/tests"
type: guide
status: active
created: 2026-10-13
qmd: "fixcity tests pages json content files design comuni homepage risultati ricerca tenant"
---

# File di contenuto delle pagine `/it/tests`

Le pagine di prova del front office (`/it/tests/<slug>`) sono **dato**: `config/database/content/pages/tests.<slug>.json` in questo modulo
(valori predefiniti) e `laravel/config/local/fixcity/database/content/pages/tests.<slug>.json` (override del tenant). La pagina Folio e' una sola
(`Themes/Sixteen/resources/views/pages/tests/[slug].blade.php`); il rendering e' dei blocchi del tema.

## Stato 2026-10-13
- `tests.homepage.json`: modello dati completo (hero, government, events, topics, theme_sites, search, links, feedback, contacts), letto da `components/blocks/homepage.blade.php`.
- `tests.risultati-ricerca.json`: dati del blocco `search-results`; da riallineare al riferimento (story DCP-07).
- Header e footer: `sections/header.json` e `sections/footer.json`; da estendere per il markup Design Comuni (story DCP-05, DCP-06).

## Regole
Nessun file con slug duplicato nella stessa cartella; nessun testo nelle viste Blade; nomi `tests.<slug>.json`.
Dettagli: `Modules/Cms/docs/json-content-slug-uniqueness.md` e `Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-00-epic-design-comuni-parity-v2.story.md`.
