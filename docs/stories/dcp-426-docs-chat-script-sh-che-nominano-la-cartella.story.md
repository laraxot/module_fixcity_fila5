---
title: "DCP-426: script .sh che nominano docs/chat"
type: story
status: ready-for-dev
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, docs-chat, bashscripts, audit]
qmd: "script sh docs chat check_github_issues auto_issue_interact guard_overflow gh-trova-esclusi audit"
related:
  - ./dcp-416-coordinamento-agenti-nelle-story-bmad-al-posto-di-docs-chat.story.md
  - ./dcp-425-docs-chat-copie-divergenti-ed-esempi-da-ritirare.story.md
issues: []
discussions: []
---

# DCP-426: script che nominano docs/chat

## Origine

[DCP-416](./dcp-416-coordinamento-agenti-nelle-story-bmad-al-posto-di-docs-chat.story.md) ha corretto le istruzioni, ma non ha letto cosa fanno gli script. Una ricerca `grep -rl 'docs/chat' bashscripts --include='*.sh'` trova 7 file:

- `bashscripts/ai/gh-ptv-trova-esclusi-criteri-refactor-audit.sh`
- `bashscripts/ai/check_github_issues.sh`
- `bashscripts/ai/auto_issue_interact.sh`
- `bashscripts/ai/gh-trova-esclusi-session-audit.sh`
- `bashscripts/ai/gh-sigma-trova-esclusi-audit.sh`
- `bashscripts/ai/guard_overflow.sh`
- `bashscripts/ai/wiki/rules/check-no-validateform.sh`

Non e' noto quali leggono la cartella e quali ci scrivono. Quelli che scrivono ricreano l'archivio e rompono la regola.

## Acceptance criteria

1. Per ciascuno dei 7 script una riga di tabella: cosa fa con `docs/chat` (solo lettura, scrive, solo testo di esempio), con la riga di codice.
2. Gli script che scrivono in `docs/chat` smettono di farlo: scrivono nella story indicata o sono marcati obsoleti con un messaggio chiaro.
3. Nessuno script viene eseguito durante l'audit: diversi chiamano `gh` verso il repository remoto e creano o commentano issue (`auto_issue_interact.sh`). L'audit e' sola lettura del sorgente.
4. Il layout degli script (`bashscripts/docs/script-layout.md`) resta rispettato: nessuno script nuovo in radice. Il file `bashscripts/docs/root-script-policy.md`, citato da `00-ON_DEMAND_USAGE.md`, non esiste (vedi [DCP-427](./dcp-427-docs-chat-lock-orfani-e-riferimenti-rotti.story.md)).
5. Il conteggio `grep` finale e' riportato; i rimasti sono solo note «archivio».

## Tasks

- [ ] Leggere ogni script e compilare la tabella.
- [ ] Decidere script per script: correggere, marcare obsoleto, lasciare.
- [ ] Correggere con `bash -n` come minima verifica di sintassi (nessuna esecuzione).
- [ ] Aggiornare la tabella e DCP-416.

## Owned file scope

I 7 file elencati sopra e la pagina di docs in `bashscripts/docs/` se serve.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
