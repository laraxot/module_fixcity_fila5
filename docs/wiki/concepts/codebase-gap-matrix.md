---
title: "Matrice gap codebase Fixcity — story vs file esistenti"
type: concept
module: Fixcity
tags: [gap-matrix, codebase, stories, actions, migrations, competitor]
created: 2026-06-17
updated: 2026-06-17
qmd: "matrice gap story Fixcity file esistenti Actions migrations mancanti competitor"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/438"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/439"
related:
  - ../../../../../../docs/stories/STORY-397-competitor-gaps-catalog.dev.md
  - ../../../../../../docs/wiki/concepts/fixcity-competitor-profiles.md
  - ticket-citizen-timeline.md
---

# Matrice gap codebase — story → file vs mancanti

Legenda: ✅ esiste · ⚠️ parziale · ❌ mancante

**Religione:** Actions `QueueableAction` + `handle()` — no nuovi `*Service.php`.

---

## Fondazione (blocca downstream)

| Story | Esiste | Mancante | Architettura |
|-------|--------|----------|--------------|
| [392](../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.dev.md) | `WorkflowService` ⚠️, `ChangeStatus` ⚠️ | Actions canon, migration Activity | [ticket-workflow-state-machine.md](ticket-workflow-state-machine.md), [ticket-activity-event-contract.md](ticket-activity-event-contract.md) |
| [396](../../../../../../docs/stories/STORY-396-ticket-citizen-timeline.dev.md) | `TicketActivity` model ✅ | `BuildTicketTimelineAction`, DTO, Folio API | [ticket-citizen-timeline.md](ticket-citizen-timeline.md) |

### Bug noti codebase (fix prima di 415/467)

| File | Bug |
|------|-----|
| `app/Models/TicketSubscriber.php:68` | `ticket()` FK `user_id` invece di `ticket_id` |
| `app/Services/WorkflowService.php` | Stati non in `TicketStatusEnum` |
| `app/Actions/ChangeStatus.php` | Nessuna Activity persistita |

---

## M8 Enterprise (436–455)

| Story | Esiste | Mancante |
|-------|--------|----------|
| [436](../../../../../../docs/stories/STORY-436-webhook-integrazioni-outbound.dev.md) | — | `webhook_endpoints` migration, `DispatchTicketWebhookAction` |
| [437](../../../../../../docs/stories/STORY-437-sla-formale-escalation.dev.md) | `Actions/GetTicketSlaMetricsAction.php` ✅ | `sla_policies` migration, `CheckSlaBreachAction` |
| [438](../../../../../../docs/stories/STORY-438-heatmap-densita-segnalazioni.dev.md) | `Actions/LoadPublicTicketsGeoJsonAction.php` ✅ | `BuildHeatmapGridAction`, Folio heatmap endpoint |
| [439](../../../../../../docs/stories/STORY-439-widget-embed-segnalazione-iframe.dev.md) | Wizard widget ✅ | Folio embed layout, `embed.js` |
| [440](../../../../../../docs/stories/STORY-440-import-bulk-csv-ticket.dev.md) | `Filament/Exports/TicketExporter.php` ✅, `Support/SpreadsheetCellSanitizer.php` ✅ | `ImportTicketsFromCsvAction`, `external_id` column, `ticket_import_batches` |
| [441](../../../../../../docs/stories/STORY-441-registro-unico-numerazione-segnalazione.dev.md) | `Models/Ticket.php` ✅ | `ticket_registry_sequences` migration, `AllocateTicketNumberAction` |
| [442](../../../../../../docs/stories/STORY-442-raccolta-rifiuti-calendario.dev.md) | — | `waste_collection_schedules` migration, `LoadWasteCalendarAction` |
| [443](../../../../../../docs/stories/STORY-443-priorita-ticket-regole-automatiche.dev.md) | `Enums/TicketPriorityEnum.php` ✅ | `priority_rules` migration, `AssignTicketPriorityAction` |
| [444](../../../../../../docs/stories/STORY-444-note-interne-operatori.dev.md) | `Models/TicketActivity.php` ⚠️ | `AddInternalTicketNoteAction`, schema 392 `event_type=internal_note` |
| [445](../../../../../../docs/stories/STORY-445-gestione-team-reparti-bo.dev.md) | `User` roles Spatie ✅ | `departments` migration, `Department` model, `AssignTicketToDepartmentAction` |
| [446](../../../../../../docs/stories/STORY-446-tracciamento-costi-intervento.dev.md) | `Models/TicketHour.php` ✅ | `estimated_cost`/`actual_cost` columns, `UpdateTicketCostAction` |
| [447](../../../../../../docs/stories/STORY-447-digest-email-personalizzato.dev.md) | `Modules/Notify` ✅ | `user_digest_preferences` migration, `SendGeoDigestMailAction` |
| [448](../../../../../../docs/stories/STORY-448-segnalazione-anonima-privacy.dev.md) | `Actions/CreateTicketAction.php` ✅ | `is_anonymous` column, `CreateAnonymousTicketAction` |
| [449](../../../../../../docs/stories/STORY-449-categorie-personalizzabili-tenant.dev.md) | `Models/Category.php` ✅ | tenant scope CRUD, `CategoryResource`, `SeedDesignComuniCategoriesAction` |
| [450](../../../../../../docs/stories/STORY-450-upload-video-segnalazione.dev.md) | `Modules/Media` ✅ | `AttachTicketVideoAction`, `TranscodeTicketVideoAction` |
| [451](../../../../../../docs/stories/STORY-451-bozza-wizard-autosave.dev.md) | `CreateTicketWizardWidget` ✅ | `ticket_drafts` migration, `SaveTicketDraftAction` |
| [452](../../../../../../docs/stories/STORY-452-survey-soddisfazione-post-chiusura.dev.md) | `SubmitCitizenTicketRatingAction.php` ⚠️ | `ticket_satisfaction_surveys` migration, `SendSatisfactionSurveyAction` |
| [453](../../../../../../docs/stories/STORY-453-archivio-ticket-retention-gdpr.dev.md) | soft deletes Ticket ✅ | `archived_at` column, `ArchiveTicketAction`, `PurgeExpiredTicketsAction` |
| [454](../../../../../../docs/stories/STORY-454-ricerca-avanzata-fo-bo.dev.md) | `BuildPublicTicketsQueryAction.php` ✅ | `search_vector` tsvector, `SearchTicketsAction` |
| [455](../../../../../../docs/stories/STORY-455-menzioni-commenti.dev.md) | `Modules/Comment` ✅ | `comment_mentions` migration, `ParseCommentMentionsAction` |

---

## M9 Automazione (456–475)

| Story | Esiste | Mancante |
|-------|--------|----------|
| [456](../../../../../../docs/stories/STORY-456-automazioni-workflow-if-then.dev.md) | — | `automation_rules` migration, `EvaluateAutomationRulesAction` |
| [457](../../../../../../docs/stories/STORY-457-canale-segnalazioni-interne-staff.dev.md) | Auth User ✅ | Folio `/staff/segnala`, `source_channel=internal_staff` |
| [458](../../../../../../docs/stories/STORY-458-geofencing-zone-servizio.dev.md) | `NormalizeTicketLocationDataAction.php` ✅, `Modules/Geo` ✅ | `service_zones` migration, `ValidateTicketInServiceZoneAction` |
| [459](../../../../../../docs/stories/STORY-459-calendario-assegnazione-lavori-bo.dev.md) | `TicketResource` ✅ | `scheduled_at` column, FullCalendar widget, `RescheduleTicketAction` |
| [460](../../../../../../docs/stories/STORY-460-bulk-actions-export-massivo-bo.dev.md) | `TicketExporter.php` ✅ | `BulkTransitionTicketsAction`, `ExportFilteredTicketsAction` async |
| [461](../../../../../../docs/stories/STORY-461-oauth2-sso-operatori.dev.md) | `User/LoginWidget` ✅ | `tenant_sso_providers` migration, `ProvisionSsoOperatorAction` |
| [462](../../../../../../docs/stories/STORY-462-layer-gis-wms-wfs-esterni.dev.md) | map-lit FO ✅ | `gis_overlay_layers` migration, `ProxyWmsTileAction` |
| [463](../../../../../../docs/stories/STORY-463-email-to-ticket-parsing.dev.md) | `CreateTicketAction.php` ✅ | `inbound_email_mailboxes`, `ParseInboundEmailAction` |
| [464](../../../../../../docs/stories/STORY-464-moderazione-commenti-abusi.dev.md) | `CommentsRelationManager` ✅ | `moderation_status`, `abuse_reports`, `ModerateCommentAction` |
| [465](../../../../../../docs/stories/STORY-465-anti-spam-captcha-rate-limit.dev.md) | Laravel RateLimiter ✅ | `tenant_spam_settings`, `ValidateWizardSubmissionAction` |
| [466](../../../../../../docs/stories/STORY-466-upvote-segnalazione-pubblica.dev.md) | `Modules/Rating` ✅ | `ticket_upvotes` migration, `UpvoteTicketAction` |
| [467](../../../../../../docs/stories/STORY-467-follow-singola-segnalazione.dev.md) | `Models/TicketSubscriber.php` ⚠️ | Fix FK bug, `SubscribeToTicketAction`, FO CTA |
| [468](../../../../../../docs/stories/STORY-468-white-label-dominio-branding.dev.md) | `Modules/Tenant` ✅ | `custom_domain` columns, `ResolveTenantFromHostAction` |
| [469](../../../../../../docs/stories/STORY-469-audit-trail-immutabile-agid.dev.md) | `Modules/Activity` pattern ✅ | `audit_logs` append-only, `RecordAuditLogAction` |
| [470](../../../../../../docs/stories/STORY-470-notifiche-pec-formali.dev.md) | `Modules/Notify` ✅ | `pec_notifications` migration, `SendTicketPecNotificationAction` |
| [471](../../../../../../docs/stories/STORY-471-canale-whistleblowing-etico.dev.md) | — | `whistleblowing_reports` separato, `SubmitWhistleblowingReportAction` |
| [472](../../../../../../docs/stories/STORY-472-filtri-mappa-fo-combinati.dev.md) | `BuildSegnalazioniFilterAggregateAction.php` ✅ | `SerializeMapFiltersToUrlAction`, URL shareable |
| [473](../../../../../../docs/stories/STORY-473-predizione-ml-tempi-risoluzione.dev.md) | `GetTicketSlaMetricsAction.php` ✅ | `PredictTicketResolutionDaysAction`, stats cache |
| [474](../../../../../../docs/stories/STORY-474-ottimizzazione-route-operatori.dev.md) | Geo coords ✅ | `OptimizeFieldRouteAction`, GPX export |
| [475](../../../../../../docs/stories/STORY-475-trust-score-cittadino.dev.md) | rating actions ⚠️ | `user_trust_scores` migration, `ComputeUserTrustScoreAction` |

---

## File legacy da migrare (anti-pattern)

| File | Violazione | Target story |
|------|------------|--------------|
| `app/Services/WorkflowService.php` | no-services | [392](../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.dev.md) |
| `app/Services/TicketService.php` | no-services | [392](../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.dev.md) |
| `app/Services/NotificationService.php` | no-services | [402](../../../../../../docs/stories/STORY-402-notifiche-intelligenti.md), [447](../../../../../../docs/stories/STORY-447-digest-email-personalizzato.dev.md) |
| `app/Actions/ChangeStatus.php` | no Activity write | [392](../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.dev.md) |

---

## Ordine implementazione consigliato

```text
392 (workflow+activity schema)
  → 396 (timeline)
  → 414, 415, 417, 420 (intake core)
  → 443, 444, 445 (BO ops)
  → 436-455 M8 enterprise
  → 456-475 M9 automazione
```

---

## Collegamenti

- [Profili competitor](../../../../../../docs/wiki/concepts/fixcity-competitor-profiles.md)
- [Catalogo gap dev](../../../../../../docs/stories/STORY-397-competitor-gaps-catalog.dev.md)
