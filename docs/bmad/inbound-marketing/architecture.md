---
title: "Architecture — inbound marketing FixCity"
type: architecture
status: proposed
updated: 2026-10-15
tags: [bmad, architecture, inbound-marketing]
qmd: "inbound architecture Blog Cms Seo Sixteen Meetup Gdpr Notify ownership"
issues: []
discussions: []
---
# Architecture

## Decisioni

| Capability | Owner | Regola |
|---|---|---|
| Post/eventi | Blog | riusare `Article` e `ArticleResource`, `content_type=post|event` |
| Hub/sezioni | Cms | comporre contenuti pubblicati e JSON esistenti |
| Metadati | Seo | derivare Article/Event dai record pubblici |
| Presentazione | Sixteen/Meetup | solo view/assets, nessuna regola dominio |
| Consenso | Gdpr | finalità e revoca separate |
| Follow-up | Notify | deferred finché privacy e governance non sono pronte |

## Flusso

`editor → Article draft → review → publish → Cms/tema → strumento/evento → adozione`

Il footer/header rimane JSON-driven tramite `<x-section slug="...">`; nessun tema
hardcoda slogan, link o sponsor.

## Non obiettivi

Nessun modulo Eventi, tabella duplicata, tracking cross-site, paywall o lead scoring
nel pilota.

## NFR

WCAG 2.2 AA, localizzazione, tenant isolation, privacy-by-design, auditabilità e
PHPStan/Pest sui confini che verranno implementati.
