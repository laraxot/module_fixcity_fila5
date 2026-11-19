---
id: dcp-206
title: "DCP-206: strumenti, flusso git, credenziali e MCP: cosa riparare e cosa decidere"
type: story
epic: dc-parity-v2
status: ready-for-dev
priority: P1
owner: repo
module:
created: 2026-10-15
updated: 2026-10-15
qmd: "merge_remote_repo_2 commit punto merge_file tracciati xot mcp ruflo puppeteer credenziali supermemory github context7 sqlite hindsight tmp inode qmd index"
related: [dcp-11, dcp-177, dcp-200, dcp-201]
tags: [strumenti, git, mcp, sicurezza]
---

# DCP-206: strumenti, flusso git, credenziali e MCP: cosa riparare e cosa decidere

## Fase BMAD
Plan. Riguarda `bashscripts/` e la configurazione degli agenti, non un modulo: per questo la story sta in `bmad-output/`.

## Da fare
- [ ] **`bashscripts/git/subtrees/merge_remote_repo_2.sh`.** Fa `git add -A && git commit -am .` in ogni repo, poi `git merge laraxot/dev` e `git push`, senza messaggio, senza controllo dei conflitti e senza fermarsi se il merge fallisce (ha gia' lasciato un editor in attesa su `Modules/Cms/.git/MERGE_MSG` e marker nei prompt). DEC-29: messaggio con data e cartelle toccate; stop se il merge ha conflitti; mai `-A` su file ignorati. Provarlo su un clone, non sul lavoro vivo.
- [ ] **162 file `.merge_file_*` tracciati in `Modules/Xot`** (commit `3c9667641`, 6 giorni fa) nonostante `.gitignore` abbia `.merge_*`: sono file temporanei del merge. DEC-30: `git rm --cached` con un commit in avanti.
- [ ] **File con nome da variabile non espansa** in `bashscripts/git/subtrees/`: `INDEX-$ORG.md`, `INDEX-.md` e simili (lo script che li crea ha un'espansione vuota). Trovare lo script, correggerlo, poi rinominare i file con lo strumento di DCP-201.
- [ ] **MCP.** Radice 10 su 10 (misurato con `tools/mcp/mcp-handshake.py`); `ruflo` e `puppeteer` restano senza versione fissata perche' con la versione esplicita non rispondono (DEC-32); `sqlite` legge `test_data.sqlite` che ha lo schema e nessun dato. `laravel/.mcp.json`: `playwright@latest` e `flowbite-mcp` senza versione.
- [ ] **Credenziali.** DEC-28: ruotare la chiave Supermemory (era in chiaro in `laravel/.mcp.json`, nella storia git) e verificare cosa resta in HEAD; `GITHUB_TOKEN`, `CONTEXT7_API_KEY`, `GOOGLE_PAGESPEED_API_KEY` non sono nell'ambiente di Claude Code: `github` e `context7` partono senza credenziale. Mai scrivere valori nei file di progetto: variabili d'ambiente.
- [ ] **`/tmp` pieno per inode.** Era un leak del plugin Hindsight (fix permanente `nr_inodes=4m` + `tmpfiles 2d`): verificare che sia ancora applicato dopo il riavvio.
- [ ] **Date nei nomi file delle memorie di `bashscripts/ai/wiki/memories/`** (27 con suffisso, piu' quelle con data): gestite da DCP-201.

## Criteri di accettazione
- [ ] Lo script di sincronizzazione prova su un clone: commit con messaggio vero, nessun file ignorato, si ferma su conflitto.
- [ ] `git ls-files | grep -c merge_file` a 0 in `Modules/Xot`.
- [ ] `mcp-handshake.py --file .mcp.json` 10 su 10; nessun segreto in `git grep` sui file di configurazione.

## Non verificato
Rotazione effettiva delle chiavi (spetta al titolare dei servizi); il comportamento dello script su rete lenta.
