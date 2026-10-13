---
id: dcp-103
title: "DCP-103: 19 test rossi residui in Modules/Fixcity/tests (pagine assenti, rate limit, factory)"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P2
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "test rossi residui fixcity area-personale privacy tracking rate limit generate tickets factory pest"
related: [dcp-90, dcp-102]
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

