---
type: bmad-initiative
status: open
created: 2026-10-17
updated: 2026-10-20
initiative_id: kp-designcomuni-faq-parity
summary: |
  Allineamento pagina /it/domande-frequenti al design-comuni (design-comuni-pagine-statiche/sito/domande-frequenti.html): breadcrumb hero, card gradient, accordion Q/A, navigazione correlata con pill, footer accessibilità WCAG 2.1 AA. Stack Tailwind + Alpine + Lit + DaisyUI + Flowbite (NO Bootstrap). Utilizzato BMAD + Second Brain + EEC.
references:
  - https://italia.github.io/design-comuni-pagine-statiche/sito/domande-frequenti.html
  - laravel/Themes/Sixteen/resources/views/components/blocks/content-page/design-comuni.blade.php
title: "Contenuto"
tags: [docs, fixcity, bmad]
qmd: "contenuto fixcity"
---

# Contenuto

Le domande non stanno nel blade. Il catalogo civico è
`database/seeders/Support/CivicFaqCatalog.php` (it, en, de, es): invio,
account, nome pubblico, foto, luogo, stato, tempi, ufficio, aggiornamento,
emergenza, visibilità, cosa fare se manca la risposta.

`FaqSeeder` le scrive sul modello `Faq`. La pagina le legge con
`ListPublishedFaqsAction`. Il JSON del tenant può ancora passare `items`
se una pagina deve un elenco diverso.

La pagina non ha una vista Folio propria. `/it/domande-frequenti` è lo slug `domande-frequenti.index` dentro `[container0]/index`. Una cartella `pages/domande-frequenti/` prenderebbe il posto di quella shell: Folio prova il nome vero prima del jolly. Il perché: [why-domande-frequenti-blade-php-must-not-exist.md](../../../../Themes/Sixteen/docs/bmad/knowledge/why-domande-frequenti-blade-php-must-not-exist.md).

Il vestito non sta in `main[data-page="domande-frequenti"]` e non sta in uno `<style>` di pagina. La pagina è un `content-page`: ricerca, accordion, parere e contatti sono `.cmp-input-search`, `.cmp-accordion`, `.cmp-rating` e `.cmp-contacts`, gli stessi delle altre pagine di contenuto. Il foglio è `20-dc-components.css`, sotto `.page-content[data-dc]`.

Dentro `data-dc` le utility Tailwind perdono: senza quel foglio il titolo del parere resta a 40px e la scheda occupa tutta la larghezza.
