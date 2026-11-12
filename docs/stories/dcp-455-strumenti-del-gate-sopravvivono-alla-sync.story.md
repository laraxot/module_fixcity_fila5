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

Stato: i cinque file sono **non committati e non tracciati** nel repository annidato. Finché restano così, la prossima sync può cancellarli di nuovo.

## Decisioni aperte (utente)

| # | Decisione | Opzioni | Proposta |
|---|---|---|---|
| 1 | **`pest-scratch-db.sh`: tenere o togliere** | (a) tenerlo e committarlo; (b) toglierlo e lanciare il gate con `--skip-pest` | **(a)**: lo stadio Pest del gate lo chiama e i test Pest dell'altra sessione lo usano. Va però ricordato che una nota del second brain (2026-10-28) dice che era stato tolto apposta: la ragione di allora non è stata riverificata, per questo la decisione è dell'utente |
| 2 | **Come proteggerli dalla sync** | (a) test da file nel modulo Fixcity che fallisce se uno dei cinque manca; (b) regola della sync: non adottare cancellazioni dentro `bashscripts/tools/` senza revisione; (c) solo committarli | **(a) + (c)**. (b) tocca lo script di sync: va deciso a parte |
| 3 | **Quando committare** | su richiesta esplicita, dopo aver controllato `/tmp/fixcity-git-sync.lock.d` | nessun commit finché l'utente non lo chiede |

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
- [ ] Strumenti committati nel repository annidato (solo su richiesta dell'utente).
- [ ] Le 8 basi di prova rimosse (solo con conferma).

## Lezioni

1. Una sync che adotta le cancellazioni dell'altro lato può togliere strumenti di rilascio **senza far fallire nulla**: il gate semplicemente non parte.
2. Dopo ogni sync grossa controllare che gli strumenti citati nei documenti esistano ancora.
3. Una guardia saltata non è verde: con `--skip-pest` il controllo `LEG-01` resta rosso per costruzione.
