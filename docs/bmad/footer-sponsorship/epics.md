---
title: "Epics — sostenibilità editoriale del footer"
type: epics
status: proposed
updated: 2026-10-15
tags: [bmad, epics, footer, sponsorship]
qmd: "epic map fixcity"
---

# Epic map

## Epic 1 — Decisione e governance

Obiettivo: stabilire se, chi e con quali regole può sostenere FixCity.

- **1.1 — Policy partner/sponsor** — definire categorie ammesse, disclosure,
  conflitti, diritti logo, revoca e owner. `backlog`
- **1.2 — Modello economico e beneficiario** — confrontare grant, partner,
  donazioni e sponsor con costi e responsabilità. `backlog`

## Epic 2 — Esperienza e contenuto

Obiettivo: validare la comprensione senza implementare il footer.

- **2.1 — Copy e prototipo statico accessibile** — testare «Partner e sostenitori»
  contro «Sponsor» e CTA opzionale. `backlog`
- **2.2 — Piano di misurazione** — definire comprensione, fiducia, reclami,
  abbandono e metriche di impatto senza tracking invasivo. `backlog`

## Epic 3 — Eventuale implementazione

Obiettivo: solo dopo readiness gate, alimentare il blocco CMS e gestirlo in Filament.

- **3.1 — Contratto CMS tenant/localizzato** — `backlog`
- **3.2 — Blueprint editoriale e approvazione** — `backlog`
- **3.3 — Rendering Sixteen e test WCAG** — `backlog`

Nessuna storia dell'Epic 3 è `ready-for-dev`: mancano decisione del tenant,
policy, beneficiario e risultati della discovery.

## Story contract

Storie discovery compilate in [stories/](./stories/):

| ID | File | Stato |
|---|---|---|
| 1.1 | [1.1-policy-partner-sponsor.story.md](./stories/1.1-policy-partner-sponsor.story.md) | backlog |
| 1.2 | [1.2-modello-economico-beneficiario.story.md](./stories/1.2-modello-economico-beneficiario.story.md) | backlog |
| 2.1 | [2.1-copy-prototipo-accessibile.story.md](./stories/2.1-copy-prototipo-accessibile.story.md) | backlog |
| 2.2 | [2.2-piano-misurazione.story.md](./stories/2.2-piano-misurazione.story.md) | backlog |

Questa mappa **non** autorizza implementazione Epic 3 e non introduce scope
runtime nel backlog globale di FixCity finché readiness resta CONCERNS.
