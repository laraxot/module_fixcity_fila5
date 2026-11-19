---
id: dcp-47
title: "Fixture autenticata per misurare il wizard senza toccare il DB di sviluppo"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [tooling, test, parity, auth]
related: [Modules/Fixcity/docs/stories/dcp-35-tickets-create-privacy-dati-coordinamento.story.md, Modules/Fixcity/docs/stories/dcp-33-tickets-create-riepilogo-design-comuni-mappa.story.md, Modules/Fixcity/docs/stories/dcp-42-tickets-create-auth-regressione.story.md]
qmd: "Fixture autenticata per misurare il wizard senza toccare il DB di sviluppo"
---

# DCP-47: fixture autenticata per le misure

## Richiesta
Dopo D17 (`tickets/create` richiede auth) il gate da ospite misura la pagina di login. DCP-33 (Aperto) chiede "fixture autenticata (utente di test + profilo + allegato) e gate a 5 larghezze". Nessuno la sta facendo; nessun utente di sviluppo ha una password nota, e non si creano utenti nel DB di sviluppo senza chiederlo.

## Approccio previsto
Un'istanza di sviluppo SEPARATA (altra porta) con database scratch, mai il DB di sviluppo; utente e profilo creati li'; login con password nota; lo stato "ticket di prova" per la conferma creato li'. Da riportare qui: meccanismo, comandi, pulizia, misure.

## Stato
**Fixture pronta, provata e rimossa (2026-10-13, 16:14-16:55 UTC, subagent FIXTURE).** Script `bashscripts/tools/visual/scratch-auth-fixture.sh` (sottocomandi `start`, `status`, `ticket`, `cookies`, `login-js`, `stop`). Guida: `bashscripts/docs/visual-comparison/parity-gate.md`, sezione «Fixture autenticata». Nessun commit, nessun JSON di pagina, nessun codice di produzione toccato.

**Comandi** (dalla root): `S=bashscripts/tools/visual/scratch-auth-fixture.sh; $S start; $S cookies; $S ticket; export NODE_OPTIONS="--import=/tmp/scratch-auth-fixture-$USER/auth-preload.mjs"; bashscripts/tools/visual/parity-gate.sh <riferimento> 'http://127.0.0.1:8091/it/tickets/create...' --widths '1440 768 375'; unset NODE_OPTIONS; $S stop`. Il preload fa aprire a ogni `node` con Playwright pagine autenticate (`storageState` su `browser.newPage`) e aggiunge il cookie a ogni `fetch` verso la 8091: `compare-screenshots.mjs` e `compare-body-html.mjs` non sono stati modificati. Provato: con il preload il `fetch` di `/it/tickets/create` risponde 200 con il wizard (135942 B), senza preload finisce su `/it/auth/login` (64918 B).

**Isolamento (provato).** Istanza `php -d variables_order=EGPCS -S 127.0.0.1:8091` con router `laravel/server.php` (porta 8091, 8000 intatta). DB `claude_scratch_auth_<ts>_data` (89 tabelle) e `_user` (34), struttura con `CREATE TABLE ... LIKE`, nessun dato; utente `giulia.bianchi@example.test` + profilo (telefono) via modelli; un solo ticket di prova (`TCK-...`, `owner_id` = utente; 1 utente e 1 ticket nello scratch contati con SQL). Ambiente: `APP_CONFIG_CACHE=/nonexistent/config.php`, `DB_DATABASE*` scratch, `LARAVEL_STORAGE_PATH` in `/tmp/scratch-auth-fixture-$USER/storage`, `MAIL_MAILER=array`. Prove: `/proc/<pid>/environ` del server ha le variabili; dopo il riscaldamento lo storage scratch ha 14 viste e a fine serie 59 viste e 14 sessioni (0 file di cache), il DB di sviluppo non e' mai stato scritto: utenti/ticket 9/4 prima, dopo `ticket`, dopo la serie e dopo `stop` (le mie istruzioni seed e ticket rifiutano ogni DB non `claude_scratch_auth_`).

**Difetti dello script trovati e corretti** (causa, non sintomo):
1. `stop` lasciava vivo l'albero php: uccideva due PID, ma `php -S` forka 4 worker che tengono la porta. Ora `stop` uccide TUTTI i processi con il `LARAVEL_STORAGE_PATH` dell'istanza (un `grep -azlxF` su `/proc/*/environ`, SIGTERM poi SIGKILL) e stampa processi, DB e porta residui.
2. `start` non finiva mai se l'output era catturato (`start | tail`: 10 minuti, bloccato): `(cd && setsid ... &)` lasciava una copia di bash con la pipe aperta e `ARTISAN_PID` era il PID di quella copia, non di php. Probabile causa del blocco del predecessore. Ora il redirect sta sul sotto-shell con `exec`: `start | tail` torna in 10-13 s.
3. **Isolamento dello storage assente**: il server integrato con `variables_order=GPCS` non mette l'ambiente in `$_SERVER`, quindi Laravel ignorava `LARAVEL_STORAGE_PATH` (provato: 0 viste nello storage scratch, sessioni e viste in `laravel/storage/framework`). Il primo giro (16:16-16:50, con `php artisan serve`) ha quindi scritto sessioni (cifrate: non attribuibili, non cancellate) e viste compilate (le stesse che compila lo sviluppo) nello storage di sviluppo; nessuna cache (0 file). Ora il server parte con `-d variables_order=EGPCS` e `start` si ferma se lo storage scratch non ha viste compilate. Ho rifatto TUTTE le misure dopo la correzione: gli stessi valori pixel del primo giro.
4. Il seed falliva in silenzio e `start` proseguiva: ora controlla l'uscita. `ticket` creava un ticket a ogni chiamata: ora uno solo per istanza.
5. `login-js`: la barriera `--click '.steppers'` consigliata cambiava passo del wizard (2 prove su 3 con 44,31% e altezza locale 2782 invece di 1938/2042); ora l'avviso indica `h1:has-text("Segnalazione disservizio")` (8 prove su 8 con le altezze giuste, pixel 84,13-84,51%).

**Misure sull'istanza scratch autenticata** (riferimento `italia.github.io/design-comuni-pagine-statiche/sito/segnalazione-0N-*.html`; ogni cella e' pixel % e altezze riferimento-locale; HTML = rigorosa / url-normalizzati; soglie del gate HTML 90, pixel 97). Script runner nello scratchpad della sessione.

| Misura | HTML | 1440 | 768 | 375 |
|---|---|---|---|---|
| Passo 1, **standard** (senza filtri) | 54,64 / 57,45 | 84,51 (1938-2042) | 80,65 (2051-2179) | 76,36 (2925-3149) |
| Passo 2, **standard** | 60,00 / 62,80 | 44,80 (3064-4521) | 55,65 (3246-4034) | 72,33 (4056-4486) |
| Passo 3, **standard** (sessione vuota: riepilogo senza dati) | 62,69 / 65,75 | 74,44 (3115-2737) | 72,53 (3206-2803) | 75,89 (3956-3571) |
| Conferma autenticata (proprietario), **standard** | 92,15 / 96,94 | 99,77 (2301-2301) | 99,58 (2264-2264) | 99,37 (3083-3083) |
| Conferma da **ospite**, **standard** | 91,75 / 96,54 | 81,92 (2301-2229) | 84,49 (2264-2200) | 85,38 (3083-3011) |
| Passo 1, FILTRI (A) | 77,66 / 82,29 | 84,51 (1938-2042) | 80,65 (2051-2179) | 76,36 (2925-3149) |
| Passo 2, FILTRI (B) | 63,30 / 66,46 | 78,03 (3064-2777) | 73,18 (3246-2890) | 74,79 (4056-3687) |
| Passo 3, FILTRI (C) | 79,91 / 84,32 | 97,23 (3001-3008) | 95,78 (3092-3099) | 93,98 (3855-3862) |

Passi 1-3 standard e conferma sono il gate senza filtri (con sessione autenticata, 3 larghezze invece di 5). I filtri (DCP-95: non sono il gate standard, vanno riportati):
- **A** `--stub-images 'picsum\.photos|via\.placeholder\.com'`, `--exclude-local '.fi-sc-wizard-step:not([id="form.autorizzazioni-e-condizioni::data::wizard-step"])'` (solo HTML: tolti 2 nodi, 501 token; i pixel non cambiano).
- **B** come A con il passo `form.dati-di-segnalazione`, piu' `--hide '.coordinate-picker-field-wrapper'` (altezza locale 4521 -> 2777, 4034 -> 2890, 4486 -> 3687) e `--exclude-local '.coordinate-picker-field-wrapper'`.
- **C** `--stub-images` come sopra; `--prepare` (JS sul componente `CreateTicketWizardWidget`: `$wire.set` di `data.name` "Panchina danneggiata", `data.type` `public_lighting`, `data.content` (testo del riferimento), `data.location.address` "Via Solferino - 50100 Firenze (FI)", latitudine 43.7791, longitudine 11.2464; il JS e' `const lw = window.Livewire.all().find((c) => c.name.endsWith('CreateTicketWizardWidget')); const set = (k, v) => lw.$wire.set(k, v, true); await set('data.name', ...);` per ciascun campo elencato); `--hide '.geo-point-map'` (mappa del riepilogo locale, 323 px a 1440 e 768, 362 a 375); `--hide-ref` e `--exclude` `.single-line-info:nth-child(5), .single-line-info:only-child` (righe Immagini e Codice Fiscale del riferimento senza sorgente locale, DCP-48: -114 px a 1440 e 768, -101 a 375); `--exclude-local` dei passi inattivi e di `.coordinate-picker-field-wrapper`. Senza il `--hide` della mappa del riepilogo e delle due righe del riferimento: 83,78 / 81,82 / 80,50 con altezze 3115-3331 (1440).

**Letture.** (1) Passo 3: il `[data-dc-map]` della story DCP-98 non esiste piu'; la mappa del riepilogo e' `.geo-point-map`. Col filtro C restano 7 px di altezza locale in piu' (3001-3008) e il testo "Tipo di disservizio" diverso (`Illuminazione Pubblica` contro `Danneggiamento proprieta pubblica`): i valori 99,5-99,85% di DCP-98 non sono confrontabili (altri filtri, altezze pareggiate), il 7 px non e' stato indagato. (2) Passo 2: 4521 px contro 3064 per la mappa e un pin SVG di circa 1700 px; l'altezza e' quasi uguale a quella vista dalla sessione tema sul DB di sviluppo (4407-4521), quindi non e' un artefatto della fixture. (3) Conferma: da proprietario passa il gate (HTML 92,15, pixel 99,37-99,77, altezze uguali); da ospite manca la riga «Abbiamo inviato il riepilogo all'email: <email>» (diff del testo: 2 righe), da qui 64-72 px in meno e 82-85%. E' coerente con la scelta di mostrare l'email solo al proprietario. (4) Le url-normalizzate variano di 0,1 punti tra due giri sulla stessa pagina.

**Limiti.** Il DB scratch ha struttura e nessun dato (nessun seed di categorie o altro). 3 larghezze (1440, 768, 375), non 5. Il ticket di prova ha codice casuale `TCK-...`, non `AN4059281`. `parity-wizard-steps.sh` non e' utilizzabile (il suo controllo da ospite rifiuta; usare `parity-gate.sh` per passo con `NODE_OPTIONS`). Un `Page crashed` transitorio di Chromium (conferma da ospite a 1440, riuscita al secondo tentativo, 81,92%). La conferma e' stata misurata con il ticket creato da `ticket`, non dopo un invio reale dal wizard (la prova end-to-end con Invia e' di DCP-98). Salvataggio con allegato non provato. L'account DCP-98 nel DB di sviluppo non e' stato toccato: con questa fixture puo' essere rimosso con il comando di DCP-98 (decisione della sessione tema). **Pulizia (dopo `stop`)**: 0 processi con porta 8091 o `scratch-auth`, `ss -ltnp | grep :8091` vuoto, 0 DB `claude_scratch_auth_%`, `/tmp/scratch-auth-fixture-*` assente, 0 processi con il storage scratch, 8000 ancora in ascolto, utenti/ticket di sviluppo 9/4.

## Coordinamento dalla sessione tema (14:25)
Esiste gia' un account di prova nel DB di sviluppo, creato dopo il «procedi» dell'utente (non e' il vostro approccio "senza toccare il DB": se preferite una fixture isolata continuate pure, i due non si escludono): vedi [DCP-98](dcp-98-account-di-prova-per-misure-autenticate.story.md) (utente `dcp-parity-test@example.test`, profilo con telefono, ticket `AN4059281`, rimozione in fondo alla story; la password e' solo in un file privato della sessione tema: chiedere, non e' nel repo). Con quell'account sono gia' state fatte le misure autenticate di riepilogo (99,48-99,85%) e conferma (99,41-99,84%), script `dcp98-auth-measure.mjs` e `dcp98-conf-measure.mjs` nello scratchpad della sessione tema. Se la vostra fixture sostituisce l'account, aggiornate DCP-98 con la rimozione dei dati. Il codice fiscale (DCP-48) resta senza sorgente: nelle mie misure la riga e' nascosta sul riferimento.

