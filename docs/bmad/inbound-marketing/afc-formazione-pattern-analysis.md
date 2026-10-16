---
title: "Analisi pattern — afcformazione.it"
type: research-report
status: decision-input
updated: 2026-10-15
source_observed: https://www.afcformazione.it/
observed_on: 2026-10-15
---

# Analisi AFC Formazione → FixCity

Osservazione del sito pubblico (home, blog, hub risorse). Non è endorsement del
prodotto AFC né copia dello stack (WordPress + Elementor + Rank Math).

## Cosa fa bene AFC (pattern riusabili)

| Pattern AFC | Evidenza sul sito | Traduzione FixCity |
|---|---|---|
| **Promessa verticale** | Title/H1 su comunicazione + leadership per manager/HR | Una promessa: segnalazioni civiche chiare, tracciabili, accessibili |
| **Metodo visibile** | «Dal caos ai risultati» in 6 step | How-it-works già in home: segnala → assegna → risolvi → trasparenza |
| **Diagnosi prima della vendita** | Segnali «dove si rompe» (messaggi, riunioni, delega) | Contenuti «dove si rompe la relazione cittadino–comune» |
| **Lead magnet progressivo** | 7 briefing email ~18 giorni, non un PDF unico | Serie guida / webinar brevissimi; consenso GDPR obbligatorio |
| **Poche risorse, per problema** | Hub risorse: diagnosi → applicazione → approfondimento | Hub `/risorse` o sezioni Cms: pochi tool utili, non cento PDF |
| **Blog tematico concreto** | Titoli su comportamenti («8 comportamenti», «gestione reclami») | Articoli su casi operativi PA/cittadino, non «cos’è lo smart city» generico |
| **CTA a scalare** | Briefing → formazione → consulenza su sito sorella | Guida → evento → adotta Comune → sostieni (solo se governance) |
| **Ecosistema multi-sito** | AFC / aferrari.it / Assodellavendita | FixCity FO / docs / area partner B2G (senza mixare copy sales) |
| **Credibilità autore** | Bio, numeri, esperienza | Team + Comuni pilota + KPI aggregati (no vanity falsa) |
| **Scoperta tecnica** | RSS, OG, JSON-LD Person/Org | Seo module + schema Article/Event; RSS se utile |
| **FAQ pre-acquisto** | «A chi si rivolge», «è un PDF?» | FAQ: chi può segnalare, SPID, tempi, privacy |

## Cosa NON copiare

- Pressione commerciale e tono «compra formazione»
- Tracking/marketing stack tipico WP (pixel, heatmaps non dichiarati)
- Dipendenza da page-builder per contenuti (FixCity = Cms JSON + Blog Article)
- Paywall aggressivo o gated content senza alternativa open
- Centinaia di lead magnet: AFC stessa dice «poche risorse reali»

## Funnel AFC (astratto)

```text
Problema riconosciuto
  → contenuto/blog o check-up
    → risorsa gratuita (briefing/serie)
      → percorso a pagamento / consulenza
```

## Funnel FixCity (proposto)

```text
Problema civico locale
  → post Blog (content_type=post) o guida Cms
    → evento/webinar (content_type=event) accessibile
      → contatto «adotta / collabora» (B2G)
        → sostieni (solo post-governance)
```

Il denaro arriva da **adozione e grant** (vedi project-sustainability), non dal
click sul blog. Il blog qualifica e riduce friction cognitiva.

## Hub risorse AFC → analoghi FixCity

| Bisogno AFC | Analogo FixCity |
|---|---|
| Dove si rompe | Guida «perché le segnalazioni falliscono» |
| Check-up assertività | Checklist accessibilità / qualità ticket (operatore) |
| Piano 1 pagina | One-pager adozione Comune |
| Esercizi pratici | Laboratorio mappa / evento locale |
| Approfondimenti blog | Module Blog evergreen |

## Eventi

AFC spinge formazione e mentoring più che un calendario eventi classico.
Per FixCity gli **eventi** restano centrali (Design Comuni ha già pattern
`eventi` / `evento-dettaglio` + tema Meetup): webinar PA, laboratori quartiere,
sessioni WCAG. Sponsor solo contestuali all’iniziativa.

## Limiti dell’osservazione

- Nessun accesso a analytics o conversion rate AFC
- Nessuna affermazione su fatturato o efficacia numerica
- Pattern qualitativi solo; validare con utenti FixCity
