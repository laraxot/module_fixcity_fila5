---
title: "FixCity — audit UI/UX runtime"
type: audit
created: 2026-09-26
updated: 2026-09-27
qmd: "FixCity UI UX browser responsive test assets map ticket listing"
issues: []
discussions: []
tags: [bmad, fixcity, ui, ux, browser, verification]
---

# Audit UI/UX runtime — 26 settembre 2026

## Ambito verificato

Route pubblica `/it/segnalazioni`, prima con lista vuota e poi con attivazione del tab **Elenco**. Viewport controllati: 320, 768 e 1.440 px. Screenshot archiviati durante il controllo in `/tmp/fixcity-final-{320,768,1440}.png`; il file `/tmp/fixcity-final-list-320.png` documenta il passaggio al pannello Elenco.

### Aggiornamento 27 settembre: CTA e metadati

Il CTA è stato cliccato nel browser Chromium: porta a `/it/tickets/create` e, da guest, termina su `/it/auth/login` con form password visibile. La lista ha titolo browser “Segnalazioni”, un solo CTA e nessun errore JS. Il testo brand mobile è stato ridotto a 16 px fino a 359 px: niente ellissi a 320 px. Nuovi screenshot temporanei: `/tmp/fixcity-after-header-{320,1440}.png`.

Il 27 settembre è stato esteso lo smoke guest alle route `/it/`, `/it/auth/login`, `/it/auth/register`, `/it/segnalazioni`, `/it/segnalazioni/create`, `/it/area-personale/pratiche`, `/it/area-personale/seguite` e `/it/tickets/track`. Tutte le risposte finali sono 200; le route protette terminano sul login localizzato. Nessun errore JS. La home usa il JSON CMS owner `config/local/fixcity/database/content/pages/home.json` e mostra elenco, mappa e CTA.

| Controllo | Esito | Evidenza |
|---|---|---|
| HTTP e JavaScript | ✅ | HTTP 200, nessuna eccezione browser o richiesta fallita |
| CSS del tema e consenso | ✅ | stylesheet caricati; regole `.lcc-*` presenti nel CSS cookie pubblicato da GDPR |
| Mappa | ✅ | `<map-lit>` definito e dimensioni non nulle nei tre viewport |
| Responsive | ✅ | `documentElement.scrollWidth === innerWidth` a 320/768/1.440 px |
| Tab Mappa/Elenco | ✅ | click reale attiva il pannello Elenco e aggiorna `aria-selected` |
| Lista vuota e filtri | ✅ | Feature test coprono empty state, conteggio live, status e paginazione |
| CTA crea segnalazione | ✅ | Pest 2/9; click guest termina al login locale |
| Titolo e metadati pagina | ✅ | Browser title “Segnalazioni” |
| Brand mobile | ✅ | “Il mio Comune” interamente visibile a 320/360 px, nessun overflow |
| Tab Elenco su viewport minimo | ✅ | Click reale: tab selezionata, empty state presente, CTA unico, 320 px senza overflow |
| Tracking guest, codice non trovato | ✅ | IT 320 px / EN 1.440 px; codice conservato, label/error localizzati, `aria-invalid`, zero overflow/JS |
| Tracking guest valido | ✅ | SQLite effimero; 320/768/1.440 px, status e timeline localizzati, code capability non ripetuto |
| Lookup tracking owner/guest by ID | ✅ | Owner autenticato vede code; browser guest separato riceve 403 |

## Correzioni associate

- I filtri e i conteggi del front office provengono dall'aggregato live basato sui Ticket; il vecchio `BuildTicketFilterAggregateAction` è un adapter, non legge più fixture statiche.
- Lo stato vuoto non renderizza una rail laterale priva di filtri e usa tutta la larghezza disponibile.
- Il CSS cookie consent resta nel pacchetto owner GDPR ed è servito dal path pubblicato `vendor/cookie-consent/css/cookie-consent.css`; il layout lo richiama senza import Composer incrociati nel bundle Sixteen.
- Il CSS delle azioni Xot è pubblicato dal provider con tag `xot-assets`; il ciclo `post-autoload-dump` root pubblica i tag `cookie-public` e `xot-assets` in modo ripetibile.

## Verifica automatica

- `APP_ENV=testing FIXCITY_TEST_SQLITE=1 vendor/bin/pest Modules/Fixcity/tests --compact`: **363 test, 1.507 asserzioni passati** il 2026-09-27; include regressioni CTA, metadata CMS e tracking IT/EN con lookup valido.
- `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress`: **zero errori**.
- `npm run build:with-webroot` per Sixteen: completato; `composer validate --no-check-publish`: valido.
- `php artisan vendor:publish --tag=cookie-public --force` e `--tag=xot-assets --force`: entrambi pubblicati correttamente.
- `bash bashscripts/quality-gates/verify-llm-wiki.sh`: PASS; `git diff --check`: PASS.

## Gap residui

Un precedente screenshot mostrava l’area tile destra grigia perché acquisito prima del completamento del caricamento. La ripetizione attende rete inattiva e ulteriore stabilizzazione: 15 tile caricati, nessuna richiesta tile fallita, mappa 1.108 px. Il GeoJSON live restituisce zero ticket; marker e popup su record deployati restano da provare. Il brand mobile ora è leggibile per intero. Tracking valido, codice errato, lookup owner e negazione guest via ID sono stati verificati nel browser su SQLite effimero. Wizard completo e smoke staging restano aperti.

Tracking capability, timeline pubblica e isolamento payload sono coperti da Pest e browser su DB effimero; deploy/MySQL reali restano da collaudare. La pagina “Segnalazioni seguite” usa link capability, gestisce ticket legacy senza codice e non espone ID sequenziale; test HTTP coprono anche limite 10/minuto. Questo audit non dimostra il wizard autenticato, rating interattivo o console PA. Tastiera/screen reader, contrasto misurato e staging restano controlli separati.
