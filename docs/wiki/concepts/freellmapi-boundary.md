---
title: "FreeLLMAPI boundary — Fixcity"
type: concept
created: 2026-10-11
updated: 2026-10-11
tags: [second-brain, freellmapi, boundary, security]
status: draft
qmd: "confine freellmapi fixcity"
---

# Confine FreeLLMAPI

FreeLLMAPI è memoria/assistenza per agenti e tooling, non una capability del
cittadino. Il modulo Fixcity documenta il contratto; l’installazione e i segreti
sono esclusivamente in `tools/freellmapi/`. Per la procedura operativa leggere il
[documento BMAD](../../bmad/freellmapi-integration-architecture.md).

Regola: se FreeLLMAPI non è disponibile, le rotte e i flussi ticket devono
continuare a funzionare.
