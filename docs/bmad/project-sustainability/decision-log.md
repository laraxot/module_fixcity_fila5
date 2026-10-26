---
title: "Decision log — project sustainability"
type: decision-log
updated: 2026-10-15
status: draft
tags: [docs, fixcity, bmad]
qmd: "decision log fixcity"
---

# Decision log

## 2026-10-15 — Modello a strati, non advertising

- **Decisione proposta:** P0 B2G/co-funding; P1 grant + partner; P2 membership/
  donazioni; P3 sponsor; **No** banner ad-tech nel FO.
- **Perché:** allinea FixCity a pattern civic-tech/OSS PA; protegge fiducia.
- **Stato:** proposta, richiede product/tenant owner.

## 2026-10-15 — Docs nei moduli come second brain

- **Decisione:** ogni pezzo di ownership vive nel `docs/` del modulo/tema
  competente; il dossier BMAD è l’indice di coordinamento.
- **Perché:** DRY + confini nwidart; evita un monolite docs root sul fundraising.

## 2026-10-15 — Nessuna implementazione in questa fase

- **Decisione:** solo documentazione BMAD + second brain.
- **Perché:** mancano beneficiario, policy, listino e approvazione owner.

## 2026-10-15 — Docs distribuiti moduli/temi allineati

- **Decisione:** SSoT in `laravel/Modules/Fixcity/docs/bmad/project-sustainability`;
  (ex `bmad-output/` — vietato); puntatori
  `sustainability-and-funding.md` + ownership `project-sustainability-*.md`
  in Fixcity, Cms, Tenant, User, Gdpr, Media, Xot, Sixteen; Blog/Notify/Meetup
  collegati via inbound e ownership map.
- **Perché:** second brain on-demand per dominio; evita duplicare il dossier.
- **Stato:** discovery; readiness CONCERNS; nessuna implementazione.
