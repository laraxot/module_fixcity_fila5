---
title: "Contratto eventi TicketActivity — payload e visibility"
type: architecture
module: Fixcity
tags: [ticket, activity, event-sourcing, payload, timeline, audit, gdpr]
created: 2026-06-17
updated: 2026-06-17
qmd: "TicketActivity event_type payload JSON visibility timeline internal_note status_change assignment"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/420"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/421"
related:
  - ticket-workflow-state-machine.md
  - ticket-citizen-timeline.md
  - ../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.dev.md
  - ../../../../../../docs/stories/STORY-444-note-interne-operatori.dev.md
---

# Contratto eventi TicketActivity

## Scopo

`ticket_activities` è la **SSoT** per: timeline cittadino [396](../../../../../../docs/stories/STORY-396-ticket-citizen-timeline.dev.md), note interne [444](../../../../../../docs/stories/STORY-444-note-interne-operatori.dev.md), webhook [436](../../../../../../docs/stories/STORY-436-webhook-integrazioni-outbound.dev.md), audit AgID [469](../../../../../../docs/stories/STORY-469-audit-trail-immutabile-agid.dev.md).

Ogni riga = un evento immutabile (append-only; correzioni = nuovo evento `correction`).

## Schema target (post-392)

| Colonna | Tipo | Obbligatorio | Descrizione |
|---------|------|--------------|-------------|
| `id` | bigint | sì | PK |
| `ticket_id` | FK | sì | |
| `user_id` | FK nullable | no | Attore; null = sistema |
| `event_type` | string | sì | Vedi enum sotto |
| `visibility` | string | sì | `public` \| `internal` \| `author_only` |
| `payload` | json | sì | Schema per `event_type` |
| `reason` | text nullable | no | Motivo transizione (duplicato in payload per query) |
| `created_at` | timestamp | sì | `occurred_at` timeline |

**Deprecare:** `old_status_id`, `new_status_id` — backfill in `payload` poi drop in fase 2.

## Enum `TicketActivityEventType`

| Valore | Chi scrive | Visibility default |
|--------|------------|-------------------|
| `status_change` | `TransitionTicketStatusAction` | `public` (se stato in `canViewByAll`) |
| `internal_note` | `AddInternalTicketNoteAction` | `internal` |
| `assignment` | `AssignTicketAction` | `public` |
| `public_comment` | Comment module bridge | `public` |
| `media_attached` | Media listener | `public` |
| `milestone` | Verify [405], merge [415] | `public` |
| `protocol_registered` | Protocol action [433] | `public` |
| `system` | Automazioni [456] | `internal` |

## Payload JSON (versione `v1`)

### `status_change`

```json
{
  "v": 1,
  "from": "pending",
  "to": "open",
  "from_label_key": "fixcity::ticket.status.pending",
  "to_label_key": "fixcity::ticket.status.open"
}
```

### `internal_note`

```json
{
  "v": 1,
  "body": "Contattato tecnico esterno.",
  "mentions": [12, 45]
}
```

### `assignment`

```json
{
  "v": 1,
  "assignee_id": 12,
  "assignee_name": "Mario Rossi",
  "office_id": 3
}
```

### `milestone`

```json
{
  "v": 1,
  "milestone": "verified",
  "source": "citizen_verify"
}
```

## Action centrale

```php
// Modules/Fixcity/app/Actions/RecordTicketActivityAction.php
final class RecordTicketActivityAction
{
    use QueueableAction;

    public function handle(
        Ticket $ticket,
        TicketActivityEventType $type,
        array $payload,
        TicketActivityVisibility $visibility,
        ?UserContract $actor = null,
    ): TicketActivity {
        // validate payload shape per type
        // insert append-only
        // dispatch TicketActivityRecorded
    }
}
```

## Proiezione timeline

`BuildTicketTimelineAction` **solo legge** Activity con:

```php
$query->whereIn('visibility', $viewer->can('view_internal_notes')
    ? ['public', 'author_only', 'internal']
    : ['public', 'author_only']);
```

Mapping `event_type` → `TicketTimelineMilestone`: tabella in [STORY-396 dev](../../../../../../docs/stories/STORY-396-ticket-citizen-timeline.dev.md).

## GDPR / retention

- `internal` mai in export open data [418](../../../../../../docs/stories/STORY-418-open-data-segnalazioni-anonimizzate.dev.md)
- Retention [453](../../../../../../docs/stories/STORY-453-archivio-ticket-retention-gdpr.dev.md): anonymize `user_id` su ticket chiusi > N anni; payload body redacted
- Audit [469]: hash chain opzionale su `payload` per immutabilità verificabile

## Pest contract tests

| File | Caso |
|------|------|
| `RecordTicketActivityActionTest` | payload invalido → exception |
| `BuildTicketTimelineActionTest` | filtra `internal` per guest |
| `TicketActivityPayloadSchemaTest` | snapshot JSON per ogni event_type |
