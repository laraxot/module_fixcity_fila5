---
title: "FreeLLMAPI integration architecture — Fixcity"
type: architecture
status: active
created: 2026-10-11
updated: 2026-10-11
tags: [bmad, fixcity, freellmapi, ai, security]
---

# Architettura d’integrazione

## Decisione

FreeLLMAPI è un servizio locale di supporto agli agenti e alla produzione della
documentazione BMAD/Second Brain. Fixcity non lo chiama nel percorso cittadino,
non lo espone via HTTP pubblico e non ne dipende per creare, leggere o aggiornare
una segnalazione.

```text
agente / tooling -> 127.0.0.1:3001/v1 -> FreeLLMAPI -> provider configurati
browser cittadino ---------------------> Laravel/Fixcity + Sixteen
```

## Ownership

| Artefatto | Owner |
| --- | --- |
| routing AI, `.env`, SQLite, processo Node | `tools/freellmapi/` |
| procedure condivise e verifiche | `bashscripts/` |
| regole di dominio ticket | `laravel/Modules/Fixcity/docs/` |
| resa visuale DesignComuni | `laravel/Themes/Sixteen/docs/` |
| piano cross-cutting | `bmad-output/front-office-design-comuni/` |

## NFR e guardrail

- **Sicurezza:** bearer key e provider keys solo su localhost e in storage
  upstream cifrato; niente segreti in Git, prompt, screenshot o issue.
- **Disponibilità:** il front office deve funzionare anche con FreeLLMAPI spento;
  timeout/failure non deve bloccare le rotte pubbliche.
- **Privacy:** non inviare dati personali o contenuti reali dei cittadini al
  router durante test/documentazione; usare fixture anonime.
- **Manutenibilità:** aggiornare questa decisione quando cambia il boundary, non
  duplicare la documentazione completa upstream.

## Riferimenti

- [tooling locale](../../../../../bashscripts/docs/bmad/freellmapi-local-tooling.md)
- [frontend DesignComuni](./design-comuni-frontend-architecture.md)
- [BMAD master plan](../../../../../bmad-output/front-office-design-comuni/README.md)
