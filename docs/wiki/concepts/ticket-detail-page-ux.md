---
title: "Ticket Detail Page UX Design - Static Map + Comments"
type: concept
tags: [ux-design, ticket, map, comments, spatie-comments, folio]
created: 2026-06-05
updated: 2026-06-05
issues:
  - "https://github.com/laraxot/module_fixcity_fila5/issues/286"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/287"
related:
  - ../../../../Themes/Sixteen/docs/wiki/concepts/fo-folio-links-multilingua.md
  - ../../../../../../Modules/Comment/docs/wiki/concepts/spatie-comments-integration.md
---

# Ticket Detail Page UX Design

## Scope

**Platform:** Web (responsive, mobile-first)  
**Accessibility:** WCAG 2.1 Level AA  
**Page:** `/it/tickets/{id}` (Folio route)

## Screen Structure (No Tabs - Single View)

```
┌─────────────────────────────────────┐
│ Header (from header-comune)          │
├─────────────────────────────────────┤
│ Breadcrumb: Home > Tickets > #14   │
├─────────────────────────────────────┤
│ Main Content                        │
│                                     │
│ ┌─────────────────────────────────┐ │
│ │ LEFT COLUMN (2/3 width)         │ │
│ │                                 │ │
│ │ ┌─────────────────────────────┐ │ │
│ │ │ TICKET INFO CARD            │ │ │
│ │ │ Title: [Ticket Title]         │ │ │
│ │ │ Status: [badge]               │ │ │
│ │ │ Created: [date]               │ │ │
│ │ │ Author: [name/email]          │ │ │
│ │ └─────────────────────────────┘ │ │
│ │                                 │ │
│ │ ┌─────────────────────────────┐ │ │
│ │ │ DESCRIPTION SECTION         │ │ │
│ │ │ [Content]                     │ │ │
│ │ └─────────────────────────────┘ │ │
│ │                                 │ │
│ └─────────────────────────────────┘ │
│                                     │
│ ┌─────────────────────────────────┐ │
│ │ RIGHT COLUMN (1/3 width)        │ │
│ │                                 │ │
│ │ ┌─────────────────────────────┐ │ │
│ │ │ STATIC MAP WITH SINGLE        │ │ │
│ │ │ marker (lat/lng from ticket)  │ │ │
│ │ │ No multiple markers           │ │ │
│ │ │ No controls - static image    │ │ │
│ │ └─────────────────────────────┘ │ │
│ └─────────────────────────────────┘ │
│                                     │
│ ┌─────────────────────────────────┐ │
│ │ FULL WIDTH BELOW COLUMNS        │ │
│ │                                 │ │
│ │ COMMENTS SECTION                │ │
│ │ - List existing comments         │ │
│ │ - Spatie Comments Livewire form  │ │
│ │ - Login required to comment      │ │
│ └─────────────────────────────┘ │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
Footer
```

## Component Specifications

### Static Map Component
```blade
{{-- resources/views/components/ticket-static-map.blade.php --}}
@props([
    'latitude' => null,
    'longitude' => null,
    'ticketId' => null
])

@if($latitude && $longitude)
<div class="ticket-static-map ratio ratio-16x9 mb-3">
    <img 
        src="https://staticmap.openstreetmap.de/staticmap.php?
             center={{ $latitude }},{{ $longitude }}&zoom=15&size=600x300&markers={{ $latitude }},{{ $longitude }},lightblue-pushpin"
        alt="Ticket location map"
        class="object-fit-cover rounded"
        loading="lazy"
    >
</div>
@endif
```

### Comments Section (Spatie Comments Livewire)
```blade
{{-- Using spatie/laravel-comments-livewire --}}
@livewire('comments::list-comments', [
    'model' => $ticket,
    'subject' => 'Ticket #' . $ticket->id
])

@auth
    @livewire('comments::create-comment', [
        'model' => $ticket
    ])
@else
    <div class="alert alert-info">
        <a href="{{ route('login') }}">Login</a> to leave a comment.
    </div>
@endauth
```

## Data Model Context

Ticket model provides:
- `latitude`, `longitude` (string|null) - for map marker
- `location` (array|null) - GeoJSON alternative
- `title`, `content` - for info card
- `status` - relation for badge
- `owner`, `responsible` - for author info

## Routing Contract

- **FO route:** `/it/tickets/{id}` (Folio pattern, no tabs)
- **Comments form:** Livewire component, posts to spatie comments controller
- **Static map:** Server-side rendered image (no JS required)

## Accessibility

- Map alt text: "Ticket location map"
- Semantic HTML: `<article>` for ticket, `<section>` for comments
- Focus management: Comment form focus after successful post
- Color contrast: WCAG AA verified for status badges

## Developer Handoff Notes

1. **Files to modify (no new Folio page):**
   - **Non** `mkdir pages/tickets` — `/it/tickets/14` = `[container0]/[slug0]` con `pageSlug=tickets.view`
   - `TicketInfolist.php` — schema FO verticale senza tab
   - `ViewWidget.php` — usa `getPublicFrontofficeSchema()`
   - `ticket-location-map.blade.php` — `detail-mode`, un marker
   - `ticket-comments.blade.php` — Spatie comments
   
2. **Dependencies:**
   - `spatiex/laravel-comments` (via Comment module)
   - `spatiex/laravel-comments-livewire` 

3. **CSS:**
   - Use Bootstrap Italia classes
   - Ensure mobile-responsive grid (`col-md-*` classes)

---

*UX Design created by BMAD Method - Phase 2 Planning*