---
id: STORY-518
title: "Cittadino — dettaglio delle proprie pratiche"
type: story
status: in_progress
module: Fixcity
priority: must
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, citizen, folio, privacy]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"
related:
  - ./STORY-008-citizen-my-tickets.md
  - ../../wiki/concepts/user-journey-map.md
  - ../../../../Themes/Sixteen/resources/views/pages/area-personale/pratiche.blade.php
  - ../../app/Actions/BuildAuthenticatedUserTicketsQueryAction.php
---

# STORY-518 — Dettaglio delle pratiche del cittadino

## Contesto verificato

`php artisan folio:list --path=area-personale/pratiche --json` mostra come route attiva
la pagina Sixteen. La sua lista è popolata da `BuildAuthenticatedUserTicketsQueryAction`
con filtro `owner_id`; ogni riga offre il dettaglio autenticato e, se il codice esiste,
anche il tracking per capability. La pagina
`Modules/User/.../pratiche.blade.php` non compare nella route Folio attiva e duplica il nome.

Il dettaglio `/{locale}/tickets/{id}` usa `BuildPublicTicketsQueryAction`, che
consente a un cittadino autenticato di consultare i propri ticket privati e non
restituisce quelli privati appartenenti ad altri cittadini.

## User story

Come cittadino autenticato, voglio aprire il dettaglio di una mia pratica
dall’elenco personale, così posso consultare i dati completi senza dover
ricordare o inserire il codice di tracking.

## Acceptance criteria

- [x] La route attiva e la query proprietario sono state identificate da codice e Folio.
- [x] Ogni pratica personale offre un’azione localizzata per aprire il dettaglio.
- [x] Il proprietario apre il dettaglio anche per una pratica privata.
- [x] Un altro cittadino e un guest ricevono 404 sui dettagli privati.
- [x] Tracking e pratiche proprie restano disponibili; dati e capability di terzi non compaiono nella lista.
- [x] Test Feature mirati: 2 test / 14 asserzioni, inclusi privacy e CTA localizzato.
- [x] Pint mirato, PHPStan mirato e `verify-llm-wiki.sh` passano.
- [x] Browser autenticato IT/EN per stato vuoto a 320/390/768/1440 px: 8/8 senza overflow o errori JS; CTA tradotto.
- [ ] Browser della lista popolata e apertura del dettaglio con dati sintetici visibili; test Feature copre accesso e privacy.

## Confini

- Frontoffice: Folio/Volt e Actions. Nessun controller e nessuna logica nel layer Services.
- Il dettaglio resta accessibile tramite la policy e la query di dominio già presenti.
- La pagina duplicata in `Modules/User` è lockata da un altro lavoro: questa story
  registra la ridondanza, non la modifica né la elimina.

## Nota di verifica

Il test `tests/Feature/Pages/CitizenMyTicketsDetailLinkTest.php` passa con
`APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests/Feature/Pages/CitizenMyTicketsDetailLinkTest.php`:
2 test, 14 asserzioni. Copre redirect guest all’area personale, CTA tradotto, 404 guest e altro
cittadino su pratiche private, dettaglio del proprietario, destinazione localizzata,
link tracking e assenza dei dati/code di terzi. Il test usa SQLite temporaneo condiviso
e transazioni, senza toccare il database MariaDB. Puppeteer ha verificato l'area
autenticata IT/EN a 320/390/768/1440 px: titolo e CTA coerenti, larghezza documento
uguale al viewport e nessun errore console/JS. Il DB demo non contiene pratiche, quindi
il browser ha verificato lo stato vuoto; la lista popolata e il dettaglio restano coperti
da Feature test, in attesa di una fixture browser isolata. Screenshot: `/tmp/fixcity-practice-details-it-390.png`.

Il legacy `STORY-008` è lockato e descrive rotte Filament del cittadino che non
corrispondono all’architettura FO corrente. Non viene riscritto in questa story;
la mappa dei percorsi e questa verifica Folio sono la baseline runtime.
