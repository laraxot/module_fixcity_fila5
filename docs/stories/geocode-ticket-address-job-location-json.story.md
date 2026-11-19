---
title: "GeocodeTicketAddressJob → location JSON"
type: story
module: Fixcity
status: in_progress
created: 2026-10-15
updated: 2026-10-15
tags: [phpstan, geocode, nominatim, location, job, swarm]
qmd: "geocode ticket address job location json nominatim cache retry no address column"
related:
  - ../wiki/concepts/ticket-location-json-architecture.md
  - ./fix-phpstan-random-swarm.story.md
issues: []
discussions: []
---

# GeocodeTicketAddressJob → location JSON

## Scopo

Il job fa reverse-geocode Nominatim dalle coordinate del ticket e scrive l'indirizzo leggibile. **Non esiste colonna `address`**: la SSoT e' `location.address` / `location.display_name` ([ticket-location-json-architecture](../wiki/concepts/ticket-location-json-architecture.md)). Serve cache per coordinate uguali, warning se mancano lat/lng, retry su HTTP 500.

## Causa radice

Il test esisteva senza la classe job (`class.notFound`). Il test assumeva `$ticket->address` (colonna inesistente) e `Log::fake()` (API inesistente). Factory via `Ticket::factory()` → `mixed` (HasXotFactory dinamico).

## Fix eseguiti (2026-10-15)

1. Creato `app/Jobs/GeocodeTicketAddressJob.php` (Http + Cache, scrive `location`).
2. Test riscritto sul contratto reale: `TicketFactory::new()`, assert su `location['address']`, `Log::shouldReceive`.
3. Verifica: `phpstan analyse` sui path toccati → `file_errors=0`.

## Verifica

```bash
cd laravel && ./vendor/bin/phpstan analyse \
  Modules/Fixcity/app/Jobs/GeocodeTicketAddressJob.php \
  Modules/Fixcity/tests/Unit/Jobs/GeocodeTicketAddressJobTest.php \
  --no-progress
```

## GitHub (tracciamento)

DA CREARE:

```bash
gh issue create --repo laraxot/module_fixcity --title "GeocodeTicketAddressJob location JSON" --body "Vedi laravel/Modules/Fixcity/docs/stories/geocode-ticket-address-job-location-json.story.md"
```
