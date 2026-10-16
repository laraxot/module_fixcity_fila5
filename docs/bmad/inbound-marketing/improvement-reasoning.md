---
title: "Ragionamento — come migliorare FixCity con inbound civico"
type: analysis
status: decision-input
created: 2026-10-15
updated: 2026-10-15
tags: [bmad, improvement, inbound-marketing, dry, kiss, sustainability]
qmd: "migliorare FixCity inbound pillar fiducia adozione eventi blog CTA"
issues: []
discussions: []
---

# Domanda

Dopo lo studio di [AFC Formazione](https://www.afcformazione.it/blog/),
[Alessandro Ferrari](https://www.aferrari.it/articoli/) e
[AssodellaVendita](https://www.assodellavendita.it/blog/), **come migliorare
FixCity** senza copiare un ecosistema B2B?

# Filosofia (perché prima del come)

| Domanda | Risposta FixCity |
|---|---|
| Scopo | Far funzionare la relazione cittadino ↔ comune sulla segnalazione |
| Religione (valori) | Trasparenza, accessibilità, privacy, indipendenza editoriale |
| Politica | B2G e co-funding prima di sponsor; no ad-tech |
| Logica | Contento utile → evento → adozione → collaborazione → sostegno |
| Zen / KISS | Riusare `Article` blog\|event; zero modulo Eventi; zero vanity KPI |

Inbound non è marketing di marca: è **ridurre l'attrito tra problemi civici e
comportamenti utili** (segnalare bene, rispondere, adottare, collaborare).

# Diagnosi attuale (gap)

1. **Promessa FO** spesso più forte delle **prove di consegna** (stato ticket,
   tempi, esiti comprensibili) — gap Asso «fiducia dopo la promessa».
2. **Contenuti** non organizzati in pillar/cluster → rischio articoli isolati
   (gap AFerrari: journey e cluster).
3. **Eventi** come date su Article senza type/validazione/FO Design Comuni
   (gap Blueprint Blog).
4. **Hub risorse** assente o disperso → niente «poche e utili» (gap AFC).
5. **Adozione PA** trattata come feature, non come frizioni quotidiane
   (gap Asso CRM).
6. **Sostegno** a rischio confusione con banner → già deciso: partners sì,
   banner commerciali no (footer-sponsorship).
7. **Nurture email** prematuro senza Gdpr + policy (deferred Notify).

# Miglioramenti prioritizzati (P0 → P2)

## P0 — Fiducia e governance (prima di più contenuti)

| Azione | Owner docs | Perché |
|---|---|---|
| Owner/revisore + calendario 90g | Blog IM-BLOG-001 | Senza governance, il blog è rumore |
| Privacy iscrizione evento | Gdpr IM-GDPR-001 | Eventi senza consenso = rischio legale |
| Prove di fiducia post-segnalazione (storytelling + KPI utili) | Fixcity IM-FIXCITY-002 | Chiude il gap «promessa vs consegna» |
| CTA progressive documentate (informati→…→sostieni) | Blog IM-BLOG-004 | Una CTA primaria per pezzo |

## P1 — Motore editoriale (pattern tre siti)

| Azione | Owner | Perché |
|---|---|---|
| 4–6 evergreen pillar | Blog IM-BLOG-005 | Densità AFC, non volume |
| Hub Cms «poche risorse» | Cms IM-CMS-001 | Strumento dopo metodo |
| Playbook/checklist asset | Blog IM-BLOG-006 | Bridge Asso playbook |
| Frizioni adozione operatori | Fixcity IM-FIXCITY-003 | Contenuti PA utili, non brochure |
| Blueprint Filament type+date | Blog IM-BLOG-002/003 | Existing Assets First |
| FO Sixteen listing | Sixteen | Presentazione Design Comuni |
| Meetup presentazione evento | Meetup | Evento = esperienza, non solo card |

## P2 — Misura e crescita responsabile

| Azione | Perché |
|---|---|
| KPI utilità/partecipazione/adozione | Anti-vanity (AFerrari) |
| Schema SEO Article/Event | Scopribilità senza tracking invasivo |
| Localizzazione + tenant isolation | Multi-comune |
| Notify nurture | Solo dopo I.3 + policy |
| Partner/sostieni con governance | Sostenibilità senza ads |

# Mappa pillar → miglioramento prodotto

```text
cittadino attivo     → guide segnalazione + prove stato ticket
comune operativo     → checklist frizioni adozione + demo BO
comunità accessibile → eventi a11y + contenuti WCAG
dati / impatti       → KPI utili aggregati, linguaggio chiaro
progetto sostenibile → grant/partner/CTA sostieni con policy
```

Ogni pillar produce **almeno** un evergreen + un asset (checklist/playbook) +
opzionale evento. Non pubblicare pezzi orfani.

# Cosa migliorare nel prodotto (inbound-driven), non solo nel blog

Questi miglioramenti sono **requisiti di esperienza** che l'inbound rende
visibili; restano discovery finché readiness = CONCERNS:

1. **Stato e tempi comprensibili** dopo la segnalazione (prova di fiducia).
2. **Onboarding operatori** riducendo campi e doppie registrazioni (adozione).
3. **Eventi locali** come laboratorio (Meetup + Article `content_type=event`).
4. **Hub risorse** collegato a sezioni Cms, non PDF sparsi.
5. **Footer sostieni** = pagina partner/mission, mai banner.

Implementazione runtime: solo dopo gate readiness e story `ready-for-dev`.

# Criteri di successo (90 giorni pilota)

- [ ] Owner + revisore nominati
- [ ] ≥4 evergreen pubblicati (o in draft approvato) su 4 pillar
- [ ] 1 hub risorse outline Cms
- [ ] 1 evento pilota con privacy documentata
- [ ] 0 banner commerciali; CTA sostieni solo se policy ok
- [ ] KPI definiti senza vanity metrics
- [ ] Nessun nuovo modulo Eventi / nessuna migrazione non necessaria

# Collegamenti

- [asso-pattern-deep-study.md](./asso-pattern-deep-study.md)
- [afc-aferrari-asso-pattern-analysis.md](./afc-aferrari-asso-pattern-analysis.md)
- [recommendation.md](./recommendation.md)
- [epics.md](./epics.md)
- [../project-sustainability/README.md](../project-sustainability/README.md)
- [../footer-sponsorship/README.md](../footer-sponsorship/README.md)

# Approfondimento Asso (2026-10-15) — come migliora il prodotto

## Tre prove di fiducia → FO segnalazione

Dall’articolo [fiducia](https://www.assodellavendita.it/fiducia-del-cliente/):

1. **Accordo** — cittadino e Comune condividono cosa è stato segnalato/preso in carico  
2. **Utilità** — cittadino vede un episodio concreto (aggiornamento, esito), non solo «inoltrato»  
3. **Continuità** — sa chi è il referente e quando arriva il prossimo aggiornamento  

Miglioramento prodotto (discovery): copy + UI di tracking ticket devono rendere
visibili queste tre prove. Story: IM-FIXCITY-002.

## Sette errori CRM → onboarding operatori

Dall’articolo [adozione CRM](https://www.assodellavendita.it/adozione-crm-venditori/):

Prima di «formare di più», riduci campi inutili, elimina doppie registrazioni,
fai usare il pannello in riunione operativa, misura next-step validi.
Story: IM-FIXCITY-003 + contenuti pillar «comune operativo».

## Perché questo migliora FixCity più di «più articoli»

Volume editoriale senza prove di consegna e senza ridurre frizioni BO aumenta
la distanza tra slogan e servizio. L’inbound deve **espore** i gap prodotto e
guidare le story di fiducia/adozione, non mascherarli.
