---
title: "FixCity — verifica navigazione pubblica e allineamento Design Comuni"
type: bmad-ux-verification
status: verified-with-follow-up
created: 2026-09-27
updated: 2026-09-27
tags: [bmad, fixcity, ux, navigation, guest, design-comuni, puppeteer]
qmd: "FixCity public guest navigation Design Comuni workflow verification Puppeteer"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
---

# Verifica pubblica guest

## Contratto atteso

Il flusso pubblico segue il modello Design Comuni: elenco con filtri e mappa,
scheda servizio/dettaglio, wizard privacy → dati → riepilogo → conferma, area
personale per pratiche e aggiornamenti. Le pagine di riferimento sono:

- [elenco segnalazioni](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazioni-elenco.html);
- [area personale](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-area-personale.html);
- [privacy](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-01-privacy.html);
- [dati](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-02-dati.html);
- [riepilogo](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html);
- [conferma](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-04-conferma.html);
- [dettaglio servizio](https://italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-dettaglio.html).

## Evidenza reale

Puppeteer/Chromium su `http://localhost:8001` ha verificato il 27/09/2026:

| Percorso | Esito | Nota |
|---|---:|---|
| `/it` | 200 | nessun errore JS, nessuna richiesta fallita, nessun overflow orizzontale |
| `/it/tickets` | 200 | elenco, mappa e CTA demo disponibili |
| `/it/tickets/11` | 200 | dettaglio Folio canonico |
| `/it/tickets/track` | 200 | tracking pubblico per codice |
| `/it/auth/login` | 200 | accesso guest |
| `/it/auth/register` | 200 | registrazione guest |
| `/it/tickets/create` | 302 → `/it/auth/login` | protezione corretta per guest |
| `/it/area-personale/pratiche` | 302 → `/it/auth/login` | locale `it` preservato |

Il popup del marker usa un link reale `/it/tickets/{id}`, testo `Dettagli`,
target minimo 48px e auto-pan; la collisione Folio `[slug]` è stata rimossa,
lasciando `[id]` come unico dettaglio canonico.

## Verifica multilingua estesa

Puppeteer ha verificato a 390px i percorsi home, news, administration, services
e tickets per `it`, `en`, `de` ed `es`: tutti HTTP 200, zero errori JavaScript,
zero richieste fallite, zero overflow orizzontale e zero link `#` o vuoti. Le
Blade pubbliche usano cataloghi di traduzione, inclusi i cataloghi Sixteen
`home`, `services`, `news` e `administration` per tutte le lingue supportate.
I link dei servizi non puntano più a pagine showcase `/tests`: usano percorsi
localizzati reali, CTA di creazione ticket o ancore della pagina corrente.
Login, registrazione e tracking hanno inoltre cataloghi dedicati per `it`, `en`,
`de` ed `es`; il test browser conferma titoli localizzati e zero richieste
fallite. L’alias `/segnalazioni` resta solo un redirect 301 verso `/tickets`.

Il comando Playwright MCP non è disponibile in questo ambiente; Puppeteer è il
sostituto riproducibile usato per l’evidenza.
`php artisan dev` è stato provato: la configurazione Sixteen ora viene avviata
dal processo demo e Vite non attraversa più i symlink ricorsivi ELOOP. Le porte
8000–8002 erano già occupate, quindi quel run ha avviato Laravel su 8003; il
server demo già attivo su 8001 è rimasto quello usato dalla verifica browser.

Il test Pest mirato non ha raggiunto le asserzioni per credenziali MariaDB di
test non disponibili (`Access denied for user '${FIXCITY_TEST_DB_USERNAME}'`);
non è un fallimento applicativo osservato dal browser.
