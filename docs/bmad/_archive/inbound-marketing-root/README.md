---
title: "BMAD dossier — inbound marketing FixCity (blog + eventi)"
type: index
status: decision-input
updated: 2026-10-15
tags: [bmad, inbound-marketing, blog, eventi, afc, second-brain, ponytail, filament-blueprint]
qmd: "inbound marketing: blog ed eventi fixcity"
---

# Inbound marketing: blog ed eventi

Discovery BMAD + second brain + Ponytail + Filament Blueprint.
**Nessuna implementazione runtime** finché readiness = CONCERNS.

## Scopo (leggere per primo)

[purpose.md](./purpose.md) — fiducia → metodo → evento → adozione B2G; non ads.

## Verdict

**Sì** a blog ed eventi come canali inbound (pattern qualitativo
[AFC Formazione](https://www.afcformazione.it/)). Adattare al civico.
**Ponytail:** riuso `Article` + `ArticleResource`; niente modulo Eventi.
**Blueprint:** wiring form/table/query sui campi già in DB.

## Percorso

1. [purpose.md](./purpose.md)
2. [afc-formazione-pattern-analysis.md](./afc-formazione-pattern-analysis.md) — studio AFC (home/blog/articolo/risorse)
3. [recommendation.md](./recommendation.md)
3. Note di ricerca in footer-sponsorship (non duplicate):
   - [../footer-sponsorship/inbound-marketing.md](../footer-sponsorship/inbound-marketing.md)
   - [../footer-sponsorship/inbound-deep-study.md](../footer-sponsorship/inbound-deep-study.md)
   - [../footer-sponsorship/inbound-marketing-plan.md](../footer-sponsorship/inbound-marketing-plan.md)
4. [decision-log.md](./decision-log.md)
5. [readiness-report.md](./readiness-report.md)

## Filament Blueprint + Ponytail

| Artefatto | Percorso |
|---|---|
| Blueprint | [Blog inbound-blog-events](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events.md) |
| Audit ladder vs codice | [ponytail-audit](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events-ponytail-audit.md) |

Gap verificato: model/DB ok; form/table non espongono ancora `type`/date;
`ThemeArticleQueries::comingSoon` non filtra `type=event`.

## Second brain (moduli/temi)

| Dove | Doc |
|---|---|
| Blog | [inbound-marketing.md](../../laravel/Modules/Blog/docs/inbound-marketing.md) · [afc-pattern-adaptation.md](../../laravel/Modules/Blog/docs/afc-pattern-adaptation.md) |
| Cms | [inbound-marketing-hub-risorse.md](../../laravel/Modules/Cms/docs/inbound-marketing-hub-risorse.md) |
| Sixteen | [inbound-marketing-blog-eventi.md](../../laravel/Themes/Sixteen/docs/inbound-marketing-blog-eventi.md) |
| Meetup | [inbound-events.md](../../laravel/Themes/Meetup/docs/inbound-events.md) |
| Wiki | [inbound-marketing-purpose](../../docs/wiki/concepts/inbound-marketing-purpose.md) · [inbound-marketing-blog-eventi](../../docs/wiki/concepts/inbound-marketing-blog-eventi.md) |
| Sostenibilità | [../project-sustainability/README.md](../project-sustainability/README.md) |
