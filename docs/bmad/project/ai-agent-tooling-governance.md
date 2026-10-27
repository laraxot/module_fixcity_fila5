---
title: "AI agent and plugin governance"
type: bmad-tooling-governance
status: proposed
updated: 2026-10-14
tags: [docs, fixcity, bmad]
qmd: "ai agent and plugin governance fixcity"
---

# AI agent and plugin governance

## Decision

BMAD + Second Brain remain the project system of record. Ponytail remains the
minimum-change/code-quality discipline. External plugins are optional accelerators,
not project dependencies and not release evidence.

| Capability | Preferred tool/workflow | Boundary |
|---|---|---|
| Planning/sharding | BMAD stories | No application code in planning phase |
| Memory/decisions | Hindsight/Second Brain | No secrets, credentials or personal data |
| TDD/debug/review | Superpowers, if connected | Must respect module ownership and BMAD AC |
| Security | Codex Security, if connected | Findings require local verification and owner triage |
| Visual design | Existing screenshot/DOM/WCAG evidence; Figma optional | Never replace candidate evidence or legal approval |

## Installation/connection status

Superpowers, Codex Security and Figma were identified as relevant optional
integrations, but no connection is treated as active until the user confirms it.
The referenced article could not be fetched from the supplied URL because the
origin terminated the HTTP/2/TLS stream; adoption is therefore based only on the
capabilities confirmed in the available plugin catalogue and repository needs.

## Safety rules

- Never send tenant data, personal data, secrets, legal drafts or production logs
  to an external plugin without explicit owner approval and a data-processing review.
- Never allow a plugin to change `phpstan.neon`, rewrite migrations, bypass Gdpr or
  publish/deploy production artifacts without the release gate.
- Every plugin-generated change follows the same BMAD story, test strategy, review,
  scope conflict and Second Brain decision record as human-authored work.

## MCP: stato verificato (2026-10-14)

Prova con una vera stretta di mano `initialize` su stdio (non basta che il comando esista), eseguita dalla cartella di ciascun file. Esito dopo le correzioni: `sqlite`, `context-mode` (fissato a `@1.0.169`), `gsc`, `fetch`, `qmd` rispondono. Le correzioni ai due `.mcp.json` sono di un'altra sessione (19:42-19:43); qui sono registrati i fatti che le giustificano.

| Server | Causa del guasto | Prova |
|---|---|---|
| `mauve`, `pagespeed`, `gsc` (vecchio) | pacchetti npm **inesistenti** (404): `mauve-mcp`, `@mcpmarket/pagespeed-insights-mcp`, `@mcpmarket/google-search-console-5-mcp`. Con `npx -y` un nome libero e' un rischio: chi lo registra esegue codice sulla macchina | `npm view` 404; entry tolte o sostituite |
| `fetch`, `sqlite` in `laravel/.mcp.json` | `@modelcontextprotocol/server-fetch` e `server-sqlite` non esistono su npm: sono server Python | ora `uvx`, handshake OK |
| `context-mode` | eseguibile non nel PATH | ora `npx -y context-mode@1.0.169`, handshake OK |
| `memory-bank` | sottocomando `serve` mancante e cartella `../.memory-bank` mai creata; terzo archivio accanto a wiki+qmd e `memory/` | **rimosso** (YAGNI) |

**Aperti, richiedono una decisione o un'azione del titolare:**
- `gsc` parte ma risponde «no credentials»: servono le credenziali Google, che non sono state inventate.
- `supermemory`: la chiave API era scritta in chiaro in `laravel/.mcp.json`, file tracciato, **gia' nel commit `cd32814db`** (remote GitHub). Ora il file usa `${SUPERMEMORY_API_KEY}`, ma lo storico la conserva: **va ruotata**. Il comando `supermemory` inoltre non e' nel PATH.
- `@modelcontextprotocol/server-github` e `server-puppeteer` esistono ma sono segnati «no longer supported» da npm: sostituirli e' una scelta di prodotto.
- Le correzioni valgono dal riavvio di Claude Code.
