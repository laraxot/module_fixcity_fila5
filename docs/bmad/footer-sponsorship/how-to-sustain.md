---
title: "Come sostenere FixCity — mappa dei modelli"
type: bmad-strategy
status: decision-input
updated: 2026-10-15
tags: [bmad, sustainability, funding, civic-tech, fixcity]
---

<!--
gateguard: supersedes nothing (new BMAD strategy page alongside recommendation.md);
linked from README.md of this dossier and from Modules/*/docs/sustainability-and-funding.md;
user: "studia come potremmo sostenere il progetto e documenta tutto dentro le cartelle docs dentro i moduli e dentro i temi , fa tutto con bmad + second brain"
-->

# Come sostenere il progetto

> **SSoT economico completo:** [../project-sustainability/README.md](../project-sustainability/README.md)
> (recommendation, research, ownership map, docs di dominio).
> Questo file è il **ponte** verso footer-sponsorship e i puntatori
> `sustainability-and-funding.md` nei moduli/temi.


Documento di discovery (solo BMAD). Non autorizza codice, pagamenti, banner o
modifiche a `footer.json`. Risponde a: *con quali modelli FixCity può vivere nel
tempo senza tradire fiducia civica e Designers Italia?*

## Perché serve (business logic)

FixCity è un servizio di segnalazioni cittadine. La sostenibilità non è «vendere
spazio pubblicitario»: è garantire continuità operativa (hosting, accessibilità,
supporto Comuni, evoluzione prodotto) restando neutrale, trasparente e
multi-tenant. Chi finanzia deve essere riconoscibile; chi usa il servizio non
deve essere profilato né distratto da inventori ads.

## Ordine raccomandato dei modelli

| Priorità | Modello | Cosa è | Perché | Prerequisiti |
|---:|---|---|---|---|
| 1 | Adozione B2G | Il Comune adotta/paga il servizio | Allinea ricavo a missione | Contratto/SLA, onboarding tenant |
| 2 | Partner istituzionali | Ente/progetto riconosce collaboratori | Designers Italia ammette partner nel footer | Policy, diritti logo, opt-in tenant |
| 3 | Grant / finanziamenti | Fondi pubblici o filantropici | Alta fiducia, bassa pressione ads | Beneficiario, rendicontazione |
| 4 | Inbound (blog + eventi) | Contenuti e relazioni qualificano lead | Costruisce fiducia prima del denaro | Piano editoriale, privacy iscrizioni |
| 5 | Contribuzioni in-kind | Codice, UX, accessibilità, traduzioni | Costo zero, allinea community | CONTRIBUTING, code of conduct |
| 6 | Donazioni / membership | Contributo volontario a ente idoneo | Solo con trasparenza totale | ETS/beneficiario, ricevute, privacy |
| 7 | Sponsor commerciale | Recognition a pagamento | Ultima leva, eccezionale | Policy pubblica, contratto, no ad-tech |

## Cosa non fare (primo rilascio)

- Fila di banner commerciali permanenti nel footer globale.
- Pixel, cookie advertising, aste programmatiche, targeting.
- CTA «dona ora» senza beneficiario legale e rendicontazione.
- Marketplace di sponsor globali che ignora isolation multi-tenant.
- Endorsement implicito («approvato da») senza disclosure.

## Dove vive la narrazione (non il ricavo)

1. **Pagina dedicata** `/sostieni` o `/collabora` — modello economico, uso fondi,
   contatti, elenco partner, report.
2. **Footer** — recognition sobria «Partner e sostenitori» *oppure* CTA verso la
   pagina; mai inventario ads. Vedi [recommendation.md](./recommendation.md).
3. **Blog / guide** — problema → evidenza → CTA soft. Vedi
   [inbound-marketing.md](./inbound-marketing.md).
4. **Eventi** — sponsor dell'*iniziativa*, non del servizio globale.
5. **Impact strip** (fuori hero) — metriche aggregate («N segnalazioni prese in
   carico») che vendono missione.

## Mappa responsabilità moduli / temi

| Componente | Ruolo nella sostenibilità | Docs locali |
|---|---|---|
| Fixcity | Outcome civici, metriche aggregate, confini dominio Ticket | `Modules/Fixcity/docs/sustainability-and-funding.md` |
| Cms | Pagine/sezioni/blocchi editoriali (partner, sostieni) | `Modules/Cms/docs/sustainability-and-funding.md` |
| Tenant | Opt-in / override partner per Comune | `Modules/Tenant/docs/sustainability-and-funding.md` |
| Gdpr | Base giuridica, consenso, no ad-tech | `Modules/Gdpr/docs/sustainability-and-funding.md` |
| Media | Asset logo locali, diritti, alt, scadenza | `Modules/Media/docs/sustainability-and-funding.md` |
| Blog | Inbound editoriale | `Modules/Blog/docs/sustainability-and-funding.md` |
| Notify | Newsletter solo con consenso | `Modules/Notify/docs/sustainability-and-funding.md` |
| Seo | Metadati evergreen, non ads | `Modules/Seo/docs/sustainability-and-funding.md` |
| UI | Pattern accessibili per loghi/CTA | `Modules/UI/docs/sustainability-and-funding.md` |
| Sixteen | Vestito FO footer/pagine; WCAG | `Themes/Sixteen/docs/bmad/sustainability-and-footer.md` |
| Meetup | Eventi e sponsor contestuali | `Themes/Meetup/docs/sustainability-and-funding.md` |
| Altri moduli/temi | Confine: non introdurre ads; puntare qui | `*/docs/sustainability-and-funding.md` |

## Gate GO (uguale al dossier)

Implementazione solo se: policy partner, beneficiario (se monetario), diritti
logo, privacy senza pixel, copy testato, opt-in tenant, contratto CMS. Altrimenti
**solo discovery**. Dettaglio checklist in [recommendation.md](./recommendation.md).

## Collegamenti

- [../project-sustainability/README.md](../project-sustainability/README.md) — dossier sostenibilità completo
- [README.md](./README.md) — indice dossier
- [research-report.md](./research-report.md)
- [inbound-marketing.md](./inbound-marketing.md)
- [epics.md](./epics.md) — storie 1.1 / 1.2 governance ed economia
