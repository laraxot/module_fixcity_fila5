---
id: story-517-design-comuni-coverage
slug: 517-design-comuni-coverage
title: "STORY-517 — Copertura del catalogo Design Comuni: 79 template e flussi FixCity"
description: "Mappa i 79 template funzionali verificati nel catalogo Design Comuni v2.4.0 (35 «Sito» + 44 «Flussi di servizio»), misura il flusso di segnalazione e registra l'hardening delle pagine pubbliche."
document_type: story
category: frontend
status: in_progress
version: 1.0.0
language: it-IT
project: FixCity Fila5
created_at: '2026-09-27'
updated_at: '2026-09-27'
author: opencode-space-bunny
epic: EPIC-FRONTOFFICE
points: 21
priority: Must

issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/383"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/392"

reference:
  catalogo: "https://italia.github.io/design-comuni-pagine-statiche/index.html"
  flussi_di_servizio: "https://italia.github.io/design-comuni-pagine-statiche/servizi/index.html"
  versione: "v2.4.0"

related:
  - ./STORY-514-public-navigation-contract.md
  - ./STORY-515-public-navigation-measured-reality.md
  - ./STORY-516-design-comuni-parity-home.md
  - ../../../../bashscripts/ai/wiki/concepts/user-journeys.md
---

# STORY-517 — Copertura del catalogo Design Comuni

> **Verifica corrente:** il catalogo v2.4.0 è stato ricontrollato integralmente.
> La prima misurazione 4/7 è superata; il conteggio corrente è 35 template
> Sito + 44 template Flussi = 79. Gli otto template appuntamento distinguono
> il richiedente autenticato e non autenticato.
> Il Flusso Disservizio ora ha i 7 template del percorso: la scheda servizio
> è `/services/report-issue`, mentre privacy, dati, riepilogo, conferma, area
> personale ed elenco/mappa sono nel wizard e nella directory pubblica. L'indice /servizi/index.html risponde
> HTTP 200; le 44 pagine funzionali collegate sono suddivise in due template comuni e sei
> tipi di servizio (graduatoria, permessi/autorizzazioni, vantaggi economici,
> multa pagoPA, IMU/F24 e servizi a pagamento). La privacy policy pubblicata è
> requisito per abilitare l'invio. Vedi
> l'audit corrente in services-catalog-gap-analysis-2026-09-27.md. Tutti i 44
> collegamenti del flusso e i 35 template funzionali collegati dall'indice Sito
> hanno risposto HTTP 200 nel controllo del 2026-09-27.

## Cosa è stato studiato

L'intero catalogo, non un campione:

| Sezione del catalogo | Gruppi | Template |
|---|---:|---:|
| **Sito** — `index.html` | 8 | **35** |
| **Flussi di servizio** — `servizi/index.html` | 7 | **44** |
| **Totale template funzionali** | 15 | **79** |

Versione dichiarata dal catalogo: **v2.4.0**.

## La scoperta che cambia il modo di misurare

I 44 template dei «Flussi di servizio» **non sono 44 progetti diversi**. Sono
**lo stesso scheletro ripetuto** per sei tipologie di servizio, più due template
comuni:

```
Universali
  accesso-servizio      accesso tramite identità digitale (SPID, CIE)
  consenso-privacy      informativa privacy + acconsento          <-- STEP 1 ovunque

Scheletro dei flussi (sei tipologie concrete lo ripetono)
  1. *-scheda-servizio   la scheda del servizio: informazioni + accesso
  2. dati-personali      chi chiede
  3. dati-specifici      i dati del servizio (da 1 a 3 fasi, secondo la
                         tipologia: la multa ne ha tre)
  4. riepilogo           riepilogo con possibilità di tornare indietro
  5. conferma            conferma con CODICE DI RIFERIMENTO e download
  6. area-personale      il messaggio di esito nell'area personale
  7. pagamento           solo dove il servizio è a pagamento
```

Le sei tipologie: iscrizione a graduatoria, permessi e autorizzazioni, vantaggi
economici, pagamento multa via pagoPA, pagamento IMU via F24, servizi a pagamento.

> **Conseguenza per la misurazione:** non ha senso chiedere «abbiamo i 44 template dei
> flussi di servizio?». La domanda che conta è un'altra: **il nostro flusso ha lo
> scheletro?** Uno scheletro sbagliato in un template vale come sette template
> sbagliati, perché è la conformità che si rompe, non l'estetica.

## Il nostro flusso di segnalazione — prima misurazione

`Segnalazione Disservizio` è il flusso del prodotto: il suo atto principale è che un
cittadino segnali un disservizio. Catalogo: 7 template. La misurazione iniziale
ne trovava 4; la verifica corrente ne rileva 6.

| # | Template Design Comuni | Che cos'è | Noi | Stato |
|---|---|---|---|---|
| 1 | `segnalazione-dettaglio` | la **scheda del servizio**: di cosa serve, a chi, come, e l'accesso | `/services/report-issue` | ✅ **IMPLEMENTATO** |
| 2 | `segnalazione-01-privacy` | informativa privacy e **acconsento** | step privacy di /tickets/create | ✅ policy tenant richiesta |
| 3 | `segnalazione-02-dati` | luogo, disservizio, allegati, autore | `/it/tickets/create` | ✅ |
| 4 | `segnalazione-03-riepilogo` | riepilogo, si può tornare indietro | step summary del wizard | ✅ |
| 5 | `segnalazione-04-conferma` | conferma con codice di riferimento | `/it/tickets/confirmation` | ✅ data runtime dalla sessione |
| 6 | `segnalazione-area-personale` | la segnalazione appena fatta, tra le ultime attività | `/it/area-personale/pratiche` | ✅ |
| 7 | `segnalazioni-elenco` | elenco e mappa, filtrabili per tipologia | `/it/tickets` | ✅ |

La copertura funzionale attuale è **7 su 7**: la scheda informativa è presente
in `/services/report-issue`; gli altri sei passaggi sono nel wizard, nell'area
personale e nell'elenco pubblico. Questo conteggio descrive la presenza delle
schermate, non l'approvazione legale della policy tenant o il collaudo su staging.

### Il difetto di conformità, non di estetica

La prima analisi mancava i passaggi privacy e riepilogo, implementati
successivamente nel wizard corrente. La privacy policy pubblicata dal tenant
rimane un prerequisito operativo; schermate presenti non equivalgono a policy
legalmente approvata o a un test su staging.

## Il secondo difetto: 9 pagine di prova indicizzabili

`public_html/robots.txt` era:

```
User-agent: *
Disallow:
```

`Disallow:` **vuoto** significa «nessun vincolo»: ogni pagina era dichiarata
indicizzabile. Misurate con HTTP reale:

| Pagina | Stato | Contenuto |
|---|---:|---|
| `/it/tests` | 200 | indice delle vetrine |
| `/it/tests/news-showcase` | 200 | vetrina |
| `/it/tests/services-showcase` | 200 | vetrina |
| `/it/tests/tickets-showcase` | 200 | vetrina |
| `/it/tests/segnalazione-create` | 200 | vetrina |
| `/it/prova` | 200 | pagina di prova |
| `/it/prova01` | 200 | **172 occorrenze di italiano non tradotto** |
| `/it/counter` | 200 | contatore di test |
| `/it/show` | 200 | pagina di test |
| `/it/bootstrap-italia-showcase` | 200 | vetrina |
| `/it/learn` | 200 | — |
| `/it/genesis/power-ups` | 500 | già rotta, ma la rotta esiste |

Su un sito che riceve segnalazioni per la pubblica amministrazione, indicizzare le
pagine di prova significa esporre contenuti non revisionati. `/it/prova01` contiene più
testo italiano non tradotto di qualunque pagina pubblicata.

**Corretto**: `robots.txt` ora esclude sviluppo e vetrine, backoffice, API e area
personale, per tutte le locali pubblicate. Verificato: **35 pattern su 36 coprono rotte
realmente registrate**; l'unico che non copriva niente (`/admin`) è stato rimosso,
perché il backoffice è `/{modulo}/admin` e quel pattern non esiste.

`Disallow` **non è una protezione**: un crawler rispettoso lo segue e il contenuto resta
pubblico. Serve a non farlo indicizzare, non a nasconderlo. Per chiudere davvero serve
togliere le pagine di prova dal percorso pubblico.

## Cosa c'è già e non va rifatto

| Template DC | Noi | Nota |
|---|---|---|
| `homepage` | `/it` | con mappa e 3 step |
| `argomenti` | `/it/.../argomenti` | navigazione |
| `lista-categorie` | `/it/lista-categorie` | ✅ esiste già |
| `servizi` | `/it/services` | ✅ |
| `eventi` | `/it/events` | ✅ |
| `notizie` | `/it/news` | ✅ |
| `novita-dettaglio` | `/it/news/{slug}` | ✅ |
| `mappa-del-sito` | — | manca, ma bassa priorità |
| FAQ | — | manca |
| `risultati-ricerca` | ricerca nell'header | ✅ funzionante |

**66 pagine Folio** sotto `/it` su **35 template Sito del catalogo**: la copertura
quantitativa è buona. Nella sezione Sito rimangono pagine generali da completare;
il Flusso Disservizio ha lo scheletro completo, con la scheda informativa pubblica
implementata in `services/report-issue`.

## AC

- [x] Studio integrale del catalogo: 79 template funzionali in 15 gruppi, versione 2.4.0
- [x] Identificato che i 44 template coprono due passaggi comuni più sei tipologie
- [x] Mappato il flusso di segnalazione: **7 template su 7** (verifica aggiornata)
- [x] Verificato che privacy e riepilogo esistono nel wizard runtime; policy tenant necessaria
- [x] Trovate 11 pagine di prova pubbliche con `robots.txt` che le dichiarava
      indicizzabili
- [x] `robots.txt` riscritto: 35 pattern verificati contro le rotte reali, il
      pattern inerte rimosso
- [x] **Consenso**: informativa privacy + acconsento; vedi «Implementazione:
      il blocco consenso» sotto
- [x] **Riepilogo**: il terzo step del `CreateTicketWizardWidget` usa
      `TicketForm::getSummarySchema()`, consente di tornare ai dati e richiede la
      conferma prima del submit; verificato nel contratto del wizard
- [x] `segnalazione-04-conferma`: `/it/tickets/confirmation` riceve il data bag
      runtime dalla sessione, mostra il codice di riferimento e il link di tracking;
      verificato con Chromium a 390px in IT/EN/DE/ES
- [x] `segnalazione-dettaglio`: scheda pubblica `/services/report-issue` con
      destinatari, procedura, requisiti, risultato, tempi, costi e CTA verso il wizard;
      vedi STORY-520
- [x] Escludere `/it/tests`, `/it/prova`, `/it/prova01`, `/it/counter`, `/it/show`,
      `/it/bootstrap-italia-showcase`, `/it/homepage` e login1–5 dal percorso
      pubblico tramite `BlockLegacyPublicPages` (le viste restano disponibili ai
      test interni)
- [x] `/it/segnalazioni` → `/it/tickets`: il path canonico è `tickets`; la 301 resta
      come compatibilità e `segnalazioni/create` preserva la locale verso il wizard

## Note per chi riprende

1. **Consenso e riepilogo sono requisiti.** Entrambi esistono nel wizard corrente;
   l'invio è disabilitato senza policy privacy pubblicata.
2. **Misurare la copertura con lo scheletro, non con il numero di pagine.** Il flusso
   di segnalazione copre 7 template su 7; la scheda informativa è la pagina pubblica
   `services/report-issue`.
3. **`robots.txt` con `Disallow:` vuoto non protegge niente** e non è un dettaglio
   cosmetico: dichiara indicizzabili le pagine di prova.
4. **Verificare i pattern contro le rotte reali** prima di dichiararli: 1 su 36
   (`/admin`) non copriva niente perché il backoffice è `/{modulo}/admin`.
5. **`Disallow` non è una protezione**, è un'indicizzazione in meno.
6. **Un blocco senza `components/<nome>.blade.php` non si monta.** Le cartelle
   `components/blocks/<nome>/<variante>.blade.php` sono varianti CMS: Blade, risolvendo
   `<x-pub_theme::consenso>`, cerca `components/consenso.blade.php` o
   `components/consenso/index.blade.php`, e con la sola cartella dà
   `Unable to locate a class or view for component`. Servono **entrambe** le forme.

## Prima implementazione del blocco `consenso` (contesto storico)

Questa sezione documenta la prima esplorazione del template. Il flusso operativo
corrente usa gli step privacy, data e summary della schema Filament Fixcity;
considerare la tabella iniziale una traccia storica, non l'elenco delle schermate
attualmente mancanti.

Creato perché **manchi del tutto** (0 blocchi con nome `consenso`/`privacy`/`riepilogo`),
mentre il modello `Ticket` ha `email` fra i campi `fillable`: si raccoglievano dati
personali senza schermata di consenso.

| File | Ruolo |
|---|---|
| `Themes/Sixteen/resources/views/components/blocks/consenso/default.blade.php` | la variante CMS, con il contratto `@props(['data' => []])` |
| `Themes/Sixteen/resources/views/components/consenso.blade.php` | l'ingresso piatto che risolve il tag componente |
| `Themes/Sixteen/lang/{it,en,de,es}/consenso.php` | le traduzioni, 4 locali |
| `Themes/Sixteen/resources/views/pages/tests/consenso.blade.php` | vetrina di verifica, sotto `/it/tests` già escluso dai crawler |
| `laravel/build/quality-gates/verify-consenso-block.mjs` | il test di interattività, 15 check |
| `Modules/Fixcity/tests/Feature/Blocks/ConsensoBlockTest.php` | la **difesa permanente**, che non ha bisogno di browser |

### Verifica: 15/15 in browser, 12/12 sul contratto, e un controllo di sensibilità

Non ho guardato uno screenshot. Il test di interattività misura, tra l'altro:

- il bottone è **disabilitato** finché il consenso non è dato, e **si sblocca** dopo
- il `<label>` punta al checkbox con lo **stesso** `id`, e gli `id` sono univoci
- `aria-describedby` punta a elementi che **esistono** (non a un riferimento vuoto)
- lo stato non interattivo (riepilogo) è davvero disabilitato
- **zero chiavi di traduzione greffe** nel testo visibile
- zero errori in console, zero richieste fallite

E in più: **4 locali × 4 istanze**, tutte HTTP 200, titolo tradotto in ciascuna lingua.

### Il conflitto con `BlockLegacyPublicPages`, e come l'ho risolto

Un peer ha implementato l'AC «escludere `/it/tests` dal percorso pubblico» con
`BlockLegacyPublicPages`, e **il middleware ha bloccato anche la mia vetrina** (404).

Non l'ho aggirato: il blocco è più forte del mio AC e risolve esattamente il difetto che
avevo io denunciato. Ma il docblock del middleware dice «le viste restano disponibili ai
test», e **non è vero** per un test HTTP.

La risoluzione è stata spostare la difesa dove non ha bisogno della pagina:

- `ConsensoBlockTest.php` asserisce la **stessa** cosa in modo strutturale e permanente:
  **un solo `x-data`**, sul contenitore che racchiude checkbox e bottone. È il difetto
  esatto che il browser aveva trovato, e questo check lo cattura senza browser, senza
  sessione e senza pagina pubblica.
- lo script browser ora **esce con codice 2** e un motivo, invece di andare in crash: un
  crash dice «qualcosa è rotto» e costringerebbe a riaprire il prefisso pubblico per farlo
  passare. Un codice di usito dedicato dice «non eseguito» — che è la verità.

**Stato onesto dei controlli in questo ambiente:**

| Controllo | Stato | Perché |
|---|---|---|
| 12 condizioni del contratto | ✅ **eseguite, 12/12** | eseguite via `artisan tinker` contro il componente reale |
| sensibilità del check | ✅ **provata** | sulla versione ricostruita del difetto il check dà FAIL, sul blocco attuale PASS |
| 15 check di interattività | ✅ **15/15** quando eseguiti | sulla vetrina, prima che il peer chiudesse il prefisso |
| `ConsensoBlockTest.php` (Pest) | ⚠️ **scritto, non eseguito qui** | `SQLSTATE[HY000] … '${FIXCITY_TEST_DB_USERNAME}'`: le credenziali del DB di test non sono configurate in questo checkout, e ogni `TestCase` del progetto le richiede |
| PHPStan level 10 | ✅ **No errors** | su `ConsensoBlockTest.php` e sull'intero modulo Fixcity |
| Pint | ✅ **PASS** | |

Il test ha un **controllo di sensibilità**: ricostruisce di proposito il difetto che il
blocco aveva (due `x-data` fratelli) e pretende che il check se ne accorga. Serve perché
*un check che non sai far fallire non è verificato*.

### I due difetti che la verifica ha trovato, e una diagnosi sbagliata

1. **Due `x-data` fratelli non condividono lo stato.** Il checkbox scriveva nel proprio
   ambito, il bottone leggeva il suo: identico nome, zero condivisione, bottone fermo per
   sempre. Risolto con **un solo `x-data` sul contenitore**.

2. **L'errore di validazione aveva logica contraddittoria** — la condizione esterna e
   quella interna erano l'una la negazione dell'altra. Sostituito con l'errore **reale**,
   quello che il server rimanda dopo un invio respinto (`$data['serverError']`), che
   prima stampava un `<p role="alert">` vuoto e un `aria-describedby` puntato a niente.

**Diagnosi sbagliata da registrare:** ho attribuito il 500 a `:data="[...]"` su più
righe. Era vero che quell'array inline è un difetto reale, ma **non era la causa**. La
causa era `:title="Verifica blocco consenso"`: `:` rende l'attributo un'espressione PHP, e
il testo senza apici viene letto come costante + identificatore. La lezione è nel metodo,
non nell'errore: **un errore di sintassi Blade che non è dove lo indica si legge sulla
vista compilata**, non a occhio sul sorgente.

Vedi `docs/wiki/memories/blade-compiles-raw-source-prose-markers-break-files.md` e
`docs/wiki/memories/alpine-sibling-scopes-and-scheduler-timing.md`.

## Change log

- **2026-09-27 — ricontrollo del conteggio:** confermati 35 template Sito
  (inclusi gli otto template di prenotazione che distinguono il richiedente
  autenticato da quello guest) e 44 template Flussi. Tutte le 79 pagine
  funzionali rispondono HTTP 200.

- **2026-09-27 — prima misurazione, poi corretta:** il conteggio iniziale di 34
  template Sito escludeva per errore la variante appuntamento per il richiedente
  autenticato; il ricontrollo qui sopra conferma 35. Tutti i 44 template Flussi
  sono suddivisi in due passaggi comuni e sei tipologie concrete. Prima
  misurazione del flusso di segnalazione: 4 su 7. Corretti
  `robots.txt` e 11 pagine di prova dichiarate indicizzabili.

- **2026-09-27** — aggiunto `BlockLegacyPublicPages` al mount Folio theme/module.
  Le pagine di showcase e prova rispondono 404, mentre `/it/services` e
  `/it/tickets` restano 200.

- **2026-09-27** — **blocco `consenso` creato e verificato**: il template CMS
  `segnalazione-01-privacy`, passaggio comune ai sei flussi di servizio concreti, e non
  esisteva da nessuna parte, mentre il modello raccoglie già `email`. Blocchi, traduzioni
  in 4 locali, vetrina e test di interattività con **15/15** e controllo di sensibilità.
  Corretti en route due difetti reali (stato Alpine non condiviso, logica di errore
  contraddittoria) e registrata una diagnosi sbagliata mia, con il metodo per non
  ripeterla. Second brain: 2 memory nuove + 2 righe nell'indice.

- **2026-09-27** — corretto il contratto del data bag CMS: il componente theme
  `x-page` ora fonde il contesto runtime con i dati del blocco anche nella variabile
  `data`. Aggiunti testi dinamici per visibilità, tracking e area personale,
  traduzioni DE/ES, stepper, breadcrumb, contatti e rating senza ricaduta nell’italiano.

- **2026-09-27** — verifica successiva del codice e dell'indice diretto:
  l'indice /servizi/index.html restituisce 200 e contiene 44 template; sono due
  comuni più sei tipologie, mentre le pagine del flusso disservizio sono elencate
  nel catalogo principale. Privacy, dati, riepilogo, conferma, pratiche personali,
  scheda servizio ed elenco/mappa sono implementati. Corretti link Issue
  e Discussion ai riferimenti reali 383 e 392.

- **2026-09-27** — aggiunta la scheda pubblica Design Comuni `services/report-issue`,
  collegata dal catalogo servizi e verificata in quattro locali su viewport mobile.
  Ripristinata anche la directory categorie con link funzionanti e corretto il redirect
  legacy `segnalazioni/create` per preservare la locale.
