---
title: "demo tickets presentation seed"
type: note
tags: [documentation]
created: 2026-09-26
updated: 2026-09-27
qmd: "demo tickets presentation seed"
issues: []
discussions: []
---

# seed ticket demo — presentazione FO

## GitHub (tracciamento)

| Repo | Issue | Discussion |
|------|-------|------------|
| base_fixcity_fila5 | [#239](https://github.com/laraxot/base_fixcity_fila5/issues/239) | [D#242](https://github.com/laraxot/base_fixcity_fila5/discussions/242) |

## scopo

Popolare il database con segnalazioni **credibili** per demo: mappa `/it`, elenco, dettaglio `/it/tickets/{id}`, file statico `/data/tickets.json`.

## perche'

`BuildPublicTicketsQueryAction` espone solo ticket con:

- `location` valorizzato (JSON + lat/lng mirror)
- stato in `TicketStatusEnum::canViewByAll()` (no `pending` / `draft`)

Il vecchio `TicketDatabaseSeeder` inseriva 4 righe senza coordinate e con `pending` → FO vuoto.

## comando

```bash
cd laravel
# demo completa (utenti STI validi → profili → categorie → ticket)
php artisan db:seed --class=Modules\\Fixcity\\Database\\Seeders\\FixcityDatabaseSeeder
# oppure solo ticket (serve almeno un utente)
php artisan db:seed --class=Modules\\Fixcity\\Database\\Seeders\\TicketDatabaseSeeder
```

Orchestrazione: `DemoUsersSeeder` (tipi STI `master_admin` / `customer_user`) → `CategorySeeder` → `ProfileSeeder` → `TicketDatabaseSeeder`.
Non usa `UserDatabaseSeeder` completo (DeviceProfile/OAuth bloccano la demo).

Idempotente: chiave `code` (`DEMO-001` …); slug `demo-*` impostato dopo `save()` (HasSlug altrimenti sovrascrive da `name`).

Credenziali demo: `cittadino@fixcity.demo` / `password` (e `marco.sottana@gmail.com` / `password`).

## output

- Tabella `tickets`: 27 record demo in 7 città italiane
- Profili Fixcity allineati agli utenti demo
- `public_html/data/tickets.json` rigenerato e validato da `GenerateTicketsJsonAction`
  nell'ultimo passaggio `GeoJsonUpdateSeeder`, dopo commenti, attività e relazioni.

## story

[STORY-135](../../../../../docs/stories/STORY-135-ticket-presentation-seeders.md) — GitHub [#239](https://github.com/laraxot/base_fixcity_fila5/issues/239) · [D#242](https://github.com/laraxot/base_fixcity_fila5/discussions/242)

## collegamenti

- [ticket-location-json-architecture.md](./ticket-location-json-architecture.md)
- [tickets-view-cms-folio-page.md](./tickets-view-cms-folio-page.md)
- [map-lit-tickets-json-ssot.md](../../../../../Themes/docs/shared-components/map-lit-tickets-json-ssot.md)
