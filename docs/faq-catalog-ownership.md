---
title: "FAQ: ownership del catalogo e rendering"
type: contract
status: active
created: 2026-10-20
updated: 2026-10-20
tags: [faq, fixcity, cms, catalog, localization]
qmd: "ownership faq fixcity"
---

# Ownership FAQ

Fixcity possiede il significato civico delle domande e delle risposte. Il
catalogo localizzato è in `database/seeders/Support/CivicFaqCatalog.php` e la
lettura pubblica è incapsulata in `ListPublishedFaqsAction`.

Sixteen veste il blocco `content-page`. Cms possiede la composizione della
pagina e il tenant JSON decide le sezioni. L’accordion con `source: faq`
chiama `ListPublishedFaqsAction`.

## Flusso

```text
CivicFaqCatalog / Action → JSON content-page → accordion source faq → browser
```

Il perché della view Folio vietata: [why-domande-frequenti-blade-php-must-not-exist.md](../../../Themes/Sixteen/docs/bmad/knowledge/why-domande-frequenti-blade-php-must-not-exist.md).

Non aggiungere domande hardcoded a una Folio page del tema e non creare
`Themes/Sixteen/resources/views/pages/domande-frequenti/index.blade.php`.
Quella view duplicherebbe il routing e potrebbe mostrare un 200 fuori dal
flusso CMS, con contenuti e localizzazione divergenti.

## Regole dati

- il catalogo supporta `it`, `en`, `de`, `es`;
- il fallback per tabella assente/vuota resta nell'Action;
- URL e label visuali restano localizzati nel tema/CMS;
- relazioni nullable e dati CMS incompleti devono essere gestiti senza
  trasformare la pagina in una soft-404;
- i test devono verificare il contratto completo, non solo lo status HTTP.
