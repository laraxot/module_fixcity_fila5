---
title: "FixCity go-live gap register"
type: bmad-go-live-register
status: no-go
updated: 2026-10-14
---

# Stato go-live

Decisione corrente: **NO-GO**. Il codice locale e alcune verifiche statiche non
sono sufficienti per mettere online il servizio. La release candidate deve avere
un ambiente isolato, dati tenant approvati, evidenze operative, test end-to-end,
contenuti legali approvati e un go/no-go firmato.

| Priorità | Gap residuo | Evidenza richiesta | Story proprietaria/coordinamento | Stato |
|---|---|---|---|---|
| P0 | Ambiente candidato riproducibile, HTTPS, config tenant, migrazioni, storage e rollback | manifest + deploy/rollback riusciti | 9.1 | open |
| P0 | Queue, SMTP, failed jobs, retry, alerting e runbook | delivery matrix + drill | 9.2 / 4.1–4.2 | open |
| P0 | Privacy/Gdpr/legal copy, retention, DPO, disclaimer/terms e cookie approvati | documenti localizzati + approvazione | 9.3 / 6.3–6.17 | owner-blocked |
| P0 | Backup cifrato e restore isolato | hash, timestamp, restore verificato | 9.2 / 5.2 | open |
| P0 | Vertical slice citizen A/B + PA con denial/error paths | Pest/browser su candidato | 9.4 / 2.1–3.3 | partial |
| P0 | WCAG 2.1 AA sui percorsi critici, upload, mappa, focus e screen reader | checklist + axe + test manuali | 9.4 / 6.1, 8.5 | partial |
| P0 | Tenant isolation, media privacy, retention, rate limit, redazione errori | security/privacy evidence | 9.5 / 5.1 | open |
| P1 | SLA/work-order semantics | decisione PA e workflow | 3.2 / G-07 | open |
| P1 | Open311 e deduplicazione | decisione esplicita build/defer/reject | G-08/G-09 | deferred, decision required |
| P1 | Moduli/theme e documentazione canonica | catalogo e owner indexes | 1.1–1.3, 6.2 | partial |
| P0 | Release sign-off | evidence index, approvazioni e rollback | 9.6 / 7.1–7.2 | blocked |

## Regola di chiusura

Un gap è chiuso solo con evidenza sul candidate identificato. `pass` locale,
file presente, screenshot isolato o testo non approvato non chiudono un gate.
Un blocker P0 aperto mantiene `status: no-go`.

## Limiti

Non sono stati inventati titolare, DPO, basi giuridiche, retention, SMTP, credenziali,
endpoint o decisioni di prodotto. Questi valori devono arrivare dai rispettivi owner.
