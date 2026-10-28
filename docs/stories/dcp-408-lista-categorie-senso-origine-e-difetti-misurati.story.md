---
title: "DCP-408: lista-categorie, origine, senso e difetti misurati"
type: story
status: in-progress
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, front-office, lista-categorie, design-comuni, tailwind, data-dc, filament-blueprint]
qmd: "lista-categorie origine senso categorie segnalazioni design comuni data-dc tailwind cta invisibile category type"
related:
  - ../bmad/lista-categorie-purpose.md
  - ../../../../Themes/Sixteen/docs/bmad/lista-categorie-rendering-contract.md
  - ../../../User/docs/stories/dcp-53-area-personale-pagine-con-scopo.story.md
  - ./fo-link-coherence-audit.story.md
  - ../bmad/stories/STORY-517-design-comuni-coverage.md
issues: []
discussions: []
---

# DCP-408: lista-categorie

Stato: **in-progress**. Altre due sessioni lavorano sulla stessa pagina (contratto di prodotto in `../bmad/lista-categorie-purpose.md`, contratto di rendering nel tema Sixteen). Questa story non li duplica: raccoglie origine, misure e decisioni aperte che mancavano.

## Richiesta (verbatim)

«guarda come si vede https://fixcity.eu/it/lista-categorie bisogna trovarci anche un senso, perche' hai fatto questa pagina ? a cosa serve ? che senso ha ? aggiorna studia e migliora le cartelle docs dentro i moduli e dentro i temi e fa tutto con bmad + second brain + filament blueprint + ponytail»

## Risposta breve

- **Perche' esiste**: copertura del censimento Design Comuni, non un bisogno civico. La Folio page e' stata aggiunta il 2026-09-24 e `STORY-517` la segna «esiste gia'». DCP-53 (2026-10-13) ha riparato un 500 e ne e' rimasta una sola card fissa che duplicava `/tickets`.
- **Cosa e' il riferimento**: `lista-categorie.html` di design-comuni-pagine-statiche e' un template «lista con risorse in evidenza e categorie» sotto Novita': categorie di *contenuti*. Non e' una directory di servizi.
- **Che senso puo' avere in FixCity**: orientare il cittadino prima di segnalare («che problemi posso segnalare?») con due azioni sempre visibili: `/tickets/create` e `/tickets`. E' la decisione D-LC-01 dei due contratti.
- **Cosa non e'**: non e' l'elenco `/tickets`, non filtra e non mostra conteggi.

## Stato misurato 2026-10-27

- `fixcity.eu` risolve a 46.225.90.178, **un altro server** rispetto allo sviluppo (192.168.1.40). Online c'e' la versione vecchia: una card, titolo e sottotitolo «Esplora le segnalazioni».
- Sviluppo, `:8081`: con il codice in corso 500 su it/en/de/es (`TypeError` in `ListPublicCategoriesAction`: la closure di `with()` riceve `HasMany`, non `Builder`). Corretto; dopo la correzione 200 nelle 4 lingue, un solo `main`, un solo `h1`, 16 card.

## Difetti

| ID | Difetto | Evidenza | Proprietario | Stato |
|---|---|---|---|---|
| LC-01 | 500 per type hint `Builder` nella closure di `with()` | `TypeError` ... `HasMany given`; 200 dopo la correzione | Fixcity | corretto |
| LC-02 | Le utility Tailwind non si applicano: nel CSS compilato ogni regola e' `:not(:where(body:has(.page-content[data-dc]), ... *))`, cioe' spenta nelle pagine `data-dc` | `p-6`, `text-xl`, `bg-primary-700`, `text-white`, `grid-cols-1` hanno tutte quel `:not` | Sixteen | aperto |
| LC-03 | CTA primaria «Segnala un problema» invisibile (bianco su bianco: `text-white` e `bg-primary-700` spenti) | screenshot 1440 px: si vede solo «Consulta le segnalazioni» | Sixteen | aperto |
| LC-04 | Niente griglia: `sm:grid-cols-2`, `lg:grid-cols-3`, `ring-offset-2` non esistono nel CSS compilato (0 blocchi `@media` 40/48/640rem); card senza padding, `h2/h3` enormi | screenshot + `grep` sul CSS | Sixteen | aperto |
| LC-05 | `<title>` «Esplora le segnalazioni» diverso da `h1` «Categorie per le segnalazioni» | `curl` su `:8081/it/lista-categorie`; sorgente del title non verificata (JSON `lista-categorie.index.json` e' il sospetto) | Fixcity | aperto |
| LC-06 | Tassonomia mista: 16 radici attive, 4 sono «argomenti» Design Comuni (icona `it-arrow-right-circle`, descrizione uguale al nome: Trasparenza e partecipazione, Servizi per la comunita', Accessibilita' e inclusione, Ambiente e spazio pubblico) | `Category::active()->root()` su DB sviluppo | Fixcity | aperto |
| LC-07 | `Category` non e' collegata alle segnalazioni: `tickets` non ha `category_id`, il legame e' `type` (`TicketTypeEnum`, 15 casi) | `DemoCategoryCatalog`, `PublicTicketListFilters` (`types[]`) | Fixcity | aperto |
| LC-08 | Nessuna Resource Filament per le categorie: la directory pubblica mostra dati curabili solo da seeder | `ls Filament/Resources` = solo `TicketResource` | Fixcity | aperto |
| LC-09 | Link in ingresso poco coerenti: 4 suggerimenti del search-modal e la CTA «Prenota» di blog ed eventi puntano a `/lista-categorie` | `blog.index.json:88`, `events/index.json:89`, `search-modal.json` | Fixcity | aperto |
| LC-10 | Il piano docs di Sixteen dice che la pagina e' servita da `[container0]/index` via JSON; in realta' il corpo viene dalla Folio page `Modules/Fixcity/resources/views/pages/lista-categorie.blade.php` (`name('services.categories')`) | la pagina rende 16 card solo grazie al `view()->share` di quella Folio page | Sixteen docs | aperto |

Causa radice di LC-02..04: il tema spegne le utility Tailwind sotto `body:has(.page-content[data-dc])` di proposito (isolamento Design Comuni). Le view di una pagina `data-dc` devono usare le classi DC, non Tailwind. Vale lo stesso per [[tickets-index-was-hardcoded-native-folio-page]] (Tailwind dentro DC = pixel 54%).

## Decisioni

- **D-LC-01 (presa)**: la pagina resta come directory delle categorie di segnalazione, con CTA. Niente conteggi ne' link filtrati finche' non esiste il legame categoria-tipo.
- **D-LC-02 (aperta, utente)**: come collegare categoria e filtro. A) colonna `type` su `categories` (migration: il progetto chiede conferma prima di toccare lo schema). B) tabella di mappatura in config. C) usare direttamente `TicketTypeEnum` come directory, con link `/tickets?types[]=<valore>` che l'elenco gia' supporta. Raccomandazione ponytail: C, zero schema nuovo; `Category` resta per descrizioni e sottocategorie.
- **D-LC-03 (aperta, utente)**: rinominare la CTA «Prenota» di blog/eventi (LC-09).
- **D-LC-04 (aperta, utente)**: escludere o spostare le 4 radici Design Comuni (LC-06).

## Blueprint Filament (piano, non eseguito)

`CategoryResource` nel modulo Fixcity (il tema Sixteen non possiede Resource, vedi `Themes/Sixteen/docs/bmad/filament-blueprint.md`): campi `name`, `description`, `icon`, `parent_id`, `is_active`, `sort_order` (e il tipo, se D-LC-02 sceglie A). Nessun relation manager, policy come `TicketResource`. Attenzione all'auto-discovery di Xot sulle cartelle `RelationManagers` ([[filament-blueprint-installed-and-relation-manager-autodiscovery]]). Le scelte di dominio sono quelle di D-LC-02: serve la risposta dell'utente prima di scrivere codice.

## Verifica

- `php -l Modules/Fixcity/app/Actions/ListPublicCategoriesAction.php`: nessun errore.
- `curl http://127.0.0.1:8081/{it,en,de,es}/lista-categorie`: 200, 1 `main`, 1 `h1`, 16 card.
- Non verificato: parity visiva, axe, viewport 375 e 768, sorgente del `<title>`.

## Per le altre sessioni

Una sola modifica mia in codice: `HasMany` nella closure di `ListPublicCategoriesAction`. Se l'avete gia' cambiata, tenete la vostra. LC-02..04 sono del tema: prima di chiudere, rimisurare con screenshot a 375, 768 e 1440 px, non solo con `curl`.

## Story figlie (2026-10-27, nessun lavoro avviato)

| Difetto o decisione | Story |
|---------------------|-------|
| LC-02, LC-03, LC-04 (classi Design Comuni al posto di Tailwind) | [DCP-430](../../../../Themes/Sixteen/docs/stories/dcp-430-lista-categorie-classi-design-comuni-al-posto-di-tailwind.story.md) |
| LC-05 (titolo), LC-06 (radici DC, D-LC-04), LC-09 (link in ingresso, D-LC-03) | [DCP-423](./dcp-423-lista-categorie-titolo-link-in-ingresso-e-tassonomia.story.md) |
| LC-07 (legame categoria e tipo, D-LC-02) | [DCP-421](./dcp-421-lista-categorie-legame-tra-categoria-e-tipo-di-segnalazione.story.md) |
| LC-08 (CategoryResource) | [DCP-422](./dcp-422-categoryresource-filament-per-le-categorie.story.md) |
| LC-10 (piano docs di Sixteen), con la prova di `folio:list` | [DCP-439](../../../../Themes/Sixteen/docs/stories/dcp-439-piano-docs-sixteen-dice-il-falso-su-chi-serve-lista-categorie.story.md); e' anche il criterio 2 di DCP-423, chi chiude una spunta l'altra |

LC-01 non ha una story: non e' un difetto aperto. Le decisioni D-LC-02, D-LC-03 e D-LC-04 restano all'utente.
