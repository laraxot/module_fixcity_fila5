---
title: "Blueprint — ticket lifecycle, communications, comments and timeline"
type: blueprint
status: ready-for-implementation
created: 2026-10-16
module: Fixcity
related:
  - ./ticket-detail-passaggi-commenti.story.md
  - ./investigation/ticket-detail-passaggi-commenti.md
  - ../Comment/docs/bmad/fo-comments/filament-blueprint.md
  - ../Fixcity/docs/bmad/design-comuni-service-workflow-audit.md
---

# Objective

Deliver a citizen-safe ticket detail at `/{locale}/tickets/{id}` that combines:

1. the authenticated citizen's private pending report;
2. moderation and assignment milestones;
3. outbound mail, inbound replies and follow-up attempts;
4. additional photos/documents;
5. the Filament `CommentsWidget` discussion;
6. a Design Comuni-style timeline and dynamic “Prossimi passi”.

The ticket is closed only after it reaches `resolved`. A response such as “not our
competence”, “we will handle it soon”, or delegation is a timeline outcome, not an
automatic closure.

# Ground truth found in the repository

- `Ticket` already implements `Commentable` and uses the Comment module's
  `HasComments` trait.
- `TicketActivity` already provides an auditable, visibility-scoped event ledger and
  `TicketActivityEventTypeEnum` already includes approval, rejection, assignment,
  sent mail, received mail, follow-up, media and closure events.
- `BuildPublicTicketDetailAction` correctly prevents a guest from seeing `pending`
  tickets and allows the owner to see their own pending ticket. `/it/tickets/66`
  currently returns 404 for a guest because its database status is `pending`; this
  is the intended privacy rule, not a missing route.
- `BuildTicketTimelineAction` currently reads only public activities and returns
  `status_from/status_to`, while the Blade and tests expect `from_label/to_label`.
  It therefore cannot yet render the intended timeline reliably.
- The ticket detail does not currently mount `CommentsWidget`.
- Spatie Media Library already stores ticket attachments in `attachments`/`ticket`
  collections. A second `timeline_steps`/`timeline_attachments` store would create
  two sources of truth and is deliberately not used by this blueprint.

# Locked domain rules

## Visibility

| Actor | Pending/rejected | Approved public ticket | Internal notes/mail metadata |
|---|---|---|---|
| Guest | none | only explicitly public summary/timeline | never |
| Citizen owner | full own report and citizen-safe timeline | own report and citizen-safe timeline | never |
| PA operator/supervisor | full operational record | full operational record | yes |

Comments are mounted in the FO detail for authenticated authorized users using the
canonical Filament widget. Guest/public rendering must not expose private discussion
or email contents.

## Lifecycle

`pending → in_review → open/in_progress → resolved → closed`

`on_hold` represents waiting for information or an external authority. `reopened`
returns a resolved case to operational work. Moderation rejection is recorded as an
approval/rejection activity and a dedicated non-public terminal decision; it is not
the same as resolving the civic problem. Existing legal status transitions remain
the single source of truth, and `closed` remains reachable only from `resolved`.

## Timeline event contract

Every event has: `event_type`, `occurred_at`, actor (when known), visibility,
human-readable label, optional reason, and typed metadata. Mail events include only
necessary audit metadata (`to`, subject, provider/message id, attempt status,
failure code); raw headers and secrets never enter the public payload. Each retry or
follow-up is a new event, never an overwrite.

Supported event families:

- report received and moderation approved/rejected;
- citizen/operator edit or additional media;
- assignment/delegation to ULSS, Comune, Vigili, or another configured office;
- outbound mail sent/failed and inbound reply received;
- follow-up/sollecito sent;
- status changes, resolution, reopening and final closure;
- approved citizen/operator comments when policy permits.

# Construction plan

## Step 1 — Repair the existing timeline contract (serial)

Owned scope: `BuildTicketTimelineAction`, timeline Blade, localization, existing
Fixcity detail tests.

- Normalize event output keys to the Blade contract.
- Render every existing `TicketActivityEventTypeEnum` with event-specific labels and
  safe metadata, not only status transitions.
- Preserve public/author-only/internal filtering and never leak recipient emails or
  private reasons.
- Add regression coverage for the key contract and mixed event types.

Exit: public timeline renders correctly for existing seeded status and assignment
activities; private activity text is absent.

## Step 2 — Add the Comment module to ticket detail (depends on 1)

Owned scope: Sixteen ticket-detail Blade, Comment widget integration, ticket detail
feature tests.

- Mount `Modules\\Comment\\Filament\\Widgets\\Commentable\\CommentsWidget` with
  `commentableType`/`commentableKey`, exactly as the Blog/Event detail does.
- Always render the section for authorized authenticated users; use `readOnly` for
  viewers who may read but not write.
- Reuse `CommentPolicy` and `TicketPolicy`; do not create an HTTP Livewire component
  or a second comment engine.
- Keep pending-ticket access owner-only and ensure a non-owner cannot comment by
  forging the widget's serialized model/key.

Exit: owner can see/write comments on `/it/tickets/{id}`; guest cannot see private
threads; no duplicate content or CSS regression.

## Step 3 — Model moderation, delegation and closure semantics (serial after 1)

Owned scope: status enum/transitions, moderation/assignment Actions, policies,
activity recording, migrations only when a field is indispensable.

- Add explicit actions for approve/reject/edit/delegate and require a reason for
  rejection, reassignment and closure.
- Record each action in `TicketActivity` with actor and visibility.
- Keep “not our competence”, “we will do it soon”, and “delegated” operationally
  open/on-hold until a real resolution exists.
- Enforce the closure invariant in the domain action, not only in Filament UI.

Exit: illegal direct closure fails; every moderation/assignment decision is auditable.

## Step 4 — Communication ledger and follow-ups (serial after 3)

Owned scope: Fixcity mail Actions/notifications, activity payload contract, scheduler
or queue integration, operator Filament actions.

- Record each outbound attempt as `mail_sent` or failed equivalent with recipient,
  subject, timestamp, provider id and result.
- Record inbound authority responses as `mail_received`, preserving the raw response
  only in protected storage if required; expose a sanitized citizen summary.
- Add manual follow-up first; schedule automation only after a tenant-configured
  deadline exists. A follow-up creates a new event and never mutates history.
- Make retry/idempotency keys explicit to avoid duplicate mail.

Exit: an operator can send and resend a message, and the detail shows the attempt
history without leaking internal recipients or headers.

## Step 5 — Media and unified citizen-facing timeline (parallel with 4 after 1)

Owned scope: media attachment action/event listener, timeline mapper, Sixteen detail
view, accessibility tests.

- Keep Spatie Media Library as the attachment source of truth.
- When a new photo/document is attached, record `media_added` with media id,
  collection, safe filename and visibility.
- Merge activity events, approved comments and media events in one chronological
  presentation model; do not duplicate binary files into `timeline_attachments`.
- Show dynamic “Prossimi passi” based on status, pending reply/follow-up and SLA
  deadline, using the Design Comuni `cmp-timeline`/calendar-vertical pattern.

Exit: the citizen sees every permitted photo and milestone in chronological order;
private operational notes remain absent; keyboard/screen-reader structure is valid.

## Step 6 — Adversarial security and release gate (after 2–5)

- Test owner/non-owner/guest access for pending, rejected, public, resolved and
  closed tickets.
- Test forged Livewire keys, forged ticket ids, unsafe filenames, HTML in comments,
  and mail metadata leakage.
- Run PHP syntax, focused Pest tests, Blade compilation, `git diff --check`, and an
  HTTP/browser smoke test for `/it/tickets/{id}`.
- Do not treat HTTP 200 alone as proof; require evidence for the actor matrix and
  timeline contents.

# Risks and deferred choices

- Incoming email transport/webhook and provider-specific message threading require
  an approved provider contract; the first implementation must not invent one.
- Exact SLA durations and tenant office addresses are configuration/legal data, not
  hard-coded copies of the static Design Comuni examples.
- “Public comments” need an explicit product decision. Until then, comments are
  owner/operator data and only sanitized timeline events are public.
- Existing untracked `TimelineStep` files are not part of this plan: adopting them
  would duplicate `TicketActivity`, and their migration creates a foreign key to
  `timeline_attachments` before that table exists.
