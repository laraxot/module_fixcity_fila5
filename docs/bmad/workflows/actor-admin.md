---
title: "BMAD actor — Amministratore"
type: workflow
status: active
created: 2026-09-26
updated: 2026-09-26
tags: [bmad, actor, admin, tenant, dashboard, fixcity]
module: Fixcity
qmd: "admin dashboard roles tenant backup release fixcity workflow"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-supervisor.md
  - 09-release.md
  - ../stories/STORY-004-admin-dashboard.md
  - ../stories/STORY-504-dashboard-xotbase-dashboard.md
---

# Attore: Amministratore

**Scopo:** configurare e custodire il tenant senza bypassare policy, audit o
dati sacri.

## Happy path

1. Dashboard `XotBaseDashboard` (non `XotBasePage`).
2. Configura ruoli/permessi nel perimetro User/tenant; categorie e stati restano
   coerenti con Enum/SSoT.
3. Controlla salute: code, notifiche fallite, export, audit.
4. Coordina con il titolare privacy la redazione e approvazione dell’informativa. Solo dopo il sign-off pubblica i Markdown localizzati in `config/<tenant>/lang/{locale}/policy.md`; fino ad allora `/privacy` restituisce 503 e l’invio resta disabilitato. L’admin tecnico non sostituisce il titolare nella validazione legale.
5. Prepara backup/restore e partecipa al sign-off di [09-release](09-release.md).
6. Ogni azione amministrativa resta tracciabile; niente “superpower” silenzioso
   oltre `XotBasePolicy::before` documentato.

## Failure path

- modifica stati fuori Enum senza migration/evoluzione;
- restore non provato;
- accesso dati cittadino senza audit.

## Prove

Smoke dashboard; checklist release; test permessi admin vs operator.
