---
title: "DCP-455: gli strumenti del gate di rilascio devono sopravvivere alla sync"
type: story
status: ready-for-decision
module: Fixcity
priority: high
created: 2026-11-12
updated: 2026-11-12
tags: [bmad, ecc, story, go-live-gate, sync, bashscripts, pest-scratch-db, f-03, f-04, f-09]
qmd: "go-live-gate production-checklist pest-scratch-db lg-design-scan heavy-slot cancellati merge sync 1af871d0c ripristino basi di prova DCP-455"
related:
  - ./dcp-154-gate-di-messa-in-produzione.story.md
  - ./dcp-200-registro-unico-delle-decisioni-aperte.story.md
  - ./dcp-377-controllo-qualita-giro-front-office-e-sync-git.story.md
  - ../../../../Themes/Sixteen/docs/stories/front-office-production-readiness-audit.story.md
  - ../../../../Themes/Sixteen/docs/stories/icon-sprite-deployability.story.md
  - ../../../../Themes/Sixteen/docs/plans/front-office-hardening-plan.md
  - ../../../../Themes/Sixteen/docs/how-to/front-office-anonymous-sweep.md
issues: []
discussions: ["https://github.com/laraxot/theme_sixteen_fila5/discussions/95"]
---

# DCP-455: gli strumenti del gate di rilascio devono sopravvivere alla sync

**Discussione (decisioni e alternative scartate):** [#95](https://github.com/laraxot/theme_sixteen_fila5/discussions/95), complementare a [#93](https://github.com/laraxot/theme_sixteen_fila5/discussions/93) (head SEO) e [#94](https://github.com/laraxot/theme_sixteen_fila5/discussions/94) (audit, area loggata).

Status: **ready-for-decision** (ripristino fatto; restano tre decisioni dell'utente).

## Fase BMAD

**Dev** (ripristino) + **Doc** (questa story) + **decisione aperta**. Origine: difetti `F-03`, `F-04` e `F-09` dell'audit
([story madre](../../../../Themes/Sixteen/docs/stories/front-office-production-readiness-audit.story.md)). Numero scelto dopo un controllo di collisione:
l'altra sessione ha riservato DCP-444..454, il mio primo numero (451) era dentro quel blocco.

## Richiesta

Utente: controllare integralmente il front office per andare in produzione, con il gate di messa in produzione come misura ([DCP-154](./dcp-154-gate-di-messa-in-produzione.story.md)).

## Cosa è successo

Il merge di sync `1af871d0c` (la sync fra le due macchine committa `"."` e adotta le cancellazioni dell'altro lato) ha **cancellato cinque strumenti** del repository annidato `bashscripts`,
tutti chiamati dal gate o dalla checklist:

| Strumento (sotto `bashscripts/tools/`) | A cosa serve |
|---|---|
| `release/go-live-gate.py` | il gate (calcola la radice dal proprio percorso) |
| `security/production-checklist.sh` | controlli di ambiente e sicurezza chiamati dal gate |
| `compliance/lg-design-scan.py` | scansione del design per `LG-*` |
| `pest-scratch-db.sh` | esegue Pest su una base sqlite di prova, senza toccare il DB di sviluppo |
| `heavy-slot.sh` | limitatore `flock` per un comando pesante alla volta (obbligatorio con carico 30-40) |

**Prova:** ripristinati **identici** (`cmp`) dal commit `f7a39cc23`; il gate è stato lanciato **due volte** (prima e dopo la ricostruzione dell'altra sessione) con
lo stesso esito, NON pubblicabile, 11 FAIL bloccanti.

Stato (aggiornato 2026-11-12): i cinque file sono **tracciati, committati e pushati** nel repository annidato `bashscripts` (commit `c79a9e893`, branch `dev`, identici al remoto; verificato con `git ls-files --error-unmatch` e `git diff laraxot/dev`). Li ha committati la sync fra le due macchine, non una richiesta esplicita dell'utente né questa sessione. *(Correzione: la versione di stamattina diceva «non committati e non tracciati».)* Essere tracciati **non** li protegge: la stessa sync ha già adottato cancellazioni in passato, per questo restano la decisione 2 e il test di esistenza.

## Decisioni aperte (utente)

| # | Decisione | Opzioni | Proposta |
|---|---|---|---|
| 1 | **`pest-scratch-db.sh`: tenere o togliere** | (a) tenerlo e committarlo; (b) toglierlo e lanciare il gate con `--skip-pest` | **(a)**: lo stadio Pest del gate lo chiama e i test Pest dell'altra sessione lo usano. Va però ricordato che una nota del second brain (2026-10-28) dice che era stato tolto apposta: la ragione di allora non è stata riverificata, per questo la decisione è dell'utente |
| 2 | **Come proteggerli dalla sync** | (a) test da file nel modulo Fixcity che fallisce se uno dei cinque manca; (b) regola della sync: non adottare cancellazioni dentro `bashscripts/tools/` senza revisione; (c) solo committarli | **(a) + (c)**. (b) tocca lo script di sync: va deciso a parte |
| 3 | **Quando committare** | su richiesta esplicita, dopo aver controllato `/tmp/fixcity-git-sync.lock.d` | nessun commit finché l'utente non lo chiede |

## Seconda perdita: la suite di parità visiva (26 file, 2026-10-28)

**Misurato il 2026-11-12** (sessione Claude, richiesta «controlla la parità Sixteen / Design Comuni»): `bashscripts/tools/visual/` contiene solo `scratch-auth-fixture.sh` (ripristinato) e `fo-sweep.mjs` (nuovo). Il resto della suite su cui poggia l'epic DCP-00 (gate di parità, confronto pixel e HTML) **non c'è più nell'albero**.

| Fatto | Evidenza |
|---|---|
| Commit che li toglie | `eb98b388e` del 2026-10-28 12:51, repo annidato `bashscripts`, messaggio `.` (sync): **40.745 file** toccati (39.707 aggiunti, 206 cancellati, 800 modificati), cioè un mirror, non una pulizia mirata |
| File cancellati sotto `tools/visual/` (26) | `parity-gate.sh`, `parity-sweep.sh`, `parity-wizard-steps.sh`, `compare-body-html.mjs`, `compare-computed.mjs`, `compare-screenshots.mjs`, `compact-markup.mjs`, `dom-diff-after-click.mjs`, `explain-style.mjs`, `extract-used-css.mjs`, `find-important-overrides.mjs`, `derive-dc-chrome-css.py`, `outline-geometry.mjs`, `keyboard-audit.mjs`, `wcag-aa-check.mjs`, `a11y-page-probe.mjs`, `guest-fo-smoke.mjs`, `guest-fo-http-smoke.sh`, `fo-session.mjs`, `fo-probe-comments.mjs`, `fo-comment-flow-probe.mjs`, `geo-picker-centering-probe.mjs`, `net-hosts-probe.mjs`, `wizard-summary-photos-probe.mjs`, `map-popup-contrast.cjs`, `scratch-auth-fixture.sh` (quest'ultimo tornato) |
| Come si verifica | `git -C bashscripts log --diff-filter=D --name-only -- tools/visual/` |
| Nessuna copia altrove | `find bashscripts -name parity-gate.sh` non trova nulla |
| Cosa ho fatto | **Non li ho rimessi nel repository.** Ne ho estratti 9 da `eb98b388e^` in una cartella di lavoro fuori dal repo (scratchpad della sessione) e li ho usati per il confronto; ho scritto lì due sonde nuove (stato responsive e bande dei breakpoint) |

**Perche' conta:** la guardia di esistenza proposta qui (decisione 2a) copre i cinque strumenti del gate, non questi 26: la prossima sync puo' ripetere la perdita e nessun test lo direbbe. Le note del second brain parlano ancora di `bashscripts/tools/visual/parity-gate.sh` come strumento vivo.

| # | Decisione | Opzioni | Proposta |
|---|---|---|---|
| 4 | **Ripristinare la suite di parità visiva** | (a) rimetterla in avanti con `git show eb98b388e^:tools/visual/<file>` e proteggerla con la guardia della decisione 2a; (b) riscrivere solo cio' che serve (gia' fatto: `fo-sweep.mjs`) e togliere i riferimenti dalle note; (c) lasciare com'e' | **(a)** per `parity-gate.sh`, `compare-*.mjs`, `compact-markup.mjs`, `explain-style.mjs`: servono a ogni nuova pagina del riferimento. Il resto (probe di una pagina specifica) solo se serve. La decisione e' dell'utente: il mirror del 28/10 potrebbe aver tolto i file apposta |

## Basi di prova rimaste (F-04)

Dopo i giri del gate restano **8 basi di prova** (sqlite scratch). Non le ho cancellate: la pulizia è un'azione su dati e aspetta conferma.
Dopo la decisione 1, lo strumento dovrebbe ripulirle da sé a fine giro.

## Cosa NON è in questa story

- I FAIL di ambiente del gate (`ENV-*`, `NET-01`): deploy, [DCP-154](./dcp-154-gate-di-messa-in-produzione.story.md).
- `REPO-02`: DCP-201 (DEC-51).
- La D-7 (canonical/hreflang): story Cms di chiusura.

## Accettazione

- [x] I cinque strumenti ripristinati identici al commit `f7a39cc23` (`cmp`).
- [x] Gate lanciato due volte con esito identico (NON pubblicabile, 11 FAIL bloccanti, tutti spiegati nella story madre).
- [ ] Decisione 1 registrata nel registro DCP-200 (`DEC-62` e seguenti) con data.
- [ ] Test da file nel modulo (`Modules/Fixcity/tests/Unit`), rosso su una copia senza uno dei cinque file, poi verde.
- [x] Strumenti committati nel repository annidato: `c79a9e893`, pushati, identici al remoto (2026-11-12). Fatto dalla sync, non su richiesta: la decisione 3 e' superata dai fatti.
- [ ] Le 8 basi di prova rimosse (solo con conferma).

## Lezioni

1. Una sync che adotta le cancellazioni dell'altro lato può togliere strumenti di rilascio **senza far fallire nulla**: il gate semplicemente non parte.
2. Dopo ogni sync grossa controllare che gli strumenti citati nei documenti esistano ancora.
3. Una guardia saltata non è verde: con `--skip-pest` il controllo `LEG-01` resta rosso per costruzione.
