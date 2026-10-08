---
title: "FixCity — benchmark competitivo e gap di prodotto"
type: benchmark
status: active
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, competitor, benchmark, gap-analysis, civic-tech, second-brain]
module: Fixcity
qmd: "competitor benchmark FixMyStreet SeeClickFix WeDU Decidim Open311 civic tech"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - gap-analysis.md
  - release-plan.md
  - workflows/01-discovery.md
  - ../../wiki/concepts/fixcity-gap-compliance-pa-italia.md
---

# FixCity — benchmark competitivo e gap di prodotto

## Scopo e metodo

Questo documento è il riferimento BMAD per capire dove FixCity deve superare le
piattaforme civic-tech esistenti. Il confronto separa tre cose che non devono
essere confuse:

1. **funzionalità dichiarata dal competitor**;
2. **presenza o assenza dimostrata nel codice FixCity**;
3. **funzionalità presente ma non ancora provata sul runtime candidato**.

La data del benchmark è 2026-09-27. Le fonti prodotto sono documentazione ufficiale
dei rispettivi proprietari; un “gap” con stato `da provare` non va risolto con una
nuova implementazione prima di una verifica runtime.

## Posizionamento dei competitor

| Piattaforma | Punto di forza osservato | Cosa dobbiamo eguagliare | Rischio per FixCity |
|---|---|---|---|
| FixMyStreet | instradamento per posizione/categoria verso l'autorità corretta, email/Open311, follow-up | routing geografico configurabile, Open311 outbound, iscrizione agli aggiornamenti | FixCity può diventare un inbox manuale senza integrazione col gestionale comunale |
| SeeClickFix 311 CRM | triage, richieste vs work order, assegnazione, priorità, scadenze, duplicati, export, watch area, integrazioni GIS/CRM | coda operativa completa, SLA, deduplica, saved searches, integrazioni bidirezionali | il cittadino vede una bella facciata ma la PA continua a lavorare fuori sistema |
| WeDU / Decoro Urbano | app mobile, GPS, invio rapido, stato, notifiche, sfocatura automatica di volti/targhe | mobile-first reale, privacy media automatica, stato comprensibile e notifiche affidabili | una app concorrente può essere più veloce e più sicura per il caso d'uso stradale |
| Decidim | proposte, commenti, voti, assemblee, sondaggi, risultati e accountability | distinguere segnalazione operativa da partecipazione e mostrare l'esito pubblico | FixCity resta un sistema di ticket, non una piattaforma di partecipazione |
| Design Comuni | pattern italiani di wizard, elenco, dettaglio, privacy, area personale e accessibilità | parity documentale/UX, contenuti tenant approvati, audit accessibilità | una gara PA valuta conformità e affidabilità oltre alla singola feature |

## Matrice gap verificabile

Legenda: `verde` = coperto e provato nella suite FixCity; `giallo` = parziale o
solo locale; `rosso` = assente/non dimostrato e candidato a lavoro; `decisione` =
serve scelta di prodotto o titolare prima del codice.

| Capacità | Evidenza competitor | FixCity oggi | Stato | Priorità |
|---|---|---|---|---|
| Segnalazione web responsive | FixMyStreet, SeeClickFix, WeDU | Folio/Livewire wizard e browser guest verificati | verde | mantenere |
| App/PWA e invio dalla strada | WeDU offre iOS/Android, GPS e invio rapido | nessuna prova di app/PWA installabile/offline | rosso | P1 |
| Geolocalizzazione e routing ente | FixMyStreet usa luogo/categoria per l'autorità | coordinate e assegnazione interna; routing Open311 outbound non dimostrato | giallo | P0 |
| Open311/API/webhook outbound | FixMyStreet e SeeClickFix integrano sistemi esterni | STORY-413 copre inbound; push verso gestionali è indicato come mancante | rosso | P0 |
| Coda PA, assegnatario, stato, audit | SeeClickFix offre filtri, assegnazione, stati e commenti | Action, Filament, timeline e filtri coperti; browser/staging PA aperti | giallo | P0 |
| Richiesta distinta da work order | SeeClickFix separa richiesta cittadino e lavoro eseguibile | Ticket unico; nessun modello work order dimostrato | rosso | P1 |
| SLA, priorità, scadenze ed escalation | SeeClickFix espone priorità/due date; FixMyStreet instrada | KPI/SLA presenti, worker/alert/escalation non provati | giallo | P0 |
| Duplicati e consolidamento | SeeClickFix permette merge/duplicate | non dimostrata deduplica automatica o merge con audit | rosso | P0 |
| Watch area / notifiche geografiche | SeeClickFix segue una polygon area | follow ticket e email opt-out; watch area non dimostrata | rosso | P1 |
| Notifiche e delivery receipts | competitor notificano stati; WeDU notifica presa in carico/risoluzione | in-app/email e retry presenti; provider, receipt, push e alert aperti | giallo | P0 |
| Media privacy | WeDU sfoca automaticamente volti e targhe | allegati presenti; redazione automatica non dimostrata | rosso | P0 |
| Moderazione e abuso | SeeClickFix consente flag e blocco submissions | policy/ownership presenti; console moderazione e anti-abuso non dimostrate | giallo | P1 |
| Mappa operativa e trend | SeeClickFix filtra la mappa e visualizza hot spot | GeoJSON, KPI e heatmap progettati; runtime con dati reali da provare | giallo | P1 |
| Export e open data | SeeClickFix esporta CSV/XLSX e integra ArcGIS | export PA presente in parte; contratto open data, anonimizzazione e CSV/XLSX da verificare | giallo | P1 |
| Partecipazione deliberativa | Decidim propone/commenta/vota/monitora risultati | FixCity gestisce segnalazioni, non proposte/processi/risultati | rosso | P2/decisione prodotto |
| Accessibilità e conformità Italia | Design Comuni e gare PA richiedono contenuti/accessibilità verificabili | UI locale buona; screen reader, contrasto, tenant policy e staging aperti | giallo | P0 |
| Backup, restore, osservabilità | prodotto PA maturo richiede continuità operativa | restore test DB locale riuscito; staging, retention, cifratura, alert aperti | giallo | P0 |

## Gap che rendono FixCity migliore, non solo uguale

La roadmap non deve copiare ogni piattaforma. Il vantaggio distintivo da costruire è:

- **Italy-first e tenant-safe**: Design Comuni, contenuti legali approvati, dati
  minimizzati, capability code owner-only e audit leggibile;
- **workflow trasparente**: richiesta → presa in carico → lavoro → verifica →
  risoluzione, con motivazione, responsabile, timestamp, SLA ed eventuale riapertura;
- **integrazione senza doppio inserimento**: Open311, webhooks, GIS e gestionali,
  con idempotenza, retry, dead-letter e replay amministrabile;
- **privacy automatica dei media**: redazione EXIF, volti e targhe prima della
  pubblicazione, con originale privato e audit della trasformazione;
- **fiducia misurabile**: deduplica spiegabile, risposta pubblica, stato SLA,
  feedback post-risoluzione e dataset esportabile senza dati personali;
- **inclusione reale**: web accessibile, PWA leggera, percorsi a bassa banda,
  lingue tenant, assistenza telefonica/importazione manuale per chi non usa app.

## Backlog BMAD prioritizzato

### P0 — prima di un pilota PA

1. **Routing e integrazione**: definire adapter Open311 outbound, mapping categorie/
     dipartimenti, retry/idempotenza, webhook di ritorno e audit.
2. **Workflow operativo**: introdurre o formalizzare work order, SLA, due date,
   escalation e stati datati (`acknowledged`, `assigned`, `work_started`, `verified`).
3. **Deduplica**: suggerimento per distanza/categoria/finestra temporale, merge con
   consenso PA, redirect dei follower e log immutabile.
4. **Media privacy**: pipeline asincrona per EXIF/volti/targhe, fallback sicuro e
   divieto di pubblicazione dell'originale.
5. **Delivery operativa**: SMTP sandbox, worker reale, failed jobs, receipt provider,
   alert e runbook replay.
6. **Release evidence**: smoke su staging con cittadino A/B e PA, accessibility
   manuale, backup/restore staging e sign-off tenant privacy.

### P1 — differenziazione competitiva

1. Watch area per poligono/raggio e preferenze di frequenza.
2. Mobile/PWA con coda offline e upload ripetibile.
3. Saved searches, viste PA condivise, export CSV/XLSX e open-data anonimizzato.
4. Moderazione, anti-spam, abuse reports, blocco temporaneo e audit.
5. Dashboard con trend, hotspot, SLA percentile, backlog aging e qualità dati.
6. Rating/appeal post-risoluzione accessibile e verificabile su DB concorrente.

### P2 — scelta prodotto esplicita

1. Layer Decidim-lite: proposte, commenti, supporti/voti, risultati e accountability.
2. Assemblee, consultazioni e bilancio partecipativo: solo se il product owner
   conferma che FixCity vuole essere anche piattaforma deliberativa.
3. App native: valutarle solo dopo aver misurato PWA, notifiche e adozione reale.

## Definition of Done competitiva

Un gap è chiuso solo quando esistono: story BMAD con Issue e Discussion, decisione
di sicurezza/privacy, Action e schema conformi alle regole FixCity, test unit/feature,
smoke browser sul percorso reale, prova su MariaDB e SQLite quando applicabile,
documentazione Second Brain aggiornata e quality gate verde. La sola presenza di una
classe o di una schermata non chiude il gap.

## Fonti ufficiali consultate

- FixMyStreet: https://fixmystreet.org/how-it-works/ e
  https://fixmystreet.org/The-FixMyStreet-Platform-DIY-Guide-v1.1.pdf
- SeeClickFix: https://www.civicplus.help/seeclickfix/docs/requests-list-overview,
  https://www.civicplus.help/seeclickfix/docs/requests-and-work-orders,
  https://www.civicplus.help/seeclickfix/docs/using-the-map-tab,
  https://www.civicservice.civicplus.help/hc/en-us/articles/7083352124183-Watch-Area-Overview
- SeeClickFix integrations: https://www.civicplus.help/seeclickfix/docs/integrations-overview
- WeDU / Decoro Urbano: https://www.decorourbano.org/applicazioni
- Decidim: https://docs.decidim.org/en/develop/features/general-description.html e
  https://decidim.org/first-steps/
- Design Comuni: https://italia.github.io/design-comuni-pagine-statiche/index.html

## Decisioni e limiti

Le pagine ufficiali descrivono capacità di prodotto, non garantiscono qualità,
adozione o performance in ogni ente. Non abbiamo usato recensioni anonime per
decidere l'architettura. Municipium resta un competitor commerciale rilevante per
l'Italia, ma va studiato con fonti e demo verificabili prima di assegnargli gap
specifici. Le funzionalità Decidim sono un confronto di categoria, non un requisito
automatico del dominio segnalazioni.
