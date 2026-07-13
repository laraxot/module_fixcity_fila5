---
title: "Ticket subscribers vs comment notification subscribers"
type: concept
module: Fixcity
tags: [fixcity, ticket, comment, subscribers, phpstan, hascomments]
created: 2026-06-12
updated: 2026-06-12
qmd: "ticket ticketSubscribers HasComments subscribers pivot comment notifications PHPStan"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/352"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/353"
related:
  - ./ticket-view-fo-enrichment-map-media-comments.md
  - ../../../../Comment/docs/wiki/concepts/native-comments-architecture.md
  - ../../../../../../docs/chat/st351-phpstan-ticket-subscribers.md
---

# Ticket subscribers vs comment notification subscribers

## Scopo

`Ticket` usa `HasComments` (modulo Comment) **e** la pivot legacy `ticket_subscribers`. Due domini distinti: non sovrascrivere `subscribers()` sul modello.

## Due relazioni, due scopi

| Metodo | Tipo | Tabella / sorgente | Uso |
|--------|------|-------------------|-----|
| `ticketSubscribers()` | `BelongsToMany<User>` | `ticket_subscribers` | Notifiche workflow ticket (owner, assignee, watcher) |
| `subscribers(?NotificationSubscriptionType)` | `Collection<CanComment>` | `HasComments` → `comment_notification_subscriptions` | Notifiche thread commenti (All / Participating / None) |

## Regola (religione)

- **Vietato** ridefinire `subscribers()` su `Ticket` con `BelongsToMany` — rompe il contratto `SupportsCommentNotifications` e PHPStan L10.
- **Obbligatorio** usare `ticketSubscribers()` per `attach()` / `whereHas` sulla pivot ticket.
- `NotificationService::getUsersToNotify()` merge su `$ticket->ticketSubscribers`, non su `subscribers()`.

## Esempio

```php
// Pivot workflow
$ticket->ticketSubscribers()->attach($user->id);

// Comment module (trait)
$commentable->subscribers(NotificationSubscriptionType::All);
```

## Riferimenti codice

- `laravel/Modules/Fixcity/app/Models/Ticket.php` — `ticketSubscribers()`, `use HasComments`
- `laravel/Modules/Comment/app/Models/Concerns/HasComments.php` — `subscribers()`
