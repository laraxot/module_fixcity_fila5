---
title: "Citizen receipt, own photos, rating prompt and real phone"
type: story
tags: [fixcity, notifications, tracking, rating, design-comuni]
created: 2026-10-26
updated: 2026-10-26
qmd: "citizen receipt photos rating real phone"
issues: []
discussions: []
status: review
---

# Citizen receipt, own photos, rating prompt and real phone

- BMAD phase: Quick Flow (dev story)
- Owner: Modules/Fixcity (Sixteen ticket-track and ticket-list blocks)

## Problems (audit findings, verified on disk)

1. The confirmation page says "Abbiamo inviato il riepilogo all'email", but no email was sent.
   `TicketCreatedListener` is not even registered (`php artisan event:list` has no `TicketCreatedEvent`:
   the Fixcity `EventServiceProvider` has an empty `$listen` and Laravel turns discovery off for subclasses).
   Even if it ran, it only changes the status, and `NotifyTicketSubscribersOfStatusChangeAction` drops PENDING.
2. `/tickets/track` received `images` from `BuildTicketPublicDetailsPayloadAction` but never showed them.
3. `TicketCitizenRatingPromptWidget` was mounted only in a test block and in `Ticket\ViewWidget`, which no real page mounts.
4. The ticket list contacts showed a fake `tel:050505` and the label "05 0505".

## Changes

- `SendTicketReceiptAction` (new, QueueableAction): `CreateTicketAction` sends the owner the existing
  `TicketStatusChangedNotification` for the initial status (database always; mail only with a verified email and
  the email preference on). `mailedTo()` gives `BuildTicketConfirmationDataAction` the address only when the receipt
  is really mailed, so the page no longer promises an email it did not send. The old listener is left alone
  (registering it would run `ChangeStatus` on creation, a separate decision).
- `ticket-track.blade.php`: "Attached photos" list with linked thumbnails, alt text
  `fixcity::ticket.track.photo_alt` (number and title), lazy loading, a hidden "opens in a new window" hint.
  The rating widget is mounted when `Ticket::needsCitizenRatingPrompt()` is true (authenticated owner, resolved or
  closed, not rated yet).
- `ResolveFooterContactPhoneAction` (new): reads `contact.phone` from the footer section blocks, like the footer.
  `ticket-list/design-comuni.blade.php` shows the phone link only when a phone exists; labels use `:phone`.
- `tests/TestCase.php`: `Mail::fake()` for every Fixcity test.

## Acceptance criteria

- [x] Creating a ticket sends one receipt to the owner, with the mail channel when the email is verified.
- [x] The confirmation email line is empty when the receipt is not mailed.
- [x] The track page shows the photos as linked thumbnails with alt text; no section without photos.
- [x] The owner of a resolved ticket sees the rating prompt; a guest or an open ticket does not.
- [x] The ticket list has no `tel:050505` and no "05 0505"; no phone link when the footer has no phone.

## Verification

- Tests: `Modules/Fixcity/tests/Feature/Actions/TicketReceiptNotificationTest.php`,
  `Modules/Fixcity/tests/Feature/Pages/TicketTrackOwnerExperienceTest.php`,
  `Modules/Fixcity/tests/Feature/Pages/TicketListContactPhoneTest.php`.
- Known environment gap: the scratch runner clones the dev schema, where `ticket_activities` has no `event_type` or
  `visibility` (migration `2026_09_26_151800_create_ticket_activities_table` is pending). Every track-page test fails
  for that reason until the dev schema is migrated.
