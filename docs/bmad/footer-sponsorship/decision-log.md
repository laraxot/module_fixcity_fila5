---
title: "Decision log — footer sponsorship"
type: decision-log
updated: 2026-10-15
---

# Decision log

## 2026-10-15 — Piano inbound approfondito

- **Decisione proposta:** adottare un sistema a pillar, cluster, risorse, eventi
  e CTA progressive, ispirato qualitativamente agli ecosistemi AFC Formazione e
  Alessandro Ferrari ma adattato a fiducia civica, accessibilità e privacy.
- **Piano:** diagnosi 30 giorni, percorso minimo 31–60, apprendimento 61–90;
  dettaglio in `inbound-marketing-plan.md`.
- **Stato:** `CONCERNS / proposed-for-validation`; non sono approvati tracking,
  newsletter, pagamenti o implementazione automatica.

## 2026-10-15 — Blueprint inbound Blog/Eventi

- **Decisione proposta:** usare il `Blog` module e l'`ArticleResource` esistente
  per articoli ed eventi inbound, riutilizzando `type`, `event_start_date` e
  `event_end_date` già presenti.
- **Perché:** Ponytail evita una tabella Eventi, un modulo duplicato e una nuova
  Resource; Meetup resta solo presentazione. Il Blueprint definisce authoring,
  validazione, filtri, policy e test senza scrivere codice.
- **Stato:** `CONCERNS / discovery`; tassonomia, tenant isolation, owner e CTA
  devono essere validati prima dell'implementazione.
- **Artefatto:** `laravel/Modules/Blog/docs/blueprints/inbound-blog-events.md`.

## 2026-10-15 — Inbound marketing: blog ed eventi prima dei banner

- **Decisione proposta:** studiare e validare blog ed eventi come canali inbound
  per spiegare il valore di FixCity e creare relazioni con cittadini, Comuni,
  associazioni e grant maker.
- **Riferimento:** il sito AFC Formazione mostra un pattern utile di promessa
  verticale, articoli tematici, risorse gratuite e CTA ricorrenti; FixCity lo
  adatta con tono civico, privacy by design e disclosure.
- **Confine:** discovery editoriale soltanto; nessuna newsletter automatica,
  iscrizione, pagamento, sponsor nel footer o nuova Resource Filament.
- **Artefatti:** `inbound-marketing.md`, `inbound-marketing-brief.md`,
  `inbound-marketing-prd.md` e puntatori nei moduli/temi.

## 2026-10-15 — Verdict marketing: partner sì, banner no

- **Decisione proposta:** adottare come direzione ufficiale «Partner e
  sostenitori» + eventuale CTA verso pagina dedicata; **rifiutare** banner
  commerciali in linea nel footer per il primo rilascio.
- **Perché:** fiducia civica, WCAG/reflow, privacy (no ad-tech), multi-tenant,
  assenza di policy/beneficiario. Designers Italia autorizza «eventuali partner»
  nella fascia branding del footer, non inventori ads. Alternative migliori:
  impact storytelling, pagina `/sostieni`, grant/B2G, sponsorship contestualizzata
  su eventi.
- **Stato:** proposta documentata in [recommendation.md](./recommendation.md);
  richiede approvazione product/tenant owner.
- **Artefatti:** research arricchita (Designers Italia, D.lgs. 33/2013, AGCOM,
  civic-tech), PRFAQ, tech-spec bozza, storie 1.1–2.2.

## 2026-10-15 — Non chiamarlo advertising nel primo pilota

- **Decisione proposta:** validare «Partner e sostenitori» statici prima dei
  banner sponsor.
- **Perché:** preserva fiducia civica, evita tracking e consente di misurare
  comprensione senza introdurre un marketplace.
- **Stato:** proposta, richiede approvazione del product/tenant owner.
- **Superseded in parte da:** entry «Verdict marketing» dello stesso giorno
  (stesso senso, formulazione più esplicita).

## 2026-10-15 — Nessuna implementazione in questa fase

- **Decisione:** produrre solo artefatti BMAD; nessuna Blade, JSON, migration,
  Filament Resource o asset sponsor viene creato.
- **Perché:** mancano beneficiario, policy, diritti sui loghi, disclosure e dati
  di validazione marketing.
