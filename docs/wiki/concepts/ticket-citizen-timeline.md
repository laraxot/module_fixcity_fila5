---
title: "Timeline cittadino — storico avanzamento segnalazione"
type: concept
tags: [fixcity, ticket, timeline, transparency, activity, milestone, design-comuni]
created: 2026-06-17
updated: 2026-06-17
qmd: "timeline segnalazione cittadino data presa carico lavori verifica ente milestone Activity trasparenza"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/436"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/437"
related:
  - ../../../../../../docs/stories/STORY-396-ticket-citizen-timeline.md
  - ticket-detail-page-ux.md
  - ticket-view-fo-enrichment-map-media-comments.md
  - ../../../../../../docs/wiki/concepts/fixcity-scopo-e-target.md
---

# Timeline cittadino — avanzamento segnalazione

## Scopo (business)

Rispondere al bisogno PA: **il cittadino sa cosa è successo e quando** — segnalazione inviata, ente ha preso in carico, ha risposto, ha fatto i lavori, ha verificato la chiusura — **senza chiamare il comune**.

Non è un elenco di 4 date fisse sul modello `Ticket`: è una **vista proiettata** su eventi reali (workflow, Activity, note pubbliche operatore).

## Separazione responsabilità

| Layer | Cosa fa | Story | Wiki |
|-------|---------|-------|------|
| Motore eventi | Transizioni stato, Activity, audit | [STORY-392](../../../../../../docs/stories/STORY-392-ticket-workflow-activity-events.md) | [ticket-workflow-state-machine.md](ticket-workflow-state-machine.md), [ticket-activity-event-contract.md](ticket-activity-event-contract.md) |
| Proiezione timeline | `BuildTicketTimelineAction` → DTO ordinati | [STORY-396](../../../../../../docs/stories/STORY-396-ticket-citizen-timeline.md) | questo file |
| UI FO | Componente Sixteen su dettaglio ticket | STORY-396 + [STORY-373](../../../../../../docs/stories/STORY-373-fixcity-fo-e2e-completion.md) |
| Verifica chiusura | Milestone `verified` | [STORY-405](../../../../../../docs/stories/STORY-405-verifica-cittadina-close-loop.md) |
| Notifiche | Nuovo evento visibile → Notify | [STORY-391](../../../../../../docs/stories/STORY-391-citizen-engagement-ticket-lifecycle.md) |

## Regole (religione)

- **Mai date inventate** — passo futuro = «In attesa», non timestamp falso.
- **Visibility** — `public` | `author_only` | `internal`; FO non espone `internal`.
- **i18n** — label in `fixcity::ticket.timeline.*`, no stringhe in Blade.
- **Cronologia** — più eventi dello stesso tipo ammessi (riaperture, più risposte ente).
- **Attore** — ogni voce indica chi ha fatto l’azione (cittadino, operatore, sistema).

## Milestone tipiche (mapping in Action)

| Chiave | Messaggio cittadino (es.) |
|--------|---------------------------|
| `submitted` | Segnalazione inviata |
| `acknowledged` | Presa in carico dall’ente |
| `entity_response` | Risposta dell’ente |
| `work_started` | Intervento avviato |
| `work_completed` | Lavori eseguiti |
| `verified` | Verifica e chiusura |

Mapping da `TicketStatusEnum` + righe Activity va **solo** in `BuildTicketTimelineAction`, non nei componenti Blade.

## Design Comuni

Timeline verticale «stato avanzamento pratica», data leggibile, passo corrente evidenziato. Parity: [STORY-390](../../../../../../docs/stories/STORY-390-design-comuni-parity-programme.md).
