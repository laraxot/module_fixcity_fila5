---
title: "Agent confidence brief — FixCity operativo"
type: concept
tags: [fixcity, agent, second-brain, sixteen, omniroute, hindsight]
created: 2026-10-26
updated: 2026-10-26
qmd: "agent confidence brief fixcity sixteen homepage ticket ownership"
status: active
confidence: high
related:
  - ./README.md
  - ./wiki/concepts/second-brain-operating-model.md
  - ../../../Themes/Sixteen/docs/knowledge-map.md
  - ../../../Themes/Sixteen/docs/page-directory-structure.md
  - ../../../../docs/wiki/concepts/architectural-invariants.md
  - ../../../../docs/wiki/rules/00-TRIGGER_MAP.md
---

# Agent confidence brief

Snapshot operativo verificato nel codice il **2026-10-26**. Serve agli agenti per
non re-litigare decisioni già chiuse. Per il dettaglio: link sotto, non copia.

## Perché esiste FixCity

Piattaforma PA di **segnalazioni civiche** (ticket): il cittadino apre una
pratica geolocalizzata; il backoffice la lavora. Il front office segue Design
Comuni / AGID sul tema **Sixteen**.

## Stack verificato (`composer.lock`)

| Pacchetto | Versione |
|---|---|
| PHP | 8.4.x |
| `laravel/framework` | v13.38.0 |
| `filament/filament` | v5.10.3 |
| `livewire/livewire` | v4.4.8 |
| `laravel/folio` | v1.2.0 |
| `livewire/volt` | v1.11.2 |
| `nwidart/laravel-modules` | v13.0.0 |

## Ownership (DRY)

| Cosa | Owner | Non owner |
|---|---|---|
| Ticket / wizard / area personale ticket | `Modules/Fixcity` | — |
| Mappe / coordinate | `Modules/Geo` | Fixcity |
| Motore pagine / `<x-page>` / CMS | `Modules/Cms` | Tema |
| Layout Design Comuni / blocchi Blade | `Themes/Sixteen` | Fixcity |
| FAQ significato/catalogo | Fixcity | Markup Sixteen |

Contratto lungo: [README Fixcity](./README.md).

## Front office Sixteen — religione pagine

In `Themes/Sixteen/resources/views/pages` solo: `auth/`, `[container0]/`,
`tests/`, e `index.blade.php`. Homepage = blocchi JSON
`config/local/fixcity/database/content/pages/home.json` (mai
`pages/home.blade.php`). Verifica:
`cd laravel && vendor/bin/pest Themes/Sixteen/tests/Unit/NoSemanticFolioPageDirectoriesTest.php`.

Homepage IT attuale (`content_blocks.it`): `home.hero`, `ticket.map-preview`,
`how-it-works`, `quick-actions`.

Blocchi sempre data-driven: JSON pagina + fallback `config('comune.*')`.

## Invarianti (non negoziabili)

1. No Controller FO/BO — Filament + Folio/Volt + Actions.
2. No Service class — `QueueableAction` / Spatie.
3. Filament → solo `XotBase*`.
4. Git: **no** `checkout -b`; forward-only (no rollback come scorciatoia).
5. BMAD nuovi solo in `Modules/*/docs` o `Themes/*/docs` — mai `bmad-output/`.
6. Nomi `.md`: minuscolo, no date/suffissi `-1.md` (eccetto `README.md`).
7. DB sacro: niente `migrate:fresh` / wipe su dati reali.
8. PHPStan: config immutabile; azzerare errori sul codice.

Elenco corto: [architectural-invariants.md](../../../../docs/wiki/concepts/architectural-invariants.md).
Routing agenti: [00-TRIGGER_MAP.md](../../../../docs/wiki/rules/00-TRIGGER_MAP.md).

## Memoria agenti (come non perdere confidenza)

| Tool | Ruolo |
|---|---|
| Hindsight | Perché / decisioni / initiative — `search` prima di agire |
| QMD | Indice docs (`sixteen-theme`, wiki) |
| Graphify | Grafo codice prima del grep a tappeto |
| Docs modulo/tema | Memoria owner; aggiornare dopo decisioni durevoli |

Hindsight bank: `coding-agent::base_fixcity_fila5`. Se una page dice
"No content yet", **non** trattarla come verità: verifica codice + ingest
`Correction:` o brief qui.

## Tooling Claude Code ↔ OmniRoute (2026-10-26)

Errore tipico: `Unable to determine provider for model 'claude-sonnet-5-5'`.

- Claude Code senza prefisso manda ID nudi; OmniRoute vuole `provider/model`
  o combo/alias.
- Un combo omonimo `claude-sonnet-5-5` che punta a `openai/...` **vince** sugli
  alias e fallisce se non c'è provider openai.
- Import da `~/.claude/.credentials.json` in OmniRoute spesso porta a token
  **revocati**: usare OAuth dashboard OmniRoute
  (`http://localhost:20128/dashboard/providers`), non condividere token CC.
- Alternativa stabile: togliere `ANTHROPIC_BASE_URL` da
  `~/.claude/settings.json` e parlare ad Anthropic diretto (model `sonnet`).

## Aperti noti (AGENTS / loop Sixteen)

Homepage parity / segnalazione-dettaglio / WCAG / tab area personale: loop
ancora aperto in `AGENTS.md` root. Prima di nuove pagine tema: JSON CMS o link,
non alias Folio.

## Collegamenti

- [Sixteen knowledge-map](../../../Themes/Sixteen/docs/knowledge-map.md)
- [page-directory-structure](../../../Themes/Sixteen/docs/page-directory-structure.md)
- [second-brain-operating-model](./wiki/concepts/second-brain-operating-model.md)
- [agent-stack how-to](../../../../docs/wiki/how-to/agent-stack-trade-fila5-operating-model.md)
