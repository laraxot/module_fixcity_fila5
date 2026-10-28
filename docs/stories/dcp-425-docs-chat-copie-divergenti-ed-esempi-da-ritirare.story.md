---
title: "DCP-425: docs/chat, copie divergenti ed esempi da ritirare"
type: story
status: needs-decision
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, docs-chat, wiki, second-brain, consolidamento]
qmd: "docs chat copie divergenti 11-forms 07-documentation github-multi-agent-coordination esempi ritirare stub"
related:
  - ./dcp-416-coordinamento-agenti-nelle-story-bmad-al-posto-di-docs-chat.story.md
  - ./dcp-426-docs-chat-script-sh-che-nominano-la-cartella.story.md
  - ./dcp-427-docs-chat-lock-orfani-e-riferimenti-rotti.story.md
issues: []
discussions: []
---

# DCP-425: copie divergenti ed esempi da ritirare

In attesa dell'utente per un punto: rigenerare le copie dal canone o ritirarle come stub. Il progetto non cancella file senza chiedere.

## Origine

[DCP-416](./dcp-416-coordinamento-agenti-nelle-story-bmad-al-posto-di-docs-chat.story.md) ha corretto le istruzioni operative (in `bashscripts/ai/wiki/`, stessa cartella di `docs/wiki/`). Restano istruzioni vecchie in copie che non ho toccato perche' divergono dal canone:

- `rules/11-forms/`: `agent-chat-directory.md`, `multi-agent-file-lock-protocol.md`, `multi-agent-coordination.mdc`, `00-TRIGGER_MAP.md` (righe 78, 289, 293), `docs-chat-filename-no-dates.md`, `reports-build-directory.mdc`.
- `rules/07-documentation/docs-chat-filename-no-dates.md` e le copie `.mdc` in `rules/`, `rules/docs/` e `rules/07-documentation/`.
- `rules/github-multi-agent-coordination.md`: 23 menzioni di `docs/chat` negli esempi bash lunghi (handoff, merge conflict, QUALITY_GATES), sotto un avviso che li traduce.
- `rules/docs-chat-filename-no-dates.md` righe 55-56 («Aggiornare INDEX»): procedura di rinomina dei file legacy, corretta solo per l'archivio.

Un agente che trova la copia sbagliata con `qmd search` torna a scrivere in `docs/chat/`.

## Acceptance criteria

1. Per ogni copia c'e' un confronto (`diff`) con il canone e la decisione scritta qui: rigenerare, trasformare in stub che rimanda al canone, o lasciare con motivo.
2. Nessun file viene cancellato. Le copie ritirate diventano stub di pochi righi che rimandano a `agent-chat-directory.md`.
3. `rules/11-forms/00-TRIGGER_MAP.md` non indica piu' `docs/chat/` come luogo dove scrivere.
4. Gli esempi di `github-multi-agent-coordination.md` sono riscritti per le story, oppure spostati in una sezione «archivio» esplicita; il conteggio di `docs/chat` nel file e' riportato prima e dopo.
5. `grep -rniE '(scrivi|append|crea|aggiorna|handoff).{0,40}docs/chat'` su `bashscripts/ai/wiki` non restituisce istruzioni imperative fuori dalle note «archivio».

## Tasks

- [ ] Fare il `diff` di ogni copia e riportare l'esito in una tabella qui.
- [ ] Chiedere all'utente se ritirare (stub) o rigenerare, con la raccomandazione: stub, meno manutenzione.
- [ ] Applicare, un file per volta, con lock e rilettura (altre sessioni lavorano sugli stessi file).
- [ ] Riportare i conteggi finali.

## Owned file scope

I file stanno in `bashscripts/ai/wiki/rules/` (`docs/wiki/rules` e' un symlink a quella cartella), nelle sottocartelle `11-forms/` e `07-documentation/`.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato |
