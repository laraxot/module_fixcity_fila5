---
title: "Mappa flussi per attore — Fixcity"
type: concept
module: Fixcity
confidence: high
created: 2026-09-26
updated: 2026-09-26
qmd: "actor flow map persona journey cittadino operatore supervisore admin Fixcity workflow"
tags: [actor, flow, journey, roadmap, persona, workflow, gap-analysis]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/444"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/445"
related:
  - user-journey-map.md
  - codebase-gap-matrix.md
  - ../../bmad/workflows/actor-citizen.md
  - ../../bmad/actor-flows.md
  - ticket-workflow-state-machine.md
---

# Mappa flussi per attore — Fixcity

**Scopo**: tracciare, per ogni attore del progetto (cittadino, operatore PA,
supervisore, amministratore/sistema), i flussi che **deve** svolgere, lo stato
di implementazione e il collegamento alla story BMAD + all'Action/Folio pagina
corrispondente. Integra [codebase-gap-matrix.md](codebase-gap-matrix.md)
(che mappa story→file per componente) con una prospettiva **per attore**.

Workflow eseguibili: [bmad/workflows](../../bmad/workflows/README.md) ·
sintesi [actor-flows](../../bmad/actor-flows.md).

**Fonti**: `BasePolicy.php::PA_ROLES`, `TicketPolicy.php` (7 capability),
`TicketStatusEnum::allowedTransitions()` (matrice workflow),
`codebase-gap-matrix.md` (stories 392–475), `STORY-493…497`.

Leggenda stato: ✅ completo · ⚠️ parziale · ❌ mancante · 🐛 bug noto.

---

## 1. Cittadino (anonimo o autenticato)

| # | Flusso | Stato | Implementazione | Story |
|---|--------|-------|-----------------|-------|
| C1 | Registrarsi/autenticarsi | ⚠️ | `User/LoginWidget` (Livewire) | STORY-495 |
| C2 | Inviare segnalazione via wizard | ⚠️ | `CreateTicketWizardWidget`, Folio `/tickets/create` | STORY-494 |
| C3 | Vedere conferma con codice + link tracking | ⚠️ | Create/confirmation bag e pagina CMS sono nel codice; smoke e2e pending | STORY-508 |
| C4 | Tracciare stato segnalazione | ⚠️ | Folio `/tickets/track` cerca col codice-capability e mostra payload pubblico | STORY-508 |
| C5 | Vedere timeline storica | ⚠️ | Track renderizza gli eventi `status_change` pubblici da `BuildTicketTimelineAction`; browser/test runtime pending | STORY-508 |
| C6 | Aggiungere commento/follow-up | ⚠️ | `Modules/Comment` (modello) | STORY-467, STORY-455 |
| C7 | Seguire la segnalazione (notifiche) | ⚠️ | `TicketSubscriber::ticket()` usa `ticket_id`; UI follow e dispatch Notify assenti | STORY-467, STORY-509 |
| C8 | Valutare/rilasciare voto | ⚠️ | `SubmitCitizenTicketRatingAction` | STORY-452 |
| C9 | Notifiche via PEC/email | ⚠️ | `SendTicketPecNotificationAction` | STORY-470 |
| C10 | Segnalazione anonima (privacy) | ⚠️ | `CreateAnonymousTicketAction` mancante | STORY-448 |
| C11 | Upvote segnalazioni pubbliche | ⚠️ | `UpvoteTicketAction` | STORY-466 |
| C12 | Embed wizard in home page | ❌ | Folio embed layout, `embed.js` | STORY-439 |

**Capability Policy**: `TicketPolicy::create()` (cittadino autenticato),
`view()` (owner via `owner_id`, status pubblico o ruolo PA). I campi `created_by` e
`updated_by` non concedono visibilità privata.

---

## 2. Operatore PA (ruolo `operator`)

| # | Flusso | Stato | Implementazione | Story |
|---|--------|-------|-----------------|-------|
| O1 | Vedere lista/mappa segnalazioni | ✅ | `BuildPublicTicketsQueryAction`, `/api/tickets/geojson` | STORY-454 |
| O2 | Aprire dettaglio ticket | ✅ | `XotBaseViewRecord` (ViewTicket) | STORY-496 |
| O3 | Prendere in carico | ⚠️ | Assign su `responsible_id` con Activity; notifica assignee mancante | STORY-509 / G-04 |
| O4 | Cambiare stato + motivazione | ✅ | `ChangeStatus` + reason + `RecordTicketActivityAction` | — |
| O5 | Assegnare a collega | ✅ | `TicketPolicy::assign()` | — |
| O6 | Aggiungere nota interna | ❌ | `AddInternalTicketNoteAction` mancante | STORY-444 |
| O7 | Moderare commenti/abusi | ❌ | `ModerateCommentAction` mancante | STORY-464 |
| O8 | Bulk transition/export | ⚠️ | `BulkTransitionTicketsAction`, `ExportFilteredTicketsAction` | STORM-460 |
| O9 | Ricerca avanzata | ⚠️ | `SearchTicketsAction` mancante | STORY-454 |
| O10 | Geofencing zona servizio | ⚠️ | `ValidateTicketInServiceZoneAction` | STORY-458 |
| O11 | Calendario assegnamento | ❌ | `scheduled_at`, FullCalendar | STORY-459 |

**Capability Policy**: `viewAny/view/update/assign/changeStatus` (via `BasePolicy::isPaOperator`).

---

## 3. Supervisore (`supervisor`)

| # | Flusso | Stato | Implementazione | Story |
|---|--------|-------|-----------------|-------|
| S1 | Audit trail immutabile | ❌ | `audit_logs` append-only, `RecordAuditLogAction` | STORY-469 |
| S2 | Statistiche + predizione ML | ⚠️ | `GetTicketSlaMetricsAction`, `PredictTicketResolutionDaysAction` | STORY-473 |
| S3 | Gestione team/reparti | ⚠️ | `departments` migration, `AssignTicketToDepartmentAction` | STORY-445 |
| S4 | Calendario raccolta rifiuti | ❌ | `waste_collection_schedules` migration | STORY-442 |
| S5 | Automazioni workflow (if/then) | ❌ | `automation_rules`, `EvaluateAutomationRulesAction` | STORY-456 |
| S6 | Trust score cittadino | ⚠️ | `user_trust_scores`, `ComputeUserTrustScoreAction` | STORY-475 |

---

## 4. Amministratore / Sistema (`admin`)

| # | Flusso | Stato | Implementazione | Story |
|---|--------|-------|-----------------|-------|
| A1 | Configurare stati/transizioni | ✅ | `TicketStatusEnum::allowedTransitions()` (SSoT) | STORY-496 |
| A2 | Configurare tipi/priorità | ⚠️ | `priority_rules` migration, `AssignTicketPriorityAction` | STORY-443 |
| A3 | Regole SLA + escalation | ⚠️ | `sla_policies` migration, `CheckSlaBreachAction` | STORY-437 |
| A4 | Import CSV bulk | ⚠️ | `ImportTicketsFromCsvAction` | STORY-440 |
| A5 | Numeraione/unico registro | ⚠️ | `ticket_registry_sequences`, `AllocateTicketNumberAction` | STORY-441 |
| A6 | Webhook integrazioni | ❌ | `DispatchTicketWebhookAction`, `webhook_endpoints` | STORY-436 |
| A7 | White-label/domain branding | ❌ | `custom_domain`, `ResolveTenantFromHostAction` | STORY-468 |
| A8 | OAuth2/SSO operatori | ⚠️ | `tenant_sso_providers`, `ProvisionSsoOperatorAction` | STORY-461 |
| A9 | Email-to-ticket parsing | ❌ | `inbound_email_mailboxes`, `ParseInboundEmailAction` | STORY-463 |
| A10 | Canale whistleblowing | ❌ | `whistleblowing_reports` separato | STORY-471 |
| A11 | Layer GIS/WMS/WFS esterni | ❌ | `gis_overlay_layers`, `ProxyWmsTileAction` | STORY-462 |
| A12 | Filtri mappa combinati | ⚠️ | `SerializeMapFiltersToUrlAction` | STORY-472 |
| A13 | Auto-save bozza wizard | ⚠️ | `ticket_drafts` migration, `SaveTicketDraftAction` | STORY-451 |
| A14 | Manutenzione: cache/traduzioni/quality | ⚠️ | `verify-llm-wiki.sh` (quality gate) | STORY-502 |

---

## 5. Stato tecnico di avvio (workflow)

| Stato corrente → prossimo | Azione | Responsabile | Story |
|---------------------------|--------|--------------|-------|
| `OPEN` (creato cittadino) → `PENDING` | Operatore accetta | O1/O3 | STORY-392 |
| `PENDING` → `IN_REVIEW` | Supervisore approva testo | O3 | STORY-392 |
| `IN_REVIEW` → `IN_PROGRESS` | Operatore prende in carico | O3 | STORY-392 |
| `IN_PROGRESS` → `ON_HOLD` | Attesa info | O3 | STORY-392 |
| `ON_HOLD` → `IN_PROGRESS` | Riprendi lavoro | O3 | STORY-392 |
| `IN_PROGRESS` → `RESOLVED` | Comune risolve | O3 | STORY-392 |
| `RESOLVED` → `CLOSED` | Cittadino accetta / scade | C8/C3 | STORY-496 |
| `RESOLVED` → `REOPENED` | Cittadino non accetta | C8 | STORY-496 |
| `REOPENED` → `IN_PROGRESS` | Riassegna lavoro | O3 | STORY-392 |

> La matrice `allowedTransitions()` in `TicketStatusEnum` è la SSoT delle
> transizioni. `Actions\ChangeStatus` scrive `TicketActivity` con motivazione.
> Resta gap FO: pagina timeline pubblica e activity su `AssignTicketAction`.
> Percorsi UX: [user-journey-map.md](user-journey-map.md).

---

## Gap prioritari per UI/UX

1. **C3 + C4**: confirmation bag, codice e tracking sono nel tree; serve prova Pest/browser.
2. **C5**: timeline pubblica nel tracking è implementata; serve prova runtime e check privacy payload.
3. **O3**: activity assign presente, notify all'assegnatario mancante; follow/notifiche cittadino restano incomplete (C7).
4. **O7**: moderazione commenti assente (abusi non segnalabili).
5. **A6**: nessun webhook outbound → integrazione terze parti bloccata.

## Collegamenti

- [codebase-gap-matrix.md](codebase-gap-matrix.md) — inverso: story→file
- [ticket-workflow-state-machine.md](ticket-workflow-state-machine.md) — matrice transizioni
- `STORY-500` — audit trail attività (ticket status)
- `STORY-503` — tracking pubblico cittadino (timeline API)
- `STORY-502` — quality gate `verify-llm-wiki.sh`
