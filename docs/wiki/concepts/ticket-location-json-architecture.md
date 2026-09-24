# Ticket Location JSON Architecture

## Rule

The `location` field on `Ticket` is the canonical source of truth for all geolocation data.
`latitude` and `longitude` are mirror columns for backward compatibility only.
There is **no** `address` column — address is stored as a key inside the `location` JSON.

## Attribute set() contract

The `Ticket::location()` Attribute `set` closure:
1. Accepts an array from the form (CoordinatePicker output)
2. Normalizes `lat`/`lng` from any of `lat`, `lng`, `latitude`, `longitude`
3. Parses Nominatim `addressdetails` via `extractAddressComponents()` into structured keys
4. Returns `['location' => json_encode($payload), 'latitude' => ..., 'longitude' => ...]`

**Never** returns `['address' => ...]` — that caused `SQLSTATE no column named address`.

## Location JSON structure

```json
{
  "lat": "45.562246",
  "lng": "12.249756",
  "latitude": "45.562246",
  "longitude": "12.249756",
  "address": "Via Rodolfo Morandi 5, Mogliano Veneto",
  "display_name": "Via Rodolfo Morandi 5, 31021 Mogliano Veneto TV, Italia",
  "provider": "nominatim",
  "place_id": 12345678,
  "osm_type": "node",
  "osm_id": 9876543210,
  "licence": "Data © OpenStreetMap contributors, ODbL 1.0.",
  "importance": 0.42,
  "type": "house",
  "class": "place",
  "boundingbox": ["45.5621", "45.5623", "12.2496", "12.2499"],
  "street": "Via Rodolfo Morandi",
  "street_number": "5",
  "zip": "31021",
  "postcode": "31021",
  "city": "Mogliano Veneto",
  "suburb": null,
  "province": "Treviso",
  "state": "Veneto",
  "country": "Italia",
  "country_code": "it",
  "address_details": { "...": "..." },
  "structured": { "...": "..." },
  "raw": { "...": "intero JSON Nominatim originale (search o reverse)" }
}
```

## Full-payload capture rule (2026-05-13)

Il `CoordinatePicker` cattura **l'intero** payload del provider (search +
reverse-geocode). La UI mostra solo `lat`, `lng` e `address`, ma il JSON
salvato deve contenere `raw`, `address_details`, `place_id`, `boundingbox`
per analisi futura.

Vedi [`Geo/docs/wiki/concepts/full-geocoding-payload.md`](../../../../Geo/docs/wiki/concepts/full-geocoding-payload.md)
per il contratto del payload e i punti di cattura.

## Nominatim key mapping

| Nominatim key | location JSON key |
|---|---|
| `road` or `street` | `street` |
| `house_number` | `street_number` |
| `postcode` | `zip`, `postcode` |
| `city` / `town` / `village` / `municipality` | `city` |
| `county` / `state_district` | `province` |
| `state` / `region` | `state` |
| `country` | `country` |
| `country_code` | `country_code` |

## Migration note

The `location` column was added via `tableUpdate()` in
`2026_04_29_100000_create_tickets_table.php` (bumped timestamp forces re-run).
