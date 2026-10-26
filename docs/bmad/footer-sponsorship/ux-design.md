---
title: "UX contract — partner e sostegno nel footer"
type: ux-design
status: proposed
updated: 2026-10-15
tags: [bmad, ux, wcag, footer, sponsorship]
qmd: "ux contract fixcity"
---

# UX contract

## Gerarchia proposta

```text
Footer istituzionale
├── contatti e servizi
├── Partner e sostenitori
│   ├── disclosure breve
│   └── loghi statici, responsive, con nome testuale
└── Sostieni il progetto (CTA separata, opzionale)
```

La sezione non deve competere visivamente con «Segnala», «Traccia» o i link di
privacy/accessibilità.

## Stati

- **Assente:** nessun blocco pubblicato; non mostrare titolo vuoto.
- **Pubblicato:** loghi e nomi leggibili, ordine editoriale stabile.
- **Scaduto/revocato:** non visibile al cittadino; audit conservato.
- **Errore asset:** mostrare il nome del partner e non un'immagine rotta.
- **Zoom/reflow:** una colonna o griglia fluida, senza overflow orizzontale.

## Accessibilità

- Link con nome accessibile che include il partner e, se pertinente, «partner
  sostenitore».
- Alt text non duplicato; loghi decorativi hanno `alt=""` solo quando il nome è
  già espresso nello stesso link.
- Focus visibile, ordine DOM logico, target almeno 44×44 CSS px.
- Nessun carosello o animazione automatica; `prefers-reduced-motion` rispettato.
- La disclosure è testo reale, non tooltip o testo dentro un'immagine.

## Copy

Titolo consigliato: **Partner e sostenitori**.

Disclosure: «Il servizio è sostenuto da partner indicati in questa sezione. La
presenza del logo non costituisce endorsement dei prodotti o servizi.»

CTA solo se approvata: **Scopri come sostenere FixCity**. Evitare «Acquista» o
messaggi di urgenza nel contesto del servizio pubblico.
