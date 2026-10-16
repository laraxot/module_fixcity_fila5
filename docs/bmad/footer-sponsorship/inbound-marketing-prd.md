---
title: "PRD discovery — blog ed eventi inbound"
type: bmad-prd
status: proposed-for-validation
updated: 2026-10-15
---

# Requisiti di discovery

- FR-IM-001: ogni contenuto dichiara pubblico, problema, autore e data di revisione;
- FR-IM-002: ogni evento dichiara modalità, accessibilità, programma e privacy;
- FR-IM-003: le CTA distinguono informazione, adozione, collaborazione e sostegno;
- FR-IM-004: una sponsorship evento è contestuale, dichiarata e non appare come endorsement;
- FR-IM-005: contenuti e URL sono localizzabili e isolati per tenant;
- FR-IM-006: le metriche sono aggregate e non richiedono advertising tracker.

## Non requisiti

Non sono inclusi: banner nel footer, caroselli sponsor, newsletter automatica,
paywall, pagamenti, profilazione, nuova dipendenza o nuova Resource Filament.
L'authoring del pilota può estendere l'`ArticleResource` esistente secondo il
[Filament Blueprint Blog/Eventi](../../laravel/Modules/Blog/docs/blueprints/inbound-blog-events.md).

## Acceptance gate

Validare con almeno un cittadino, un operatore e un referente PA: comprensione
della CTA, riconoscimento della disclosure, navigazione da tastiera, reflow e
chiarezza dell'iscrizione evento.
