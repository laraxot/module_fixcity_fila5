---
title: "Audit front office 2: segnalazioni e area personale"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "audit front office 2: segnalazioni e area personale audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Audit front office: gruppo fo-2 segnalazioni e area personale

Data: 2026-10-26. Autore: sub-agent auditor FO (persone OSPITE e CITTADINO). Istanza usata: `http://127.0.0.1:8091` (scratch, DB usa e getta). Nel repository non e' stato scritto nulla. Tutti i dati di prova sono stati creati solo dalla UI del sito (registrazione di un secondo utente, una segnalazione con foto, un "segui", una preferenza).

Convenzioni:

- M = MISURATO: ho eseguito uno script Playwright o letto un output/screenshot.
- D = DEDOTTO: letto nel codice, non provato nel browser.
- `S/` = `/tmp/claude-1000/-mnt-nas07-var-www--bases-base-fixcity-fila5/fa79ffe9-c6fb-4eb5-892f-259fcfdd19d2/scratchpad/fo-audit/fo-2-segnalazioni-e-area-personale/shots/`
- `O/` = stessa cartella ma `out/` (JSON degli script). Gli script `s1..s36*.mjs` e `lib.mjs` stanno nella cartella madre.
- Gravita': BLOCCA go-live, QUALITA', RIFINITURA. Taglie: S (meno di mezza giornata), M (1-2 giorni), L (oltre).
- Persone: ospite = nessun cookie. Cittadino = `giulia.bianchi@example.test` (proprietaria di tutte le 13 segnalazioni del fixture) e, per le prove "altro cittadino", `Paolo Verdi` (registrato via UI, proprietario della segnalazione 17 creata dal wizard).

Nota sugli agenti in parallelo: mentre lavoravo risultavano attivi gli agenti "Ondata 2B notifiche ciclo di vita (DCP-284)" e "Ondata 2C User e Gdpr (DCP-285)". I riscontri FO-2-25, 29, 30, 34, 35, 38 possono cambiare sotto i miei piedi: ricontrollare dopo il loro merge.

## 1. Inventario di pagine e flussi esaminati

Totale: 229 screenshot (alcuni sono dettagli di elementi) e 34 JSON di output. I caricamenti di pagina distinti sono oltre 80 (stima dai nomi degli screenshot, non da un contatore). Per ogni pagina ho controllato anche console, risposte HTTP 4xx/5xx, overflow orizzontale e chiavi di traduzione grezze.

### 1.1 Elenco pubblico `/it/tickets`

| URL (varianti) | Persona | Esito HTTP | Screenshot |
|---|---|---|---|
| `/it/tickets` (tab Mappa di default) 1280 e 375 | ospite, cittadino | 200 | `S/guest-1280-it_tickets.png`, `S/guest-375-it_tickets.png` |
| `?view=list` | ospite | 200 | `S/guest-1280-it_tickets_view_list-q.png`, `S/guest-1280-list-top.png`, `S/guest-1280-list-card-expanded.png` |
| `?q=buca`, `?q=lampione`, `?q=zzzzzz` (zero risultati), `?q=<script>` | ospite | 200 (xss: 0 risultati, nessun script eseguito) | `S/guest-1280-search-lampione.png`, `S/guest-1280-empty.png` |
| `?types[]=public_lighting`, `?statuses[]=resolved`, `?period=7d`, `?sort=oldest`, combinazioni | ospite | 200 | `S/guest-1280-filter-combo.png`, `S/guest-1280-filter-road.png` |
| `?page=2`, `?page=999` | ospite | 200 (999 viene riportato all'ultima pagina) | `S/guest-1280-it_tickets_page_999-q.png` |
| Modale filtri mobile (apri, seleziona, applica) 375 | ospite | 200 | `S/guest-375-filters-modal.png`, `S/guest-375-modal-selected.png`, `S/guest-375-after-modal-filter.png` |
| Mappa: popup marker, click cluster, zoom, tastiera | ospite | 200, `/api/tickets/geojson` 200 | `S/guest-1280-map-popup2.png`, `S/guest-375-map-popup2.png`, `S/guest-1280-map-cluster-click.png`, `S/guest-1280-kbd-checkbox.png` |
| `/en/tickets`, `/de/tickets`, `/es/tickets` (+ `?view=list`) | ospite | 200 | `S/guest-1280-en_tickets.png`, `S/c-de-list.png`, `S/c-es-list.png` |
| Riferimento ufficiale Design Comuni `segnalazioni-elenco` | n/a | 200 | `S/dc-ref-segnalazioni-elenco.png` |
| Produzione `https://fixcity.eu/it/tickets` (sola lettura) | ospite | 200 | `S/prod-_it_tickets.png` |

### 1.2 Dettaglio pubblico `/it/tickets/{id}`

| URL | Persona | Esito | Screenshot |
|---|---|---|---|
| `/it/tickets/1..12` | ospite | 200 (12 segnalazioni pubbliche) | `S/guest-1280-it_tickets_1.png` ... `_12.png` |
| `/it/tickets/1` 375 | ospite, cittadino, cittadino 2 | 200, nessun overflow | `S/citizen2-375-it_tickets_1.png` |
| `/it/tickets/0`, `abc`, `99999`, `13..18` | ospite | 404 a tema | `S/guest-1280-it_tickets_13.png`, `S/guest-1280-it_tickets_abc.png` |
| `/it/tickets/13` | cittadino (proprietaria) | 404 (artefatto fixture, vedi FO-2-36) | `S/citizen-1280-it_tickets_13.png` |
| `/it/tickets/17` (pendente, wizard) | proprietario Paolo | 200 con "verificata da un moderatore"; ospite e Giulia 404 | `S/citizen2-1280-it_tickets_17-own-pending-detail.png` |
| `/it/tickets/{1,3,7,9,12}` da proprietaria | cittadino | 200, vista uguale a quella dei non proprietari | `S/giulia-owner-1280-it_tickets_3-owner.png` |
| Segui / Smetti di seguire (ticket 1 e 5) | cittadino, cittadino 2 | 200, conferma `role=status` | `S/citizen-1280-after-follow.png`, `S/citizen2-1280-it_tickets_5-before-follow5.png` |
| Emulazione stampa | ospite | 200 | `S/print-detail17.png`, `S/print-detail17.pdf` |
| `/en`, `/de`, `/es` dettaglio | ospite, cittadino | 200 | `S/citizen-de-1280-de_tickets_1.png` |
| Riferimento DC `segnalazione-dettaglio` | n/a | 200 | `S/dc-ref-segnalazione-dettaglio.png` |

### 1.3 Tracciamento `/it/tickets/track`

| Caso | Persona | Esito | Screenshot |
|---|---|---|---|
| Form vuoto | ospite | 200 | `S/guest-1280-track-form.png` |
| Codice valido (maiuscolo e minuscolo, anche `DEMO-PUB-xx`) | ospite | 200 con risultato | `S/guest-1280-track-valid.png`, `S/guest-1280-track-lower.png`, `S/guest-1280-track-demo.png` |
| Codice sbagliato, malformato, XSS | ospite | 200 "Nessuna segnalazione trovata" | `S/guest-1280-track-wrong.png`, `S/guest-1280-track-malformed.png` |
| Oltre 10 richieste in un minuto | ospite | 429 a tema | `S/guest-1280-track-xss.png` (e' la pagina 429) |
| Codice del proprio ticket / codice di un ticket di altri (follower) | cittadino 2 | 200 | `S/citizen2-1280-it_tickets_track_TCK_QXVLBUGYGOSF0VCE-own-track-path.png`, `S/citizen2-1280-it_tickets_track_code_DEMO_PUB_05-follower-track.png` |
| `/en`, `/de`, `/es` | ospite, cittadino | 200 | `S/guest-1280-en_tickets_track.png` |
| Produzione | ospite | 200 | `S/prod-_it_tickets_track.png` |

### 1.4 Invio e conferma

| Caso | Persona | Esito | Screenshot |
|---|---|---|---|
| Wizard completo (3 passi, foto PNG, via Livewire) | cittadino 2 | creata la segnalazione 17, codice `TCK-...` | `S/citizen2-1280-wizard-step1.png`, `_step2.png`, `_step3.png` |
| `/it/tickets/confirmation?code=...` | cittadino 2 | 200 | `S/citizen2-1280-confirmation.png` |
| `/it/tickets/confirmation` senza dati | ospite | 200 con titolo di successo (vedi FO-2-24) | `S/guest-1280-it_tickets_confirmation.png` |
| `/it/tickets/create` da ospite | ospite | redirect al login | `S/guest-1280-it_tickets_create.png` |
| Riferimento DC `segnalazione-04-conferma` | n/a | 200 | `S/dc-ref-segnalazione-04-conferma.png` |

### 1.5 Area personale

| URL | Persona | Esito | Screenshot |
|---|---|---|---|
| `/it/area-personale` | cittadino | redirect a `/pratiche` | `S/citizen-1280-it_area_personale.png` |
| `/it/area-personale/pratiche` (Le mie segnalazioni), 1280 e 375, righe aperte | cittadino, cittadino 2 (vuoto e dopo invio) | 200 | `S/citizen-1280-it_area_personale_pratiche.png`, `S/citizen-375-it_area_personale_pratiche.png`, `S/citizen2-1280-it_area_personale_pratiche-after-submit.png` |
| `/it/area-personale/seguite` | cittadino, cittadino 2 | 200 | `S/citizen2-1280-it_area_personale_seguite-follows5.png` |
| `/it/area-personale/notifiche` | cittadino, cittadino 2 | 200 (vuota) | `S/citizen2-1280-it_area_personale_notifiche-after-submit.png` |
| `/it/area-personale/impostazioni` (salva e ripristina toggle) | cittadino | 200, salvataggio con feedback | `S/citizen-1280-settings-saved.png`, `S/citizen-1280-settings-unchecked.png` |
| Le quattro pagine da ospite | ospite | redirect a `/it/auth/login`, ritorno corretto dopo login | `S/citizen-1280-after-login-from-_it_area_personale_pratiche.png` |
| `/it/profile`, `/it/profilo` | ospite | 404 | `S/guest-1280-it_profile.png` |
| Le quattro pagine in `en`, `de`, `es` | cittadino | 200, nessuna chiave grezza | `S/citizen-de-1280-de_area_personale_impostazioni.png` |
| Menu utente, hamburger mobile, logout e tasto Indietro | cittadino | funzionano | `S/citizen-1280-usermenu.png`, `S/citizen-375-usermenu.png`, `S/paolo-1280-after-logout-back.png` |
| Riferimento DC `segnalazione-area-personale` | n/a | 200 | `S/dc-ref-segnalazione-area-personale.png` |

### 1.6 Controlli trasversali

axe-core (WCAG 2a, 2aa, 21, 22aa) su 9 pagine: `O/axe.json`. Console warning, risposte 4xx e overflow su tutte le pagine sopra (negli output JSON `O/*.json`).

## 2. Riscontri

### 2.1 Indice

| ID | Pagina | Persona | Sintesi | Gravita' | Taglia |
|---|---|---|---|---|---|
| FO-2-01 | `/it/tickets` | entrambi | Valore di ricerca sopra l'etichetta dopo l'invio | QUALITA' | S |
| FO-2-02 | `/it/tickets` | entrambi | URL con parametri vuoti | RIFINITURA | S |
| FO-2-03 | `/it/tickets` mobile | entrambi | Mappa primo tab, controlli tagliati e sopra la modale filtri | QUALITA' | S |
| FO-2-04 | `/it/tickets` mobile | entrambi | Nessun indicatore di filtri attivi, "Rimuovi tutti" nascosto | QUALITA' | S |
| FO-2-05 | `/it/tickets` | entrambi | Card povere: dati nascosti, stato senza colore, niente miniatura | QUALITA' | M |
| FO-2-06 | `/it/tickets` | entrambi | Zero risultati: faccette sparite, vuoto sotto il titolo | RIFINITURA | S |
| FO-2-07 | `/it/tickets` mappa | entrambi | Cluster senza nome accessibile, tab senza frecce | QUALITA' | M |
| FO-2-08 | `/it/tickets` mappa | entrambi | Popup troncato su mobile, skeleton lento, `city` vuota | RIFINITURA | S |
| FO-2-09 | `/it/tickets` mappa | entrambi | Con zero segnalazioni pubbliche mappa vuota senza messaggio (produzione oggi) | QUALITA' | S |
| FO-2-10 | pagine multilingua | entrambi | "Home" non tradotto in de/es, registro formale/informale misto, JSON solo it/en | RIFINITURA | S |
| FO-2-11 | tutte | entrambi | Warning Livewire "assets out of date" | RIFINITURA | S |
| FO-2-12 | `/it/tickets/{id}` | ospite | "Accedi per seguire" perde il ritorno alla segnalazione | QUALITA' | S |
| FO-2-13 | `/it/tickets/{id}` | entrambi | Nessuna foto mostrata, nemmeno al proprietario | QUALITA' | M |
| FO-2-14 | `/it/tickets/{id}` | entrambi | Niente condividi, niente stampa, niente ultimo aggiornamento | RIFINITURA | S |
| FO-2-15 | `/it/tickets/{id}` | ospite | Niente og:image ne' JSON-LD | RIFINITURA | S |
| FO-2-16 | `/it/tickets/{id}` | entrambi | Timeline: testo fuorviante, "Prossimi passi" identici, nessun evento di ricezione | QUALITA' | M |
| FO-2-17 | `/it/tickets/{id}` | cittadino proprietario | Il proprietario vede la pagina degli altri: nessuna azione | QUALITA' (candidato BLOCCA) | L |
| FO-2-18 | area personale seguite | cittadino | Il follower vede il codice di tracciamento di segnalazioni altrui | QUALITA' | S |
| FO-2-19 | `/it/tickets/track` | ospite | Badge stato con contrasto 2,44:1 | QUALITA' | S |
| FO-2-20 | `/it/tickets/track` | ospite | Throttle conta anche le visualizzazioni; 429 senza attesa | QUALITA' | S |
| FO-2-21 | `track`, `confirmation` | ospite | Nessun noindex ne' Disallow su URL con capability | QUALITA' | S |
| FO-2-22 | `/it/tickets/track` | ospite | Risultato scarno, codice nell'URL, stesso errore per ogni formato | RIFINITURA | S |
| FO-2-23 | `/it/tickets/track` | ospite | Voce di menu sbagliata evidenziata, fascia scura vuota sotto il footer | RIFINITURA | S |
| FO-2-24 | `/it/tickets/confirmation` | ospite | Senza dati mostra comunque spunta verde e "Segnalazione inviata" | QUALITA' | S |
| FO-2-25 | `/it/tickets/confirmation` | cittadino | Dichiara un'email di riepilogo che nessun codice invia | BLOCCA | S/M |
| FO-2-26 | `/it/tickets/confirmation` | entrambi | 5 link `#`, servizio inventato, numero verde copiato dal template | BLOCCA | S |
| FO-2-27 | `/it/tickets/confirmation` | cittadino | Nessuna ricevuta PDF, nessun copia/stampa del codice | QUALITA' | M |
| FO-2-28 | header utente | cittadino | Il menu mostra l'email al posto del nome | RIFINITURA | S |
| FO-2-29 | impostazioni | cittadino | Diritti GDPR non esercitabili: "contatti del Comune" porta a un footer senza contatti | BLOCCA | M |
| FO-2-30 | area personale | cittadino | Nessun profilo modificabile; `/it/profile` e `/it/profilo` 404 | QUALITA' | M |
| FO-2-31 | pratiche | cittadino | "Le mie segnalazioni" senza ricerca, filtri, ordinamento, ultimo aggiornamento | QUALITA' | M |
| FO-2-32 | area personale | cittadino | Terminologia incoerente (pratiche/segnalazioni, Aperta/Aperto) | RIFINITURA | S |
| FO-2-33 | seguite | cittadino | Nessun "Smetti di seguire" dalla lista | QUALITA' | S |
| FO-2-34 | notifiche | cittadino | Voci col codice e non col titolo, nessuna paginazione, nessun badge "non lette" | QUALITA' | M |
| FO-2-35 | impostazioni | cittadino | Un solo toggle; nessuna lingua per le comunicazioni | QUALITA' | M |
| FO-2-36 | dettaglio da area personale | cittadino | "Vedi il dettaglio" da 404 per segnalazioni senza `location` | QUALITA' | S |
| FO-2-37 | area personale, conferma | cittadino | Testo `\|---LINE:n---\|` visibile (artefatto dell'istanza, non del codice) | RIFINITURA | S |
| FO-2-38 | ciclo di vita | cittadino | Nessuna notifica di ricezione; notifiche solo per cambi di stato pubblici | QUALITA' (D) | M |

Conteggio: BLOCCA 3 (FO-2-25, 26, 29), QUALITA' 23, RIFINITURA 12. Totale 38.

### 2.2 Dettaglio dei riscontri

#### Elenco `/it/tickets`

**FO-2-01 Valore di ricerca sopra l'etichetta**
- Dove/chi: `/it/tickets?q=lampione`, ospite e cittadino.
- Difetto: dopo l'invio il testo "lampione" si sovrappone all'etichetta "Cerca nelle segnalazioni", perche' il valore e' reso dal server e l'etichetta non sale.
- Evidenza: M, `S/guest-1280-search-lampione.png`, `S/guest-1280-empty.png`.
- Gravita': QUALITA'. Taglia: S.
- Filament: il form filtri come componente Livewire con proprieta' `#[Url]` e `Filament\Forms\Components\TextInput::make('q')` con etichetta sopra il campo (niente etichetta flottante). Soluzione minima senza Filament: classe `active` sulla label quando il valore non e' vuoto.
- Owner: `Themes/Sixteen` blocco `ticket-list/filters.blade.php`.
- Legami: DCP parity elenco.

**FO-2-02 URL con parametri vuoti**
- Evidenza: M, dopo l'invio l'URL e' `?q=&period=&sort=recent`.
- Difetto: URL sporchi, non canonici, rendono meno condivisibili i link.
- Filament: con proprieta' Livewire `#[Url(except: '')]` i parametri vuoti spariscono da soli.
- Owner: `Themes/Sixteen` blocco filtri / `PublicTicketListFilters` in `Modules/Fixcity`. RIFINITURA, S.

**FO-2-03 Mobile: mappa primo tab, controlli e modale**
- Evidenza: M. A 375 il primo tab e' la mappa (alta 320 px), lo stack di 5 pulsanti di controllo e' tagliato (manca il "-"). Con la modale "Filtri" aperta i controlli della mappa restano sopra la modale. `S/guest-375-it_tickets.png`, `S/guest-375-filters-modal.png`.
- Difetto: su telefono il cittadino che cerca una segnalazione trova prima una mappa piccola; i controlli si sovrappongono alla modale (z-index).
- Come: su mobile aprire sul tab Elenco (o ricordare l'ultimo tab scelto); dare al contenitore mappa `isolation: isolate` (D: non verificato quale contesto di stacking causa la fuga); alzare i controlli solo dentro la mappa.
- Filament: il filtro mobile come `Filament\Actions\Action` con `->slideOver()` in un componente Livewire gestisce da solo pila e z-index delle modali.
- Owner: `Themes/Sixteen` (blocco + CSS), `Modules/Geo` (`map-lit`). QUALITA', S.

**FO-2-04 Mobile: filtri attivi non visibili**
- Evidenza: M, `S/guest-375-after-modal-filter.png`: dopo aver filtrato il pulsante "Filtra" non mostra alcun indicatore e "Rimuovi tutti i filtri" e' `d-none d-lg-block` (nascosto su mobile).
- Come: badge col numero di filtri sul pulsante e "chip" removibili sopra i risultati; "Rimuovi tutti" visibile anche su mobile.
- Filament: gli indicatori dei filtri di `Filament\Tables\Table` (`->filters([...])`) mostrano i filtri attivi con rimozione singola di serie. Se si resta sul markup DC: una riga di chip costruita dalla stessa struttura dati dei filtri.
- Owner: `Themes/Sixteen`. QUALITA', S.

**FO-2-05 Card povere**
- Evidenza: M, `S/guest-1280-list-top.png`, `S/guest-1280-list-card-expanded.png`. Ogni card mostra solo titolo, tipo e stato; indirizzo, descrizione e data stanno dietro "Mostra tutto"; nessuna miniatura; lo stato e' testo semplice senza colore.
- Nota: il riferimento DC ufficiale usa lo stesso accordion (`S/c-dc-elenco.png`): l'accordion e' voluto dalla parita'. Il difetto vero e' lo stato senza colore/etichetta e l'assenza di una data visibile.
- Filament: `Tables\Columns\Layout\Stack` + `Tables\Table::contentGrid()` per una griglia di card; `TextColumn::make('status')->badge()->color(...)` e `TextColumn::make('created_at')->since()`; `ImageColumn` per una miniatura derivata (vedi FO-2-13).
- Owner: `Themes/Sixteen` `ticket-list/design-comuni.blade.php`. Decisione di prodotto: restare fedeli al DC o allinearsi a Filament. QUALITA', M.

**FO-2-06 Zero risultati**
- Evidenza: M, `S/guest-1280-empty.png`: il testo di stato vuoto c'e' ma le faccette Categoria e Stato spariscono, quindi non si capisce quale filtro ha svuotato la lista; resta un vuoto verticale sotto il titolo. RIFINITURA, S.

**FO-2-07 Accessibilita' tastiera della mappa**
- Evidenza: M (`s28-kbd.mjs`). I cluster hanno `role=button` senza nome; ci sono circa 10 tab stop tra marker e cluster; il `tablist` (`role=tab`) non risponde a ArrowRight/ArrowLeft. I marker hanno invece `title`. axe segnala 0 violazioni (`O/axe.json`), quindi sono difetti che axe non vede.
- Come: `aria-label` "Gruppo di N segnalazioni, premi per ingrandire"; tabindex a rotazione (roving) sul `tablist`; scorciatoia per saltare la mappa.
- Owner: `Modules/Geo` componente `map-lit`; tab in `Themes/Sixteen`. QUALITA', M.

**FO-2-08 Popup mappa**
- Evidenza: M, `S/guest-375-map-popup2.png`: titolo troncato ("Fermata dell'autobu..."), skeleton visibile oltre 1 s al primo caricamento; la proprieta' `city` del geojson e' sempre vuota (`O/g-map*.json`). RIFINITURA, S.

**FO-2-09 Mappa vuota senza messaggio**
- Evidenza: M su produzione, `S/prod-_it_tickets.png`: con zero segnalazioni pubbliche il tab Mappa mostra l'Europa vuota, senza testo. Al lancio il primo visitatore vedra' esattamente questo.
- Come: se `features.length === 0` mostrare un avviso "Non ci sono ancora segnalazioni pubbliche. Segnala tu il primo problema" con il CTA; centrare sulla `default_view` (Italia).
- Owner: `Modules/Geo`, `Themes/Sixteen`. QUALITA', S.

**FO-2-10 Lingue**
- Evidenza: M. it, en, de, es: nessuna chiave grezza ne' italiano non voluto, ma "Home" non tradotto nel breadcrumb del dettaglio in de/es (mentre in es l'area personale dice "Inicio"); registro misto tra pagine ("Ihr/Su" e "Sie/Tu"); `tickets/index.json` ha blocchi solo per it/en (de/es funzionano per fallback). `S/citizen-de-1280-de_tickets_1.png`.
- Come: un test di lingue per i breadcrumb; scegliere un registro unico per lingua. Nota di memoria progetto: il fallback it nasconde le traduzioni mancanti, usare `audit-locales.py`.
- Owner: `Modules/Fixcity/lang`, JSON in `config/local/fixcity/database/content/pages`. RIFINITURA, S.

**FO-2-11 Warning Livewire**
- Evidenza: M, console di ogni pagina: "Livewire: The published Livewire assets are out of date". Probabile ambiente dell'istanza (asset pubblicati non aggiornati); non ho potuto lanciare il publish. RIFINITURA, S.

#### Dettaglio `/it/tickets/{id}`

**FO-2-12 Ritorno dopo il login**
- Evidenza: M (`O/login-return.json`). Da ospite, "Accedi per seguire" porta a `/it/auth/login` semplice; dopo il login si atterra su `/it`. Provati `?redirect=`, `?next=`, `?intended=`, `?returnUrl=`: tutti finiscono su `/it`. Le pagine protette (`/it/area-personale/*`, `/it/tickets/create`) invece tornano correttamente (redirectIntended di sessione).
- Difetto: l'intenzione di seguire e' persa; il cittadino deve rifare il percorso.
- Come: il blocco imposta l'URL intended di sessione prima di mostrare il link, oppure il widget di login legge un parametro di ritorno validato (solo percorso relativo stesso host, per evitare open redirect).
- Filament: nel login Livewire usare `redirect()->intended(...)`, equivalente al `LoginResponse` di Filament.
- Owner: `Modules/User` (login), `Themes/Sixteen` blocco azioni del dettaglio. QUALITA', S.

**FO-2-13 Foto assente**
- Evidenza: M. Il wizard ha caricato una foto per la segnalazione 17; il dettaglio (proprietario) non la mostra, e non c'e' nessuna sezione foto in nessun dettaglio. Il codice di `BuildPublicTicketDetailAction` dichiara "Mai: immagini originali" (regola giusta per la privacy) ma non prevede una derivata.
- Come: una derivata pubblica (miniatura ridotta, senza EXIF) mostrata solo per stati pubblici e con consenso del moderatore (volti, targhe); il proprietario vede sempre la propria foto originale.
- Filament: `Infolists\Components\ImageEntry` per la miniatura (conversione Media Library `thumb`), in una `Schemas\Components\Section`; lato back office un `Toggle` "foto pubblica" per il moderatore.
- Owner: `Modules/Fixcity` (Action di dettaglio), Media. Decisione: DEC su cosa e' pubblico. QUALITA', M.

**FO-2-14 Condividi, stampa, ultimo aggiornamento**
- Evidenza: M. Nessun pulsante condividi; nessuno stile di stampa (l'emulazione stampa stampa header, pulsanti e controlli mappa: `S/print-detail17.png`); non c'e' la data dell'ultimo aggiornamento ne' un tempo atteso.
- Come: `navigator.share` con ripiego "copia il link"; `@media print` per nascondere header, footer, azioni; mostrare `updated_at` accanto allo stato.
- Owner: `Themes/Sixteen`. RIFINITURA, S.

**FO-2-15 Meta social e dati strutturati**
- Evidenza: M. Ci sono canonical, hreflang, Open Graph e Twitter, ma nessun `og:image` e nessun JSON-LD. La sitemap elenca solo le 12 pubbliche (bene).
- Owner: `Modules/Seo`. RIFINITURA, S.

**FO-2-16 Timeline e "Prossimi passi"**
- Evidenza: M. Con timeline vuota, per le segnalazioni "Risolto"/"Chiuso" il testo e' "Per ora non ci sono aggiornamenti: la segnalazione e' «Risolto»", fuorviante per un caso concluso. "Prossimi passi" mostra la stessa riga ("Lavorazione: presa in carico") per Aperto e per Riaperto.
- D: `BuildTicketTimelineAction` non ha un evento "ricevuta/creata" e non prevede un messaggio libero del Comune; ci sono solo cambi di stato e etichette mail/seguito/media. Non ho potuto provare una timeline con eventi reali (nessuna credenziale admin).
- Come: aggiungere l'evento iniziale "Segnalazione ricevuta"; messaggi diversi per stato; un'azione "Aggiungi messaggio pubblico" nel back office.
- Filament: `Infolists\Components\RepeatableEntry` per la timeline; lato back office `Actions\Action` con modale (`Textarea` o `RichEditor`) per il messaggio pubblico.
- Owner: `Modules/Fixcity`. QUALITA', M.

**FO-2-17 Il proprietario non ha nessuna azione**
- Evidenza: M. Giulia (proprietaria delle 13) sui ticket 1, 3, 7, 9, 12 e Paolo sul 17 vedono le stesse tre azioni di un estraneo: "Segui questa segnalazione", "Torna all'elenco", "Segnala un altro problema". Seguire la propria segnalazione non ha senso. Mancano: modifica, ritiro/annulla, integra (testo e foto), riapri, conferma risoluzione, valuta (la Action `SubmitCitizenTicketRatingAction` esiste, la UI no), ricevuta, codice di tracciamento. `S/giulia-owner-1280-it_tickets_3-owner.png`, `O/c-owner-resolved.json`.
- Come: un widget del proprietario sul dettaglio, visibile solo se `auth()->id() === owner_id`, con azioni abilitate per stato.
- Filament (Fixcity, widget Livewire con `Filament\Actions\Action`):
  - "Integra": `Action::make('integrate')->modalHeading(...)->schema([Textarea::make('note')->required()->maxLength(2000), FileUpload::make('photos')->image()->multiple()->maxFiles(3)->maxSize(5120)])->action(...)`.
  - "Ritira": `Action::make('withdraw')->requiresConfirmation()->color('danger')->schema([Select::make('reason')->options(...)])`.
  - "Riapri": visibile solo per RESOLVED/CLOSED entro N giorni, `requiresConfirmation()` con motivo obbligatorio.
  - "Conferma e valuta": `ToggleButtons::make('score')->options([1..5])->inline()` + `Textarea`, chiama `SubmitCitizenTicketRatingAction`.
  - Ogni azione con `->visible(fn () => ...)` e una policy; la logica resta in Queueable Actions di Fixcity (no Services).
- Legami: A-15 (nessuna UI proprietario). Serve una DEC sugli stati ammessi per ogni azione. Qui metto QUALITA' ma se la DEC dice che il ciclo completo e' requisito di go-live passa a BLOCCA. L.

**FO-2-18 Il follower vede il codice di tracciamento altrui**
- Evidenza: M (`O/c2-follower.json`, `S/citizen2-1280-it_area_personale_seguite-follows5.png`). Paolo ha seguito la segnalazione 5 (di Giulia); in "Segnalazioni che seguo" compare `Codice di tracciamento: DEMO-PUB-05` con il link `/it/tickets/track?code=DEMO-PUB-05`. Questo contraddice l'invariante di `BuildPublicTicketDetailAction` ("Mai: code").
- Oggi il codice permette solo di leggere la pagina di tracciamento (gia' in sostanza pubblica), quindi l'impatto e' basso; diventa BLOCCA se il codice abilitera' mai azioni (integra, ritira).
- Come: per i follower mostrare solo "Vedi il dettaglio"; il codice solo al proprietario.
- Owner: `Themes/Sixteen` blocco `personal-area-practices/followed.blade.php` (ed eventualmente l'Action che costruisce le righe in Fixcity). QUALITA', S.

#### Tracciamento `/it/tickets/track`

**FO-2-19 Contrasto del badge**
- Evidenza: M. `.badge.rounded-pill.bg-primary.dc-ticket-track__status` ha testo `#17324d` su `#007a52`, rapporto 2,44:1; axe lo segnala "serious color-contrast" (`O/axe.json`, `S/guest-1280-track-valid.png`). E' l'unico difetto axe sulle 9 pagine controllate.
- Come: testo bianco su verde scuro o usare il token DC corretto. Filament: `TextEntry::make('status')->badge()->color(...)` produce badge con contrasto controllato. Owner: `Themes/Sixteen` CSS. QUALITA', S.

**FO-2-20 Throttle e pagina 429**
- Evidenza: M. `throttle:10,1` per IP conta anche le visualizzazioni della pagina, non solo le ricerche (intestazione `X-RateLimit-Limit: 10`). Con piu' sessioni sullo stesso IP (e dietro NAT) si arriva presto al 429. La pagina 429 e' a tema, con messaggio chiaro e pulsante, ma ha un'impaginazione diversa dal resto (menu sottolineato, footer diverso), e non mostra i secondi di attesa (D: nessun `Retry-After` in pagina). `S/guest-1280-track-xss.png`.
- Come: un limiter dedicato `RateLimiter::for('ticket-track')` solo sulle richieste con `code`, chiave IP + hash del codice; mostrare i secondi. Legami: G-28, DCP-149/150. QUALITA', S.

**FO-2-21 Nessun noindex**
- Evidenza: M. Ne' `track` ne' `confirmation` hanno meta `robots` o intestazione `X-Robots-Tag`; `robots.txt` non ha `Disallow`. Sono URL con codice (capability).
- Come: middleware con `X-Robots-Tag: noindex, nofollow` su queste rotte e `Disallow` in robots.txt. Owner: `Modules/Seo`. QUALITA', S.

**FO-2-22 Risultato scarno**
- Evidenza: M. Il risultato mostra titolo, stato, descrizione e "Aggiornamenti: Non ci sono ancora aggiornamenti pubblici". Mancano indirizzo, data, mappa, foto; il proprietario vede la riga "Codice segnalazione". Il codice resta nell'URL (`?code=`), codice sbagliato e codice malformato danno lo stesso messaggio e ogni tentativo consuma una delle 10 richieste al minuto, senza controllo del formato lato client.
- Come: una validazione del formato (prefisso `TCK-` + lunghezza) prima della richiesta; invio via POST. RIFINITURA, S.

**FO-2-23 Menu e fascia**
- Evidenza: M. Sulla pagina di tracciamento il menu evidenzia "Segnalazioni" e non "Traccia"; su diverse pagine c'e' una grande fascia scura vuota sotto il footer. RIFINITURA, S (`Themes/Sixteen`).

#### Conferma `/it/tickets/confirmation`

**FO-2-24 Falso successo per ospite**
- Evidenza: M, `S/guest-1280-it_tickets_confirmation.png`. Aprendo la pagina senza dati compare la spunta verde e il titolo "Segnalazione inviata" con sotto "Non troviamo i dati dell'ultima segnalazione inviata".
- Come: senza codice in sessione, redirect all'elenco o titolo neutro. Owner: pagina JSON `tickets/confirmation.json` + blocco. QUALITA', S.

**FO-2-25 Email di riepilogo promessa, non inviata**
- Evidenza: la conferma di Paolo dice "Abbiamo inviato il riepilogo all'email: ...". D: `TicketCreatedListener` imposta solo lo stato PENDING; `TicketReceivedStaffNotification` e `TicketLifecycleNotification` non vengono mai inviate. M: dopo l'invio, "Notifiche" di Paolo e' vuota. L'istanza usa `MAIL_MAILER=array`, quindi la posta non e' osservabile: NON posso provare se parte una mail; la prova e' solo nel codice.
- Difetto: una promessa del servizio che il codice non mantiene, in un sito pubblico dell'ente.
- Come: o togliere la frase subito, o implementare una Queueable Action `SendTicketReceiptAction` agganciata al listener dopo il commit, con notifica nella lingua dell'utente (A-22) che contiene codice e link.
- Filament: `Filament\Notifications\Notification::make()->title(...)->sendToDatabase($user)` per la copia in-app piu' il canale mail dalla `Notification` Laravel.
- Owner: `Modules/Fixcity` (listener/Action), `Modules/Notify` (canali). Legami: A-01, A-22, G-07, G-08, D-24; in lavorazione DCP-284. BLOCCA, S (togliere la frase) o M (implementare).

**FO-2-26 Link morti e contenuto inventato**
- Evidenza: M (`O/c2-submit.json`). Nella pagina di conferma ci sono 5 link `href="#"`: "Richiesta appuntamento", "Leggi le domande frequenti", "Richiedi assistenza", "Chiama il numero verde 05 0505", "Prenota appuntamento". Il "servizio correlato" "Richiesta appuntamento" e il numero verde arrivano dal template DC, non dal Comune titolare. Non ho nessuna prova che "05 0505" sia un contatto reale del servizio.
- Difetto: contatti e servizi inventati su una pagina pubblica di una PA.
- Come: togliere il blocco dal JSON `tickets/confirmation.json` o collegarlo a pagine e numeri veri presi dalla configurazione dell'ente. Owner: `Modules/Cms` JSON. Legami: G-10 (contatti dell'ente). BLOCCA, S.

**FO-2-27 Ricevuta e codice**
- Evidenza: M. La pagina del DC ufficiale (`S/dc-ref-segnalazione-04-conferma.png`) ha "Scarica la ricevuta (PDF)"; la nostra no. Il codice si mostra ma non c'e' copia ne' stampa.
- Come: `Filament\Actions\Action::make('receipt')` che chiama laravel-pdf (skill gia' presente nel repo); pulsante "Copia codice" con `navigator.clipboard`. Owner: `Modules/Fixcity`. QUALITA', M.

**FO-2-28 Menu utente con l'email**
- Evidenza: M (`S/citizen2-1280-confirmation.png`). Per l'utente registrato via UI (Paolo Verdi) il menu in alto mostra l'indirizzo email; la pagina impostazioni mostra correttamente il nome. RIFINITURA, S (`Modules/User` / header Sixteen).

#### Area personale

**FO-2-29 Diritti GDPR non esercitabili**
- Evidenza: M. La pagina Impostazioni elenca i diritti (art. 15-21) con il pulsante "Vai ai contatti del Comune", che porta a `#footer`; il footer "Contatti" non contiene nessun contatto reale (ne' email, ne' PEC, ne' telefono, ne' DPO). Il testo dice esplicitamente "Oggi l'area personale non permette di scaricare o cancellare i dati in autonomia". Il cittadino non ha quindi nessun canale per esercitare i diritti. `S/citizen-1280-it_area_personale_impostazioni.png`.
- Come: dati di contatto e DPO reali (DEC dell'ente); per l'autonomia: un'azione "Scarica i miei dati" (export JSON/PDF) e "Cancella il mio account" con conferma.
- Filament: `Actions\Action::make('exportData')` e `Action::make('deleteAccount')->requiresConfirmation()->modalHeading(...)->schema([TextInput::make('password')->password()->required()])`, con la logica in Queueable Action di `Modules/Gdpr`.
- Legami: G-10, D-xx sul DPO, DCP-112 (conformita'). In lavorazione DCP-285. BLOCCA, M.

**FO-2-30 Profilo non modificabile**
- Evidenza: M. `/it/profile` e `/it/profilo` danno 404. Nome, email, telefono sono di sola lettura ("Da questa pagina non puoi modificarli"); manca la modifica di password, email, telefono. D-03 (verifica email) collegato.
- Come: una `Schemas\Components\Section` con form `TextInput` (nome, telefono), `TextInput::make('password')->password()->revealable()`, cambio email con verifica. Owner: `Modules/User`. Non creare una pagina nel tema (regola di progetto). QUALITA', M.

**FO-2-31 "Le mie segnalazioni" povera**
- Evidenza: M, `S/citizen-1280-it_area_personale_pratiche.png`. Ci sono le sezioni "In corso (9)" e "Concluse (4)", righe con stato e data, "Vedi il dettaglio" e "Traccia la segnalazione". Mancano ricerca, filtro, ordinamento, categoria, indirizzo, foto, ultimo aggiornamento, azioni del proprietario (vedi FO-2-17). Lo stato vuoto per Paolo e' buono (vedi sezione 3).
- Filament: `Filament\Tables\Table` in un componente Livewire (`->searchable()`, `->filters([SelectFilter::make('status')])`, `->defaultSort('updated_at','desc')`) con `->recordActions([...])` per integra/ritira. Owner: `Modules/Fixcity` + blocco in Sixteen. QUALITA', M.

**FO-2-32 Terminologia incoerente**
- Evidenza: M. Area personale: "Aperta/Risolta/Chiusa/Riaperta/In attesa" (femminile); elenco e dettaglio: "Aperto/Risolto/Chiuso/Riaperto". Menu utente: "Le mie pratiche / Segnalazioni seguite / Notifiche / Impostazioni / Esci"; titoli: "Le mie segnalazioni / Segnalazioni che seguo / Impostazioni e privacy". "Pratiche" e' gergo della PA.
- Come: una sola etichetta per stato (un `TicketStatusEnum::label()` per tutte le viste) e una sola dicitura per voce. Owner: `Modules/Fixcity/lang`. RIFINITURA, S.

**FO-2-33 Seguite senza "Smetti di seguire"**
- Evidenza: M, `S/citizen2-1280-it_area_personale_seguite-follows5.png`. Le righe hanno solo "Vedi il dettaglio" e "Traccia"; per smettere si deve aprire il dettaglio.
- Come: `Action::make('unfollow')->requiresConfirmation()` per riga. Owner: `Modules/Fixcity`. QUALITA', S.

**FO-2-34 Notifiche**
- Evidenza: pagina vuota M (`S/citizen2-1280-it_area_personale_notifiche.png`). D (codice `notifications-list.blade.php`): le voci dicono "Segnalazione TCK-...: stato X" (codice, non titolo), con "Segna come letto" e "Segna tutte come lette"; `has_more` mostra solo testo (nessuna paginazione); nessun badge "non lette" nel menu utente (M).
- Non provato: nessuna notifica reale (nessun admin per cambiare lo stato).
- Come: titolo della segnalazione al posto del codice, paginazione "Carica altre", contatore nel menu. Filament: `Filament\Notifications\Notification` + il componente `DatabaseNotifications` (D: da verificare fuori da un panel; altrimenti lista Livewire con le stesse notifiche). Legami: A-01, DCP-110/111. QUALITA', M.

**FO-2-35 Preferenze**
- Evidenza: M. Un solo interruttore "Ricevi aggiornamenti via email", salva con messaggio "Preferenze di notifica salvate." e persiste (provato salva e ripristina). Mancano scelta per evento/canale e lingua delle comunicazioni (A-22).
- Come: form Filament con `Toggle` per evento/canale e `Select::make('locale')` salvato sull'utente, letto dal canale mail. Owner: `Modules/User`, `Modules/Notify`. Legami: A-22, D-24. QUALITA', M.

**FO-2-36 Dettaglio 404 per segnalazioni senza `location`**
- Evidenza: M. La segnalazione 13 del fixture, di proprieta' di Giulia, da 404 sul dettaglio anche a lei. Causa verificata: non ha `location` e `BuildPublicTicketsQueryAction` richiede `location` NOT NULL. La segnalazione 17 creata dal wizard ha `location` ed e' visibile al proprietario (200). Quindi e' un artefatto del fixture, ma il rischio resta (D): qualsiasi segnalazione creata senza posizione (canale diverso dal wizard) darebbe 404 al proprietario dal link "Vedi il dettaglio".
- Come: nel percorso del proprietario non filtrare per `location`; oppure nascondere il link. Owner: `Modules/Fixcity`. QUALITA', S.

**FO-2-37 Marker `|---LINE:n---|`**
- Evidenza: M. Testo visibile `|---LINE:151---|` / `|---LINE:153---|` sulle pagine dell'area personale e sulla conferma. Causa tracciata: marcatori del renderer di eccezioni Laravel rimasti nelle view compilate dell'istanza scratch (`/tmp/scratch-auth-fixture-zorin/storage/framework/views/`), non nei sorgenti. Si risolve con `view:clear`, che non potevo lanciare. Gia' noto in memoria progetto ("|---LINE:n---| nell'HTML"). Non va contato come difetto del codice. RIFINITURA, S.

**FO-2-38 Ciclo di vita e notifiche (non provato)**
- D: le sole notifiche inviate sono cambio di stato (solo verso stati pubblici), assegnazione e commento; nessuna notifica di ricezione ne' mail di ricevuta; `NotifyTicketSubscribersOfStatusChangeAction` invia a follower e proprietario. Il passaggio PENDING -> OPEN e' un cambio di stato pubblico, quindi dovrebbe produrre una notifica, ma non l'ho potuto provare (nessun admin).
- Come: dopo DCP-284, ripetere la prova con un account staff. Legami: A-01, A-12, A-22. QUALITA', M.

## 3. Cosa funziona bene

| Cosa | Prova |
|---|---|
| Ricerca e filtri via GET, URL condivisibili, categoria/stato/periodo/ordine, `page=999` riportata all'ultima pagina, `?q=<script>` sicuro | `S/guest-1280-filter-combo.png`, `S/guest-1280-it_tickets_page_999-q.png`, `S/guest-1280-it_tickets_q_25_27_3Cscript_3Ealert_1_3C_2Fscript_3E-q.png` |
| Filtri su mobile in modale; il focus del checkbox e' visibile | `S/guest-375-filters-modal.png`, `S/guest-1280-kbd-checkbox.png` |
| Popup marker con tipo, indirizzo, descrizione, "Dettagli" e link di navigazione | `S/guest-1280-map-popup2.png` |
| Dettaglio con numero, tipo, data, stato, indirizzo, mappa, "Stato della segnalazione", "Prossimi passi" | `S/guest-1280-it_tickets_1.png` |
| Un ID non pubblico o inesistente da 404 a tema (non rivela l'esistenza); la sitemap elenca solo le 12 pubbliche | `S/guest-1280-it_tickets_13.png` |
| La segnalazione pendente e' visibile solo al proprietario, con "verificata da un moderatore" | `S/citizen2-1280-it_tickets_17-own-pending-detail.png` |
| "Segui" funziona con conferma `role=status` e il bottone diventa "Smetti di seguire" | `S/citizen-1280-after-follow.png` |
| Tracciamento: codice valido (anche minuscolo) mostra titolo, stato, descrizione; 429 a tema con messaggio chiaro | `S/guest-1280-track-valid.png`, `S/guest-1280-track-xss.png` |
| Il wizard completo con foto produce la conferma con codice e pulsante "Traccia" | `S/citizen2-1280-confirmation.png` |
| Area personale: ospite rimandato al login e riportato alla pagina dopo l'accesso; pagine private `Cache-Control: no-store`; logout e tasto Indietro tornano al login | `S/citizen-1280-after-login-from-_it_area_personale_pratiche.png`, `S/paolo-1280-after-logout-back.png` |
| Stato vuoto di "Le mie segnalazioni" con CTA "Invia la tua prima segnalazione" | `S/citizen2-1280-it_area_personale_pratiche.png` |
| Preferenza email: salva, mostra feedback, persiste | `S/citizen-1280-settings-saved.png` |
| it/en/de/es: tutte le pagine controllate tradotte, nessuna chiave grezza | `S/citizen-es-1280-es_area_personale_pratiche.png` |
| Mobile 375: nessun overflow orizzontale su nessuna pagina provata | `O/c-area.json`, `O/g-list.json` |
| axe-core: 0 violazioni su elenco, elenco in lista, dettaglio (ospite e cittadino), 4 pagine area personale; 1 solo difetto sul badge di tracciamento | `O/axe.json` |

## 4. Domande per l'utente

1. Chi decide e fornisce i contatti reali dell'ente (email, PEC, telefono, DPO) per i diritti GDPR e per la pagina di conferma (FO-2-26, FO-2-29)? Finche' mancano, "Vai ai contatti del Comune" porta a un footer vuoto.
2. Quali azioni deve avere il proprietario dopo l'invio, e in quali stati: integrare, ritirare, riaprire, confermare la risoluzione, valutare (FO-2-17)? Ricevuta PDF si o no (FO-2-27)?
3. Al go-live deve partire una mail di ricevuta alla creazione (FO-2-25)? Se no, togliamo la frase dalla conferma?
4. I follower devono vedere il codice di tracciamento? Io proporrei di no (FO-2-18).
5. Quale politica di ritorno dopo il login: solo il percorso relativo di sessione, o anche un parametro validato (FO-2-12)?
6. Le foto delle segnalazioni devono essere mostrate al pubblico, con quale controllo (moderazione, sfocatura targhe e volti) (FO-2-13)?
7. Unifichiamo la terminologia: "segnalazioni" ovunque (via "pratiche") e un solo genere per gli stati (FO-2-32)?
8. `track` e `confirmation` devono essere `noindex` (FO-2-21)? Proposta: si.
9. Vogliamo la preferenza di lingua per le comunicazioni in area personale (A-22) in questo ciclo o dopo (FO-2-35)?
10. Il tab Mappa deve restare il primo anche su mobile (FO-2-03), e va bene lo stato vuoto con CTA al lancio con zero segnalazioni (FO-2-09)?
11. Condividi e stampa servono al primo rilascio (FO-2-14)?

## 5. Osservazioni su commenti

Il blocco commenti sul dettaglio sembrava senza stile nelle mie catture (barra strumenti resa come "||" e "xxxxxxxxxx", "Pubblica commento" non stilizzato) ed e' presente per Giulia ma assente per Paolo sul ticket 1; il log mostra errori di tipo su `CommentWidget::$uiConfig` intorno alle 13:03-13:05 (ora di Roma) mentre un'altra sessione modificava il codice; non analizzato.

## 6. Cosa NON ho potuto provare

- Notifiche reali di cambio di stato e voci vere in "Notifiche": servono credenziali staff per cambiare lo stato (non ne ho).
- Timeline con eventi reali (stesso motivo).
- Consegna delle email: l'istanza usa `MAIL_MAILER=array`; FO-2-25 e' solo DEDOTTO dal codice.
- Paginazione oltre 20 risultati: il fixture ha 12 segnalazioni pubbliche; ho misurato solo `page=2` e `page=999`.
- `sort=updated` diverso da `recent`: nel fixture l'ordine coincide.
- Foto pubblica sulle segnalazioni del fixture (non hanno foto).
- Scelte di notifica per evento/canale (non esistono).
- Altri browser oltre Chromium, lettori di schermo, condivisione tramite app native.
- Flussi autenticati su produzione.
- Il lato PA/back office (fuori gruppo).
