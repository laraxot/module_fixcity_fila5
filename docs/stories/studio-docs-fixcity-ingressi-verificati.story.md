---
title: "Studio delle docs del modulo Fixcity e correzione degli ingressi con fatti verificati nel codice"
type: story
status: done
created: 2026-10-14
updated: 2026-10-14
module: Fixcity
epic: docs-organization
related:
  - dcp-56-organizzazione-docs-moduli-e-temi.story.md
  - mailmap-e-passata-meccanica-docs-moduli-temi.story.md
  - ../../../../../bmad-output/docs-architecture/module-theme-docs-contract.md
tags: [docs, organizzazione, verifica-nel-codice, second-brain, doc-010, doc-002]
qmd: "studio docs fixcity ingressi README 00-INDEX index verificati codice Actions Ticket enum wizard tickets create phpstan config 122 errori docs-organizer"
---

# Studio delle docs di Fixcity: ingressi corretti con fatti verificati

Fase BMAD: documentazione e qualita' (DOC-010 + DOC-002 del piano `bmad-output/stories/docs-organization/`). Non tocca codice applicativo, non sposta e non cancella file.

## Richiesta
Utente, 2026-10-13 e 2026-10-14: studiare, aggiornare, migliorare e organizzare le cartelle `docs/` di moduli e temi, con BMAD e second brain. Perimetro di questa story: `laravel/Modules/Fixcity/docs`. Le story DCP-130..152 e i file di privacy, cookie e conformita' sono fuori perimetro (li segue il coordinatore).

## Analisi: cosa e' stato letto nel codice
`module.json` e `composer.json`; `app/` (64 file in `Actions/`, 15 in `Models/` tra cui `Ticket`, 6 enum, 9 widget Filament, `TicketResource` con 4 pagine, 4 classi Livewire, 2 notifiche); `routes/` (entrambi i file vuoti); `config/` (`ticket.php`, `config.php`); `database/seeders/`; `resources/views/pages/` (Folio); `lang/`; `tests/` (128 file PHP). Prove eseguite: `php artisan module:list`, `php artisan route:list --json`, PHPStan sul modulo, due file Pest su database usa-e-getta.

### Cosa e' risultato falso o mancante negli ingressi
| Documento | Affermazione | Realta' verificata |
|---|---|---|
| `index.md` | modelli `TicketStatus`, `TicketPriority`, `TicketType`; `UpdateTicketAction`; `TicketListPage`/`TicketCreatePage`/`TicketEditPage`; `DashboardWidget`; `UpdateTicketStatusAction` | sono enum (`app/Enums/Ticket*Enum.php`); `UpdateTicketAction` e `UpdateTicketStatusAction` non esistono (c'e' `ChangeStatus`); le pagine sono `ListTickets`, `CreateTicket`, `EditTicket`, `ViewTicket`; esiste `Filament/Pages/Dashboard.php`, non un `DashboardWidget` |
| `index.md` | stati: open, in_progress, resolved, closed; priorita' 4; tipi `bugReport`, `featureRequest`, `support` | 9 stati, 5 priorita' (c'e' `urgent`), tipi civici (`road_maintenance`, `public_lighting`, ...): `TicketTypeEnum::BUGREPORT` non esiste |
| `index.md` | URL `/it/tests/segnalazione-crea` | l'entrypoint e' `/it/tickets/create` (Folio `tickets.create`, JSON con `middleware: ["auth"]`) |
| `index.md` | `db:seed --class=FixcitySeeder` | la classe non esiste: `FixcityDatabaseSeeder`, `FixcityDemoSeeder` (namespace `Modules\Fixcity\Database\Seeders`) |
| `index.md` | report PDF, notifiche real-time | nessun report PDF (i riferimenti a `pdf` sono MIME di allegati); canali `mail` e `database`, `app/Broadcasting/` vuota |
| `index.md` | prova `Ticket::find(1)->notify(new TicketStatusChangedNotification())` | `Ticket` non e' `Notifiable` e il costruttore richiede `string $ticketCode, TicketStatusEnum $status` |
| `index.md` | "PHPStan Level 10", "Production Ready", "IT/EN" | PHPStan: 122 errori, tutti in `config/`; "Production Ready" non provato; `lang/` ha PHP per molte lingue |
| `README.md` | era un instradamento generico a tutti i moduli, senza identita' di Fixcity; link a `../Fixcity/docs/README.md` e a `Themes/docs/README.md` rotti | aggiunta sezione con identita', ownership, superficie HTTP, quick start e limiti; link corretti |
| `INDEX.md` | `stories/` "55 file"; `phpstan/` "Risultati PHPStan per livello (JSON)"; percorsi di navigazione | 64 file; i 10 `level_*.json` sono rinvii di 196 byte (non JSON) a un canonico inesistente; percorsi `../../../docs/...` puntavano a cartelle inesistenti |
| `wiki/MODULE-STATUS.md` | modello `Segnalazione`, "60% completato" | segnato `status: stale` con lo strumento |
| `wiki/README.md` | stub con `module: theme` e canonical inesistente | ora punta a `index.md` |
| `wiki/index.md` | "Wiki trasversale per tutti i moduli"; link a `ai-harness-xot-discipline` inesistente | nota con il contenuto reale (121 concept, 5 rules, ...); link annotato |
| `00-INDEX.md` | mancava la classificazione canonica/evidence/legacy richiesta dal contratto | aggiunta tabella |
| `wiki/log.md` | 4 link a bersagli inesistenti | annotati |

Scoperte laterali (non corrette, fuori perimetro): `FixcityServiceProvider::registerFolioApiRoutes()` calcola `dirname($this->module_dir, 3).'/resources/views/pages/api'`, cioe' `laravel/Modules/resources/views/pages/api` (cartella inesistente, verificato con `ls`): e' un no-op, gli endpoint `pages/api/*` sono montati da `Modules/Cms/app/Providers/FolioVoltServiceProvider.php`. `config/morph_map.php` ha 12 chiavi duplicate.

## Modifiche (solo sostituzioni mirate)
`docs/README.md`, `docs/00-INDEX.md`, `docs/index.md`, `docs/INDEX.md`, `docs/wiki/index.md`, `docs/wiki/README.md`, `docs/wiki/MODULE-STATUS.md`, `docs/wiki/log.md`, questa story, nota di memoria `bashscripts/ai/wiki/memories/docs-fixcity-geo-studio.md`. Nessun file spostato, rinominato o cancellato; nessun commit; git usato solo in lettura.

## Verifica (output reale, 2026-10-14)
- Link non risolti (`docs-organizer.py --busy-minutes 0 links ... --by-name`, dry-run) sui file toccati: README 2 -> 0, index 1 -> 0, wiki/index 1 -> 0, wiki/README 1 -> 0, wiki/MODULE-STATUS 1 -> 0, wiki/log 4 -> 0, 00-INDEX 0 -> 0, INDEX 0 -> 0; somma 10 -> 0. Intera cartella: 264 -> 254 (il resto e' fuori dagli ingressi).
- Controllo indipendente di ogni link relativo (script mio): README 0/41, 00-INDEX 0/42, index 0/20, INDEX 0/6 rotti.
- `phpstan analyse Modules/Fixcity Modules/Geo` (config unica, livello `max`): Geo 0 errori; Fixcity 122, tutti in `config/*.php` (105 `larastan.noEnvCallsOutsideOfConfig`, 12 `array.duplicateKey`, 4 `class.notFound`, 1 `binaryOp.invalid`); 0 in `app/`, `tests/`, `database/`.
- Pest su DB usa-e-getta (`APP_CONFIG_CACHE=/nonexistent/config.php`, `DB_DATABASE_LIMESURVEY=`): `TicketStatusEnumTest` + `TicketPriorityEnumTest` = 16 passati, 127 asserzioni. Conteggi del DB di sviluppo prima e dopo identici (18 ticket, 11 utenti). La suite completa non e' stata eseguita.
- `php artisan module:list`: Fixcity e Geo `Enabled`. `route:list`: `fixcity/admin/tickets...`, `POST livewire/form-create-ticket`, `POST {lang}/tickets/{ticket}/follow`.

## Difetti di docs-organizer misurati
1. Il dry-run di default (`--busy-minutes 10`) salta in silenzio i file toccati da meno di 10 minuti: subito dopo la seconda passata di frontmatter ha riportato 158 eventi invece di 264 (106 nascosti, 40%) e 0 per `README.md` e `index.md` che ne avevano 2 e 1. La misura prima/dopo va fatta con `--busy-minutes 0`.
2. `--fix` e `--annotate` non hanno uno scope per file: agiscono su tutta la cartella (dopo le correzioni: Fixcity 254 eventi non risolti, Geo 1762), quindi annotare solo gli ingressi richiede modifiche a mano.
3. Vede solo link `[testo](target)` e quelli in frontmatter: non vede percorsi scritti a mano (`See canonical documentation: ../..`) ne' percorsi tra apici inversi (in `INDEX.md` tre percorsi di navigazione erano sbagliati e il tool riportava 0).
4. `--by-name` non risolve bersagli rinominati (`tracking-links-capability-2026-09-27.md` contro `tracking-links-capability.md`): giusto per prudenza, ma va saputo.

## Decisioni
- Gli ingressi restano: `README.md` (identita' e quick start), `00-INDEX.md` (canonico, con classificazione), `INDEX.md` (mappa delle sottocartelle), `index.md` (panoramica). Nessun indice con nome alternativo.
- Le sezioni non verificabili (`index.md` Best Practices) non sono cancellate: marcate come desiderata.

## Aperto
- `ticket-wizard-frontoffice.md` ha 23 link a `_bmad-output/implementation-artifacts/7-*` che non esistono nel repo; vanno annotati o riscritti (non e' un ingresso, non toccato).
- `Modules/Fixcity/README.md` (radice del modulo) ha doppio frontmatter e dichiara "PHPStan livello 10" come target: fuori da `docs/`.
- 122 errori PHPStan in `config/` (duplicati di `morph_map.php`, classi inesistenti in `comments.php` e `orbit.php`): decisione se escludere o correggere.
- Il bug del `registerFolioApiRoutes()` va corretto o rimosso (codice, non docs).
- Le directory `workflows/`, `roadmap/`, `llm-wiki/` non sono state confrontate con il codice.
