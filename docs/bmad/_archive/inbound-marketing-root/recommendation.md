---
title: "Raccomandazione — blog ed eventi inbound"
type: recommendation
status: decision-input
updated: 2026-10-15
tags: [docs, fixcity, bmad]
qmd: "raccomandazione fixcity"
---

# Raccomandazione

Scopo: [purpose.md](./purpose.md).

## Verdict

**Mettere blog ed eventi nel piano inbound è corretto** (AFC: problema → metodo
→ strumento → relazione), adattato al civico. Completa sostenibilità a strati;
non sostituisce B2G/grant.

## Priorità

| Iniziativa | Priorità |
|---|---|
| Blog evergreen PA/cittadino | **P0** |
| Eventi pilota (webinar + laboratorio) | **P0** |
| Hub risorse poche | P1 |
| Serie email | P2 (Gdpr+Notify) |
| Banner/ads | **No** |

## Ponytail + Blueprint

1. Un `Article` con `type` blog|event — no modulo Eventi
2. Estendere `ArticleResource` (form/table) — no Resource nuova
3. Query FO: filtrare anche `type=event`
4. CTA: info → evento → adotta; sostieni solo post-governance

Dettaglio gap codice: [ponytail-audit](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events-ponytail-audit.md).

## Evidenza AFC (home + blog + risorse)

Dettaglio: [afc-formazione-pattern-analysis.md](./afc-formazione-pattern-analysis.md).

- Home = metodo + diagnosi + lead magnet soft + ecosistema
- Blog = titoli sul comportamento concreto
- Risorse = poche, ordinate per bisogno (diagnosi/applicazione/approfondimento)
- Eventi FixCity = equivalente relazionale dei briefing: sincronizzano adozione

## Gate

Discovery **sì**. Codice Filament solo post readiness GO + Blueprint.
Niente banner, ad-tech, newsletter forzata, modulo Eventi duplicato.
