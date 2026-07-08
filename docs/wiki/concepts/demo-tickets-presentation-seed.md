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
php artisan db:seed --class=Modules\\Fixcity\\Database\\Seeders\\TicketDatabaseSeeder
```

Idempotente: chiave `code` (`DEMO-001` …); slug `demo-*` impostato dopo `save()` (HasSlug altrimenti sovrascrive da `name`).

## output

- Tabella `tickets`: 15 record demo (area Mogliano Veneto)
- `public_html/data/tickets.json` rigenerato da `GenerateTicketsJsonAction`

## story

[STORY-135](../../../../../docs/stories/STORY-135-ticket-presentation-seeders.md) — GitHub [#239](https://github.com/laraxot/base_fixcity_fila5/issues/239) · [D#242](https://github.com/laraxot/base_fixcity_fila5/discussions/242)

## collegamenti

- [ticket-location-json-architecture.md](./ticket-location-json-architecture.md)
- [tickets-view-cms-folio-page.md](./tickets-view-cms-folio-page.md)
- [map-lit-tickets-json-ssot.md](../../../../../Themes/docs/shared-components/map-lit-tickets-json-ssot.md)
