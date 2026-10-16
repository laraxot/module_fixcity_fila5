---
title: "BMAD dossier — inbound marketing FixCity (blog + eventi)"
type: index
status: decision-input
updated: 2026-10-16
tags: [bmad, inbound-marketing, blog, eventi, afc, second-brain]
---

# Inbound marketing: blog ed eventi

Discovery BMAD + second brain + Ponytail + Filament Blueprint.
**Nessuna implementazione runtime** finché readiness = CONCERNS.
**Prima di abilitare FO:** studio Design Comuni
[design-comuni-static-pages-study.md](../design-comuni-static-pages-study.md)
+ gate 2026-10-16 [design-comuni-study-gate.md](./design-comuni-study-gate.md)
(nav JSON: Novità=`/blog`, Eventi=`/events` già in header/footer;
Ponytail: no modulo Event; demo `DemoContentSeeder`).

## Scopo

Leggere [purpose.md](./purpose.md): fiducia → metodo → evento → adozione B2G,
non pubblicità.

Domanda: per far conoscere e adottare FixCity servono blog ed eventi come canali
inbound (sul modello qualitativo di [AFC Formazione](https://www.afcformazione.it/))?

## Verdict

**Sì — blog ed eventi sono il canale inbound giusto**, meglio dei banner.
Adattare i *pattern* AFC (promessa verticale, metodica, risorse poche e utili,
CTA progressive, blog tematico) al contesto civico: trasparenza, WCAG, GDPR,
multi-tenant. **Non** copiare WordPress, Elementor, tracking o copy commerciale.

Gate: **GO discovery / CONCERNS implementazione** finché mancano owner
editoriale, calendario pilota, privacy iscrizione e test comprensione.

Ponytail: riusare `Article` + `ArticleResource`; Filament Blueprint significa
collegare form/table/query ai campi esistenti, senza migrazione o modulo Eventi.

## Percorso di lettura

0. [purpose.md](./purpose.md) — **perché** (fiducia → adozione; governa Ponytail)
0b. [editorial-plan-afc-aferrari.md](./editorial-plan-afc-aferrari.md) — **piano** 90gg da AFC+aferrari
1. [afc-formazione-pattern-analysis.md](./afc-formazione-pattern-analysis.md) — cosa fa AFC e cosa riusiamo
2. [afc-aferrari-asso-pattern-analysis.md](./afc-aferrari-asso-pattern-analysis.md) — confronto dei tre ecosistemi
3. [asso-pattern-deep-study.md](./asso-pattern-deep-study.md) — fiducia, adozione, playbook
4. [improvement-reasoning.md](./improvement-reasoning.md) — come migliorare FixCity
5. [recommendation.md](./recommendation.md) — GO/NO-GO e funnel FixCity
6. [product-brief.md](./product-brief.md)
7. [prd.md](./prd.md)
8. [ownership-map.md](./ownership-map.md)
9. [content-operations-plan.md](./content-operations-plan.md)
10. [epics.md](./epics.md)
11. [readiness-report.md](./readiness-report.md)
12. [decision-log.md](./decision-log.md)
13. [architecture.md](./architecture.md)
14. [ux-design.md](./ux-design.md)
15. [stories-index.md](./stories-index.md)
16. [sprint-status.yaml](./sprint-status.yaml)
17. [parallelization-plan.md](./parallelization-plan.md)
18. [quality-gates.md](./quality-gates.md)
19. [brainstorming-report.md](./brainstorming-report.md)
20. [tech-spec.md](./tech-spec.md) — post-governance; non autorizza codice

Studio IA Design Comuni: [design-comuni-static-pages-study.md](../design-comuni-static-pages-study.md).

Wiki: skill/rule/memory `fixcity-inbound-editorial*`.

Artefatti già presenti (mantenuti, non duplicati):

- Questo dossier è il punto canonico: non creare copie in `bmad-output`.
- Le decisioni su sostenibilità sono richiamate solo quando servono al gate;
  il piano inbound resta qui, nel modulo Fixcity.
- [Blog blueprint + Ponytail](../../../../Blog/docs/blueprints/inbound-blog-events.md)
- Sostenibilità: [project-sustainability](../project-sustainability/README.md)

## Docs di dominio (second brain)

| Dove | File |
|---|---|
| Blog | [inbound-marketing.md](../../../../Blog/docs/inbound-marketing.md) · [afc-pattern-adaptation.md](../../../../Blog/docs/afc-pattern-adaptation.md) |
| Meetup | [inbound-events.md](../../../../../Themes/Meetup/docs/inbound-events.md) |
| Sixteen | [inbound-marketing-blog-eventi.md](../../../../../Themes/Sixteen/docs/inbound-marketing-blog-eventi.md) |
| Cms | sezioni/nav verso blog-eventi (link da sustainability content) |
| Wiki root | [inbound-marketing-blog-eventi.md](../../../../../../docs/wiki/concepts/inbound-marketing-blog-eventi.md) |

## Wiki (skill / rule / memory)

- Skill: [fixcity-inbound-editorial](../../../../../../docs/wiki/skills/fixcity-inbound-editorial.md)
- Rule: [fixcity-inbound-editorial](../../../../../../docs/wiki/rules/fixcity-inbound-editorial.md)
- Memory: [fixcity-inbound-editorial-system](../../../../../../docs/wiki/memories/fixcity-inbound-editorial-system.md)
- [content-type-contract.md](./content-type-contract.md) — post agnostico vs Parental type
