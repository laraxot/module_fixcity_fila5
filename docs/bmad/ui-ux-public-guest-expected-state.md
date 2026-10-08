---
title: "FixCity — stato visivo atteso del percorso guest"
type: bmad-ux-spec
status: approved-for-implementation
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, ui, ux, guest, responsive, accessibility]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
---

# Stato visivo atteso

## Riferimento Design Comuni

La UI segue il flusso e i componenti dei prototipi ufficiali:

- [Elenco segnalazioni](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html): riepilogo, filtri, conteggio, mappa, schede accessibili, CTA e contatti. La rotta canonica FixCity è `/{locale}/tickets`; `/{locale}/segnalazioni` resta un alias di compatibilità.
- [Scheda dettaglio servizio](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html): descrizione, come fare, requisiti, esito, costi, canali, condizioni e contatti.
- [Wizard, privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html): primo passaggio con informativa e consenso esplicito.
- [Wizard, dati](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html): luogo, tipologia, titolo, dettaglio, allegati e dati del cittadino.
- [Wizard, riepilogo](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html): riepilogo verificabile, modifica dei dati e conferma prima dell'invio.
- [Wizard, conferma](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html): ricevuta, stato di presa in carico e accesso alla richiesta.
- [Area personale](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-area-personale.html): pratiche, stati, aggiornamenti, ricerca e documenti dell'utente autenticato.

I prototipi sono riferimenti di struttura e contenuto: dati personali e testi dimostrativi non vanno copiati nel prodotto.

## Scope

Il percorso riguarda un visitatore non autenticato su /it e /en, a 320, 768 e
1440 pixel, senza dipendere da un processo Vite avviato manualmente.

## Definition of Done

- Il browser riceve CSS e JavaScript dallo stesso host della pagina o dal Vite
  server dichiarato dal comando Artisan dev.
- Header, skip links, lingua, CTA, filtri, mappa/lista e footer hanno layout
  leggibile, senza overflow orizzontale.
- La CTA per una nuova segnalazione è unica e porta al login mantenendo la
  lingua; nessun link operativo punta a #.
- La CTA della mappa apre `/{locale}/tickets`; la rotta legacy `segnalazioni`
  reindirizza a quella canonica.
- Ogni marker offre “Dettagli” e apre una scheda pubblica funzionante nella
  lingua corrente.
- Immagini e icone hanno dimensioni finite, alt quando informative e non
  deformano la card.
- Console JavaScript, richieste fallite e errori HTTP sono vuoti nel percorso
  nominale.
- Il contenuto visibile non contiene chiavi di traduzione grezze.

## Evidenza richiesta

Puppeteer/Chromium deve salvare screenshot desktop e mobile, misurare
scrollWidth === innerWidth, verificare i fogli di stile, cliccare la CTA e
controllare la lingua. Il report deve distinguere un difetto applicativo da un
runtime Vite non avviato.
