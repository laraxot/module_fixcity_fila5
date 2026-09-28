---
title: "FixCity ticket deletion follows least privilege"
type: story
status: done
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, authorization, audit, filament]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - STORY-009-pa-operator-flow.md
  - STORY-010-admin-config-flow.md
---

# STORY — Eliminazione ticket secondo il ruolo

## Problema

La story dell'operatore PA vieta l'eliminazione, ma `TicketPolicy::delete()`
autorizzava qualsiasi ruolo PA e il proprietario. La pagina dettaglio, modifica
e bulk non applicavano in modo coerente il confine della policy. I record hanno
soft delete: l'operazione conserva la riga, ma la sottrae ai flussi attivi.

## Criteri di accettazione

- [x] Cittadini/proprietari, operatori e supervisori non possono eliminare per
      ruolo; anche il proprietario è negato.
- [x] Admin e utenti con il permesso esplicito `ticket.delete` possono usare
      soft delete.
- [x] L'azione su dettaglio/modifica e la bulk action chiedono la capability
      `delete` / `deleteAny`.
- [x] Test UI Livewire verifica che operatore non veda delete su dettaglio,
      modifica e bulk; l'admin vede le azioni singole.
- [x] `DeleteTicketAction` registra `deleted_by` e applica soft delete nella
      stessa transazione.
- [x] Suite FixCity, PHPStan Modules e quality gate wiki verdi dopo il fix.

## Rischio e limiti

Questo change non introduce force-delete e non modifica i record esistenti.
La capability `ticket.delete` può essere assegnata in modo granulare al di fuori
dei tre ruoli predefiniti. La cancellazione logica scrive `deleted_by` prima del
soft delete nella transazione FixCity, così il flusso resta verificabile.

## Verifiche

- Pest policy + Livewire: 10 test / 58 asserzioni.
- Suite FixCity completa: 379 test / 1.626 asserzioni.
- PHPStan Modules: zero errori; Pint mirato, wiki quality gate e `git diff --check` verdi.
