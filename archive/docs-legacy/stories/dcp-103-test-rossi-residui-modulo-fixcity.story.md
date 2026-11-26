---
id: dcp-103
title: "DCP-103: 19 test rossi residui in Modules/Fixcity/tests (pagine assenti, rate limit, factory)"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P2
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-22
qmd: "test rossi residui fixcity area-personale privacy tracking rate limit generate tickets factory pest"
related: [dcp-90, dcp-102, refreshdatabase-wipe-shared-test-db]
---

# DCP-103: i rossi che restano dopo DCP-90

## Contesto
DCP-90 ha corretto i due rossi del flusso (route cache con altro `APP_KEY` nei test Livewire: `phpunit.xml` ora ignora `APP_ROUTES_CACHE` e `APP_CONFIG_CACHE`; redirect `tickets/track/{code}` ora nella pagina del tema). Esecuzione dell'intera cartella `Modules/Fixcity/tests`, un solo processo, 398 s: **19 falliti, 1 saltato, 462 passati**. Con la route cache ignorata i test Livewire ora eseguono davvero (alcuni prima erano falsi verdi o rossi).

## Elenco con la causa (da DCP-90; non corretti)
| Gruppo | Test | Causa dichiarata |
|---|---|---|
| Pagine assenti (13) | `CitizenMyTicketsDetailLinkTest` (2), `FollowedTicketsPageTest` (2), `NotificationPreferencesTest` (3), `TicketPrivacyPublicationTest` (2), `TicketPagesTest` (4) | `area-personale/*` e `privacy` non esistono in `Modules/Fixcity/resources/views/pages` e rispondono 200/404 dalla pagina generica del tema (`/it/privacy` e `/privacy` 404: serve un JSON di pagina, da chiedere all'utente; vedi DCP-46) |
| Altro non indagato (1) | `TicketListCreateCallToActionTest` | probabilmente dello stesso tipo |
| Rate limit (4) | `TrackingTranslationTest` (3: 2 sul 429, 1 sul markup `<code>` per `ticket_id`), `FollowedTicketsPageTest` (1, 429) | il throttle del modulo e' oscurato dal tema: DCP-102 |
| Factory (1) | `GenerateTicketsActionTest > _creates_tickets_with_valid_states` | la factory crea PENDING/MEDIUM, il test accetta solo OPEN/RESOLVED o PENDING+URGENT |
| Ambiente | `Modules/Geo/tests`: manca `database/fixcity_user_test.sqlite` | DCP-93 |

## Acceptance criteria
1. Per ogni gruppo: causa provata, correzione al codice o al contratto del test con motivo scritto (mai asserzioni rimosse in silenzio).
2. Cartella intera verde o con eccezioni documentate.
3. PHPStan a zero sui file toccati.

## Riconferma (16:25, sessione tema, dopo tutte le modifiche del pomeriggio)
Sottoinsieme wizard + pagine + conferma (`CreateTicketWizardWidgetTest`, `TicketFormWizardSummarySchemaTest`, `CreateTicketWizardWidgetViewTest`, `Feature/Pages`, `Feature/Wizard`, `BuildTicketReviewSummaryActionTest`, `CreateTicketConfirmationFlowTest`): `18 failed, 1 skipped, 100 passed (522 assertions)`. I 18 rossi sono tutti nei gruppi gia' elencati sopra (area-personale, FollowedTickets, NotificationPreferences, TicketPages, TicketPrivacyPublication, TrackingTranslation, TicketListCreateCallToAction): nessun rosso nuovo dalle modifiche a shell, stepper, footer di ripiego, header attivo, asset relativi, `middleware: ["auth"]`.

## Suite intera rilanciata il 2026-10-14 (20:05, sessione tracker/MCP)
`bashscripts/tools/pest-scratch-db.sh Modules/Fixcity/tests` (DB di prova, `APP_CONFIG_CACHE` escluso), un processo, 990 s: **27 falliti, 1 todo, 13 saltati, 1131 passati (8526 asserzioni)**. L'elenco di sopra (19 rossi) e' superato: i test sono raddoppiati e la composizione dei rossi e' cambiata. Raggruppati per messaggio d'errore:

| Gruppo | N | Causa provata | Stato |
|---|---|---|---|
| `ticket_hours.user_id` intero, utenti UUID | 20 | Decisione (a) in [ticket-hours-user-boundary](../decisions/ticket-hours-user-boundary.md); migrazione `2026_10_22_000003_…` applicata; `user_id` = `varchar(36)` su `fixcity_data` | **chiuso** — vedi [dcp-210](./dcp-210-ore-di-lavoro-dei-ticket-user-id-intero-con-utenti-uuid.story.md) |
| Contratto di parita' lingue | 1 | «Cookie policy» e' il termine inglese stesso (de/es tradotte, `local` ed `eu` identici) | **Chiuso**: aggiunto alle eccezioni documentate `en` di `ContentLocaleParityTest` (accanto a «Media policy»); 5/5 verdi |
| Factory dei ticket | 1 | `GenerateTicketsActionTest` non e' piu' nel tree; `GenerateTicketsAction` dispatcha stati `open`/`urgent`/`resolved` | **chiuso** (test assente; action coerente) |
| `TicketFormRenderTest` | 2 | il test impostava `latitude`/`longitude` piatti; `TicketForm` valida `location.{latitude,longitude}` (CoordinatePicker) | **chiuso** (test allineato allo schema form) |
| `CategoryMigrationTest` | 1 | causa radice: `RefreshDatabase` Unit suite sostituiva `categories` con schema Blog; test riallineato a `Schema::connection('fixcity')` + indici reali (no `categories_name_idx`) | **chiuso** — vedi [refreshdatabase-wipe-shared-test-db](./refreshdatabase-wipe-shared-test-db.story.md) |
| `FixcityDemoSeederTest` | 1 | `TicketHourSeeder` poteva scrivere `value=0.0` quando il budget mezze-ore era esaurito a meta' loop | **chiuso** (guard `left < MIN_UNITS`) |
| `PersonalAreaSettingsPagesTest` | 1 | due stringhe diverse, non indagato | aperto |

**Decisione `ticket_hours`:** (a) applicata — vedi [ticket-hours-user-boundary](../decisions/ticket-hours-user-boundary.md) e [dcp-210](./dcp-210-ore-di-lavoro-dei-ticket-user-id-intero-con-utenti-uuid.story.md).

**Cosa dice di piu' il gruppo:** altre tabelle del DB dati hanno `user_id` intero con utenti UUID (`comments` int(11), `contacts` int(11), `posts`, `post_views`, `translations`, `upvote_downvotes`, `ai_messages`, `ai_tool_logs` bigint). Non provato se siano usate da FixCity: nessun test le ha colpite.

