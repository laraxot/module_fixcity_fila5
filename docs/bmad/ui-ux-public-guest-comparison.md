---
title: "FixCity — confronto stato atteso/reale guest"
type: bmad-ux-gap-analysis
status: verified-baseline
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, ui, ux, gap, puppeteer]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
---

# Confronto

## Reale osservato

La richiesta GET /it restituisce HTTP 200 e il contenuto testuale è presente,
ma il browser non riceve:

- http://127.0.0.1:5173/resources/css/app.css;
- http://127.0.0.1:5173/resources/js/app.js;
- http://127.0.0.1:5173/@vite/client.

Le tre richieste falliscono con ERR_CONNECTION_REFUSED. Il risultato visivo è
HTML non stilizzato, con controlli impilati e immagini sproporzionate. Questo
è un difetto di avvio demo, non un problema di dati o di route.

## Delta

| Area | Atteso | Reale | Gap |
|---|---|---|---|
| Asset | CSS/JS caricati | Vite assente | blocco P0 |
| Responsive | layout fluido | layout nativo HTML | non valutabile prima degli asset |
| Navigazione | CTA e menu usabili | link presenti ma non presentati | degrado UX |
| Browser | zero richieste fallite | 3 richieste fallite | P0 |

Questa è la baseline storica pre-fix; la verifica successiva è riportata sotto
e non si limita al solo status HTTP.

## Verifica dopo il fix — 2026-09-27

Con build Sixteen ricompilata e senza un file `hot` obsoleto, Puppeteer/Chromium
ha caricato `/it` e `/en` su `localhost:8001`: stylesheet, script e sprite
ritornano 200; nessuna richiesta fallita e nessuna eccezione JavaScript. Il
contenuto inglese è localizzato. A 320, 390, 768, 1024 e 1440 px
`documentElement.scrollWidth === innerWidth`.

Il secondo confronto ha trovato un difetto aggiuntivo: gli override globali
`h1`, `p` e `span` con `!important` annullavano il contrasto utility della hero;
inoltre il padding orizzontale Tailwind e la decorazione dei CTA non erano
effettivi nella pagina compilata. Gli override circoscritti a `#welcome-heading`
ora mantengono titolo e testo bianchi, CTA leggibili senza sottolineatura e
padding interno coerente. Screenshot post-fix temporanei: `/tmp/fixcity-ui-audit/post-it-{320,390,768,1024,1440}.png`.

Il controllo delle destinazioni aveva confermato: `/it/tickets` raggiungeva
l'elenco Folio `/it/segnalazioni`, rendendo non canonico il percorso richiesto.
Ora `/it/tickets` ospita l'elenco Folio e `/it/segnalazioni` è un alias
compatibile con redirect localizzato. La CTA di creazione preserva `it` e porta al login
richiesto; login e registrazione rispondono 200. Il breadcrumb elenco esponeva
la chiave non definita `pub_theme::ui.home`; ora usa `pub_theme::footer.home`,
presente nei cataloghi EN/IT. La home mostrava inoltre il nome framework
`Laravel`: l'overline è stato allineato al brand FixCity.

## Confronto con i flussi ufficiali Design Comuni

Verificate le pagine di [elenco](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html), [area personale](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-area-personale.html), [privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html), [dati](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html), [riepilogo](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html), [conferma](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html) e [scheda servizio](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html). I riferimenti mostrano elenco con categoria, conteggio, mappa e schede; wizard in tre step con riepilogo modificabile; conferma con ricevuta e consultazione pratica; area personale con attività/pratiche; scheda servizio con requisiti, costi, contatti e condizioni. Aspettative e delta FixCity sono riportati nel contratto BMAD.

### Aggiornamento home — CTA elenco

Il markup della hero include ora un link dedicato “Vai all’elenco” accanto alle
azioni account, così la navigazione verso le segnalazioni non dipende dalla
scoperta del footer della mappa. Il link usa la traduzione del tema e genera
`/it/tickets` o `/en/tickets`; entrambe le home e le due liste rispondono HTTP 200.
Playwright Chromium ha verificato entrambe le lingue a 320, 390, 768, 1024 e
1440 px: zero overflow, target alto 44 px, focus outline 3 px, testo bianco su
verde con contrasto 5.38:1, nessun errore JS o richiesta fallita. Il click porta
alla lista localizzata; screenshot in `/tmp/fixcity-home-cta-audit/`. Per eseguire
Chromium senza sudo, le sole librerie mancanti sono state estratte in `/tmp`.

Il browser ha verificato il marker reale: CTA “Dettagli” / “Details”, destinazione HTTP 200 e altezza minima 48px; su viewport stretti il popup resta entro il viewport e l'azione è visibile. Il popup applica override circoscritti per resistere alle regole globali che rendevano il titolo bianco e il pulsante privo di contrasto.

Resta da correggere e verificare l'avvio HMR tramite `php artisan dev`: nel test
il comando ha avviato Vite sulla porta 5174 ma la configurazione root ha
terminato per `ELOOP` su `Modules/Seo/.agents/skills/qmd`; il processo `logs`
fallisce anche perché `pail` non è registrato. Questo non ha richiesto controller:
la pagina pubblica resta Folio e il back office Filament.
