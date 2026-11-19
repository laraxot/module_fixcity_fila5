---
title: "Fixcity front-office production audit"
type: bmad-evidence
tags: [front-office, production, guest, authenticated, tickets]
---

# Fixcity front-office production audit

Fixcity owns the ticket journeys consumed by the Sixteen public theme: listing, detail, creation, tracking, following, personal-area practices and notifications.

## Current evidence

- Public ticket detail previously failed when `ticket_subscribers` was absent; the user-connection migration and `TicketSubscriber` soft-delete contract are now covered by targeted Fixcity tests.
- `Ticket` now uses Laravel `SoftDeletes`, matching the existing `tickets.deleted_at` schema and the `withTrashed()` code-allocation/deletion paths. This prevents authenticated ticket creation and Filament deletion flows from throwing `BadMethodCallException`.
- The authenticated and guest route contracts are covered by the existing `Modules/Fixcity/tests/Feature/Pages` suite. Tests must run from `laravel`, with the user and domain connections configured; never use destructive refresh commands against shared data.
- A browser release check still needs an isolated authenticated storage state. Production checks must verify that a guest cannot read owner-only ticket data and that a signed-in owner can reach their own tracking/follow controls.
- A fresh isolated local authenticated fixture reached the ticket wizard (including map
  and summary) and the personal-area pages; strict axe checks reported zero violations
  at 375, 768 and 1440 px. This is local evidence only and does not replace production
  authorization/schema verification.

## Release checklist

1. Run the ticket page feature tests and the targeted subscriber migration tests.
2. Sweep `/tickets`, `/tickets/{id}`, `/tickets/create` and `/tickets/track` in `it`, `en`, `de`, `es` as guest and authenticated user.
3. Verify loading, validation, empty, unauthorized and not-found states at 375, 768 and 1440 px.
4. Recheck the live `user` connection schema before deployment; `users`, `socialite_users` and `ticket_subscribers` do not belong to the tenant `mariadb` connection.

## Esiti crawl guest (2026-11-13, `http://127.0.0.1:8899`)

| URL | Esito | Note |
|---|---|---|
| `/it/tickets` | 200 | elenco pubblico |
| `/it/tickets/{id}` pubblico | 200 | es. 573 (open) |
| `/it/tickets/{id}` pending/draft | 404 | 583, 587, 999999 — correlretto |
| `/it/tickets/track` | 200 | form + `?code=` |
| `/it/tickets/track/{code}` | 301 → `?code=` | canonical |
| `/it/tickets/track?code=<reale>` | 200 | capability token: mostra stato anche se pending — voluto (DCP-149) |
| `/it/tickets/create` | 302 → login | `auth` + `EnsureVerifiedIdentity` — voluto |
| `/it/segnalazioni` | 301 → `/it/tickets` | canonical |
| `/api/tickets/list` | 200 JSON | solo stati pubblici |
| `/api/tickets/geojson` | 200 JSON | solo stati pubblici |
| `/api/ticket-details/{id}` | 200/404 | fixato: era 500 per non pubblici |
| `/it/area-personale` (guest) | 302 → login | corretto |
| `/it/area-personale` (auth) | 301 → pagina figlia canonica | coperto da test |
| `/it/dashboard`, `/it/profile/edit`, `/it/notifications` | 404 | pagine Folio legacy del modulo User, oscurate dalle route CMS `[container0]`; nessun link FO le punta — vedi «Fuori perimetro» |
| `/it/prova`, `/it/tests/*` | 404 | `BlockShowcasePages` — voluto |
| `/it/pagina-che-non-esiste` | 404 | pagina errore temizzata «Pagina non trovata» |

## Fix applicati in questo giro

1. **Alias Seo tenant** (`Modules/Seo`): i default `path_aliases` producevano 301 verso
   pagine inesistenti (`contatti`→`contacts`, `cookie`→`cookies`, `about`→`chi-siamo`)
   e una catena `faqs`→`faq`→`domande-frequenti`. Creato `config/local/fixcity/seo.php`
   (coperto anche da `config/eu/fixcity` che è symlink): `categories`→`lista-categorie`,
   `cookie`/`cookies`→`cookie-policy`, `faq`/`faqs`→`domande-frequenti`, `home`→home.
   `contatti`/`about` restano 404 diretto: non esiste una pagina canonica.
   Story: `Modules/Seo/docs/stories/fo-audit-seo-path-aliases-fixcity.story.md`.
2. **`/api/ticket-details/{id}` 500**: `Ticket::withTrashed()` non esiste (Ticket non usa
   SoftDeletes). Ripristinato `Ticket::query()->whereKey()` che trova comunque le righe
   cancellate. Story: `fo-audit-ticket-details-api-500-withtrashed.story.md`.
3. **Test**: `Modules/User/tests/Feature/FoAuthPagesVerificationTest` ora copre anche il
   redirect canonico di `/it/area-personale` per utenti autenticati (9 test, 10 assertion).

## Fuori perimetro / decisioni aperte

- Pagina «contatti» e «chi-siamo» assenti dal CMS: il footer mostra i recapiti inline.
  Se servono URL pubblici dedicati è lavoro di contenuto, non di codice.
- `/it/dashboard`, `/it/profile/edit`, `/it/notifications`: pagine Folio scaffold del
  modulo User oscurate dal catch-all CMS. Nessun link FO le usa; valutare se mantenerle
  per altri tenant o marcarle deprecated.
- Codici demo `DMO-XXXX` sequenziali esistono solo nei seed: i codici reali sono
  `PREFIX-`+16 char casuali (`AllocateTicketCodeAction`), non indovinabili.
