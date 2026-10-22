---
title: "FO link coherence audit — search modal, profile, logout"
type: story
status: in-progress
module: Fixcity
created: 2026-10-22
updated: 2026-10-22
tags: [bmad, front-office, links, design-comuni, playwright]
qmd: "front office link coherence search-modal profile edit auth logout area-personale"
related:
  - ../bmad/filament-blueprint.md
  - ../bmad/design-comuni-information-architecture.md
  - ../links.md
  - ../../../../Themes/Sixteen/docs/wiki/concepts/fo-folio-named-routes-header.md
  - ../../../../Themes/Sixteen/docs/compliance/03-front-office-scheda-per-pagina.md
  - dcp-230-ticket-pubblici-residui-dell-audit-del-front-office.story.md
---

<!--
Supersedes: nessuno story dedicato ai link FO morti (DCP-230 copre ticket pubblici,
non search-modal/profile/logout). Linked from: links.md, filament-blueprint FO section,
Sixteen fo-folio-named-routes-header. User: «controlla tutto il front office, controlla
tutti i links del front office… bmad + second brain + filament blueprint + playwright-mcp».
-->

# Scopo

Ogni link del front office FixCity deve portare a una destinazione che esiste,
è localizzata e ha senso nel dominio segnalazioni (non demo Design Comuni CIE /
tributi / appuntamenti).

## Perché

Il tema Sixteen monta prima il catch-all `[container0]/[slug0]`: le pagine Folio
dei moduli restano in `folio:list` ma possono rispondere 404 se manca il JSON CMS.
I suggerimenti della search-modal erano ancora copy comunale generico.

## Findings (verificati HTTP su `127.0.0.1:8000`)

| Destinazione | Prima | Dopo |
| --- | --- | --- |
| Search-modal suggestions | `/it/services/*`, `/it/appointments` (demo) | `/tickets`, `/tickets/create`, `/tickets/track`, `/domande-frequenti`, `/lista-categorie`, `/blog` |
| `/it/profile/edit` | 404 (jolly tema senza CMS) | 302 → login (ospite) / alias → `area-personale/impostazioni` |
| `/it/auth/logout` GET | 404 | 302 home (ospite) / conferma + POST `route('logout')` |
| Dropdown user Design Comuni | `dashboard` / `notifications` / `profile.edit` / POST path errato | `area-personale.{pratiche,notifiche,impostazioni}` + POST `route('logout')` |
| Blog/events CTA «Prenota» | `/servizi` | `/lista-categorie` |

Crawl JSON+HTML (183 candidati): gli unici 404 reali rilevanti erano logout e path
con doppio locale / placeholder Blade nei JSON di test; le pagine pubbliche FO
core rispondono 200/302 atteso.

## File toccati

- `config/local/fixcity/.../sections/search-modal.json` + twin Fixcity
- `Themes/Sixteen/.../pages/profile/edit.blade.php` (alias)
- `Themes/Sixteen/.../pages/auth/logout.blade.php` (`auth.logout`)
- `Themes/Sixteen/.../header/partials/user-dropdown.blade.php`
- `Themes/Sixteen/.../header/partials/personal-area-guest-parity.blade.php`
- `pages/blog/index.json`, `pages/events/index.json` (+ twin blog)

## Acceptance

- [x] Search-modal homepage senza link `/services` o `/appointments`
- [x] `GET /it/profile/edit` non 404
- [x] `GET /it/auth/logout` non 404
- [x] Form logout Design Comuni → `route('logout')` (POST `/logout`)
- [ ] Smoke browser autenticato (pratiche → seguite → impostazioni → logout)
- [x] Smoke Playwright Chromium CLI (guest): home/tickets/track/blog/faq + profile→login + logout→home; zero leak `/services|/appointments`
- [ ] Chrome / Playwright MCP: rieseguire quando il canale ha Google Chrome stable

## Canon navigazione area personale

SSoT UI: JSON `header` + pagine CMS `area-personale.*` (auth). `profile.edit` resta
alias. Blueprint Filament non governa queste pagine FO: vedi
[filament-blueprint.md](../bmad/filament-blueprint.md) e
[design-comuni-information-architecture.md](../bmad/design-comuni-information-architecture.md).

## Religione URL (correzione 2026-10-22)

Errore ripetuto da evitare: `LaravelLocalization::localizeUrl` e `route('tickets.show'|create)`.
Canon unico: [frontoffice-link-conventions.md](../frontoffice-link-conventions.md) —
`route('container0.view', ['container0' => …, 'slug0' => …])`. Test contratto
ampliati in `Frontoffice*ContractTest`.

