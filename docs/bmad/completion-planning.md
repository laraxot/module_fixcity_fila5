---
title: "Piano per il completamento del progetto"
type: planning
status: draft
updated: 2026-10-26
tags: [bmad, piano, completamento, fixcity, onde, percorso-critico, decisioni]
qmd: "piano completamento fixcity onde storie dcp-280 dcp-281 dcp-282 dcp-283 dcp-284 dcp-285 dcp-286 dcp-287 dcp-288 dcp-289 dcp-290 dcp-291 percorso critico calendario decisioni build"
related: ["completion-gap-register.md", "completion-architecture.md", "completion-brainstorming.md", "../stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md"]
---

# Piano per il completamento del progetto

## Fase BMAD

Planning e sprint planning. Ordina le storie in onde, dice quali file sono di chi, qual e' il percorso critico verso la messa online e quali decisioni servono e quando. Le lacune sono nel [registro](completion-gap-register.md), i contratti nell'[architettura](completion-architecture.md).

## Stato al 2026-10-26

Tutto il lavoro e' nel working tree, senza commit. Il numero di storie riservato a questo lavoro e' 280..359; la sessione f5 usa 275..279 e dal 360 in su.

| Storia | Oggetto | Proprietario | Stato |
|---|---|---|---|
| DCP-280 | Storia ombrello del completamento | Fixcity | Da scrivere |
| DCP-281 | Rinforzo del pannello operatore e pagine legacy | Fixcity | Chiusa, verificata. Aperti: A-23, schermata di creazione Mobile |
| DCP-282 | Igiene di sicurezza della radice web, bootstrap e controllo di go-live | Fixcity | Chiusa, verificata |
| DCP-283 | Esecuzione dinamica e fail-open in Cms e Notify | Cms | Chiusa, verificata (78 test, 432 asserzioni) |
| DCP-284 | Notifiche del ciclo di vita | Fixcity | Chiusa, verificata. Aperti DD-1..DD-6 |
| DCP-285 | Policy utenti, reset password, revoca consensi | User | Chiusa, verificata (34 test). Aperto: schema |
| DCP-286 | Wizard: invio idempotente e conferma onesta | Fixcity | In corso |
| DCP-287 | Dettaglio per il proprietario, follower, ritorno dopo il login | Fixcity | In corso |
| DCP-288 | Ricerca del sito, intestazione accessibile, metadati | Sixteen | In corso |
| DCP-289 | Dati dell'ente: fonte unica e blocchi che si nascondono | Cms o Tenant | In corso |
| DCP-290 | Pagine di accesso e reset password a tema | User | In corso |
| DCP-291 | Accessibilita' di traccia, FAQ, banner, valutazione, `main` | Sixteen | In corso |
| DCP-299 | Riordino dei documenti di moduli e temi | Tutti | Da scrivere |

## Onde

1. Onda 0, igiene. DCP-282. Chiusa.
2. Onda 1, rinforzo del back office. DCP-281. Chiusa.
3. Onda 2, correttezza e sicurezza. DCP-283, DCP-284, DCP-285. Chiuse.
4. Onda 3, front office. DCP-286..DCP-291, in parallelo con file disgiunti (tabella sotto).
5. Onda 4, da numerare quando l'onda 3 e' chiusa e verificata (il numero si prende con il controllo che blocca):
   - Wizard accessibile: annuncio degli errori, focus al cambio passo, stepper `ol` con `aria-current`, link finti nella card "Contatta il comune" (FO-5-12, FO-5-13, FO-5-14, FO-3-17, FO-3-23).
   - Intestazione accessibile: trappola del focus nel menu, selettore lingua senza ruoli `menu`, contrasti in hover, punti di riferimento senza nome (FO-5-08, FO-5-09, FO-5-22, FO-5-28, FO-5-29).
   - Mappa da tastiera: popup raggiungibile, Esc, etichette tradotte, "vai all'elenco" (FO-5-15).
   - Azioni del proprietario, in tre storie piccole: modificare e integrare, ritirare, riaprire e valutare (FO-2-17).
   - Contenuti e servizi: testi mancanti nelle quattro lingue, segnaposto nei servizi (FO-4), dopo che il titolare fornisce i dati.
   - Lingua alla prima visita e canonical (FO-1-20), dopo la decisione 11.
   - Privacy: prova del consenso, registro delle richieste, cancellazione (A-04, A-05, D-01), dopo l'approvazione dello schema.
6. Onda 5, operativita': posta reale e coda, scheduler, backup, rotazione dei segreti, `TRUSTED_HOSTS`, pulizia dei lock orfani. Non e' codice: dipende dalle decisioni.

## File di chi, nell'onda 3

Un solo autore per file. I lock sono in `bashscripts/lock/`.

| Storia | File e zone di sua proprieta' | Non toccare |
|---|---|---|
| DCP-286 | Widget e Action del wizard in Fixcity, viste del wizard in Sixteen, JSON `tickets.create` e `confirmation` (anche tel e aiuto) | Header, footer |
| DCP-287 | Action e blocco `ticket-detail` (senza le righe dei commenti), area personale | Commenti, wizard |
| DCP-288 | Modale di ricerca, metadati, CSS sorgente dell'intestazione | `header.json`, `footer.json` |
| DCP-289 | `header.json`, `footer.json`, sorgente dati dell'ente, blocchi aiuto e contatti | Modale di ricerca |
| DCP-290 | Pagine di accesso, registrazione, reset in User e nelle viste dei widget di Sixteen | `UserServiceProvider`, `UserPolicy` |
| DCP-291 | Pagine di traccia, FAQ, valutazione, layout `main`, banner cookie in Gdpr | Intestazione, wizard, mappa |

Altre sessioni: f5 ha concluso su `Modules/Comment` e sul login con ritorno (`StoreIntendedUrlAction`). Il thread dei commenti e le risposte sono suoi. Nessuno tocca `FixcityServiceProvider`, `bootstrap/*` e `routes/web.php` senza rileggere il file un attimo prima.

## Percorso critico verso la messa online

Il codice non e' il collo di bottiglia. Il percorso critico e':

1. Dati dell'ente forniti (decisione 1). Senza, restano blocchi nascosti o avvisi onesti.
2. Nome e contatto del DPO, testi legali approvati (decisioni 5 e 6). Senza, il controllo di go-live resta rosso per costruzione.
3. Posta in produzione: relay, coda, prova reale (decisione 4). Senza, la cittadina non riceve nulla.
4. Segreti: rotazione e rimozione dall'indice di git (decisione 8).
5. Schema approvato (decisione 12), poi le storie di privacy.
6. Una build del tema, una verifica visiva a tre larghezze, il controllo di go-live, la suite dei test dei moduli toccati, PHPStan sui moduli.

## Calendario delle decisioni

| Quando | Decisione | Perche' prima delle altre |
|---|---|---|
| Subito | 2 thread dei commenti agli ospiti | E' gia' visibile agli ospiti: un dato dei cittadini e' esposto ora |
| Subito | 8 segreti tracciati | Rischio attivo |
| Prima della build finale | 3 ricerca, 15 valutazione | Cambiano cosa si mostra |
| Prima dell'onda 4 | 1, 5, 6, 9, 10, 11, 12, 13, 14 | Sbloccano le storie successive |
| Prima della messa online | 4, 7 | Operativita' |

Il testo completo delle domande e la mia proposta sono nel [brainstorming](completion-brainstorming.md); le decisioni definitive vanno nel registro DCP-200.

## Come si chiude una storia

1. Il test e' stato scritto prima ed e' stato rosso per la ragione giusta.
2. Verde con il comando di `pest-scratch-db.sh`, e output salvato.
3. PHPStan a zero sui file toccati, dichiarando il perimetro.
4. Per le modifiche visibili: misura nel browser a 375, 768 e 1280 px, prima e dopo, con axe sulle pagine toccate.
5. Esito scritto nella storia con comandi e output. Se qualcosa non si e' potuto provare, lo dice.
6. Lock rilasciati; nessun file rimosso con `rm`; i ritirati sono `.old`.
7. Una riga nel registro delle decisioni del modulo o tema.
8. Il coordinatore ripete un campione delle prove prima di segnare la lacuna come chiusa nel [registro](completion-gap-register.md).

## Build e verifica visiva

Una sola build di Sixteen, a fine onda 3, lanciata dal coordinatore con `flock`. Poi: il campo di tracciamento a 320 e 375 px, il focus dell'accordion FAQ, il contrasto del badge di stato, il menu mobile, la modale di ricerca, il banner cookie, i colori forzati. Poi di nuovo axe sulle trentanove pagine pubbliche usate dall'audit, per confrontare i numeri.

## Riordino dei documenti (DCP-299)

Vincolo: niente spostamenti in blocco. Dopo l'errore fatale del 2026-08-05 si sposta un file alla volta con `git mv` e riscrittura dei collegamenti in ingresso.

1. Ogni modulo e tema ha un solo `docs/README.md` come porta d'ingresso, con elenco di `bmad/`, `stories/`, `wiki/` e dei documenti storici.
2. I documenti duplicati o con suffisso numerico si marcano superseded con un collegamento al documento che li sostituisce; non si cancellano.
3. Lo spostamento di un file richiede una voce in una mappa di migrazione e i collegamenti in ingresso riscritti.
4. Prima si riparano i collegamenti rotti (gia' fatto con lo strumento sicuro: 2565 riparati), poi si decide cosa spostare.
5. I documenti generici del 2026-10-26 sono sostituiti dal registro e dal piano.
