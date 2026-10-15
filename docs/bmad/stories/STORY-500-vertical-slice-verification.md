---
title: "STORY-500 — Verifica vertical slice FixCity"
type: story
module: Fixcity
status: blocked
created: "legacy"
updated: 2026-09-26
tags: [bmad, fixcity]
qmd: "STORY 500 vertical slice verification FixCity BMAD story"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
---
# STORY-500 — Verifica vertical slice FixCity

**Epic:** Release pilota  
**Priorità:** Must  
**Stato:** Blocked dal database di test

## Criteri di accettazione

- [ ] Pest Feature Fixcity eseguibile;
- [ ] cittadino crea e vede il proprio ticket;
- [ ] PA visualizza, assegna, aggiorna stato e commenta;
- [ ] notifica e timeline sono verificate;
- [ ] ticket di altri cittadini risultano inaccessibili;
- [ ] smoke browser staging verde;
- [ ] report di accessibilità e runbook release allegati.

## Evidenza corrente

`SQLSTATE[HY000] [1044] Access denied for user 'marco'@'localhost' to database 'fixcity_data_test'`.

## GitHub (tracciamento)

| Risorsa | Ruolo |
|---|---|
| [Issue tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/issues/383) | epic di completamento parent |
| [Discussion tracker base_fixcity_fila5](https://github.com/laraxot/base_fixcity_fila5/discussions/392) | coordinamento test e readiness |
