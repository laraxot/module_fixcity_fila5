---
title: "Ticket FO page design — mappa statica + comments"
type: concept
tags: [ticket, folio, leaflet, spatie-comments, frontoffice]
created: 2026-06-05
updated: 2026-06-05
qmd: "ticket frontoffice folio mappa statica spatie-comments"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/284"
---

# Ticket FO page design

## Obiettivo

Pagina `/it/tickets/{id}` con:
1. **Nessuna tab** (rimuovere tabs esistenti)
2. **Mappa statica Leaflet** con solo il marker del ticket
3. **Spatie Comments** sotto la mappa

## Stato attuale

- Pagina admin: `Modules/Fixcity/resources/views/tickets/show.blade.php`
- Modello: `Modules/Fixcity/Models/Ticket.php` con `location` (Point)
- Comments: relazione `comments` esistente

## File owner (vietato `pages/tickets/`)

**Non creare** `Themes/Sixteen/resources/views/pages/tickets/` — directory semantica vietata ([page-directory-structure.md](../../../../Themes/Sixteen/docs/page-directory-structure.md)).

| Layer | File SSoT |
|-------|-----------|
| Folio shell | `Themes/Sixteen/.../pages/[container0]/[slug0]/index.blade.php` (già esiste) |
| CMS | `config/.../pages/tickets.view.json` |
| Widget FO | `Modules/Fixcity/.../Widgets/Ticket/ViewWidget.php` |
| Schema UI | `TicketInfolist::getPublicFrontofficeSchema()` |
| Mappa | `fixcity::filament.infolist.ticket-location-map` |
| Commenti | `fixcity::filament.infolist.ticket-comments` + Spatie `HasComments` |

## Spatie Comments integration

```php
// In [Ticket].blade.php
@comments(['model' => $ticket])
```

Modulo esistente: `Modules/Comment` → `Modules/Xot/Data/Models/Comment.php`

## Mappa statica

Leaflet JS già disponibile in tema. Iniettare JSON singolo ticket:

```php
@php
$mapData = [
    'type' => 'Feature',
    'geometry' => $ticket->location,
    'properties' => ['id' => $ticket->id]
];
@endphp
<div id="ticket-map" data-ticket="{{ json_encode($mapData) }}"></div>
```