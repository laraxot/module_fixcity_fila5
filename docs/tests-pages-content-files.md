---
id: fixcity-tests-pages-content-files
title: "File di contenuto delle pagine /it/tests"
type: guide
status: active
created: 2026-10-13
qmd: "fixcity tests pages json content files design comuni homepage risultati ricerca tenant"
updated: 2026-10-13
tags: [docs, fixcity]
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
Dettagli: `Modules/Cms/docs/json-content-slug-uniqueness.md` e `Themes/Sixteen/docs/bmad/stories/dcp-00-epic-design-comuni-parity-v2.story.md`.

## Sezioni condivise (aggiornamento 2026-10-13)
`config/database/content/sections/header.json`, `footer.json` e `search-modal.json` (tenant: `laravel/config/local/fixcity/database/content/sections/`) servono due template:
la variante canonica `v1` del sito reale. Eventuali `tpl="design-comuni"` nelle pagine legacy sono alias di compatibilità verso `v1`, non un renderer parallelo. Le chiavi Design Comuni sono **additive**
(header: `brand.header_title/menu_title`, `region.title/aria_label`, `languages`, `social_links`, `search`, `menu`, `account.element/login_label`, `navigation[].element`;
footer: `eu_logo`, `groups`, `contact.details/office_url`, `social_title`, `social[].icon`; modale: tutto in `search-modal.json`), cosi' il sito reale non cambia.
Il modulo ha una copia piu' vecchia del footer (senza `contact`): il tenant ha la precedenza.
