---
title: "FixCity — gap normativi/interoperabilità PA italiana (deep dive)"
type: comparison
confidence: high
created: 2026-06-17
updated: 2026-06-17
tags: [fixcity, competitor, gap, municipium, comuni-chiamo, open311, pec, protocollo, gdpr, agid, accessibilita, pagopa, bmad, product]
qmd: "deep dive gap Fixcity Open311 GeoReport v2 protocollo informatico PEC GDPR moderazione PII AgID legge stanca 4 2004 dichiarazione accessibilità PagoPA webhook gestionali MapIt asset layer Decidim story BMAD compliance PA italiana"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/601"
  - "https://github.com/laraxot/module_fixcity_fila5/issues/62"
  - "https://github.com/laraxot/base_fixcity_fila5/issues/438"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/605"
  - "https://github.com/laraxot/module_fixcity_fila5/discussions/65"
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/439"
related:
  - ../../roadmap/product-strategy.md
  - ../../../../../../docs/wiki/competitive-analysis-fixcity-2026.md
  - ../../../../../../docs/stories/STORY-397-competitor-gap-programme.md
---

# FixCity — gap normativi/interoperabilità PA italiana (deep dive)

> **Addendum profondo** al programme [STORY-397](../../../../../../docs/stories/STORY-397-competitor-gap-programme.md) (gap 413–432).
> Non duplica la matrice internazionale ([competitive-analysis-fixcity-2026.md](../../../../../../docs/wiki/competitive-analysis-fixcity-2026.md)) né i gap già coperti.
> Aggiunge ciò che manca **ovunque**: il competitor #1 (**Municipium**) e il **layer compliance/back-office** che decide le gare nei comuni, con **norma precisa + design Fixcity + AC** per ogni gap.

## 0. Tesi (perché questo, perché ora)

Il censimento dello swarm guarda la **parità feature lato cittadino**. Ma in Italia un comune sceglie il fornitore per **conformità normativa** e **integrazione col back-office** — e questo è **nullità di contratto** se manca (art. 4 L.4/2004 per l'accessibilità). È il moat «Italy-first / GDPR-by-design» della [product-strategy](../../roadmap/product-strategy.md), **non ancora tradotto in story**. In più, **Municipium (Maggioli, ~2.000 comuni)**, il rivale più diffuso, non è censito da nessuna parte.

## 1. Capability map civic-tech (dove cade ogni cosa)

Otto capability. Le prime quattro sono già presidiate dallo swarm; le ultime quattro sono **il mio lane** (sotto-presidiate).

| Capability | Cosa significa | Leader di riferimento | Copertura Fixcity |
|---|---|---|---|
| Intake | wizard, foto, geo, multi-canale, guest | FixMyStreet, Municipium | 401/417/420 ✅ |
| Triage & routing | categoria→ufficio, duplicati, asset GIS | FixMyStreet Pro, Comuni-Chiamo | 413/414/415/424 ✅ |
| Lifecycle & trasparenza | timeline, stati, verifica, statistiche | SeeClickFix, FixMyStreet | 400/404/405/427 ✅ |
| Partecipazione | proposte, bilancio, sondaggi | Decidim | 422/423 ✅ (+436 sotto) |
| **Interoperabilità back-office** | Open311, protocollo, PEC, webhook, gestionali | FixMyStreet Pro (Open311), Comuni-Chiamo | **413 parz.** → **433/439** ⚠️ |
| **Privacy & moderazione** | blur PII, anti-spam, diritti GDPR | nessuno (vuoto di mercato) | **gap** → **434/435** ❌ |
| **Accessibilità legale** | dichiarazione AgID, feedback, RTD | nessuno civic-tech IT | **394 tecnico** → **437** ⚠️ |
| **Pagamenti & emergenze** | PagoPA, allerte Protezione Civile | Municipium | **gap** → **438/440** ❌ |

## 2. Municipium — il competitor #1 mancante

Maggioli/Municipium, «l'app più usata dai Comuni italiani» (~2.000 enti, ANCI 2025): **super-app comunale**, non solo segnalazioni.

| Area Municipium | In scope FixCity? | Note |
|---|---|---|
| Segnalazioni geo+foto riservate; categoria→ufficio destinatario | ✅ core | coperto (wizard/timeline/auto-routing 414) |
| Allerte Protezione Civile + piano emergenza + numeri utili | ⚠️ adiacente | gap → #440 (più ampio di alert 430) |
| Sondaggi cittadini | ✅ | gap → #436 |
| Multe + pagamento PagoPA | ⚠️ strategico | gap → #438 |
| Modulo emergenza sordi / accessibilità | ✅ legale | gap → #437 |
| Certificati ANPR, rifiuti porta-a-porta, prenotazioni, news | ❌ fuori scope | altri domini/moduli (vedi §6) |

Fonti: municipiumapp.it/faq, play.google.com/Municipium, maggioli.com (ANCI 2025); Comuni-Chiamo (comuni-chiamo.com): protocollazione interna, pianificazione attività ricorrenti, esito chiusura tipizzato, API integrazione gestionali.

## 3. Lezioni architetturali dai leader (alimentano il design)

- **Open311 GeoReport v2** (wiki.open311.org): `GET /services` (lista) + `GET /services/{code}` (service definition: `attribute` con `code/datatype/required/order/values`) + `POST /requests` (`service_code`, `lat|long|address_string|address_id`, `attribute[code]=value`, `media_url`) + `GET /requests/{id}` con stati canonici (`submitted, open, assigned, in progress, closed, rejected…`) + **Atom feed**. È lo schema che rende **plug-and-play** la PA → orienta #433/#439 (mappare `TicketStatusEnum`↔stati Open311; categorie↔`service_code`).
- **FixMyStreet Pro** (societyworks.org): **asset layer** GIS per categoria (snap del report sull'asset, inietta ID interno comune) + **body-determination** (MapIt: geo→ente competente) + **Open311 bidirezionale** + **soppressione duplicati** «suggerisci report esistenti entro raggio configurabile per categoria» + **template risposta** + auto-triage a enti terzi/parrocchie. → conferma il design di 414/415/424 e la necessità di **status sync bidirezionale** (#439).
- **Decidim** (docs.decidim.org): **componenti pluggable** (Proposals, Budgets «voto a spesa», Surveys, Accountability per monitoraggio esecuzione) scoping per «participatory space». → modello per 422/423/#436: il **sondaggio/CSAT** è un componente riusabile, non logica nel ticket.

## 4. Gap senza story → deep dive (numeri proposti 433+)

Verificato contro **400–432**. Per ogni gap: **norma**, **evidenza competitor**, **design Fixcity** (DRY+KISS, Action/Model, mai Services), **AC verificabili**.

### #433 — Protocollo informatico + PEC + connettori gestionali — **Must**

- **Business logic.** La segnalazione che diventa atto deve entrare nel **protocollo** dell'ente e poter essere inoltrata via **PEC**: requisito di capitolato, non opzionale.
- **Norma.** CAD D.Lgs 82/2005 (gestione documentale/protocollo informatico, art. 40-bis/protocollo); PEC obbligatoria per la PA (AgID, ogni registro di protocollo ha la sua PEC).
- **Competitor.** Comuni-Chiamo: «segnalazioni protocollate internamente», «integrabile con ogni gestionale, API integrazione». FixMyStreet Pro: Open311 bidirezionale verso CRM/asset.
- **Design Fixcity.** `Modules/Fixcity/app/Actions/Protocol/RegisterTicketProtocolAction` (QueueableAction) → emette numero protocollo, persiste su `ticket.protocol_number` (campo in `tableUpdate` della migrazione owner ticket, **una sola** migrazione + bump timestamp). PEC via **Notify** (driver dedicato), non nuovo Service. Connettore gestionale dietro **contract** `Models/Contracts/ProtocolGatewayContract` (adapter per fornitore).
- **AC.** (1) Su transizione `acknowledged` parte `RegisterTicketProtocolAction`; (2) numero protocollo visibile in BO Filament + timeline; (3) invio PEC tracciato in Activity; (4) gateway sostituibile via binding senza toccare il dominio.

### #434 — Moderazione UGC + anti-spam + blur PII nelle foto — **Must**

- **Business logic.** I contenuti sono pubblici e generati dai cittadini: foto con **volti/targhe** = dato personale; serve oscuramento + moderazione + anti-spam.
- **Norma.** GDPR (Reg. UE 2016/679) minimizzazione/liceità; pubblicazione di immagini identificative senza base giuridica = illecito.
- **Competitor.** Vuoto di mercato (nessuno lo fa bene) → **differenziatore**, oltre che obbligo.
- **Design Fixcity.** Pipeline su **Media**: `Modules/Media/app/Actions/Image/BlurPiiAction` (volti/targhe) eseguita in coda all'upload, **prima** della pubblicazione FO; `Modules/Fixcity/app/Actions/Moderation/ModerateTicketAction` (stato `pending_review` → `published`); anti-spam con rate-limit + honeypot nel wizard (Form schema, non widget). Stato moderazione in `TicketStatusEnum`/flag dedicato.
- **AC.** (1) Foto pubblicate solo dopo blur PII; (2) coda di moderazione in Filament BO con bulk approve/reject; (3) report sotto soglia spam non pubblicato e tracciato; (4) nessun PII grezzo raggiungibile da URL pubblico.

### #435 — Diritti GDPR del cittadino (export / cancellazione / retention) — **Should**

- **Business logic.** «GDPR-by-design» dichiarato in strategy va reso **verificabile**: DSAR (accesso/portabilità), cancellazione, retention configurabile per comune.
- **Norma.** GDPR artt. 15–17, 20; retention (limitazione conservazione, art. 5).
- **Competitor.** Assente nei rivali.
- **Design Fixcity.** `Modules/User/app/Actions/Gdpr/{ExportSubjectDataAction, EraseSubjectDataAction}` + policy retention schedulata (comando artisan idempotente, **mai** distruttivo sui dati ancora dovuti). Audit immutabile via **Activity** (Spatie). Anonimizzazione ticket invece di delete dove serve continuità statistica (collega 418 open data).
- **AC.** (1) Cittadino richiede export → archivio dati in formato aperto; (2) cancellazione → anonimizzazione coerente con statistiche; (3) retention per-tenant configurabile; (4) ogni operazione loggata in Activity.

### #436 — CSAT + sondaggi cittadini — **Should**

- **Business logic.** Chiude il **loop qualità**: distinto da 405 (verifica esito) e 427 (metriche PA). Misura la soddisfazione → KPI accountability.
- **Norma/standard.** Best practice CX PA; Municipium «Sondaggi».
- **Design Fixcity.** Componente riusabile (lezione Decidim Survey): `Modules/Rating` per CSAT post-chiusura (1 click su email/timeline) + sondaggi tematici. Niente logica nel Ticket: rating polimorfico già esistente.
- **AC.** (1) Alla chiusura, invito CSAT (Notify); (2) punteggio aggregato nella dashboard PA (404/427); (3) sondaggi creabili in BO senza deploy; (4) anonimato configurabile.

### #437 — Accessibilità AgID: dichiarazione + meccanismo feedback + RTD — **Should/Must (gara)**

- **Business logic.** WCAG tecnico (Design Comuni / [STORY-394](../../../../../../docs/stories/STORY-394-pa-accessibility-i18n-completion.md)) **non basta**: serve il layer **legale/processo**.
- **Norma (precisa).** Legge 4/2004 (Stanca) + Dir. UE 2016/2102 (WAD); **Dichiarazione di accessibilità** via **form.agid.it** entro **23 settembre** (a cura del **RTD**); **obiettivi di accessibilità** entro 31 marzo; **meccanismo di feedback** obbligatorio per le segnalazioni di accessibilità (prima istanza) con escalation al **Difensore civico per il digitale** se nessuna risposta in **30 giorni**; **art. 4**: contratti per siti/app **nulli** senza requisiti di accessibilità → è un requisito di gara.
- **Competitor.** Nessun civic-tech IT lo integra nativamente → differenziatore di compliance.
- **Design Fixcity.** Pagina FO «Dichiarazione di accessibilità» (Cms) con link `form.agid.it`; **meccanismo feedback** come categoria segnalazione dedicata con SLA 30 gg e routing al RTD (riusa auto-routing 414); modulo intake accessibile (es. canale «emergenza sordi» = canale alternativo, riusa multi-channel 420).
- **AC.** (1) Dichiarazione pubblicata e linkata in footer; (2) feedback accessibilità come ticket con SLA 30 gg + escalation; (3) ruolo RTD assegnatario; (4) intake accessibile verificato.

### #438 — PagoPA (pagamenti) — **Could**

- **Business logic.** Abilita servizi/pratiche a pagamento collegati alle segnalazioni; moat Italy-first.
- **Norma.** PagoPA obbligatorio per incassi PA (CAD art. 5).
- **Competitor.** Municipium «paga la multa da smartphone».
- **Design Fixcity.** Gateway dietro contract `PaymentGatewayContract` + Action `CreatePagoPaPositionAction`; nessuna logica di pagamento nel dominio ticket.
- **AC.** (1) Posizione debitoria generata da pratica; (2) esito pagamento riconciliato e tracciato; (3) gateway sostituibile.

### #439 — Webhook outbound + connettori gestionali (oltre Open311 inbound) — **Should**

- **Business logic.** [STORY-413] espone Open311 **inbound**; manca il **push** verso i gestionali già in uso (no doppio inserimento dati).
- **Standard.** Open311 status sync; pattern webhook firmati.
- **Competitor.** Comuni-Chiamo «integrabile con ogni gestionale»; FixMyStreet Pro «propaga status updates».
- **Design Fixcity.** `Modules/Fixcity/app/Actions/Integration/DispatchTicketWebhookAction` su eventi di dominio (creazione/cambio stato), payload Open311-shaped, firma HMAC, retry in coda. Endpoint registrabili per-tenant.
- **AC.** (1) Cambio stato → webhook firmato; (2) retry con backoff; (3) mappatura stati↔Open311; (4) log consegna in Activity.

### #440 — Allerte Protezione Civile / emergenze broadcast geo — **Could**

- **Business logic.** Più ampio di 430 (alert su zona): broadcast PA→cittadino in emergenza (meteo, eventi), con piano emergenza e numeri utili.
- **Competitor.** Municipium (allerte meteo, piano emergenza, geolocalizzate).
- **Design Fixcity.** `Modules/Notify` broadcast geo + canale push (riusa 410 PWA); contenuti piano emergenza in **Cms**. Non confondere con i ticket.
- **AC.** (1) Broadcast geo-targettizzato; (2) opt-in per zona; (3) piano emergenza consultabile offline (PWA).

## 5. Priorità sintetica

| Ordine | Story | Driver | Effort |
|---|---|---|---|
| 1 | #437 Accessibilità AgID | **nullità contratto** (gara) | S |
| 2 | #434 Moderazione + blur PII | GDPR + reputazione | M |
| 3 | #433 Protocollo + PEC | capitolato PA | L |
| 4 | #439 Webhook outbound | no doppio data-entry | M |
| 5 | #435 Diritti GDPR | compliance | M |
| 6 | #436 CSAT/sondaggi | loop qualità | S |
| 7 | #438 PagoPA · #440 PC broadcast | estensione | L/M |

## 6. Confini di scope (esclusi per KISS)

Fuori da FixCity-segnalazioni (altri domini): calendario rifiuti, certificati ANPR, prenotazione sportello generico, news/eventi (già in **Cms**).

## 7. Coordinamento swarm

- **Non** ricreata la SSoT `docs/wiki/concepts/fixcity-competitor-census.md` (referenziata da STORY-397, in scrittura swarm) per evitare collisione su path; **Municipium/Decoro Urbano** vanno aggiunti lì quando stabile.
- Numerazione gap satura fino a **432** con doppioni 410/411/412 e schema 413–432 non ancora allineato ai file su disco: assegnare 433+ **dopo** lo sprint di consolidamento (deliverable aperto in STORY-397).

## 8. Ancoraggio al codice reale (verifiche + innesti)

Design verificato sul dominio Fixcity esistente — non più teorico.

### 8.1 Stato reale (cosa c'è già)

- **`TicketStatusEnum`** (reale): `DRAFT, PENDING, IN_REVIEW, IN_PROGRESS, ON_HOLD, RESOLVED, CLOSED, REOPENED, OPEN` (default `OPEN`). → **mancano** le milestone datate (`acknowledged, assigned, work_started, verified`) che STORY-400 timeline promette: oggi la timeline non ha eventi datati nativi.
- **Schema `tickets`** (owner): presenti `owner_id`(uuid), `responsible_id`, `status`(string) + `status_id`(legacy), `latitude/longitude/location`(json), **`email`** (→ guest reporting già abilitabile), `slug`, `type/priority/code`. **Assenti**: `protocol_number`, stato moderazione/`published_at`, `pa_office_id`, `resolved_at`.
- **Action riusabili**: `CreateTicketAction`, `ChangeStatus`, `GetTicketSlaMetricsAction`, `GetTicketKpiAggregateAction`, `BuildTicketPublicDetailsPayloadAction`, `TicketCitizenRating/*` + `SubmitCitizenTicketRatingAction` → **#436 CSAT riusa il rating esistente**, non reinventa.

### 8.2 Finding architetturali (prerequisiti, da fare PRIMA)

- **F1 — doppia migrazione owner**: `2026_04_29_..._create_tickets_table` **e** `2026_06_10_..._create_tickets_table`. Viola 1-migrazione-per-modello + dati sacri. **Consolidare in una sola** (bump timestamp) prima di aggiungere campi #433/#434.
- **F2 — `ChangeStatus` senza evento**: fa solo `$ticket->setStatus()`, `reason` non persistito. #433 (protocollo) e #439 (webhook) servono un **hook affidabile** → introdurre evento `TicketStatusChanged` (o `ChangeStatus` → QueueableAction che dispatcha). Prerequisito.
- **F3 — SLA su `updated_at`**: `GetTicketSlaMetricsAction` calcola risoluzione come `updated_at - created_at`: ogni modifica falsa il dato. Serve `resolved_at` (o storico stati Spatie) per SLA/CSAT accurati (#436, dashboard 404/427).

### 8.3 Mapping `TicketStatusEnum` ↔ Open311 (per #413/#439)

| Fixcity | Open311 GeoReport v2 |
|---|---|
| `DRAFT` | (non esposto) |
| `PENDING, IN_REVIEW, OPEN, REOPENED` | `open` |
| `IN_PROGRESS` | `in progress` |
| `ON_HOLD` | `open` (+ nota) |
| `RESOLVED, CLOSED` | `closed` |

`TicketTypeEnum` ↔ `service_code`; campi dinamici per categoria ↔ `service_definition.attributes`.

### 8.4 Innesto preciso dei 3 gap top

- **#437 (AgID)** — zero campi dominio: pagina Cms «dichiarazione accessibilità» (link `form.agid.it`) + nuovo case `TicketTypeEnum::ACCESSIBILITY_FEEDBACK` con SLA 30 gg e auto-routing (#414) al RTD. Solo config + 1 enum case.
- **#434 (blur PII + moderazione)** — `moderation_status` + `published_at` in `tickets.tableUpdate` (dopo F1); `Modules/Media/app/Actions/Image/BlurPiiAction` in coda upload; FO pubblica solo `whereNotNull('published_at')`. Riusa pipeline Media.
- **#433 (protocollo+PEC)** — `protocol_number` in `tableUpdate` (dopo F1); `RegisterTicketProtocolAction` agganciata a `TicketStatusChanged` (F2); PEC via driver **Notify**; gateway dietro `Models/Contracts/ProtocolGatewayContract`.

### 8.5 Per lo swarm

F1/F2/F3 impattano **STORY-400** (timeline senza eventi datati), **404/427** (SLA su `updated_at`), **413/414** (mapping stati/eventi). Da consolidare prima dei gap 433+.

## Collegamenti

- Programme gap: [STORY-397](../../../../../../docs/stories/STORY-397-competitor-gap-programme.md)
- Matrice internazionale: [competitive-analysis-fixcity-2026.md](../../../../../../docs/wiki/competitive-analysis-fixcity-2026.md)
- Strategia: [product-strategy](../../roadmap/product-strategy.md)
- Accessibilità tecnica: [STORY-394](../../../../../../docs/stories/STORY-394-pa-accessibility-i18n-completion.md)
