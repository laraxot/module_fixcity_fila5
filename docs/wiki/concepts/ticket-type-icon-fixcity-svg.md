---
title: "Icona tipologia ticket — una sola SVG in Fixcity"
type: concept
confidence: high
created: 2026-06-03
updated: 2026-06-03
tags: [fixcity, ticket-type, svg, map, architecture]
related:
  - ./segnalazioni-elenco-map-architecture.md
  - ../../../../Geo/docs/wiki/concepts/map-legend-status-semantics.md
  - ../../../../../docs/wiki/architecture/frontoffice-map-filters-ssot-architecture.md
  - ../../../../../docs/wiki/agents/agents-project-rules.md
---

# Icona tipologia ticket — una sola SVG in Fixcity

## Regola

Per ogni `TicketTypeEnum` esiste **una sola** rappresentazione grafica in frontoffice/mappa.

| Vietato | Consentito |
|---------|------------|
| `icon` heroicon **e** `iconUrl` diversa | Solo `iconUrl` |
| File `fixcity-public-buildings.svg` (prefisso modulo **ripetuto** nel nome file) | File `public-buildings.svg` |
| SVG in `Modules/UI` per tipi ticket | `laravel/Modules/Fixcity/resources/svg/` |

## Autoregistrazione prefisso modulo

`XotBaseServiceProvider::registerBladeIcons()` registra il set Blade con:

- **prefisso** = nome modulo in minuscolo (`fixcity`)
- **path** = `Modules/Fixcity/resources/svg/`

Quindi il riferimento runtime è sempre:

```php
app(AssetAction::class)->execute('fixcity::svg/public-buildings.svg');
```

`AssetAction` risolve `fixcity::svg/{path}` → `Modules/Fixcity/resources/svg/{path}` → URL pubblico `/assets/fixcity/svg/{path}`.

**Non** anteponere `fixcity-` al nome file: il prefisso `fixcity::` è già il namespace del modulo.

## Naming file (TicketTypeEnum)

| Enum value | File su disco |
|------------|---------------|
| `public_buildings` | `public-buildings.svg` |
| `waste_collection` | `waste-collection.svg` |
| `road_maintenance` | `road-maintenance.svg` |

Formula: `{str_replace('_', '-', $enum->value)}.svg`

Fallback se manca: `other.svg`

## GeoJSON `properties.type`

```json
{
  "value": "waste_collection",
  "label": "Raccolta Rifiuti",
  "iconUrl": "/assets/fixcity/svg/waste-collection.svg"
}
```

Niente `icon`, niente `color` sul type (colore pin = `status.color`).

## Writer / consumer

- PHP: `ResolveTicketTypeMarkerPropertiesAction`
- JS: `buildMarkerGlyphHtml(iconUrl)` — solo `<img>`, stesso URL in filtri sidebar e pin

`heroicon-o-*` in `lang/it/ticket_type_enum.php` resta per **Filament** admin.

## Collegamenti

- [agents-project-rules.md](../../../../../docs/wiki/agents/agents-project-rules.md) — Module SVG Rules
- STORY-125 — colore = stato, icona = tipo
