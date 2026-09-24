---
title: Valutazione cittadino ticket — modulo Rating
type: concept
created: 2026-05-29
tags: [rating, ticket, fixcity, architecture]
related:
  - ../../../../../../docs/wiki/concepts/module-boundaries-rating.md
---

# Valutazione cittadino — solo modulo Rating

## Errore da non ripetere

Aggiungere `citizen_rating` / `citizen_rated_at` su `tickets` duplica un dominio già coperto da **Modules/Rating** (`Rating` + `RatingMorph` polimorfico).

Motivi (grave):

1. **Confine modulo** — Fixcity non possiede il dominio "valutazione".
2. **DRY** — KPI admin, export, doppio voto andrebbero riscritti fuori dal modulo Rating.
3. **Velocità story** — STORY-043 ha accettato colonne locali invece di riusare `HasRating` / `RatingMorph` (anti-pattern documentato).

## Implementazione corretta

| Pezzo | Ruolo |
|-------|--------|
| `EnsureTicketCitizenRatingDefinitionAction` | `Rating` slug `fixcity-ticket-citizen-satisfaction` |
| `RatingMorph` | `model` = Ticket, `user_id`, `value` 1–5 |
| `InteractsWithTicketCitizenRating` | Accessor `citizen_rating` / `citizen_rated_at` (solo lettura UI) |
| `SubmitCitizenTicketRatingAction` | Crea `RatingMorph`, non `UPDATE tickets` |

## Checklist agente

- [ ] Nessuna colonna `citizen_rating*` su `tickets`
- [ ] `Ticket implements HasRatingContract`
- [ ] Aggregati KPI → query `rating_morph`, non `avg()` su tickets
