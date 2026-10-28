---
title: "DCP-416: il coordinamento tra agenti vive nelle story BMAD, non in docs/chat"
type: story
status: in-progress
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, multi-agent, docs-chat, second-brain, coordinamento]
qmd: "docs chat archivio story bmad coordinamento multi agente claim handoff claims-open regole corrette"
related:
  - ../wiki/concepts/ai-harness-module-discipline.md
issues: []
discussions: []
---

# DCP-416: coordinamento agenti nelle story BMAD

## Richiesta

Utente, 2026-10-27: «quello che facevamo con la cartella docs/chat/ ora lo facciamo con le bmad stories , correggi».

## Causa

La regola esisteva gia' (memoria `multi-agent-coordination-standing`, `docs/chat` congelata), ma le istruzioni che un agente legge davvero dicevano ancora il contrario: protocollo di lock, quality gate, how-to, skill di bootstrap, template dei lock. Un agente che le seguiva tornava a scrivere in `docs/chat/`. Correggere solo la memoria non basta: va corretta la regola che l'agente apre.

## Cosa e' cambiato

Dove prima c'era «scrivi/leggi/append in `docs/chat/`», ora c'e' «claim ed `## Esito` nella story BMAD del modulo/tema owner, committata subito; claim aperti con `python3 bashscripts/tools/claims-open.py [--file <path>]`». `docs/chat/` resta archivio storico: si legge, non si scrive, non si cancella.

Prima sessione (altra sessione, stessa data): `agent-chat-directory.md`, `.agents/BOOTSTRAP.md`, `.agents/00-ON_DEMAND_USAGE.md`, `.agents/docs/git-workflow.md`, `multi-agent-coordination.md` (2 copie), INDEX/index (4 copie), `docs/wiki/index.md`, righe di `00-TRIGGER_MAP.md` (24 -> 18 menzioni), concept `ai-harness-module-discipline`.

Questa sessione, in `bashscripts/ai/wiki/` (stessa cartella di `docs/wiki/`): `rules/file-locking-validation-protocol.md` (§4, §5, §6), `rules/post-edit-quality-gate.md` (§4 riscritta, 7 punti), `how-to/multi-agent-coordination-discipline.md`, `rules/lock-coordination-template.md` (handoff = riga nella story), `rules/agent-conduct-rules.md`, `rules/multi-agent-file-lock-protocol.md`, `rules/github-multi-agent-coordination.md` (titolo, scopo, Layer 1, avviso in cima), `skills/cursor-second-brain-max-workflow.md`, `skills/docs-chat-filename-no-dates.md`, `rules/docs-chat-filename-no-dates.md`, `memories/docs-chat-slug-per-topic.md`, `rules/recuperare-codice-cancellato-prima-di-riscriverlo.md`, `how-to/autocompact-thrashing-recovery.md`, `skills/multi-agent-coordination-phpstan-fixes.md`, `GEMINI.md`.

## Verifica (conteggi di `docs/chat` per file, prima -> dopo)

| File | Prima | Dopo |
|------|-------|------|
| file-locking-validation-protocol | 13 | 1 (nota «archivio») |
| post-edit-quality-gate | 13 | 1 (nota «archivio») |
| multi-agent-coordination-discipline | 9 | 3 (note «archivio») |
| lock-coordination-template | 5 | 2 (note «archivio») |
| agent-conduct-rules | 5 | 1 |
| multi-agent-file-lock-protocol | 4 | 1 |
| cursor-second-brain-max-workflow | 16 | 2 (note «archivio») |
| github-multi-agent-coordination | 25 | 23 (esempi, vedi Aperto) |
| 00-TRIGGER_MAP (altra sessione) | 24 | 18 |

`claims-open.py --file` esiste e gira (verificato con `--help`). Le menzioni rimaste nei file sopra sono note «archivio storico» scritte apposta.

## Aperto

Story figlie (2026-10-27): [DCP-425](./dcp-425-docs-chat-copie-divergenti-ed-esempi-da-ritirare.story.md) copie ed esempi, [DCP-426](./dcp-426-docs-chat-script-sh-che-nominano-la-cartella.story.md) script `.sh`, [DCP-427](./dcp-427-docs-chat-lock-orfani-e-riferimenti-rotti.story.md) lock e riferimenti rotti.

- `rules/github-multi-agent-coordination.md`: 23 menzioni restano negli esempi bash lunghi (handoff, merge conflict, QUALITY_GATES). Hanno in cima un avviso che li traduce in «claim nella story owner»; riscriverli tutti e' sproporzionato finche' nessuno li usa.
- Copie in `rules/11-forms/` (agent-chat-directory, multi-agent-file-lock-protocol, multi-agent-coordination.mdc, 00-TRIGGER_MAP righe 78/289/293, docs-chat-filename-no-dates, reports-build-directory.mdc) e `rules/07-documentation/docs-chat-filename-no-dates`: divergenti, non toccate. Decidere se rigenerarle dal canone o ritirarle.
- `rules/docs-chat-filename-no-dates.md` righe 55-56 («Aggiornare INDEX») descrivono la procedura di rinomina dei file legacy: corretta solo per l'archivio.
- Script `.sh` in `bashscripts/ai/` che nominano `docs/chat`: non verificato cosa scrivono.
- Il `docs/stories/5.85-stop-using-docs-chat-use-bmad-stories.story.md` citato dalla memoria `multi-agent-coordination-standing` non esiste; il `docs/planning-artifacts/multi-agent-coordination-protocol.md` citato dal Trigger Map nemmeno (l'altra sessione ha rimandato a `dcp-35-...-coordinamento.story.md`).
- Lock orfani di `docs/chat/*.lock` (oltre 44000 minuti) compaiono in `claims-open.py`: pulizia da decidere con chi li ha creati.
- `bash bashscripts/docs/llm-wiki-qmd.sh update` non lanciato: indice QMD di questo progetto punta a un altro repo (memoria `qmd-index-trade-fila5-points-to-ptvx`).

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude (sessione camera upload) | seconda tornata di correzioni sulle istruzioni operative | la prima era di un'altra sessione (`agent-chat-directory.md`, bootstrap, righe del Trigger Map); nessun file toccato due volte |
