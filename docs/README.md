---
title: "Fixcity: documentazione del modulo"
type: readme
tags: [module, documentation, fixcity, ownership]
created: 2026-09-26
updated: 2026-10-20
qmd: "Fixcity modulo ownership ticket FAQ documentazione"
issues: []
discussions: []
status: active
---

# Modulo Fixcity

This directory routes to the active documentation roots for the Laravel modules in this repository.

## Fixcity: identita, ownership e limiti (verificato nel codice il 2026-10-14)

- **Identita**: modulo nwidart `Fixcity` (alias `fixcity`, namespace `Modules\Fixcity`, package `laraxot/module_fixcity_fila5`, repo git separato). Provider in `module.json`: `Providers/FixcityServiceProvider` e `Providers/Filament/AdminPanelProvider`. `php artisan module:list` lo mostra `Enabled`.
- **Cosa possiede**: le segnalazioni dei cittadini (ticket). Modello `app/Models/Ticket.php`, enum `app/Enums/Ticket{Status,Priority,Type}Enum.php`, 64 file in `app/Actions/` (QueueableAction, nessun Controller), wizard `app/Filament/Widgets/CreateTicketWizardWidget.php` con schema `app/Filament/Resources/TicketResource/Schemas/TicketForm.php` (passi `privacy`, `data`, `summary`), pagine Folio in `resources/views/pages/` (`tickets/create`, `tickets/confirmation`, `tickets/track`, `tickets/[id]`, API JSON in `pages/api/`), area personale e pagine legali.
- Le operazioni asincrone dei ticket sono Queueable Actions in `app/Actions/`; non esistono `app/Jobs/` o `app/Services/`. Il contratto e la motivazione sono in [Queueable Actions](../../Xot/docs/architecture/queueable-actions.md).
- **Cosa NON possiede**: mappe e picker di coordinate (modulo Geo), layout, Design Comuni e blocchi del front office (tema Sixteen), il motore delle pagine dinamiche (modulo Cms: `PageSlugMiddleware`, `Page`). I JSON delle pagine di Fixcity stanno in `config/database/content/pages/` (105 file) e hanno copie in `laravel/config/local/fixcity/`.
- **Superficie HTTP**: `routes/web.php` e `routes/api.php` del modulo sono vuoti. I POST stanno in `laravel/routes/web.php` (`/livewire/form-create-ticket` verso `CreateTicketWizardWidget@submit`, `/{lang}/tickets/{ticket}/follow` verso `FollowTicketAction`). Il pannello admin risponde su `/fixcity/admin/tickets` (da `php artisan route:list`).
- **Quick start** (dalla cartella `laravel/`):
  - stato del modulo: `php artisan module:list`;
  - analisi statica: `./vendor/bin/phpstan analyse Modules/Fixcity` (config unica `laravel/phpstan.neon`, senza `--level`). Misura del 2026-10-14: 122 errori, tutti in `config/*.php` (105 `larastan.noEnvCallsOutsideOfConfig`, 12 `array.duplicateKey` in `morph_map.php`, 4 `class.notFound`, 1 `binaryOp.invalid`); 0 in `app/`, `tests/`, `database/`;
  - test su database usa-e-getta (mai sul DB di sviluppo): `export DB_DATABASE_LIMESURVEY= APP_CONFIG_CACHE=/nonexistent/config.php` poi, dalla root del repo, `bash bashscripts/tools/heavy-slot.sh bash bashscripts/tools/pest-scratch-db.sh Modules/Fixcity/tests/Unit/Enums/TicketStatusEnumTest.php`. Esito del 2026-10-14 con anche `TicketPriorityEnumTest.php`: 16 test passati, 127 asserzioni. La suite completa (128 file PHP in `tests/`) non e' stata eseguita in questa verifica;
  - dati demo (scrive sul database, non eseguito qui): `php artisan db:seed --class='Modules\Fixcity\Database\Seeders\FixcityDatabaseSeeder'`, attivo solo in ambiente `local`, `testing` o `demo`.
- **Limiti noti**: nella radice di `docs/` ci sono 99 file `.md` (524 in tutto l'albero; molti rapporti storici, non evidenza corrente) e 13 `.lock` di sessioni vecchie (60 nell'albero); i link a `_bmad-output/implementation-artifacts/7-*` non hanno bersaglio nel repo; molti documenti storici descrivono nomi superati (`Segnalazione`, `/it/tests/segnalazione-crea`, `FixcitySeeder`). Fonte di verita' per cio' che e' attuale: [00-INDEX.md](./00-INDEX.md), le story in [stories/](./stories/) e il [decision log](./decision-log.md).

## How To Use This Index

- Start here only to choose the module.
- Then open that module's `docs/README.md`.
- Use archive files only when the active docs do not answer the question.

Il contratto comune per tutti i docs di moduli e temi è in
[DOCUMENTATION_GOVERNANCE.md](../../../../docs/DOCUMENTATION_GOVERNANCE.md).
Questo `README.md` è l'ingresso canonico del modulo; `00-INDEX.md` resta una
mappa operativa compatibile con la documentazione storica e non una seconda
fonte di verità.

## Core Modules

| Module | Role | Docs |
| --- | --- | --- |
| `Xot` | shared base framework, conventions, base classes | [README](../../Xot/docs/README.md) |
| `User` | auth, profiles, permissions | [README](../../User/docs/README.md) |
| `Tenant` | tenant isolation and tenancy contracts | [README](../../Tenant/docs/README.md) |
| `UI` | shared UI components and Filament-facing primitives | [README](../../UI/docs/README.md) |
| `Lang` | translations and localization | [README](../../Lang/docs/README.md) |
| `Job` | queues and background processing | [README](../../Job/docs/README.md) |
| `Media` | uploads and media handling | [README](../../Media/docs/README.md) |
| `Notify` | notifications and templates | [README](../../Notify/docs/README.md) |
| `Activity` | audit trail and activity logging | [README](../../Activity/docs/README.md) |
| `Gdpr` | privacy and GDPR support | [README](../../Gdpr/docs/README.md) |

## Domain Modules

| Module | Role | Docs |
| --- | --- | --- |
| `Fixcity` | ticketing and frontoffice reporting flows | [README](./README.md) (this file) |
| `Cms` | CMS blocks, content modeling, frontoffice composition | [README](../../Cms/docs/README.md) |
| `Blog` | editorial content | [README](../../Blog/docs/README.md) |
| `Comment` | comments and moderation | [README](../../Comment/docs/README.md) |
| `Rating` | ratings and feedback | [README](../../Rating/docs/README.md) |
| `Seo` | metadata and SEO support | [README](../../Seo/docs/README.md) |
| `Geo` | geography, addresses, map-related concerns | [README](../../Geo/docs/README.md) |
| `AI` | AI integration and MCP-related work | [README](../../AI/docs/README.md) |

## Documentation Rules For Modules

- The active documentation home for a module is `laravel/Modules/<Module>/docs/README.md`.
- Never create `lang/lang/` or `_docs/` in a module; see [directory-structure-rules.md](./directory-structure-rules.md).
- Do not create new parallel entry files if `README.md` already exists.
- Prefer links over copied explanations across modules.
- Keep implementation details inside topic files, not inside indexes.
- Treat `app/docs`, `resources/views/docs`, and nested `docs/docs` as non-canonical unless explicitly justified.

## Fast Paths

- Shared project rules: [docs/README.md](../../../../docs/README.md)
- Project documentation governance: [DOCUMENTATION_GOVERNANCE.md](../../../../docs/DOCUMENTATION_GOVERNANCE.md)
- Theme docs: [laravel/Themes/docs/README.md](../../../Themes/docs/README.md); each theme owns its docs, for example [Sixteen](../../../Themes/Sixteen/docs/README.md)
- LLM wiki workflow: [docs/wiki/README.md](../../../../docs/wiki/README.md)
- Modules note: [llm-wiki.md](./llm-wiki.md)
- Agent rules: [AGENTS.md](../../../AGENTS.md)

## Documentation

- [FAQ catalog ownership](./faq-catalog-ownership.md) — dominio FAQ in Fixcity,
  composizione in Cms e renderer nel tema Sixteen.

Il modulo possiede significato, catalogo, pubblicazione e fallback delle FAQ;
non possiede la route Folio né il markup del tema. Una modifica a domande o
risposte parte da `CivicFaqCatalog`/`ListPublishedFaqsAction`; una modifica
visuale parte dal blocco Sixteen. Non creare view FAQ nel tema per compensare
un difetto del catalogo.

- [On-Demand Pattern](./ON-DEMAND-PATTERN.md) — Pattern per caricamento efficiente
- [QMD Setup](./QMD-SETUP.md) — Configurazione ricerca locale
- [Performance](./PERFORMANCE-OPTIMIZATION.md) — Metriche e best practice
- [Project Structure](./PROJECT-STRUCTURE.md) — Directory layout
- [File di contenuto delle pagine /it/tests](./tests-pages-content-files.md) — `tests.<slug>.json`, stato e regole (2026-10-13)
- [Indice canonico del modulo](./00-INDEX.md), [mappa delle sottocartelle](./INDEX.md), [panoramica](./index.md)
- [Scopo e conformita' normativa](./compliance/00-scopo-del-progetto.md), [decision log](./decision-log.md), [wiki](./wiki/index.md), [story](./stories/index.md)

## BMAD — inbound marketing (blog + eventi)

SSoT: [bmad/inbound-marketing/README.md](./bmad/inbound-marketing/README.md)

Wiki: skill/rule/memory `fixcity-inbound-editorial*` in `docs/wiki/`.
