---
title: "Scopo — perché inbound (blog + eventi) per FixCity"
type: purpose
status: decision-input
updated: 2026-10-15
tags: [bmad, inbound-marketing, purpose, philosophy, afc, ponytail]
qmd: "scopo inbound fiducia capacità adozione B2G blog eventi FixCity AFC"
issues: []
discussions: []
---

<!--
gateguard: supersedes thinner purpose of same path; SSoT for inbound WHY;
linked from Fixcity inbound README, Blog Blueprint, Ponytail audit, wiki purpose;
user verbatim: "cerca di capire a fondo lo scopo, poi fa tutto con bmad + second brain + ponytail + filament blueprint" (+ studio afcformazione.it blog/eventi)
-->

# Lo scopo (prima di ogni feature)

## Domanda vera

Non è «dobbiamo avere un blog?». Non è «dobbiamo avere eventi?».

È: **come fa FixCity a diventare comprensibile e adottabile senza vendere spazio
pubblicitario sul percorso del cittadino?**

La segnalazione funziona solo se esistono tre cose insieme:

1. **Fiducia** — il cittadino crede che qualcuno legga e agisca;
2. **Capacità** — il Comune sa usare backoffice e processi;
3. **Adozione** — altri enti pagano o co-finanziano la manutenzione.

Inbound (blog + eventi) costruisce **1 e 2** e **qualifica 3**. Non massimizza
pageview e non trasforma il FO in marketplace.

## Religione del canale (dogmi)

1. Il contenuto serve il **servizio pubblico**, non il contrario.
2. Prima si risolve un problema leggibile; poi si chiede una relazione.
3. La CTA proporzionata batte la CTA urgente («adotta» ≠ «dona ora»).
4. Trasparenza e accessibilità non sono optional di marketing.
5. Se un pezzo di codice non serve fiducia/capacità/adozione, è YAGNI.

## Cosa fa AFC che ci interessa (scopo, non skin)

Su [afcformazione.it](https://www.afcformazione.it/) lo scopo non è «avere
contenuti». È:

> far riconoscere un problema → offrire un metodo → dare uno strumento →
> invitarti a una relazione successiva.

Home: diagnosi «dove si rompe» + metodo a step + lead magnet soft.
Blog: titoli sul comportamento concreto + griglie applicabili.
Risorse: poche, ordinate per **bisogno** (diagnosi → applicazione →
approfondimento), non per formato file.

Analisi: [afc-formazione-pattern-analysis.md](./afc-formazione-pattern-analysis.md).

Per FixCity il «prodotto» successivo **non** è un corso a pagamento: è
**adozione B2G, collaborazione, grant** — e solo dopo, eventuale sostegno
economico trasparente (vedi la documentazione di sostenibilità del modulo Fixcity).

## Traduzione civica

| Intento AFC | Intento FixCity |
|---|---|
| Far avanzare il lavoro del team | Far avanzare la relazione cittadino–comune |
| Diagnosi «dove si rompe» | Dove falliscono segnalazioni, tempi, trasparenza |
| Briefing gratuiti | Guide Cms + eventi accessibili (email solo con consenso) |
| Formazione / consulenza | Webinar PA, laboratorio locale, one-pager adozione |
| Contatto commerciale | Contatto «collabora / adotta» (non «compra ads») |

## Perché blog **e** eventi (insieme)

| Canale | Ruolo nello scopo |
|---|---|
| **Blog** | Memoria + SEO: spiega una volta, scala, revisabile |
| **Eventi** | Relazione: sincronizzano persone, accountability |

Solo blog = silenzio relazionale. Solo eventi = conoscenza che evaporata.
FixCity collega **articoli ↔ eventi ↔ adozione**.

## Come lo scopo governa Ponytail + Filament Blueprint

| Scopo | Conseguenza tecnica |
|---|---|
| Serve contenuto blog+evento | Un solo dominio: `Article` |
| Non inventare piattaforme | **No** modulo Eventi / tabella duplicata |
| Editor distingue i due | Esporre `type` + date in `ArticleResource` |
| FO non mescola blog e calendario | Query eventi filtrano `content_type=event` |
| Fiducia > ads | Zero pixel, zero Action nurture, zero paywall |

Blueprint (piano, non codice):
[inbound-blog-events.md](../../../../Blog/docs/blueprints/inbound-blog-events.md)

Audit Ponytail vs codice:
[inbound-blog-events-ponytail-audit.md](../../../../Blog/docs/blueprints/inbound-blog-events-ponytail-audit.md)

### Gap codice confermati (ri-verificati 2026-10-15)

1. `ArticleFormMainGridSchema` — manca `Select type` e date evento condizionali
2. `ArticlesTable` — manca badge/filtro `type` (solo category/user)
3. `ThemeArticleQueries::comingSoon` / `paginatedComingSoon` — solo
   `content_type=event` e `event_start_date > now()`; **non** `Article.type`

Finché readiness = CONCERNS: **documentare il piano, non patchare il runtime**.

## Cosa lo scopo **proibisce**

- Inventorio pubblicitario / banner nel FO civico
- Paywall sulla conoscenza di base del servizio
- Sponsor evento come endorsement del prodotto
- Tracking comportamentale per CTA economiche
- Secondo dominio Eventi finché `Article` basta
- Resource Filament parallela o widget calendario BO (YAGNI pilota)

## Criterio di successo

> «Ho capito il problema, ho visto un caso, ho partecipato a un incontro,
> so chi contattare per adottare — e non mi sento venduto.»

Non vanity metric del blog né click su footer sponsor.

## Collegamenti

- [README.md](./README.md)
- [recommendation.md](./recommendation.md)
- [ownership-map.md](./ownership-map.md)
- [editorial-plan-afc-aferrari.md](./editorial-plan-afc-aferrari.md)
- Wiki: [inbound-marketing-purpose](../../../../../../docs/wiki/concepts/inbound-marketing-purpose.md)
- SSoT: [inbound-marketing/README.md](./README.md); non creare uno specchio in `bmad-output`.
