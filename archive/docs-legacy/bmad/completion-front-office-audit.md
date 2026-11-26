---
title: "Audit del front office: indice e sintesi"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, audit, front-office, ospite, cittadino, accessibilita, lacune]
qmd: "audit front office ospite cittadino pagine flussi accessibilita lingue seo prestazioni sintesi indice"
related: ["completion-gap-register.md", "completion-front-office-blueprint.md", "completion-planning.md"]
---

# Audit del front office: indice e sintesi

## Fase BMAD

Analysis. Sei analisti hanno percorso il front office da ospite e da cittadino collegato, pagina per pagina e flusso per flusso, il 2026-10-26, su un'istanza di prova isolata (database usa e getta, utente di prova, dodici segnalazioni pubbliche, una personale, due articoli, due eventi). Non hanno scritto nel repository. I rapporti per intero sono nella cartella `completion-audit/`; qui c'e' l'indice e la sintesi. Ogni riscontro e' una riga del [registro](completion-gap-register.md), con la storia che lo chiude.

## Come sono stati misurati i riscontri

- MISURATO: riprodotto con uno script (Playwright, Puppeteer, curl, axe) o letto su uno screenshot guardato.
- DEDOTTO: letto nel codice, non provato in esecuzione.
- Ogni correzione riparte da una nuova riproduzione. Due volte la causa indicata da un'analisi era sbagliata, e una volta una misura era stata fatta sull'istanza sbagliata (identificatori diversi tra istanza di prova e istanza di sviluppo).
- Limiti dichiarati: nessun lettore di schermo reale, nessun dispositivo touch, nessun Safari o Firefox, nessuna stampa reale, nessuna posta consegnata.

## Rapporti

| Rapporto | Perimetro | Riscontri | Bloccanti |
|---|---|---|---|
| [1. Intestazione e piede](completion-audit/front-office-1-intestazione-e-piede.md) | Header, footer, ricerca, menu, sitemap, 404, lingua | 29 | 5 |
| [2. Segnalazioni e area personale](completion-audit/front-office-2-segnalazioni-e-area-personale.md) | Elenco, mappa, dettaglio, traccia, area personale | 38 | 3 |
| [3. Wizard e autenticazione](completion-audit/front-office-3-wizard-e-autenticazione.md) | Login, registrazione, reset, wizard, conferma | 30 | 7 |
| [4. Contenuti, servizi e testi legali](completion-audit/front-office-4-contenuti-servizi-e-testi-legali.md) | Blog, eventi, FAQ, privacy, note legali, servizi | 33 | 8 |
| [5. Accessibilita' e responsive](completion-audit/front-office-5-accessibilita-e-responsive.md) | 39 pagine a 5 larghezze, tastiera, axe, contrasto | 33 | 3 |
| [6. Lingue, SEO, prestazioni e sicurezza visibile](completion-audit/front-office-6-lingue-seo-e-prestazioni.md) | 168 pagine scaricate, 4 lingue, peso, intestazioni | 52 | 5 |

Totale: 215 riscontri (31 bloccanti, 127 di qualita', 57 di rifinitura), contati dal registro. Il proprietario di ogni riga e' stimato dal parser: puo' cambiare quando la storia lo conferma.

## I difetti che piu' pesano

1. Dati finti che sembrano veri: ente "Demo", regione "Name of the Region", numero verde 05 0505, contatti e social vuoti, link `#`. In lavorazione: DCP-289.
2. Testi legali incompleti in quattro lingue e nessuna pagina con i recapiti dell'ente o del DPO (404 su `/it/contatti` e `/it/dpo`). Decisione del titolare.
3. Il cittadino non puo' esercitare i propri diritti: nessun canale reale, nessuna esportazione, nessuna cancellazione. Dipende da DEC-10 e da modifiche di schema.
4. Il proprietario riceve 404 sulla propria segnalazione non pubblica, anche dal link nell'area personale; il follower vede il codice di tracciamento di segnalazioni altrui. In lavorazione: DCP-287.
5. Un doppio clic su "Invia" crea due segnalazioni (dimostrato); con la sessione scaduta l'invio non fa nulla; la conferma promette una mail. In lavorazione: DCP-286, DCP-284 (fatta).
6. La ricerca nell'intestazione non cerca: il campo si chiama `autocomplete-two`, l'elenco legge `q`. In lavorazione: DCP-288.
7. Password dimenticata: pagina stock di Laravel in inglese, senza header, footer, titolo, con chiavi grezze. In lavorazione: DCP-290.
8. Accessibilita': il campo del codice di tracciamento e' largo 16 px su telefono; il focus dell'accordion FAQ e' invisibile; la barra cookie copre il campo con focus; skip link verso un id che non esiste; errori del wizard non annunciati. In lavorazione: DCP-291 e onda 4.
9. Peso: 3,4 MB di CSS e 1,4 MB di JS su ogni pagina anche se non servono; primo contenuto mobile a 5-6 s. Solo misura e proposta; la build non si rifa a mano.
10. Sicurezza visibile: `/pulse` aperto all'ospite quando l'ambiente e' locale (il cancello di Pulse sovrascrive quello del progetto; in produzione la prova da' falso, quindi e' un indurimento), `APP_DEBUG` e Boost attivi in sviluppo, host riflesso nei link canonici (`TRUSTED_HOSTS`).

## Cosa funziona bene

Le traduzioni delle pagine pubbliche sono coerenti in quattro lingue (nessuna chiave grezza in 168 pagine). Nessuno scroll orizzontale a 320, 375, 768, 1024 e 1440 px su 39 pagine. Un solo `h1` ovunque. Il login mostra gli errori con aria corretti e frena dopo cinque tentativi. FAQ con ricerca, pagina 404 a tema, cookie policy, link del footer tutti raggiungibili, nessun dato personale nelle API pubbliche. Il dettaglio pubblico non espone mai codice, `owner_id` o email.

## Domande per il titolare emerse dall'audit

Sono nel [brainstorming](completion-brainstorming.md), con la proposta per ciascuna.
