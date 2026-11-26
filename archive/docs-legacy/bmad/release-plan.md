---
title: "FixCity — Release plan BMAD"
type: release-plan
status: active
module: Fixcity
created: 2026-09-26
updated: 2026-09-27
tags: [bmad, release, staging, runbook, fixcity, go-no-go]
qmd: "FixCity release readiness test vertical slice backup staging privacy"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - gap-analysis.md
  - workflows/09-release.md
  - workflows/06-quality-assurance.md
  - workflows/07-ui-ux.md
  - workflows/08-security.md
  - stories/STORY-FIXCITY-PRIVACY-PUBLICATION-GATE.md
  - ../../../../../docs/stories/STORY-495-platform-completion-evidence-and-closeout.md
---

# FixCity — piano verificabile di release

## Decisione corrente

**NO-GO per pilota/staging pubblico.** La baseline codice e test locali è buona, ma
mancano prerequisiti del titolare e prove di deploy: l'informativa tenant è un
segnaposto, il submit resta correttamente disabilitato, lo staging non è stato
verificato e le prove browser responsive della change privacy non sono eseguibili
nell'ambiente corrente. Non aggirare questi gate con copy legale fittizio, credenziali
condivise o modifiche ai dati/grant.

## Gate e prove attuali

| Gate | Stato | Evidenza | Chiusura richiesta |
|---|---|---|---|
| G1 — baseline codice/test | **PARZIALE** | Verifica corrente 2026-09-27: PHPStan `analyse Modules` zero errori; suite Fixcity completa verde su **SQLite e MariaDB: 399 passati / 1748 asserzioni** per driver. La suite Pest globale include ancora failure legacy fuori Fixcity. | Eseguire la candidata CI/staging su database isolato e chiudere o accettare formalmente i test legacy fuori Fixcity. Il rerun locale non equivale a prova staging |
| G2 — flussi cittadino/PA | **PARZIALE** | Feature/Livewire coprono ownership, creazione, assegnazione, transizioni, timeline, follow e tracking. Browser locale 2026-09-27: `demo-operator-panel-access.spec.js` PASS 1/1; l'operatore demo autentica e apre `/fixcity/admin/tickets` senza errori JS. Smoke di sola lettura, nessun ticket modificato | Smoke integrato su deploy con cittadino A/B e operatori autorizzati, inclusi allegati/notifiche, verifica di negazioni e ciclo di vita completo |
| G3 — UI/UX e accessibilità | **PARZIALE** | Runtime locale verificato il 2026-09-27. Playwright post-build: servizi 3/3 (IT/EN/DE/ES, 320/390/768/1440, link/tap target e ricerca); FAQ/sitemap 2/2 (stessa matrice, disclosure da tastiera e redirect guest localizzato); tracking form 1/1 (IT/EN/DE/ES, 320/390/575/576/768/1440, focus e Tab). Tutti i controlli eseguiti senza submit o mutazione ticket. `/it/privacy` rende Markdown demo tenant, non approvazione | Completare verifica manuale screen reader e QA attuale di lista, wizard e console PA; ripetere sul deploy candidato. Ottenere sign-off tenant per privacy |
| G4 — privacy e contenuti tenant | **BLOCCATO DA APPROVAZIONE/DECISIONE** | La route privacy funziona localmente (HTTP 200), ma testi e contatti tenant necessitano verifica e sign-off; G-31 lascia da definire se il checkbox è presa visione o consenso e quali prove conservare | Il titolare approva testi e contatti e definisce semantica/prova/retention; testare locale, wizard e pagina sul deploy dopo approvazione |
| G5 — dati, backup e ripristino | **PARZIALE** | Backup dei DB di test creati il 2026-09-27 con `mysqldump --single-transaction --routines --triggers --no-tablespaces`; restore riuscito in due database temporanei isolati, 85 tabelle dati + 31 tabelle user, poi rimossi. Hash e percorsi sono nel log Second Brain. | Ripetere su staging/candidato con dati approvati, retention, cifratura e runbook di ripristino |
| G6 — code, SMTP e monitoraggio | **NON VERIFICATO** | Retry `ShouldQueueAfterCommit` testati; worker, provider, `failed_jobs` e alert non provati | Verificare worker e SMTP sandbox, retry/esaurimento, retention, alert e runbook replay |
| G7 — go/no-go PA | **NON PRONTO** | Mancano prove di deploy e sign-off del titolare/owner PA | Allegare evidenze G1–G6 e raccogliere sign-off nominativi prima del pilota |

## Sequenza di chiusura

1. Owner del tenant: approvare l'informativa privacy e i contatti/accessibilità
   ufficiali; pubblicare i Markdown localizzati in `config/<tenant>/lang/{locale}/`.
2. Maintainer/DBA: fornire ambiente MySQL di test isolato e staging; senza alterare
   `.env.testing` con segreti condivisi o assegnare grant fuori approvazione.
3. QA: riprodurre G1 sulla candidata e completare smoke end-to-end per guest,
   cittadino A/B, operatore, supervisore e amministratore. Verificare anche negazioni.
4. Ops: provare backup/restore, queue worker, SMTP sandbox, failed jobs, alert e
   procedura incidenti; annotare timestamp, ambiente e output senza segreti.
5. UX: raccogliere screenshot responsive e controlli tastiera/accessibilità sulle
   pagine effettivamente rilasciate.
6. Product owner + PA: firmare go/no-go con le evidenze allegate e i gap accettati.

Ogni gate riaperto aggiorna [gap analysis](gap-analysis.md) e la story owner; nessuna
prova storica sostituisce un test della release candidata. Il pilota non parte finché
G4 non è approvato e i gate operativi non hanno owner/evidenza.
