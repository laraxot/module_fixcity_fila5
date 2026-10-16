---
title: "Raccomandazione — partner vs banner sponsor nel footer"
type: recommendation
status: decision-input
updated: 2026-10-15
tags: [bmad, marketing, sponsorship, footer, fixcity]
---

# Raccomandazione esecutiva

## Verdict in una frase

**Buona idea come spazio di trasparenza partner/sostenitori; cattiva idea come fila
di banner pubblicitari commerciali nel footer del servizio civico.**

## Risposta diretta alla proposta

| Elemento proposto | Giudizio | Motivo |
|---|---|---|
| «Sostieni il nostro progetto» | Condizionato | Solo se esistono beneficiario, canale pagamento, ricevuta, privacy e rendicontazione |
| Banner sponsor in linea nel footer | Non raccomandato nel primo rilascio | Confonde servizio pubblico e advertising; WCAG/reflow; rischio tracking e governance scarsa |
| Loghi partner istituzionali statici | Raccomandato | Credibilità, zero tracking, allineato a policy civiche internazionali |
| CTA «Collabora / Scopri come sostenere» verso pagina dedicata | Preferibile ai banner | Sposta conversione e disclosure fuori dal chrome istituzionale |

## Perché i banner non sono la prima mossa

1. **Fiducia:** il footer di un servizio segnalazioni è spazio istituzionale, non
   inventario pubblicitario. Loghi commerciali senza disclosure riducono la
   percezione di neutralità.
2. **Design system PA:** Designers Italia ammette esplicitamente «eventuali
   partner» nella fascia di branding del footer — quindi **partner sì**, non
   griglia banner. Fonte: designers.italia.it/design-system/componenti/footer/
3. **Accessibilità:** una riga di banner è fragile a reflow, zoom e tastiera;
   WCAG 2.2 1.4.10/1.4.12 rende costoso mantenere una griglia «in linea».
4. **Privacy:** i banner commerciali tipici portano pixel/script; FixCity deve
   restare senza advertising tech nel primo rilascio.
5. **Economia:** senza policy, beneficiario e diritti logo non si può firmare né
   fatturare; lo spazio vuoto o con placeholder danneggia più di quanto aiuti.
6. **Governance PA:** in contesto multi-tenant comunale ogni tenant deve poter
   disattivare o filtrare i partner; un marketplace globale rompe isolation.

## Cosa raccomandiamo invece (ordine)

1. **Partner e sostenitori** — loghi statici, nome testuale, disclosure, asset
   locali, nessun carosello.
2. **Pagina dedicata «Sostieni / Collabora»** — modello economico, uso fondi,
   contatti, eventuali donazioni o grant; CTA nel footer punta lì.
3. **Canali B2G/B2B** — adozione Comuni, grant, membership ente; non dipendono
   dal chrome del cittadino.
4. **Sponsorship commerciale** — solo dopo policy pubblica, contratto scritto,
   registro trasparente e pilota misurato; recognition «in partnership with»,
   non endorsement.

## Idee migliori della sola fila di banner

- **Impact strip** (fuori hero): «X segnalazioni prese in carico / Y comuni» +
  link «Come sosteniamo il servizio» — vende missione, non inventario.
- **Tiers di riconoscimento** su pagina dedicata (Builder vs Supporter), con
  footer che mostra solo il tier alto — pattern open-source sostenibile.
- **Partner istituzionali + EU funding disclosure** già coerente con footer
  legale/UE, senza mescolare brand commerciali.
- **Eventi/iniziative locali sponsorizzate** (pagina evento Design Comuni ha già
  pattern `sponsors.list`) — sponsorship contestualizzata, non permanente sul
  chrome globale.

## Condizioni di GO per implementazione

GO solo quando tutti sono veri:

- [ ] Tenant/product owner approva categorie partner ammesse
- [ ] Policy pubblica (accettazione, conflitto, revoca, non-endorsement)
- [ ] Beneficiario legale e canale di pagamento definiti (se c’è CTA monetaria)
- [ ] Diritti logo e scadenze contrattuali
- [ ] Copy + disclosure testati con utenti (comprensione/fiducia)
- [ ] Piano privacy senza pixel di terzi
- [ ] Contratto CMS/JSON e Filament Blueprint dopo Epic 1–2

Finché una casella manca: **NO-GO implementazione; sì discovery documentale**.

## Collegamenti

- [research-report.md](./research-report.md)
- [brainstorming-report.md](./brainstorming-report.md)
- [prd.md](./prd.md)
- [readiness-report.md](./readiness-report.md)
- [epics.md](./epics.md)
