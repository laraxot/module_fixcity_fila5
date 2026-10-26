---
title: "Analisi pattern — AFC Formazione"
type: bmad-research
status: decision-input
updated: 2026-10-15
tags: [bmad, inbound-marketing, afc, blog, eventi, research]
observed_urls:
  - https://www.afcformazione.it/
  - https://www.afcformazione.it/blog/
  - https://www.afcformazione.it/blog/differenza-tra-capo-e-leader/
  - https://www.afcformazione.it/risorse-strumenti-comunicazione-aziendale/
qmd: "analisi afc formazione (osservazione 2026-10-15) fixcity"
---

<!--
gateguard: supersedes nothing (new analysis referenced by purpose/README/Blog docs);
archived from the former root dossier; the canonical copy now lives in
`Modules/Fixcity/docs/bmad/inbound-marketing/` and is linked by Modules/Blog;
user verbatim: "per inbound marketing dobbiamo mettere anche blog ed eventi , studia https://www.afcformazione.it/  e fa tutto con bmad + second brain"
-->

# Analisi AFC Formazione (osservazione 2026-10-15)

Analisi qualitativa di pagine pubbliche. **Non** copiare stack WordPress,
tracking, copy commerciale o tassi di conversione inventati. Trasferire lo
*schema di scopo*, adattato al servizio civico FixCity.

## 1. Home — sistema, non landing

Fonte: [afcformazione.it](https://www.afcformazione.it/)

| Elemento osservato | Cosa fa | Adattamento FixCity |
|---|---|---|
| Promessa unica in hero | «Comunicazione e leadership…» | Una promessa sul problema civico (segnalazioni affidabili), non dashboard promo |
| Audience esplicita | imprenditori, manager, HR | cittadini / operatori / referenti PA (CTA diverse) |
| Prove di credibilità | anni, persone formate, step metodo | KPI aggregati ticket + tenant (da Fixcity), non vanity |
| Diagnosi «dove si rompe» | messaggi ambigui, riunioni senza seguito, delega | dove si rompe segnalazione: foto mancante, tempi, ownership |
| Metodo in 6 step | Diagnosi → … → Governance | Metodo servizio: invio → presa in carico → chiusura → rendicontazione |
| Lead magnet soft | 7 briefing email ~18 giorni | Guide Cms + eventi (email solo con consenso Gdpr/Notify) |
| Ecosistema 3 siti | AFC / aferrari / Asso | Blog + eventi + docs/adozione (stesso motore, verticali diversi) |
| FAQ oneste | cosa è / non è il percorso gratuito | chiarire cosa è FixCity vs cosa non è (ads, paywall segnalazione) |

Insight: la home vende un **metodo verificabile**, non un catalogo prodotti.

## 2. Blog — titoli sul comportamento concreto

Fonte: [afcformazione.it/blog](https://www.afcformazione.it/blog/)

Titoli osservati (esempi):

- «Differenza tra capo e leader: 8 comportamenti concreti»
- «Errori email marketing: … il cliente sa che cosa fare?»
- «Gestione reclami: … senza mettersi subito in difesa»

Pattern: **problema + criterio operativo**, non «5 tip generici».

Per FixCity: titoli tipo «Come una segnalazione senza foto rallenta il Comune»,
non «Il blog FixCity».

## 3. Articolo pilastro — struttura osservata

Fonte: [Differenza capo/leader](https://www.afcformazione.it/blog/differenza-tra-capo-e-leader/)

Sequenza:

1. scena concreta (consegna urgente, errore, decisione);
2. esperienza narrata come contesto (non testimonial inventato);
3. definizione chiara della domanda;
4. griglia pratica (tabella 8 comportamenti);
5. criteri (urgenza / competenza / rischio);
6. esempi «da adattare» dichiarati come tali;
7. checklist / verifiche operative;
8. FAQ + CTA verso risorsa/formazione;
9. autore e pezzi correlati.

Trasferimento: ogni articolo FixCity = problema civico → evidenza → griglia →
CTA soft (guida / evento / collabora). Dichiarare cosa è esempio vs fatto.

## 4. Hub risorse — poche, per bisogno

Fonte: [risorse AFC](https://www.afcformazione.it/risorse-strumenti-comunicazione-aziendale/)

Organizzazione: **Diagnosi → Applicazione → Approfondimento**.
Messaggio esplicito: «Perché non trovi cento download?» — qualità > quantità.

Per FixCity (Cms): hub `risorse`/`guide` con checklist cittadino/operatore,
one-pager adozione, link blog evergreen e eventi — non dump PDF.

## 5. Cosa NON trasferire

- Pressione commerciale e lead scoring individuale
- Serie email senza consenso e preferenze
- UTM/pixel cross-site come default
- Paywall sulla conoscenza di base del servizio
- Confondere «formazione a pagamento» con «adozione B2G»

## 6. Implicazione BMAD

GO discovery editoriale (calendario, policy, outline). Implementazione Filament
solo dopo readiness e Blueprint: riuso `Article` `type=blog|event` (Ponytail).
Vedi [recommendation.md](./recommendation.md) e
[Blueprint](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events.md).

## Collegamenti

- [purpose.md](./purpose.md)
- [README.md](./README.md)
- Studio correlato: [../footer-sponsorship/inbound-deep-study.md](../footer-sponsorship/inbound-deep-study.md)
- Adattamento Blog: [afc-pattern-adaptation](../../laravel/Modules/Blog/docs/afc-pattern-adaptation.md)
