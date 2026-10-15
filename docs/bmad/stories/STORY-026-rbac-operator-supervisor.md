---
title: "STORY-026 — RBAC per operatore e supervisore"
type: story
module: Fixcity
status: in_progress
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, fixcity, authorization, rbac, pa]
qmd: "RBAC operator supervisor admin ticket policy UserContract access"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ../../../app/Policies/BasePolicy.php
  - ../../../app/Policies/TicketPolicy.php
  - ../../../tests/Feature/Filament/TicketResourceTest.php
---

# Obiettivo

Applicare permessi coerenti alle operazioni PA usando i contratti utente e impedendo accessi di cittadini o utenti senza ruolo.

## Stato nel codice

- [x] `BasePolicy` definisce i ruoli PA (`operator`, `supervisor`, `admin`) e usa `UserContract`.
- [x] `TicketPolicy` eredita la gerarchia owner-module corretta e protegge `assign`.
- [ ] Test matrice per guest/citizen/operator/supervisor/admin su view, update, assign, delete e dashboard.
- [ ] Verifica browser con almeno due ruoli reali.

## Criteri di accettazione

1. Ogni capability ha un test positivo per i ruoli ammessi e negativo per i ruoli non ammessi.
2. L'owner cittadino può leggere i propri ticket senza ottenere capability PA.
3. I campi audit non concedono ownership.
4. Le azioni Filament e le route usano la stessa policy; nessun controllo basato direttamente su `Modules\\User\\Models\\User`.
5. L'eventuale super-admin bypass è registrato e auditabile.

## Verifica corrente

Sono presenti test di assegnazione negata all'utente non PA e il fix owner-only (STORY-510). La matrice completa e i test browser restano aperti; Pest dipende dall'accesso MariaDB test.
