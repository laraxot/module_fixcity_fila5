---
id: dcp-275
title: "DCP-275: credenziali in chiaro nei docs e file .env tracciati in git"
type: story
epic: go-live-security
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 275 password root in chiaro docs env tracciato matomo token app key segreti rotazione scansione"
related: [dcp-139, dcp-202, dcp-208]
tags: [sicurezza, segreti, docs, go-live, bmad]
---

# DCP-275: credenziali in chiaro nei docs e file .env tracciati in git

## Fase BMAD
Analisi (misure) e Dev (oscurare il testo nei docs, guardia permanente). La rotazione dei segreti e' un'azione dell'utente, non dello sviluppo.

## Fatti misurati (2026-10-26)
Nessun valore e' riportato qui: si elencano solo posizioni e confronti per hash.

1. **Password di root della macchina di sviluppo, in chiaro in 10 righe di docs tracciati** («Root password used: ... (provided by user)» e due righe nei documenti
   «missing work» creati oggi):
   - `docs/SYSTEM_ADMIN_SUMMARY_2026.md:287`
   - `docs/second-brain/project/missing-work-index.md:60` e `docs/second-brain/project/bmd-brainstorming-remaining-work.md:162`
   - sette copie di uno stesso riepilogo in `Modules/Notify/docs/`: `system-admin-summary-administration.md:306`, `system_admin_summary.md:306`,
     `SYSTEM_ADMIN_SUMMARY.md:306`, `bmad/architecture/system-admin-summary.md:307` e tre `.md.old`.
   **Password del database di sviluppo, in chiaro in 5 righe** di `docs/lamp/` (`MARIADB_SETUP.md:39,66`, `README.md:56`, `HEIDISQL_CONNECTION.md:10,24`): lo sha1 del
   valore coincide con quello di `DB_PASSWORD` nel `.env` in uso, quindi non e' un esempio ma la password vera.
2. **Due file `.env` tracciati in git** (`laravel/.env.fixcity`, `laravel/.env.latest`; `.gitignore` copre solo `laravel/.env`). Contengono `APP_KEY`,
   `DB_PASSWORD`, `DB_PASSWORD_USER`, `MAIL_PASSWORD` valorizzati e un `MATOMO_AUTH_TOKEN` di 32 caratteri. Il token e' **identico** a quello del `.env`
   in uso (confronto sha1: stesso hash). La `APP_KEY` dei due file tracciati e' diversa da quella del `.env` locale (non si sa se sia quella di produzione).
3. Il repository ha un remote GitHub (`laraxot/base_fixcity_fila5`). **Non verificato** se e' privato.
4. Scansione di altri formati noti (AWS `AKIA`, GitHub `ghp_`/`github_pat_`, Slack `xox`, `sk-`, Daytona `dtn_`, Google `AIza`, chiavi private PEM) su docs di
   radice, moduli, temi e `bashscripts/docs`: **nessuna chiave reale**. Un solo riscontro, `GITHUB_ACTION_SETUP_REQUIRED.md` (quattro copie), e' testo che nomina
   «-----BEGIN OPENSSH...» come istruzione, non una chiave.

## Causa
Un riepilogo di amministrazione di sistema ha copiato in un file la password che l'utente aveva fornito in chat, e il file e' stato duplicato e committato
(sette copie nel solo Notify). Due file `.env` di lavoro sono stati committati perche' non corrispondono al nome ignorato (`.env`). Non c'e' una guardia che
cerchi credenziali nei docs: la regola «mai segreti in git» e' scritta, non verificata.

## Decisioni
- **Tecnica (procede):** oscurare la riga della password nei 10 file; guardia permanente che fallisce se un `.md` contiene una password tra apici o backtick
  dopo «password»; i `.env` tracciati non si toccano da qui (vedi sotto).
- **Dell'utente (non anticipare):**
  1. cambiare la password di root: va considerata esposta, perche' resta nella storia git (git e' solo in avanti: la storia non si riscrive);
  2. ruotare il token Matomo;
  3. decidere se togliere dall'indice `laravel/.env.fixcity` e `laravel/.env.latest` (`git rm --cached` + riga in `.gitignore`, con un commit dell'utente) e se la loro
     `APP_KEY` e' mai stata usata in produzione (in quel caso rigenerarla invalida sessioni e cookie cifrati);
  4. dire se il remote GitHub e' privato.

## Criteri di accettazione
- [x] Nessuna riga dei 15 punti (10 della password di root, 5 della password del database) contiene piu' la credenziale: grep con conteggio 0.
- [x] Una guardia automatica fallisce se riappare una password in chiaro in un `.md` dei docs: `Modules/Xot/tests/Unit/Architecture/NoPlaintextCredentialsInDocsTest.php`.
- [ ] Password di root e del database cambiate, token Matomo ruotato, destino dei due `.env` deciso (utente).

## Verifica (2026-10-26, DB di prova)
- Oscuramento: sostituzione limitata alle 15 righe esatte, ogni riga con una sola corrispondenza (altrimenti lo script si fermava); poi `grep` sul
  regex delle credenziali nei docs: **0** corrispondenze su 33.947 file `.md`.
- Guardia, verde: `1 passed`, exit 0 (`pest-scratch-db.sh`, 2,7 s su 33.947 file).
- Guardia, **prova inversa**: con un file temporaneo `docs/zz-credential-probe.md` contenente una finta «Root password used» il test e' diventato rosso
  (`1 failed`, exit 1) indicando `docs/zz-credential-probe.md:3`; il valore finto **non** compare nell'output. File tolto.
- PHPStan sul test: exit 0, nessun errore.
- Falsi positivi: i due regex, misurati su tutti i docs prima dell'adozione, davano 0 corrispondenze non-segnaposto dopo la pulizia.

## Limite della cura
Oscurare la riga non toglie il segreto dalla storia di git (i file erano tracciati e il remote e' GitHub). Finche' le credenziali non sono cambiate dall'utente
restano da considerare esposte.

## Non verificato
- Visibilita' del remote GitHub e validita' attuale delle credenziali.
- Se la `APP_KEY` dei file tracciati sia o sia stata di produzione.
- File fuori da `docs/`, `Modules/*/docs`, `Themes/*/docs`, `bashscripts/docs` (script, fixture, config) non scansionati con questo criterio.
