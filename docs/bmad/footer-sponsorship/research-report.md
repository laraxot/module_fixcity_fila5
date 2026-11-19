---
title: "Footer sponsorship and support research"
type: research-report
status: decision-input
updated: 2026-10-15
tags: [bmad, marketing, sponsorship, civic-tech, accessibility, fixcity]
qmd: "research report   sostenibilità del progetto nel footer fixcity"
---

# Research report — sostenibilità del progetto nel footer

## Domanda

È opportuno usare il footer di FixCity per una formula «Sostieni il nostro
progetto» e una fila di banner sponsor? La valutazione deve proteggere fiducia
civica, neutralità del servizio, accessibilità, privacy e sostenibilità operativa.

## Sintesi

La proposta è valida come area di comunicazione, ma i banner commerciali non sono
la prima opzione consigliata per il percorso principale. Il footer dovrebbe partire
da una gerarchia più neutra:

1. **Trasparenza del progetto** — chi gestisce FixCity, perché esiste e come sono
   usati eventuali contributi.
2. **Partner e finanziatori dichiarati** — loghi sobri, senza endorsement implicito
   e senza tracciamento.
3. **Supporto** — CTA «Sostieni il progetto» solo quando esistono soggetto
   beneficiario, pagamento, ricevuta, termini e contatto verificabili.
4. **Sponsor commerciali** — ammessi soltanto con policy pubblica, disclosure e
   approvazione del tenant; mai aste pubblicitarie o targeting comportamentale.

La versione minima da validare è quindi una **sezione “Partner e sostenitori”**,
non una griglia pubblicitaria.

## Ordine strategico dei ricavi

Per il contesto civico di FixCity l'ordine più coerente è:

1. adozione/partner istituzionali da parte dei Comuni;
2. grant pubblici o filantropici per accessibilità, interoperabilità e ricerca;
3. donazioni ricorrenti, solo con un ente beneficiario idoneo e rendicontazione;
4. membership, solo se offre una partecipazione reale e non un paywall civico;
5. sponsorship commerciale eccezionale, senza banner e con criteri pubblici.

Grant e contratto pubblico non sono sinonimi: il primo finanzia un progetto o
un'attività d'interesse pubblico, il secondo remunera un servizio acquistato dalla
PA. Questa distinzione va verificata con il titolare prima di usare parole come
«donazione», «finanziato» o «partner».

Fonti per la discovery economica:

- Commissione europea, funding e grant: https://commission.europa.eu/funding-and-tenders/how-apply/you-apply-eu-funding-beginners_en
- M+R Benchmarks 2025 (benchmark internazionale, non obiettivo italiano): https://2025.mrbenchmarks.com/fundraising.html
- Ministero del Lavoro, linee guida raccolta fondi ETS: https://www.lavoro.gov.it/notizie/Documents/Linee-guida-raccolta-fondi-ETS.pdf
- RUNTS: https://servizi.lavoro.gov.it/runts/it-it/Scopri-il-RUNTS

## Alternative confrontate

| Opzione | Fiducia civica | Ricavo potenziale | Complessità | Raccomandazione |
|---|---:|---:|---:|---|
| Banner sponsor commerciali | Bassa/variabile | Media | Alta | Solo dopo policy e pilota |
| Loghi partner istituzionali | Alta | Indiretto | Bassa | Prima scelta |
| CTA sostegno/donazione | Variabile | Media | Alta | Solo con governance pronta |
| Grant e finanziamenti pubblici | Alta | Alta ma lenta | Alta | Canale strategico |
| Membership PA/associazioni | Alta | Media | Media | Da esplorare B2B/B2G |
| Sponsorizzazione di iniziative locali | Media/alta | Media | Media | Pilota con disclosure |

## Insight di marketing

- Vendere la **missione e l'impatto misurabile**, non lo spazio visivo: numero di
  segnalazioni prese in carico, tempo medio di risposta, comuni serviti e risultati
  verificabili.
- Separare «partner che rendono possibile il servizio» da «fornitori/sponsor».
  Sono messaggi diversi e non devono sembrare un'approvazione del prodotto.
- Il copy deve essere concreto: destinatario del contributo, uso previsto, periodo,
  contatto e rendicontazione. Evitare promesse vaghe come «aiutaci a cambiare il
  mondo».
- Misurare fiducia e comprensione, non soltanto click: riconoscimento del partner,
  comprensione della disclosure, completamento della CTA, reclami e abbandono del
  flusso principale.

## Vincoli di accessibilità e privacy

- Ogni logo è contenuto non testuale e necessita di testo alternativo equivalente;
  se il nome è già vicino al logo, l'alt text può essere vuoto per evitare
  duplicazione. Fonte: WCAG 2.2 SC 1.1.1.
- Testo e controlli devono rispettare contrasto e focus visibile; la grafica del logo
  non può sostituire l'informazione testuale. Fonte: WCAG 2.2 SC 1.4.3 e 1.4.11.
- La sezione deve funzionare a reflow stretto, zoom 200–400%, tastiera e con testo
  spaziato; nessuna riga orizzontale di banner deve diventare un ostacolo. Fonti:
  WCAG 2.2 SC 1.4.10 e 1.4.12.
- Immagini locali, nessun pixel, beacon, cookie o script di advertising nel primo
  rilascio. Eventuali misurazioni devono essere aggregate e documentate.
- Se la CTA raccoglie dati o pagamento, il titolare, la finalità, la base giuridica,
  la conservazione e i responsabili devono essere definiti prima del design.
  Riferimento: GDPR, Regolamento (UE) 2016/679.

## Contesto normativo e di mercato (aggiornamento)

### Italia / PA

- **Designers Italia — Footer (linee guida design siti PA):** la fascia di
  branding in apertura del footer può includere «logo e denominazione dell'ente
  o del progetto e di eventuali partner». Questo **autorizza partner** nel chrome
  istituzionale; **non** equivale a inventori pubblicitari commerciali o aste
  banner. Fonte: https://designers.italia.it/design-system/componenti/footer/
- **D.lgs. 33/2013** disciplina trasparenza e «Amministrazione trasparente» sui
  siti istituzionali, non la vendita di spazi banner. È però il quadro culturale
  in cui un servizio civico vive: pubblicità commerciale nel chrome istituzionale
  va giustificata con trasparenza e non-endorsement, non con inventori ads.
  Fonte: https://www.normattiva.it/eli/id/2013/04/05/13G00076/CONSOLIDATED/20231118
- **AGCOM / d.lgs. 208/2021 art. 49** riguarda le somme impegnate dagli enti
  pubblici per *acquistare* spazi di pubblicità istituzionale sui media, non la
  vendita di inventori sul proprio sito. Utile come segnale: la PA tratta la
  pubblicità come spesa regolamentata e rendicontata, non come marketplace
  improvvisato. Fonte: https://www.agcom.it/entipubblici

### Best practice civiche / open source

- Policy UK (es. Peterborough / Sheffield): accordi scritti, policy pubblica,
  artwork approvato, WCAG, riconoscimento «sponsored by / in partnership with»
  distinto dall’endorsement del prodotto.
- Open Source Collective / Nano Collective: pagina `/sponsor` come SSoT,
  footer solo per tier alti, ledger pubblico sull’uso fondi, policy di
  accettazione chiara.
- Civic Tech DC: partner = contesto, hosting, in-kind; sponsor ≠ sostituzione
  della ownership pubblica del servizio.

Implicazione FixCity: **prima policy e pagina dedicata; footer come recognition
sobria; mai ad-tech.**

## Fonti consultate (2026-10-15)

- W3C, Non-text Content: https://www.w3.org/WAI/WCAG22/Understanding/non-text-content.html
- W3C, Contrast Minimum: https://www.w3.org/WAI/WCAG22/Understanding/contrast-minimum.html
- W3C, Reflow: https://www.w3.org/WAI/WCAG22/Understanding/reflow.html
- W3C, Text Spacing: https://www.w3.org/WAI/WCAG22/Understanding/text-spacing.html
- European Commission, Data protection in the EU:
  https://commission.europa.eu/law/law-topic/data-protection/data-protection-eu_en
- European Union, Regulation (EU) 2016/679:
  https://eur-lex.europa.eu/eli/reg/2016/679/oj
- FTC, Endorsement Guides and disclosure principles:
  https://www.ftc.gov/business-guidance/resources/ftcs-endorsement-guides-what-people-are-asking
- Normattiva, D.lgs. 33/2013:
  https://www.normattiva.it/eli/id/2013/04/05/13G00076/CONSOLIDATED/20231118
- AGCOM, Pubblicità degli enti pubblici: https://www.agcom.it/entipubblici
- Peterborough City Council, Advertising and Sponsorship Policy:
  https://www.peterborough.gov.uk/council/strategies-policies-and-plans/council-strategies/advertising-and-sponsorship-policy
- Open Source Collective, Accepting Sponsorships:
  https://docs.oscollective.org/resources/resources/accepting-sponsorships
- Nano Collective, Sponsorship Operations:
  https://docs.nanocollective.org/collective/sponsors/operations
- Civic Tech DC, Partnerships: https://www.civictechdc.org/partners.html
- Designers Italia, Footer: https://designers.italia.it/design-system/componenti/footer/

## Limiti

Non esistono dati di conversione, disponibilità di sponsor, forma giuridica del
beneficiario o policy del tenant. La decisione economica non è quindi ancora
approvabile: questo documento formula ipotesi e criteri di validazione.
Vedi [recommendation.md](./recommendation.md) per il GO/NO-GO operativo.
