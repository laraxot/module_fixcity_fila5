---
title: "UX contract — inbound blog ed eventi"
type: ux-design
status: proposed
updated: 2026-10-15
tags: [bmad, ux, wcag, design-comuni]
qmd: "inbound UX card blog evento CTA accessibilità Design Comuni"
issues: []
discussions: []
---
# UX contract

## Principio

Una pagina risponde a un bisogno: problema → spiegazione → strumento → passo
successivo. La CTA primaria è una sola e descrive l'azione, non la pressione.

## Contratto visivo

- testo leggibile, peso tipografico non eccessivo e contrasto verificato;
- heading gerarchici, focus visibile, target ampi e uso completo da tastiera;
- data/stato evento testuali, non solo colore o icona;
- immagini con alt utile e media decorativi ignorabili;
- rispetto di `prefers-reduced-motion`, zoom e viewport 320 px;
- header/footer alimentati dai JSON delle sezioni, senza duplicare markup nel tema.

## Stati

Lista: loading, risultati, empty, errore. Evento: prossimo, esaurito, concluso,
annullato. Ogni stato deve restare comprensibile senza JavaScript.
