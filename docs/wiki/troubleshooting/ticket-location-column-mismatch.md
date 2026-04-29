---
name: ticket-location-column-mismatch
description: Admin/frontoffice ticket creation can fail if the CoordinatePicker payload is persisted to a non-existent location column
type: troubleshooting
---

# Ticket Location Column Mismatch

## Symptom

Ticket creation fails with:

`table tickets has no column named location`

## Root cause

The `CoordinatePicker` sends a structured `location` payload, but the active `tickets` table in SQLite only exposes legacy columns like `latitude`, `longitude`, and `address`.

## Fix

Normalize the `location` attribute in `Modules/Fixcity/Models/Ticket.php` so writes degrade to:

- `latitude`
- `longitude`
- `address`

and do not attempt to insert a `location` column when the current schema does not support it.
