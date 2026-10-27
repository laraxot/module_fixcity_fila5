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
status: draft
---

# Ticket subscribers vs comment notification subscribers

## Scopo

`Ticket` usa `HasComments` (modulo Comment) **e** la pivot legacy `ticket_subscribers`. Due domini distinti: non sovrascrivere `subscribers()` sul modello.

## Due relazioni, due scopi

| Metodo | Tipo | Tabella / sorgente | Uso |
|--------|------|-------------------|-----|
| `ticketSubscriberQuery()` | `Builder<TicketSubscriber>` su `user` | `fixcity_user.ticket_subscribers` | Notifiche workflow ticket (owner, assignee, watcher) |
| `subscribers(?NotificationSubscriptionType)` | `Collection<CanComment>` | `HasComments` → `comment_notification_subscriptions` | Notifiche thread commenti (All / Participating / None) |

## Regola (religione)

- **Vietato** ridefinire `subscribers()` su `Ticket` con `BelongsToMany` — rompe il contratto `SupportsCommentNotifications` e PHPStan L10.
- **Obbligatorio** usare `TicketSubscriber::query()` o `ticketSubscriberQuery()` per leggere/scrivere il pivot; non usare una relazione Eloquent cross-database.
- `NotificationService::getUsersToNotify()` merge su `$ticket->ticketSubscribers`, non su `subscribers()`.
- In `TicketSubscriber::ticket()` la foreign key canonica è `ticket_id`; usare `user_id` qui causa il lookup sul ticket errato.
- `ticket_subscribers.user_id` deve avere tipo UUID/stringa a 36 caratteri perché `User` usa chiavi stringa; la migrazione evolutiva usa `foreignIdFor(XotData::make()->getUserClass(), 'user_id')` e conserva i valori legacy per audit.

## Esempio

```php
// Pivot workflow
TicketSubscriber::query()->create(['ticket_id' => $ticket->getKey(), 'user_id' => $user->getAuthIdentifier()]);

// Comment module (trait)
$commentable->subscribers(NotificationSubscriptionType::All);
```

## Riferimenti codice

- `laravel/Modules/Fixcity/app/Models/Concerns/HasTicketRelations.php` — `ticketSubscriberQuery()`, `use HasComments`
- `laravel/Modules/Comment/app/Models/Concerns/HasComments.php` — `subscribers()`
