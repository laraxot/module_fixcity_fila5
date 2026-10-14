---
id: dcp-177
title: "DCP-177: marker di merge non risolti nei prompt e nelle docs"
type: story
epic: dc-parity-v2
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "marker di merge conflitti non risolti prompt audit-prompts CONFLICT-MARKER laraxot dev 55-md-conventions"
related: [dcp-176]
---
# DCP-177: marker di merge non risolti nei prompt e nelle docs

Fase BMAD: Analyze, Implement, Verify. Nata dall'esecuzione del prompt `bashscripts/docs/prompts/55-md-conventions.md`.

## Lacuna e prova (2026-10-14)
- 85 file `.md` contengono marker di merge reali (`<<<<<<< HEAD`, `<<<<<<< .merge_file_*`, `>>>>>>> laraxot/dev`): 33 in `bashscripts/docs/prompts`, 17 in `bashscripts/ai/wiki/memories`, 8 in `bashscripts/ai/wiki/rules`, 1 in `second-brain`, 1 in `how-to`, 24 in `laravel/Modules/Xot/docs` (stories e bmad/stories), 1 in `laravel/Modules/UI/docs`. Misura su 36.097 `.md` delle cartelle docs, wiki, prompt e bmad-output (escluse `node_modules`, `vendor`).
- I marker sono nel commit `HEAD` del repo `bashscripts` (merge `laraxot/dev` del 2026-10-09 committato senza risolvere): per esempio `16-git.md` ne ha 4 nel `HEAD`. In alcuni file i marker cadono dentro il frontmatter (riga 3).
- 56 file su 85 hanno marker sbilanciati o annidati (merge sopra merge). 188 conflitti in tutto: 46 divergenti, 61 con il lato sinistro vuoto, 50 con il destro vuoto, 30 con un lato contenuto nell'altro.
- `audit-prompts.sh` dichiarava 0 bloccanti sui marker: guardava solo i fence bash (`EATEN-MARKER`).

## Fatto (verificato)
- `bashscripts/quality-gates/audit-prompts.sh`: nuova classe bloccante `CONFLICT-MARKER` (righe che iniziano con sette `<` o `>` seguiti da spazio; il solo `={7}` e' escluso perche' e' anche la sottolineatura di un titolo). Ora segnala i prompt con conflitti (33 prima della correzione, 27 dopo).
- Risolti i 19 file con conflitti meccanici (un lato vuoto, identico o contenuto nell'altro, marker bilanciati): 6 prompt, 3 memorie, 10 file di Xot e UI. Regola: si tiene il lato che contiene l'altro, quindi nessuna perdita. Controllo: 60 righe di marker tolte, 0 righe di contenuto perse, 0 marker residui, frontmatter in testa intatto. Copie degli originali nello scratchpad della sessione.
- Prompt 55: corretto (altra sessione lo aveva gia' riscritto): esempio di pattern `^{7}` sbagliato, posizione delle story in contrasto con la policy, verifica che non copriva i repo separati (`git -C <repo> diff --check`), `qmd update` generico al posto dello script del progetto, `role` aggiunto.

## Da fare (66 file, richiedono una scelta tra le due versioni)
- `bashscripts/ai/wiki/how-to` (1): `git-merge-marker-sweep.md`
- `bashscripts/ai/wiki/memories` (14): `INDEX.md`, `MEMORY.md`, `bmad-story-then-dev-story-mandatory.md`, `committed-markers-invisible-to-git-status-8char.md`, `contract-suffix-no-interfaces-folder.md`, `export-xls-ratings-titles-filament-native.md`, `gitmodules-sync-quality-gates-run.md`, `laraxot-wipe-still-active-plus-unclaimed-merge-2026-09-01.md`, `merge-remote-repo-2-reinjects-conflict-markers.md`, `module-theme-root-max-six-md.md`, `pest-gate-mandatory-standing-order.md`, `phpstan-modules-swarm-session.md`, `phpstan-modules-zero.md`, `workspace-naming.md`
- `bashscripts/ai/wiki/rules` (8): `00-TRIGGER_MAP.md`, `agent-bootstrap-on-demand.md`, `bmad-v6-on-demand.md`, `committed-conflict-markers.md`, `module-theme-root-hygiene.md`, `module-theme-root-md-files-limit.md`, `no-git-lfs.md`, `tests-auditcoverage-forbidden.md`
- `bashscripts/ai/wiki/second-brain` (1): `phpstan-journey.md`
- `bashscripts/docs/prompts` (27): `01-architecture-patterns.md`, `02-controller-to-folio-actions.md`, `04-datas-not-dtos.md`, `04b-controller-to-folio-actions.md`, `05-livewire-to-filament-widgets.md`, `05-widget.md`, `06-filament-audit.md`, `06-testing-standards.md`, `07-contracts.md`, `08-playwright-ui.md`, `10-pest-xot-base-test.md`, `10-ponytail-audit.md`, `11-phpinsights.md`, `13-path-and-naming-rules.md`, `14-module-dependency-direction.md`, `16-git-forward-only.md`, `16-git.md`, `17-ponytail.md`, `18-github.md`, `20-filesystem-before-assertions.md`, `28-livewire-to-filament-widgets.md`, `38-config-routes-views.md`, `39-composer-dependencies.md`, `44-module-docs-continuous.md`, `57-second-brain-operating-model.md`, `57-second-brain.md`, `second-brain-master.md`
- `laravel/Modules/Xot/docs/bmad/stories` (15): `2026-10-06-phpstan-modules-level10.story.md`, `5.260-phpstan-modules-current-remediation.story.md`, `5.262-docs-consolidation-modules-20261006.story.md`, `5.301-psr-4-autoload-compliance-fleet.story.md`, `8.24-merge-conflict-resolution-fleet-swarm.story.md`, `docs-100-fleet-merge-20261005.story.md`, `docs-consolidation-orphan-files.story.md`, `fix-navigationicon-violations.story.md`, `fleet-blocking-merge-conflict-markers-20260929.story.md`, `phpstan-fleet-remediation-2026-10-06.story.md`, `phpstan-l10-job-media-notify-20261006.story.md`, `phpstan-ptv-incentivi-indennitaresponsabilita-20261006.story.md`, `swarm-phpstan-modular-docs-org.story.md`, `xot-base-resource-navigation-icon-translation.story.md`, `xotbase-navigationicon-rule.story.md`

Metodo: per ogni file leggere i due lati, tenere cio' che e' piu' recente e coerente con la regola canonica (`bashscripts/ai/wiki/rules/`), unire le parti complementari, togliere i marker; mai rollback (Git forward-only). Gruppi indipendenti, adatti a agenti in parallelo (massimo 3 per volta: limite di sessione). Dopo ogni gruppo: `bash bashscripts/quality-gates/audit-prompts.sh` e `rg -n '^(<{7}|>{7}) '` sul gruppo.

## Criteri di accettazione
- [x] Il gate segnala i marker di merge (prova: 33 prompt segnalati prima della correzione).
- [ ] `audit-prompts.sh` senza bloccanti (oggi 27 prompt).
- [ ] `rg -n '^(<{7}|>{7}) '` senza risultati nelle cartelle docs, wiki, prompt.
- [ ] Il repo `bashscripts` e i moduli hanno il commit di risoluzione (richiede la richiesta esplicita dell'utente).

## Decisione aperta
Il prompt 55 vieta date nei nomi file senza eccezioni per le story; `bashscripts/docs/modular-bmad-story-policy.md` prescrive `YYYY-MM-DD-<slug>.story.md` e vieta di rinominare le story esistenti; 877 file hanno una data nel nome. Finche' l'utente non decide, i file esistenti non si rinominano e le story nuove seguono la policy della cartella in cui nascono.

## Aggiornamento 2026-10-14 (sera)
- **Causa trovata**: lo script `bashscripts/git/subtrees/merge_remote_repo_2.sh laraxot` committa l'albero sporco come «.» e fa `git merge laraxot/dev -s resolve` in ogni repo; i conflitti restano nei file con etichette `.merge_file_XXXX` e, in `Modules/Cms`, il processo e' rimasto fermo in un editor su `MERGE_MSG`. Memoria: `bashscripts-sync-merge-leaves-conflicts-and-hangs-in-editor`.
- Alle 15:04 il merge in corso in `bashscripts` (MERGE_HEAD 289bf0cb) ha riportato i marker in 10 prompt, compresi 6 che avevo risolto: nuovo conteggio del gate 9 prompt con `CONFLICT-MARKER` (era 10 con il 55). Risolto `55-md-conventions.md`, unendo la versione in arrivo (struttura, comandi di verifica che girano) con le correzioni mie: story secondo la policy BMAD invece di una regola che la contraddiceva, niente frasi «Nuovo:» (il prompt vieta il changelog), niente em dash, controllo dei marker, verifica QMD generica. I 9 prompt rimasti (09-migrations, 09-migrations-forward-only, 21-translations, 23-optimize, 26-actions-architecture-audit, 34-factories-seeders-audit, 35-policies-permissions-audit, 98-daily-report, daily-report) appartengono al merge in corso: lasciati a chi lo chiude, per non scrivere sopra il suo lavoro.
- Il comando di verifica del prompt 55 ha trovato difetti veri: nomi non conformi (`PLUGIN_INVENTORY.md`, `97-YAML-FRONTMATTER-CONVENTION.md` maiuscolo, citato da molti file: non rinominato) e `qmd` assente o frontmatter duplicato in `58-plugins-and-skills.md`, `98-daily-report.md`, `daily-report.md`.
- I 39 `.md` con marker fuori dal merge (24 in `bashscripts/ai/wiki`, 15 in `laravel/Modules/Xot/docs/bmad/stories`) sono stati affidati a due agenti: vedi l'esito sotto.

## Esito della pulizia (2026-10-14, sera)
Controllo indipendente dopo i due agenti (`rg --no-ignore -n '^(<{7}|>{7}) '` e `^={7}$` sui 39 file): **0 file con marker**, tutti con il frontmatter in testa (l'unica eccezione e' `bashscripts/ai/wiki/memories/MEMORY.md`, un indice). Backup degli originali nello scratchpad (`md55-backup3/`).
Scelte da riguardare, dichiarate dagli agenti: in `rules/module-theme-root-md-files-limit.md` e `memories/module-theme-root-max-six-md.md` vince la versione «max 6» (la «max 5» e' scartata, compresa la sezione «Nomi preferiti (fino a 5)»); in `rules/bmad-v6-on-demand.md` vince il lato con i percorsi esistenti su disco (`bashscripts/ai/.agents/...`) e si scarta quello con `.claude/skills/bmad` e `install-bmad-v6-project.sh`; `rules/00-TRIGGER_MAP.md` da 878 a 807 righe (varianti vecchie delle righe di tabella, contenute nelle tenute); nelle story di Xot il `status` e' scelto secondo il corpo (done, in-progress, draft). Aggiunto un frontmatter minimo a `workspace-naming.md` e `phpstan-journey.md`.
Restano: 9 prompt nel merge in corso (sopra), `laravel/Modules/Xot/LICENSE.md` (risolto: era una seconda copia identica del testo MIT con il segnaposto `:vendor_name <author@domain.com>`, il titolare del copyright resta da indicare, DCP-170), `laravel/Modules/UI/README.md` e `bashscripts/git/fix.md` (affidati a un terzo agente). I mirror in `docs/wiki/` indicati dall'agente (per esempio `workspace-naming.md`) non risultano piu' con marker nella scansione completa.
Difetti non da marker trovati e NON toccati: `committed-conflict-markers.md` righe 23-24 e 58 (marker mangiati in passato), `workspace-naming.md` righe 92-99 (righe duplicate), link a `bmad-method-v63.md` e `bmad/INSTALL.md` in `bmad-v6-on-demand.md` verso file inesistenti.

