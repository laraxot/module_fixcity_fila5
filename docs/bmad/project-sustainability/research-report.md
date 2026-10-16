---
title: "Research — sostenibilità FixCity"
type: research-report
status: decision-input
updated: 2026-10-15
tags: [bmad, sustainability, civic-tech, oss, pa]
---

# Research report — come sostenere FixCity

## Domanda

Quali modelli di finanziamento e partnership sono realistici per FixCity come
piattaforma di segnalazioni civiche multi-tenant in Italia/UE, senza erodere
fiducia, accessibilità e neutralità del servizio?

## Sintesi

Il pattern dominante per software pubblico riusabile non è l’advertising sul sito
cittadino, ma:

1. finanziamento pubblico iniziale + manutenzione condivisa;
2. **co-funding comunale** (più enti pagano in base a uso/dimensione);
3. grant e bandi per innovazione;
4. ecosistema di fornitori/servizi intorno al codice aperto;
5. recognition partner con policy, non inventorio banner.

## Modelli osservati

### Developers Italia / AgID (IT)

Community e catalogo OSS per la PA, partiti con funding pubblico nazionale, poi
manutenzione mista (volontari + gare). Segnale: la sostenibilità passa da
**commit istituzionale** e riuso obbligato/incentivato, non da ads.

Fonte: [OSOR — Developers Italia case studies](https://interoperable-europe.ec.europa.eu/collection/open-source-observatory-osor/document/developers-italia-case-studies-sustainability-public-sector-open-source-communities)

### Co-funding municipale (EU)

Casi come Signalen (NL) e eVaka (FI): più città condividono costi di sviluppo e
esercizio. Raccomandazione OSOR: diversificare i fondi, pianificare manutenzione
dal giorno zero, stewardship chiara.

Fonte: [OSOR funding opportunities](https://interoperable-europe.ec.europa.eu/collection/open-source-observatory-osor/funding-opportunities-open-source-software-projects-public-sector)

### Decidim / DPI municipale

Piattaforma governata come infrastruttura democratica della città: budget
ricorrente + governance partecipata. Segnale: **mission alignment > growth ads**.

Fonte: [CIVICUS funding models](https://publications.civicus.org/publications/from-platforms-to-commons/part-v-action-framework/funding-models-for-shared-infrastructure/)

### Pooling / OSPO locali

Dove il singolo comune non ha budget OSPO, ha senso un associazione o soggetto
condiviso che stewards il progetto.

Fonte: arXiv OSPO archetypes (2026) — pooling risorse tra enti locali.

## Implicazioni FixCity

| Insight esterno | Traduzione FixCity |
|---|---|
| Co-funding multi-comune | Pricing/contratto per tenant + eventuale consorzio |
| Grant ≠ esercizio | Grant per R&D; contratto per run |
| Stewardship early | Owner prodotto + roadmap manutenzione pubblica |
| Diversify | Mix B2G + grant + partner; no mono-dipendenza ads |
| Recognition ≠ endorsement | Footer/pagina partner con disclosure (vedi footer-sponsorship) |
| Document everything | Docs nei moduli = second brain di ownership |

## Vincoli già nel prodotto

- Multi-tenant per host (`Tenant`): ogni comune deve poter disattivare partner/CTA
- FO Design Comuni / WCAG: niente caroselli ads
- GDPR: ogni pagamento/donazione richiede titolare e base giuridica
- CMS JSON = contenuto; Sixteen = vestito (DCP-222)

## Limiti di questa research

Beneficiario legale, listino B2G, pipeline grant e elenco partner reali non sono
ancora definiti dal product owner. Questo report orienta; non chiude i contratti.
