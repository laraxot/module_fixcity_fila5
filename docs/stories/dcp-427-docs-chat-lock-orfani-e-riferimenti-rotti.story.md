---
title: "DCP-427: docs/chat, lock orfani e riferimenti rotti"
type: story
status: needs-decision
module: Fixcity
created: 2026-10-27
updated: 2026-10-27
tags: [bmad, story, docs-chat, lock, riferimenti-rotti, igiene]
qmd: "lock orfani claims-open unlock stale 5.85 planning-artifacts multi-agent-coordination-protocol riferimenti rotti"
related:
  - ./dcp-416-coordinamento-agenti-nelle-story-bmad-al-posto-di-docs-chat.story.md
  - ./dcp-425-docs-chat-copie-divergenti-ed-esempi-da-ritirare.story.md
issues: []
discussions: []
---

# DCP-427: lock orfani e riferimenti rotti

In attesa dell'utente: sciogliere lock di altri agenti e' un'azione sul lavoro altrui.

## Origine

`python3 bashscripts/tools/claims-open.py` elenca lock attivi con eta' enormi (misurato il 2026-10-27; il README dei lock considera stale un lock dopo 60 minuti):

| Lock | Agente | Task | Eta' (min) |
|------|--------|------|------------|
| `docs/chat/INDEX.md.lock` | second-brain | fixcity-capability-privacy | ~44400 |
| `docs/chat/fixcity-g25-footer.md.lock` | root | fixcity-footer-test-evidence | ~43970 |
| `docs/chat/fixcity-public-capability-code-privacy.md.lock` | second-brain | fixcity-capability-privacy | ~44400 |
| `docs/chat/fixcity-backlog-bmad-alignment.md.lock` | second-brain | fixcity-backlog-collision | ~44370 |
| `docs/chat/fixcity-bmad-story-audit.md.lock` | second-brain | fixcity-bmad-frontmatter | ~44380 |
| `docs/sprint-status.yaml.lock` | second-brain | fixcity-backlog-collision | ~44370 |
| `bashscripts/ai/wiki/memories/fixcity-module-architecture.md.lock` | codex-root | fixcity-module-memory | ~44750 |
| `bashscripts/ai/wiki/memories/xotbase-dashboard-extension.md.lock` | codex-root | fixcity-dashboard-memory | ~44760 |
| `bashscripts/ai/wiki/rules/policy-hierarchy.mdc.lock` | codex-root | fixcity-policy-contract-doc | ~44760 |
| `laravel/Themes/Barthelemy/docs/shared-components/migrations.md.lock` | codex-root | theme-migration-doc-dedup | ~44740 |
| `laravel/Themes/Sixteen/docs/decision-log.md.lock` | qwen | sixteen-home-json-route-fix | ~26140 |

Alcuni bloccano file vivi (`docs/sprint-status.yaml`, `policy-hierarchy.mdc`, `decision-log.md` di Sixteen). Le regole dicono di non cancellare un `.lock` a mano e di non rubarlo se e' recente; qui l'eta' e' di settimane.

Riferimenti rotti trovati: la memoria `multi-agent-coordination-standing` cita `docs/stories/5.85-stop-using-docs-chat-use-bmad-stories.story.md`, che non esiste; il Trigger Map citava `docs/planning-artifacts/multi-agent-coordination-protocol.md`, che non esiste (un'altra sessione ha gia' rimandato al modello `dcp-35-...-coordinamento.story.md`). Terzo: `bashscripts/docs/root-script-policy.md`, citato da `bashscripts/ai/.agents/rules/00-ON_DEMAND_USAGE.md` (e dalle copie in `skills/` e `skill/`) e dal Trigger Map, non esiste; il documento sul tema e' `bashscripts/docs/script-layout.md`.

## Acceptance criteria

1. Per ogni lock: eta' riletta al momento, `claims-open.py --file <path>` per vedere se una story in corso lo cita, e l'esito (sciolto con `bashscripts/lock/unlock.sh <file> "<motivo>"`, lasciato, o chiesto al proprietario).
2. Nessun `.lock` e' rimosso con `rm`: si usa lo script con il motivo, e l'azione e' scritta qui.
3. Il riferimento a «5.85» punta a DCP-416 (o a una story reale), senza inventare una story con quel numero. I riferimenti a `root-script-policy.md` puntano a `script-layout.md` (o il documento mancante viene scritto, su decisione dell'utente).
4. Dopo la pulizia `claims-open.py` non mostra piu' lock oltre la soglia, salvo quelli volutamente lasciati con motivo.
5. L'utente ha confermato l'elenco prima di sciogliere qualunque lock.

## Tasks

- [ ] Rileggere l'elenco con `claims-open.py` (puo' essere cambiato).
- [ ] Proporre all'utente l'elenco e la raccomandazione (sciogliere quelli oltre 60 minuti senza story in corso).
- [ ] Dopo conferma: unlock con motivo, uno alla volta, rilettura del file dopo.
- [ ] Correggere il riferimento a 5.85 nella memoria del second brain.

## Owned file scope

I `.lock` elencati (solo tramite `unlock.sh`), la memoria `multi-agent-coordination-standing.md`.

## Coordinamento

| Data | Agente | Scope | Nota |
|------|--------|-------|------|
| 2026-10-27 | Claude | stesura | nessun lavoro avviato; nessun lock sciolto |
