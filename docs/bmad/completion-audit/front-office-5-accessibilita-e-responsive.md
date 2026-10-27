---
title: "Audit front office 5: accessibilita e responsive"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "audit front office 5: accessibilita e responsive audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# FO-5: accessibilita, tastiera e responsive del front office

Auditor: trasversale FO-5. Data: 2026-10-26. Istanza: `http://127.0.0.1:8091` (isolata, DB usa e getta). Nessun file del repository e' stato scritto o modificato. Nessun git, composer, npm, pest, phpstan, migrate.

Convenzione per le prove: `D/` = `/tmp/claude-1000/-mnt-nas07-var-www--bases-base-fixcity-fila5/fa79ffe9-c6fb-4eb5-892f-259fcfdd19d2/scratchpad/fo-audit/fo-5-a11y-responsive/`. Gli script Node (Playwright e axe presi da `laravel/Themes/Sixteen/node_modules`) sono in `D/*.mjs`, i risultati grezzi in `D/out/`, le immagini in `D/shots/` e `D/sheets/`.

Etichette: MISURATO = riprodotto da me con uno script o letto su uno screenshot che ho guardato. DEDOTTO = ricavato dal markup o dal codice letto, non provato in esecuzione.

## 0. Sintesi

33 finding: 3 BLOCCA go-live, 18 QUALITA', 12 RIFINITURA.

I tre che bloccano:
1. FO-5-01: su telefono il campo del codice di tracciamento e' largo 16 px (pagina Traccia, ogni larghezza sotto 576 px).
2. FO-5-02: la pagina "Password dimenticata" e' la pagina stock di Laravel, in inglese, senza header, footer, h1, main, con la chiave grezza `passwords.sent` come messaggio di conferma.
3. FO-5-03: il pulsante "Vedi il dettaglio" della propria segnalazione porta a un 404 (osservato fuori perimetro, e' dell'auditor dell'area personale).

Cio' che regge molto bene: nessuno scroll orizzontale a 320, 375, 768, 1024, 1440 su 39 pagine (195 misure), un solo h1 ovunque, lingua corretta, nessuna immagine senza alt, nessun campo senza etichetta, focus a due toni forte ovunque tranne la FAQ, movimento ridotto rispettato, errori di login legati con `aria-describedby`. Dettaglio in sezione 5.

Avvertenze di misura, da leggere prima dei numeri:
- Nelle pagine di questa istanza compaiono marcatori `|---LINE:151---|` e `|---LINE:153---|` nel testo di `main`. Vengono dal renderer delle eccezioni di Laravel (`BladeMapper`) dopo un 500 e sono un artefatto dell'istanza di prova, non un difetto del prodotto: non li conto. Appaiono anche negli screenshot.
- `/it/tickets/track` ha risposto 429 sotto carico parallelo (freno DCP-149) a 320 e 375 nello sweep e `/it/tickets/track/DEMO-PUB-01` a 1024 e 1440. Ho scartato quelle 4 misure e rimisurato a parte, da solo, a 375 e 768 (HTTP 200, `D/tr3.mjs`).
- Nella colonna Id della matrice "2" significa che l'id `main-container` e' presente due volte nella stessa pagina (conferma B-16).

## 1. Copertura effettiva

| Misura | Pagine | Larghezze o condizione |
|---|---|---|
| Sweep responsive: stato HTTP, overflow orizzontale, bersagli tocco, elementi fissi | 39 (30 ospite, 9 cittadina) | 320, 375, 768, 1024, 1440 = 195 misure (191 valide, 4 con 429 scartate) |
| Semantica DOM e axe (wcag2a, 2aa, 21a, 21aa, 22aa, best-practice) | 39 | 1280 e 375 |
| Tastiera: percorso Tab, nomi accessibili CDP, skip link, anello focus | 39 | 1280; 9 pagine chiave anche a 375 |
| Pixel del focus (differenza tra ritaglio con e senza focus) | 21 (20 + FAQ) | 1280 |
| Contrasto del testo reale, piu stato hover | 39 | 1280 |
| Reflow: 320x256 (400% di zoom), 640x400, font 200%, spaziatura testo 1.4.12 | 12 | vari |
| Banner cookie: stop coperti, copertura a 320x256 | 9 | 375 |
| Movimento ridotto, tema scuro, colori forzati, alto contrasto, stampa (PDF A4 e emulazione print) | 4 a 10 pagine | 1280 e A4 |
| Moduli e annunci: login, registrazione, traccia, password, wizard passi 1, 2, 3 (senza invio finale) | 8 | 1280 e 375 |
| Screenshot letti a 375 e 768 (fogli in `D/sheets/`) | 17 pagine | 375, 768 |

Non ho creato nessuna segnalazione: il wizard non e' stato inviato.

## 2. Matrice pagine per criteri

Legenda. Largh = nessun overflow orizzontale su 5 larghezze (5/5 salvo nota). Tab = percorso da tastiera: numero di stop a 1280 e esito dello skip link ("ok" porta in `main`, "tgt" porta in un `div` senza tabindex e il focus resta su body ma il Tab successivo entra in main, "manca" bersaglio assente, "-" nessuno skip link o non misurato). Focus = anello visibile: "px" verificato a pixel, "eur" solo euristica, "KO" nessun anello. Sem = violazioni axe a 1280 (HO heading-order, RG region, LU landmark-unique, CC color-contrast, TS target-size, TM landmark-main-is-top-level, DM no-duplicate-main, M1 landmark-one-main, H1 page-has-heading-one, AR aria-allowed-role; numero = occorrenze). Contr = testi sotto soglia a 1280 (0 = nessuno). T24 = bersagli sotto 24 px a 375, esclusi gli skip link nascosti, i radio 1x1 della valutazione (le loro etichette sono 34x32) e l'area commenti (fuori perimetro). Id = occorrenze di `id="main-container"`.

| Pagina | Persona | Largh | Tab (stop, skip) | Focus | Sem | Contr | T24 | Id | Finding |
|---|---|---|---|---|---|---|---|---|---|
| `/it` | ospite | 5/5 | 53, ok | px | HO1 RG1 | 0 | 1 (Leaflet) | 1 | 05, 15, 19, 22, 23, 27, 28 |
| `/en` | ospite | 5/5 | 53, ok | eur | HO1 RG1 | 0 | 1 | 1 | 05 |
| `/de` | ospite | 5/5 | 53, tradotto | eur | HO1 RG1 | 0 | 1 | 1 | 05 |
| `/es` | ospite | 5/5 | 53, tradotto | eur | HO1 RG1 | 0 | 1 | 1 | 05 |
| `/it/tickets` | ospite | 5/5 | 72, ok | px | HO1 LU1 RG1 | 0 | 1 | 2 | 05, 06, 15, 16, 17, 23 |
| `/it/tickets/1` | ospite | 5/5 | 38, tgt | px | HO1 LU1 RG1 | 0 | 0 | 1 | 06, 32 |
| `/it/tickets/12` | ospite | 5/5 | 38, tgt | eur | HO1 LU1 RG1 | 0 | 0 | 1 | 06 |
| `/it/tickets/track` | ospite | 3/5 + rimisurato 375, 768 | 32, manca | px | HO1 LU1 RG1 | 0 | campo 16x49 | 0 | 01, 06 |
| `/it/tickets/track/DEMO-PUB-01` | ospite | 3/5 (429 a 1024, 1440) | 2 (pagina 429), manca | eur | CC1 HO1 LU1 RG1 | 1 (badge) | 1 (campo 16x49) | 0 | 01, 06, 18 |
| `/it/tickets/create` (l'ospite vede il form di login) | ospite | 5/5 | 24, - | eur | HO1 RG1 | 0 | 1 (casella 20x20) | 1 | come login: 11, 20, 26, 30 |
| `/it/blog` | ospite | 5/5 | 45, ok | px | HO1 LU1 RG1 | 0 | 2 | 1 | 25 |
| `/it/blog/...` (2 articoli) | ospite | 5/5 | 35, tgt | eur | HO1 LU1 RG1 | 0 | 0 | 1 | 06 |
| `/it/events` | ospite | 5/5 | 44, ok | px | HO1 LU1 RG1 | 0 | 4 | 1 | 23 |
| `/it/events/...` (2 eventi) | ospite | 5/5 | 35, tgt | eur | HO1 LU1 RG1 | 0 | 0 | 1 | 06 |
| `/it/domande-frequenti` | ospite | 5/5 | 45, ok | KO (px) | HO1 LU1 RG1 | 0 | 0 | 2 | 04, 21, 23 |
| `/it/lista-categorie` | ospite | 5/5 | 33, ok | px | HO1 RG1 | 0 | 0 | 1 | |
| `/it/mappa-sito` | ospite | 5/5 | 42, ok | px | HO1 RG1 | 0 | 0 | 1 | |
| `/it/services` | ospite | 5/5 | 35, ok | px | HO1 RG1 | 0 | 0 | 1 | |
| `/it/privacy` | ospite | 5/5 | 32, ok | px | HO1 LU1 RG1 | 0 | 0 | 2 | 06, 32 |
| `/it/note-legali` | ospite | 5/5 | 32, ok | eur | HO1 LU1 RG1 | 0 | 0 | 2 | 06, 32 |
| `/it/accessibilita` | ospite | 5/5 | 34, ok | px | HO1 LU1 RG1 | 0 | 0 | 2 | 06, 32 |
| `/it/media-policy` | ospite | 5/5 | 32, ok | eur | HO1 LU1 RG1 | 0 | 0 | 2 | 06 |
| `/it/cookie-policy` | ospite | 5/5 | 34, ok | eur | HO1 LU1 | 0 | 0 | 2 | 06 |
| `/it/terms` | ospite | 5/5 | 32, ok | px | HO1 LU1 RG1 | 0 | 0 | 2 | 06, 32 |
| `/it/auth/login` | ospite | 5/5 | 24, - (autofocus sul campo email) | px | HO1 RG1 | 0 | 1 | 1 | 11, 20, 26, 30 |
| `/it/auth/register` | ospite | 5/5 | 48, ok | px | HO1 RG1 | 0 | 3 | 1 | 10, 11, 25, 26 |
| `/it/auth/password/reset` | ospite | 5/5 | 1, assente | eur | M1 H1 RG3 | 0 | 1 (link 83x21) | 0 | 02, 30 |
| `/it/non-esiste-404` | ospite | 5/5 (pagina 404) | 34, ok | eur | HO1 TM1 DM1 LU1 RG1 | 0 | 0 | 2 | 06 |
| `/it/tickets` | cittadina | 5/5 | 72, ok | eur | HO1 LU1 RG1 | 0 | 1 | 2 | come ospite |
| `/it/tickets/13` | cittadina | 5/5 (pagina 404) | 34, ok | px | HO1 TM1 DM1 LU1 RG1 | 0 | 0 | 2 | 03 |
| `/it/tickets/1` | cittadina | 5/5 | 40, tgt | eur | HO1 LU1 RG1 | 0 | 0 | 1 | 06 |
| `/it/tickets/track/TCK-...` | cittadina | 5/5 | 36, manca | eur | CC1 HO1 LU1 RG1 | 1 (badge) | 1 (campo 16x49) | 0 | 01, 06, 18 |
| `/it/tickets/create` (wizard) | cittadina | 5/5 | 41, tgt | px | AR1 HO1 LU1 RG1 | 0 | 0 | 1 | 12, 13, 14, 20, 31 |
| `/it/area-personale/pratiche` | cittadina | 5/5 | 54, tgt | px | HO1 LU1 RG1 | 0 | 0 | 1 | 03, 24 |
| `/it/area-personale/seguite` | cittadina | 5/5 | 37, tgt | px | HO1 LU1 RG1 | 0 | 0 | 1 | 24 |
| `/it/area-personale/notifiche` | cittadina | 5/5 | 38, tgt | px | HO1 LU1 RG1 | 0 | 0 | 1 | 24 |
| `/it/area-personale/impostazioni` | cittadina | 5/5 | 45, tgt | px | HO1 LU1 RG1 | 0 | 0 | 1 | 24 |

Criteri trasversali non per pagina (esito sul sito intero):

| Criterio | Esito | Prova |
|---|---|---|
| Un solo h1 | 38 su 39 (manca solo `/it/auth/password/reset`) | `D/out/semantics.json` |
| Lingua della pagina | corretta su /it /en /de /es; `/it/auth/password/reset` ha `lang=it` ma testo inglese | `D/out/semantics.json` |
| Immagini senza alt, link o pulsanti senza nome, campi senza etichetta, tabindex positivo | 0 su tutte le pagine | `D/out/semantics.json` |
| Movimento ridotto | ok: transizioni a 0s con `reduce`, nessuna animazione infinita, circa 52-55 regole | `D/out/motion.json` |
| Tema scuro | parziale e incoerente (FO-5-33) | `D/shots/th-home-dark.png` |
| Colori forzati | 41 regole ma con buchi (FO-5-20) | `D/shots/th-*-forced.png` |
| Alto contrasto richiesto (`prefers-contrast`) | 0 regole | `D/out/themes.json` |
| Stampa | header, banner cookie e controlli mappa stampati (FO-5-32) | `D/out/print.json`, `D/shots/print794-*.png` |
| Zoom 400% (320x256) | nessuno scroll orizzontale; il banner cookie copre il 100% dell'altezza (FO-5-05) | `D/out/reflow.json` |
| Annunci in moduli e wizard | parziali (FO-5-10, 11, 12) | `D/out/forms.log`, `D/out/wizard-*.log` |

## 3. Finding

Le sigle di taglia: S = poche righe di CSS o markup, M = un componente o una pagina, L = lavoro su piu moduli.
Link ai gap precedenti usati solo per orientamento (B-02, B-05, B-09, B-13, B-16, B-17 dal documento gap-B-temi; DEC-xx dal registro DCP-200). Per ogni difetto indico solo cio' che ho riprodotto io.

### 3.1 BLOCCA go-live (3)

| ID | Pagina / URL | Persona | Difetto | WCAG | Evidenza | Gravita | Come si fa (alla maniera Design Comuni / Filament) | Taglia | Link |
|---|---|---|---|---|---|---|---|---|---|
| FO-5-01 | `/it/tickets/track`, `/it/tickets/track/{codice}` (anche per la cittadina) | ospite e cittadina, telefono | Sotto 576 px il campo del codice e' largo 16 px su 49 di altezza (genitore 343 px). A 768 e' 624 px. L'azione principale della pagina Traccia non e' usabile su telefono. Il pulsante sotto ("Cerca" a 375) e' largo, il campo si riduce a un trattino. | 1.4.10 (reflow, perdita di funzione), 2.5.8 (bersaglio minimo) | MISURATO a 375: input 16x49, `D/tr3.mjs` (6 misure, HTTP 200) e `D/out/sweep.json`. Foglio `D/sheets/s-track-375-0.png`. Causa DEDOTTA: il contenitore ha classe `input-group` (markup `Themes/Sixteen/resources/views/components/pages/ticket-track.blade.php` riga 69) e Bootstrap impone `.input-group > .form-control { width: 1% }`; la regola mobile in `css/app/21-ticket-track.css` (riga 288, `flex-direction: column`) non rimette `width: 100%`. | BLOCCA | Pattern Design Comuni "form di ricerca": campo a larghezza piena e pulsante sotto. Nel blocco `@media (max-width: 575.98px)` di `Themes/Sixteen/resources/css/app/21-ticket-track.css` aggiungere `.dc-ticket-track__input.form-control { width: 100%; }` oppure togliere `input-group` dal markup. Poi ricostruire il tema (il CSS compilato non si tocca a mano). Test: a 320 e 375 larghezza = larghezza del genitore. | S | gap-B-temi (nessuno specifico) |
| FO-5-02 | `/it/auth/password/reset` (collegata da "Password dimenticata?" in login e registrazione) | ospite | Pagina stock di Laravel dentro un guscio vuoto: testo inglese ("Reset password", "Or return to login", "Email address", "Send password reset link") con `<html lang="it">`; nessun `main`, nessun h1, nessun skip link, nessun header e footer del sito, titolo solo "FixCity", logo diverso, classi Tailwind `dark:`, `autofocus` sul campo, campo email senza `autocomplete`. Dopo l'invio mostra la chiave grezza `passwords.sent` (non e' in una regione `status`, il focus va su body). | 3.1.1, 3.1.2, 1.3.1, 2.4.1, 2.4.2, 4.1.3, 1.3.5 | MISURATO: `D/misc.mjs` (testo, invio con la mail della cittadina, esito "passwords.sent", focus BODY), axe a 1280 e 375: landmark-one-main, page-has-heading-one (1 ciascuno) e region x3 (`D/out/semantics.json`), foglio `D/sheets/s-reset-375-0.png`. | BLOCCA | Riusare la pagina del login: stesso layout Sixteen e stesso form Filament (widget Livewire con schema, `Password::broker()`), messaggi da `lang/*/passwords.php` del modulo User in it, en, de, es, conferma in una regione `role="status"`. Il codice vive in `Modules/User/resources/views/pages/auth/password/reset.blade.php` (Folio con Volt, rotta `password.request`); esiste gia' `Themes/Sixteen/resources/views/livewire/auth/passwords/email.blade.php`, da collegare invece di scrivere altro. Non aggiungere file sotto `Themes/*/resources/views/pages`. | M | B-17 (traduzioni) |
| FO-5-03 | `/it/tickets/13` (link "Vedi il dettaglio" della propria segnalazione in "Le mie segnalazioni") | cittadina | Il link porta a un 404, mentre "Traccia la segnalazione" per la stessa pratica (`/it/tickets/track/TCK-DGFEIXRKJJBU58MT`) risponde 200. Le altre 12 voci dell'elenco aprono dettagli 200. La pagina 404 ha anche `main` annidati (FO-5-06). | non WCAG: percorso rotto nel flusso principale | MISURATO: `D/own.mjs` (link letti dalla pagina e GET con la sessione della cittadina: 404 per il 13, 200 per gli altri), sweep `a-ticket-13` HTTP 404 a tutte le larghezze. Osservato fuori dal mio perimetro: lo segnalo, lo correggera' l'auditor dell'area personale. | BLOCCA (da confermare con fo-2) | Il dettaglio pubblico mostra solo pratiche pubbliche. Per le proprie pratiche private il link deve puntare alla traccia con codice, oppure la rotta del dettaglio deve autorizzare il proprietario (policy sul ticket, non una pagina nel tema). | S | DCP-148 |

### 3.2 QUALITA' (18)

| ID | Pagina / URL | Persona | Difetto | WCAG | Evidenza | Gravita | Come si fa | Taglia | Link |
|---|---|---|---|---|---|---|---|---|---|
| FO-5-04 | `/it/domande-frequenti` | ospite | I 5 pulsanti dell'accordion non mostrano nessun focus: `outline` bianco 3px a offset 3px e `box-shadow` assente; differenza di pixel zero tra il ritaglio con e senza focus. Su tutte le altre 20 pagine pixel-verificate il focus e' sempre visibile. | 2.4.7, 1.4.11 | MISURATO: `D/focuspixel.mjs` + `D/pixdiff.py`, `D/shots/faq-focus.png` e `D/shots/faq-nofocus.png` (ripetuto con controllo dello stato HTTP dopo un 500 transitorio), `D/out/focuspixel.log`. | QUALITA' (alta, AA su pagina principale) | Pattern Bootstrap Italia `accordion`: `h2.accordion-header > button.accordion-button` con `:focus-visible` a due toni come il resto del sito. Aggiungere la regola del focus globale (outline bianco + box-shadow nero) a `.accordion-button:focus-visible` nel tema (la regola che oggi lo azzera non e' stata individuata: cercare tra i file con `accordion-button` in `Themes/Sixteen/resources/css/`, per esempio `app/20-dc-components.css` e `design-comuni/overrides.css`). | S | |
| FO-5-05 | tutte le pagine, banner cookie `section.gdpr-cookiebar` | ospite e cittadina | Il banner e' fisso in basso, alto 231 px a 375x800 (29%) e 255 px a 320x800 (32%); a 320x256 (zoom 400%) copre il 100% e non scorre (il bottone resta raggiungibile). E' il primo stop Tab di ogni pagina, prima dello skip link. Mentre e' visibile copre il campo con focus: sulla registrazione a 375 ho contato 13 stop su 40 con l'elemento in focus coperto del tutto (Cognome, Email, Password, le tre caselle e i loro link, "Crea account", un link del footer). Dopo "Ho capito" il focus va su body. Il banner stampa su ogni pagina. Il testo e' solo informativo (cookie tecnici). | 2.4.11 (WCAG 2.2), 1.4.10, 2.4.3, 2.1.1 | MISURATO: `D/cov.mjs` (registrazione 375, 40 Tab), `D/out/kbd-375.json` (9 pagine, "covered" tra 9 e 23), `D/out/sweep.json` ("fixed"), `D/out/reflow.json`, `D/shots/print794-*.png`. | QUALITA' (alta) | Il banner e' solo un avviso: renderlo in flusso a fondo pagina (non `position: fixed`) oppure compatto su una riga. Se resta fisso: `scroll-padding-bottom` uguale all'altezza del banner sul `html`, ritorno del focus al primo elemento dopo la chiusura, `@media print { .gdpr-cookiebar { display: none } }`. Il codice e' in `Modules/Gdpr/resources/views/cookie-consent/index.blade.php`. | S | B-19 (duplicati cookiebar) |
| FO-5-06 | Skip link e struttura `main`: tre URL di traccia, 11 pagine, 404 | ospite e cittadina | (a) Sulle tre pagine di traccia (`/it/tickets/track`, `?code=`, `/track/TCK-...`) `<main>` non ha id: lo skip link "Vai ai contenuti" punta a `#main-container` che non esiste (`skipTargetExists:false`, 6 misure). (b) `id="main-container"` e' duplicato su 11 pagine (tickets, FAQ, privacy, note legali, accessibilita, media policy, cookie policy, terms, 404 e altre). (c) Sulle pagine di dettaglio (ticket, blog, evento, area personale) il bersaglio e' un `div` senza `tabindex`: il focus resta su body, anche se il Tab successivo entra in main in Chrome. (d) La pagina 404 ha due `main` annidati (axe landmark-main-is-top-level e landmark-no-duplicate-main). | 2.4.1, 4.1.1 (id duplicati), 1.3.1 | MISURATO: `D/tr3.mjs`, `D/out/semantics.json` (conteggio `mainIdCount`), `D/out/kbd-1280.json` (campo `skipTest`), axe. | QUALITA' | Un solo `<main id="main-container" tabindex="-1">` emesso dal layout Sixteen (`Themes/Sixteen/resources/views/components/layouts/app.blade.php` riga 19 lo fa gia'); le pagine e i blocchi non devono riemettere l'id (causa non verificata: il layout emette l'id, quindi le pagine di traccia devono passare da un altro guscio e le altre lo riemettono dentro il contenuto). Pagina 404: il contenuto va in un `div`, non in un secondo `main`. | S | B-16 |
| FO-5-07 | Modale "Cerca nel sito" (header, ogni pagina) | tutte | All'apertura il focus resta sul pulsante di apertura e il Tab va alla pagina dietro (nessun focus spostato nella modale, nessun trap). Esc la chiude ma il focus non torna al pulsante. La modale ha `role=dialog` e `aria-modal` ma nessun nome (il titolo `<h2>Cerca</h2>` non e' collegato con `aria-labelledby`). Due pulsanti di chiusura con lo stesso nome. Il form non ha `action` e il pulsante "Cerca" e' `type=button` (DEDOTTO); premendo Invio la home si ricarica come `/it?autocomplete-two=buca` senza risultati (MISURATO). La ricerca quindi non cerca. | 2.4.3, 4.1.2, 2.4.7, 3.2.2 | MISURATO: `D/out/widgets.log`, `D/misc.mjs` (Invio su "buca"). | QUALITA' (alta; la ricerca che non risponde e' DEC-22) | Pattern Bootstrap Italia `modal-search` con `aria-labelledby` sul titolo, `data-bs-focus` o evento `shown.bs.modal` che fa `input.focus()`, ritorno del focus al trigger alla chiusura. Collegare il campo a `/it/tickets?search=` come previsto da DEC-22 o togliere il pulsante. Codice: `Themes/Sixteen/resources/views/components/sections/search-modal/design-comuni.blade.php`. | M | DEC-22, DCP-166 |
| FO-5-08 | Menu hamburger (sotto 992 px) | ospite e cittadina, telefono | Si apre con il focus sul pulsante di chiusura (bene) ma il Tab esce dal menu verso il contenuto dietro (nessun trap e nessun `inert`); chiudendo con il pulsante il focus va su body invece che sul pulsante hamburger. Esc chiude e riporta il focus (bene). | 2.4.3, 3.2.1 | MISURATO: `D/out/widgets.log` (375). | QUALITA' | Pattern Bootstrap Italia `navbar-collapsable` con `inert` o `aria-hidden` sul resto della pagina mentre e' aperto, e ritorno del focus al toggle in ogni chiusura. Codice: `Themes/Sixteen/resources/views/components/header-comune.blade.php` e `blocks/header/nav-wrapper.blade.php`. | S | |
| FO-5-09 | Selettore lingua e menu utente (header) | ospite, cittadina | Il selettore lingua ha `role=menu` con voci `<a>` semplici (nessun `menuitem`); il menu utente ha `role=menu` e `menuitem` ma ArrowDown e Home non spostano il focus e Invio lascia il focus sul toggle. Esc funziona e riporta il focus. Le voci lingua ("ITA", "ENG", "DEU", "SPA") non hanno `lang` ne' `hreflang`. | 4.1.2, 3.1.2 | MISURATO: `D/out/widgets.log`, `D/misc.mjs` (lingua: `lang=null hreflang=null`). | QUALITA' | Togliere `role="menu"` dal selettore lingua: e' una lista di link dentro un `ul` disclosure (pattern Bootstrap Italia `dropdown` senza ruoli menu), con `lang` e `hreflang` sui link. Per il menu utente stesso: o frecce vere o via i ruoli. Codice: `components/sections/header/partials/language-switcher.blade.php` e `user-dropdown.blade.php`. | S | |
| FO-5-10 | `/it/auth/register`, invio a vuoto | ospite | Gli errori sono `span.auth-register-error` senza `id`, senza `aria-invalid` e senza `aria-describedby` sul campo; compare solo un riepilogo `role=alert`. I testi usano il nome tecnico dell'attributo: "first name è richiesto.", "last name è richiesto.", "email è richiesto.". | 3.3.1, 3.3.3, 4.1.2, 3.1.1 | MISURATO: `D/out/forms.log`, foglio `D/sheets/s-register-375-0.png`. Colore errore rgb(217,54,79) su bianco = 4.57:1, 14 px (passa). | QUALITA' (alta) | Come il login (che e' corretto): schema Filament che emette `aria-invalid` e `aria-describedby` da solo. Etichette di campo vere nel `validationAttributes` (it/en/de/es) invece dei nomi tecnici. Codice: `Themes/Sixteen/resources/views/filament/widgets/auth/register.blade.php` e lang del modulo User. | M | B-17 |
| FO-5-11 | `/it/auth/login`, `/it/auth/register`, dopo invio fallito | ospite | Dopo un invio fallito il focus va su body; nessun riepilogo che sposti il focus; nell'avviso live c'e' solo il primo errore. Il login ha `autofocus` sul campo email: il primo Tab va a Password e l'header resta fuori dal percorso. | 3.3.1, 4.1.3, 2.4.3 | MISURATO: `D/out/forms.log`, `D/out/kbd-375.json` (login: prima stop `Password*`). | QUALITA' | Dopo la validazione fallita: focus sul primo campo con `aria-invalid` (evento Livewire `$wire.$dispatch` e `focus()`), avviso `role=alert` con il conteggio ("2 campi da correggere"). Togliere `autofocus` dal login. | S | |
| FO-5-12 | Wizard `/it/tickets/create`, passi 1, 2, 3 | cittadina | (a) Passo 2: "Avanti" con campi vuoti lascia il focus sul pulsante; i 5 errori sono collegati con `aria-describedby`, ma `role=alert#alert-message` resta vuoto, quindi nessun annuncio. (b) Al cambio passo il focus va su body (nessun annuncio del passo nuovo). (c) I testi di errore sono "Il campo ho letto e compreso l'informativa sulla privacy deve essere accettato" e "Il campo indirizzo del disservizio è obbligatorio" (nome attributo grezzo). (d) Gli errori sono 12 px, colore 4.57:1 (limite). (e) Il passo 1 sposta correttamente il focus sulla casella non spuntata, con errore in `aria-describedby` (bene). | 3.3.1, 4.1.3, 2.4.3 | MISURATO: `D/out/wizard-1280-a.log`, `D/out/wizard-375-b.log`, `D/werr.mjs` (12px 4.57:1), foglio `D/sheets/wz-2-errori-375-0.png`. Come risponda davvero uno screen reader e' DEDOTTO. | QUALITA' (alta; promuovere a BLOCCA se NVDA conferma il silenzio) | Filament Wizard: dopo `afterValidation` fallita emettere evento che porta il focus sul primo `[aria-invalid=true]` e scrive nel `#alert-message` "5 campi da correggere"; dopo `next` spostare il focus sul titolo del passo (`h2` con `tabindex="-1"`). Etichette vere in `validationAttributes`. Codice: `Themes/Sixteen/resources/views/components/blocks/form-wizard/default.blade.php` e `components/wizard.blade.php`. | M | B-24 |
| FO-5-13 | Wizard, struttura | cittadina | Il `form` del passo ha `role=group` senza nome e axe segnala `aria-allowed-role` (1); l'indicatore dei passi e' un elenco semplice senza `ol` ne' `aria-current="step"` (DEDOTTO dal `ariaSnapshot`); sezioni `aria-live=polite` avvolgono tutto il contenuto del passo e il blocco coordinate, quindi ogni cambio verrebbe riletto intero. | 1.3.1, 4.1.2, 4.1.3 | MISURATO (axe e `D/out/kbd-1280.json`: `noName` sul form); stepper DEDOTTO. | QUALITA' | Togliere il ruolo dal form; stepper = `<ol>` con `aria-current="step"` sul passo attivo (pattern Design Comuni `steppers`); `aria-live` solo su una regione stretta di stato. | S | |
| FO-5-14 | Wizard passo 1 e 2, card "Contatta il comune" | cittadina | Link finti: "Usa la tua posizione" ha `href="#"` (duplica il pulsante mappa "Usa la mia posizione"); nella card "Richiedi assistenza", "Prenota appuntamento" e "Chiama il numero verde 05 0505" hanno `href="#"` (il numero non e' un `tel:`). | 2.4.4, 4.1.2, 1.3.1 | MISURATO: `D/out/semantics.json` (`hashLinksNoTarget`), `D/out/wizard-*.log`. | QUALITA' | Link veri (`mailto:`, `tel:05505` o rotta di prenotazione) oppure `<button type="button">` con la loro azione; i dati dell'ente vengono dal CMS, non dal template. | S | B-09 |
| FO-5-15 | Mappa `/it/tickets` e home | ospite, cittadina | Con Invio su un marker il popup si apre ma il focus resta sul marker e 8 Tab successivi non entrano mai nel popup: il link "Dettagli" e' irraggiungibile da tastiera. Esc non chiude il popup. Il link di chiusura e' `<a href="#close">` con `aria-label` inglese "Close popup". Il contenuto arriva dopo 1-3 s (scheletro senza `aria-busy`). Su `/it/tickets` 17 dei 24 stop Tab fino al footer stanno nella mappa e non c'e' "salta la mappa" (resta l'alternativa elenco, vedi FO-5-16). A 375 un marker 20x44 e' parzialmente coperto (axe target-size serious). | 2.1.1, 3.1.1, 2.4.1, 2.5.8 | MISURATO: `D/mapkbd.mjs`, `D/mapkbd2.mjs`, `D/popup.mjs`, `D/out/ticketsui.log`. | QUALITA' | Il popup Leaflet deve spostare il focus sul suo dialogo e restituirlo al marker alla chiusura; etichetta tradotta; aggiungere un link "Vai all'elenco" prima della mappa. Codice: componenti Lit `map-lit` e `Modules/Geo/resources/js/`. | M | |
| FO-5-16 | Elenco/Mappa su `/it/tickets` | ospite, cittadina | `role=tab` su `a.nav-link`; le frecce, End e Home non si muovono tra le schede e le due schede sono entrambe nell'ordine Tab (il pattern ARIA tabs vuole una sola stop e le frecce). | 4.1.2, 2.1.1 | MISURATO: `D/ticketsui.mjs`, `D/out/ticketsui.log`. | QUALITA' | Se si tengono link, togliere `role=tablist/tab` (e' navigazione tra viste); se si vogliono tab veri, implementarli con frecce (Bootstrap Italia `nav-tabs` li ha). Codice: `components/blocks/segnalazioni-elenco/tabs-navigation.blade.php`. | S | |
| FO-5-17 | Valutazione "Quanto sono chiare le informazioni?" (`/it/tickets` e altre pagine) | ospite, cittadina | I 5 `radio` sono 1x1 px con etichette a stella (34x32 px, bersaglio buono); nel DOM l'ordine e' invertito (freccia destra va da 5 a 4); triplo nome ("Valuta 5 stelle su 5" su input, immagine e testo); dopo la scelta nessun annuncio (le regioni live restano vuote) e non si salva nulla (B-05). Stelle rgb(125,151,176) su bianco = 3.03:1 (passa per soli 0.03). | 4.1.2, 4.1.3, 1.4.11 | MISURATO: `D/ticketsui.mjs`, `D/rate.mjs`. Salvataggio: da B-05, non riprodotto da me. | QUALITA' | Pattern Design Comuni `rating` ma con ordine DOM 1..5 e una sola etichetta; messaggio di ringraziamento in `role=status`; colore stelle piu scuro. Codice: `components/blocks/feedback/rating.blade.php`. | S | B-05 |
| FO-5-18 | `/it/tickets/track/DEMO-PUB-01`, `/it/tickets/track/TCK-...` | ospite, cittadina | Il badge di stato `span.badge.bg-primary.dc-ticket-track__status` ha testo #17324d su sfondo #007a52: 2.44:1 su "Aperto" e "In attesa" (axe color-contrast serious). | 1.4.3 | MISURATO: `D/out/contrast-1280.json` e axe (`D/out/semantics.json`). | QUALITA' | Badge con testo bianco (5.38:1 sul verde del tema) oppure sfondo chiaro con testo scuro. CSS: `Themes/Sixteen/resources/css/app/21-ticket-track.css`. Controllare gli altri stati (Risolto, In lavorazione), non misurati. | S | |
| FO-5-19 | Home a 375 con `html{font-size:200%}` e testo ingrandito dal browser | ospite | Il titolo e il testo dell'hero, il pulsante "Invia una segnalazione", la card "Impegno civico", le card "Come funziona" e la valutazione escono a destra e vengono tagliati (il `scrollWidth` non lo segnala, lo screenshot e' largo 581 px). Il marchio in header si riduce a "F.". A 320x256 e 640x400 senza ingrandimento font non ci sono perdite. | 1.4.4, 1.4.10 | MISURATO: `D/shots/font200-home-375.png`, foglio `D/sheets/font200-home-375-0.png`, `D/out/reflow.json` (`li.fc-how-card`, `fieldset.rating` oltre il bordo). | QUALITA' | Griglie con `minmax(0,1fr)`, `min-width: 0` sui figli flex, `overflow-wrap: anywhere` e dimensioni dei titoli in `clamp()`; provare con `font-size: 200%` a 320 e 375. CSS: `Themes/Sixteen/resources/css/overrides/homepage-parity.css` (da verificare). | M | |
| FO-5-20 | Colori forzati (alto contrasto di Windows), emulato `forced-colors: active` | utente con alto contrasto | Il logo bianco su trasparente sparisce (resta uno spazio vuoto a sinistra di "FixCity"); i pulsanti primari "Accedi" e "Avanti" diventano testo semplice senza bordo; nello stepper del wizard i tre passi sono indistinguibili (nessun segno del passo attivo). Il resto (hero, carte, link) resta leggibile. | 1.4.11, 1.4.1 | MISURATO: `D/shots/th-home-forced.png`, `th-login-forced.png`, `th-a-wizard1-forced.png` (emulazione Chromium, non Windows reale). | QUALITA' | `@media (forced-colors: active)`: `border: 1px solid ButtonText` sui `.btn`, logo con `forced-color-adjust: none` o sfondo `Canvas`, passo attivo con `border-bottom` o `font-weight`. | S | |
| FO-5-21 | FAQ: filtro e struttura | ospite | Il filtro ("Cerca nelle domande") ha l'etichetta (bene) ma digitando "foto" restano 4 voci visibili e per "zzzzqqq" compare "Nessuna domanda corrisponde alla ricerca." senza regione live (nessun annuncio). I pulsanti dell'accordion non stanno dentro un titolo (`h2`/`h3`), quindi non compaiono nell'elenco dei titoli. L'accordion si apre con Invio e Spazio e `aria-expanded` e `aria-controls` sono corretti (bene). | 4.1.3, 1.3.1 | MISURATO: `D/misc2.mjs`. | QUALITA' | Pattern Bootstrap Italia: `h2.accordion-header > button`; messaggio risultati in `role="status"` ("4 domande"). | S | |

### 3.3 RIFINITURA (12)

| ID | Pagina / URL | Persona | Difetto | WCAG | Evidenza | Gravita | Come si fa | Taglia | Link |
|---|---|---|---|---|---|---|---|---|---|
| FO-5-22 | Header, tutte le pagine, stato hover | tutte | Il pulsante "Accedi all'area personale" in hover ha testo bianco su rgb(0,150,106): 3.77:1 a 14 px; le voci di menu principale ("Segnalazioni", "Segnala", "Traccia", ...) in hover bianco su rgb(36,141,106): 4.13:1 a 18 px peso 600 (non e' testo grande). A riposo sono 5.38:1. La lente di ricerca e' verde su bianco (5.38:1): il valore 1:1 dello scanner e' un falso positivo (icona). | 1.4.3 | MISURATO: `D/hov.mjs` (transizioni disattivate, 3 elementi), `D/out/contrast-1280.json` su 39 pagine. | RIFINITURA | Hover piu scuro (per esempio `#006b47`) invece di piu chiaro. CSS dell'header del tema Sixteen. | S | |
| FO-5-23 | Gutter e margini visibili a 375 e 768 | ospite | Home: la sezione "Come funziona" ha un rientro di circa 56 px per lato (contenuto 263 px) contro 12 px altrove. FAQ: le domande dell'accordion toccano il bordo sinistro (x=0) mentre i titoli hanno 12 px. Eventi: card "Tutti gli eventi" a soli 5 px dal bordo, foto a tutta larghezza prima. Elenco segnalazioni: schede dentro una cornice grigia con altro padding (pagina lunga 6704 px). Piede: la riga "Media policy / Mappa del sito / Cookie policy" va a capo e "Gestisci cookie" resta da solo, centrato, sulla terza riga. | 1.4.10 (non violato), coerenza | MISURATO sugli screenshot a 375: `D/sheets/s-home-375-1.png`, `s-faq-375-0.png`, `s-events-375-0.png`, `s-tickets-list-375-*.png`, `s-e404-375-0.png`. | RIFINITURA | Un solo contenitore con gutter 12 px (`container` di Bootstrap Italia) per tutte le sezioni; piede con `flex-wrap` e `gap` uniformi. | M | |
| FO-5-24 | Area personale (pratiche, seguite, notifiche, impostazioni) a 375 | cittadina | Le quattro schede vanno su 3 righe disuguali e la scheda attiva cambia riga tra pagine; sulla pagina pratiche ci sono 13 testi di stato a 11 px (unico punto del sito sotto i 12 px). | 1.4.4, 1.4.12 | MISURATO: `D/out/fontsize.json`, fogli `D/sheets/s-a-seguite-375-0.png`, `s-a-notifiche-375-0.png`, `s-a-pratiche-375-0.png`. | RIFINITURA | Barra di schede scorrevole orizzontale (`nav-tabs` con `overflow-x:auto`) o a icone; stato a 14 px. | S | |
| FO-5-25 | Caselle di spunta e collegamenti piccoli | ospite | Caselle 20x20 px in login ("Ricordami") e registrazione (3); link attribuzione "Leaflet" 51x17 px (tutte le mappe); su blog ed eventi i link di categoria "NOTIZIE" (55x21) e "EVENTI" (50x21) e "Leggi tutto" (113x21) sono alti 21 px; sulla pagina password dimenticata il link "return to login" e' 83x21. | 2.5.8 (WCAG 2.2 AA) | MISURATO: `D/out/sweep.json` (`small24list`), `D/out/kbd-375.json`. | RIFINITURA | Casella 24 px con area cliccabile dell'etichetta estesa; link attribuzione con padding. WCAG 2.2 non e' ancora nel perimetro AgID. | S | |
| FO-5-26 | Registrazione a 375 e 768; login a 375 | ospite | Il pulsante "Accedi con Google" nella registrazione e' una grande scatola con il solo logo "G" (circa 125x180 px) e un piccolo link sottolineato sotto; nel login e' un normale pulsante largo. Nel login il campo email e' una riga con etichetta mobile mentre la password e' una scatola con bordo (stile diverso nello stesso form). | 3.2.4, coerenza | MISURATO: `D/sheets/s-register-768-0.png`, `s-login-375-0.png`. Bordi dei campi: 5.11 e 5.2:1 (passano). | RIFINITURA | Usare lo stesso componente del login (`Themes/Sixteen/resources/views/components/utilities/google-button.blade.php`) in entrambe le pagine; un solo stile di campo. | S | |
| FO-5-27 | Header, titolo "FixCity" | tutte | Il titolo del marchio ha il taglio dell'asta discendente (scrollHeight 20 contro clientHeight 17 a 320, 22 contro 19 a 375); lo stesso accade con la spaziatura del testo imposta (1.4.12) sui link di salto. | 1.4.12 | MISURATO: `D/out/reflow.json`. | RIFINITURA | `line-height` 1.3 e niente altezza fissa sul titolo. | S | |
| FO-5-28 | Punti di riferimento e titoli, tutte le pagine | tutte | `div.it-header-slim-right-zone role="navigation"` senza nome (axe landmark-unique su 28 pagine); `nav aria-label="Secondaria"` vuoto con `ul` vuoto; l'etichetta "Seguici su" e' visibile da lg senza nessun link social; i titoli del footer sono `h4` dopo `h2` (heading-order su 38 pagine); gli skip link stanno fuori dai riferimenti (region); nome del sito nell'intestazione "Demo" (B-09). | 1.3.1, 2.4.6, 2.4.1 | MISURATO: `D/out/semantics.json`, `D/misc.mjs` (nav). | RIFINITURA | Dare un nome alla zona di navigazione o toglierne il ruolo; nascondere "Seguici su" se non ci sono link; titoli del footer `h2` visivamente piccoli. | S | B-09, B-13 |
| FO-5-29 | Nomi di link duplicati | tutte | Stessi nomi accessibili verso destinazioni diverse: "elenco segnalazioni", "notizie", "eventi", "accedi all'area personale" (2-3 destinazioni). | 2.4.4 | MISURATO: `D/out/semantics.json` (`dupLinkNames`). | RIFINITURA | Etichette distinte o `aria-label` con il contesto. | S | |
| FO-5-30 | `autofocus` su login e password dimenticata | ospite | Il campo email prende il focus al caricamento: lo screen reader parte da li, salta titolo e contesto della pagina. | 2.4.3 | MISURATO: `D/out/semantics.json` (`autofocus`), `D/out/kbd-375.json` (login). | RIFINITURA | Togliere `autofocus`. | S | |
| FO-5-31 | Asterisco "obbligatorio" nelle etichette | cittadina | L'asterisco fa parte del testo di etichette e titoli ("Disservizio*", "Titolo*", "Cerca un luogo*"), quindi viene letto "asterisco" o "stella". | 3.3.2, 1.3.1 | MISURATO: `D/out/wizard-375-b.log`, `D/sheets/wz-2-errori-375-0.png`. | RIFINITURA | `aria-hidden` sull'asterisco e `required` sul campo (c'e'); la frase "I campi con asterisco sono obbligatori" c'e' gia'. | S | |
| FO-5-32 | Stampa di dettaglio ticket e pagine legali | ospite | In stampa restano visibili l'header e lo skip link, il banner cookie (fisso sopra il contenuto a pagina 1), i controlli della mappa e i pulsanti; footer e menu spariti (bene); nessuna espansione degli URL dei link (0 regole `::after` con contenuto). PDF A4: ticket 2 pagine, privacy 3, accessibilita 3, terms 1. 15 regole `@media print` in tutto il tema. | non WCAG | MISURATO: `D/out/print.json`, `D/out/print-*.pdf`, `D/shots/print794-ticket-1-0.png`. Non ho potuto aprire un vero dialogo di stampa. | RIFINITURA | Foglio di stampa: nascondere header, skip link, banner e controlli mappa; `a[href^="http"]::after { content: " (" attr(href) ")" }` solo nel contenuto principale; titolo e codice della pratica in testa. | S | |
| FO-5-33 | Tema scuro e alto contrasto | utente con tema scuro | Esistono 7-8 regole `prefers-color-scheme: dark`: solo l'hero e poche sezioni diventano scure (header resta verde, resto chiaro). Il testo resta leggibile ma il risultato e' incoerente; 0 regole `prefers-contrast`. Non e' un obbligo WCAG. | 1.4.3 (ok), coerenza | MISURATO: `D/shots/th-home-dark.png`, `D/out/themes.json`. | RIFINITURA | O completare il tema scuro con i token di colore, o togliere le poche regole per non lasciarlo a meta. | M | |

## 4. Difetti visibili sugli screenshot a 375 e 768

Ho guardato le pagine reali (fogli in `D/sheets/`). Quello che si vede:

- Traccia (375): sotto l'etichetta "Codice segnalazione" si vede solo un trattino corto al posto del campo, poi il pulsante verde "Cerca" a tutta larghezza. Il cittadino non capisce dove digitare il codice (FO-5-01).
- Home (375): l'hero e' leggibile e i pulsanti sono larghi; piu in basso "Come funziona" e la sezione verde "Segui la tua segnalazione" hanno il testo che parte a circa 56 px dal bordo, contro 12 px del resto (FO-5-23). La valutazione e' una scheda bianca su fondo verde con stelle grigio-azzurre.
- Home con testo al 200%: titolo e card tagliati a destra, marchio ridotto a "F." (FO-5-19).
- Elenco segnalazioni (375): la vista "Elenco" mette ogni scheda dentro una cornice grigia con un secondo rientro, che spreca larghezza e allunga la pagina (6704 px); la vista "Mappa" e' a tutta larghezza e pulita. Su 768 la lista e la mappa sono leggibili e i filtri stanno in una riga.
- Dettaglio segnalazione (375): titolo grande, stato "Aperto" come pillola con bordo verde, mappa a larghezza piena, tre pulsanti a larghezza piena in fondo; leggibile. Il testo secondario "I tempi dipendono dall'ufficio..." e' piccolo (circa 12 px).
- Wizard (375): il passo 1 e' pulito (titolo, indicatore "1/3", casella, pulsante "Avanti"); il passo 2 con errori mostra errori rosa piccoli (12 px) sotto ogni campo, la mappa con comandi tondi e il pulsante "Avanti" a destra di "Indietro" (FO-5-12). Sotto compare la card "Contatta il comune" con link senza destinazione (FO-5-14).
- Login (375 e 768): carta centrale, buona; stile del campo email diverso dalla password (FO-5-26).
- Registrazione (768): moduli larghi e leggibili; il pulsante Google e' la scatola con la "G" (FO-5-26).
- Area personale (375): quattro schede su tre righe, titoli grandi, elenco con stato in testo piccolo (FO-5-24).
- Privacy e legali (375): leggibili; riquadro con filo arancione "TESTO NON ANCORA COMPLETO" in cima, elenco di sezioni "assenti o vuote" ben formattato.
- Eventi (375): foto a tutta larghezza per tutte le card (stessa foto di placeholder), data in un riquadro bianco; buono, con il rientro da 5 px (FO-5-23).
- 404 (375): pulita, tre pulsanti a larghezza piena; il footer ha la riga irregolare (FO-5-23).

## 5. Cosa funziona bene (con la prova)

- Nessuno scroll orizzontale: 195 misure, 0 casi (`D/out/sweep.json`); a 320x256 e 640x400 nessuno scroll (`D/out/reflow.json`).
- Un solo h1 su 38 pagine su 39; `lang` giusto su /it /en /de /es (`D/out/semantics.json`). Skip link tradotti in tedesco e spagnolo ("Zum Inhalt springen", "Ir al contenido").
- Zero immagini senza alt, zero link o pulsanti senza nome, zero campi senza etichetta, zero `tabindex` positivi (`D/out/semantics.json`).
- Focus a due toni (outline bianco 3 px + ombra nera) verificato a pixel su 20 pagine: nessun elemento senza focus visibile (`D/out/focuspixel-1280.json`), eccetto la FAQ (FO-5-04).
- Axe quasi pulito: sulle pagine normali solo heading-order, region e landmark-unique (moderate) (`D/out/semantics.json`).
- Movimento ridotto rispettato: transizioni a 0s con `reduce`, nessuna animazione infinita (`D/out/motion.json`).
- Errori del login legati con `aria-invalid` e `aria-describedby`, regione `alert`; errore di traccia con `role=alert` collegato; passo 1 del wizard sposta il focus sulla casella non spuntata (`D/out/forms.log`, `D/out/wizard-1280-a.log`).
- Modale di ricerca con `role=dialog` e `aria-modal`; pulsante hamburger con `aria-expanded` e `aria-label`; Esc chiude hamburger e selettore lingua e restituisce il focus (`D/out/widgets.log`).
- Pagina elenco: marker con nome (`title`), alternativa "Elenco" alla mappa, valutazione in `fieldset` con `legend` (`D/out/ticketsui.log`).
- Il passo 2 usa un `select` nativo; la mappa nel riepilogo e' una regione con etichetta.
- Contrasto del testo: 0 testi sotto soglia su 37 pagine su 39 a 1280 (le uniche eccezioni sono i due badge di FO-5-18) (`D/out/contrast-1280.json`). Bordi dei campi: 5.1 e 5.2:1.
- Bersagli: circa la meta dei bersagli sono 44 px o piu; salvo i casi di FO-5-25 tutti sono almeno 24 px.
- Anche a 320x256 (zoom 400%) il pulsante "Ho capito" del banner resta raggiungibile da tastiera (`D/out/reflow.json`).

## 6. Dichiarazione di accessibilita: confermo o contraddico

La pagina `/it/accessibilita` e' dell'altro auditor; riporto solo il confronto con le mie misure.
- La pagina non dichiara conformita: si presenta come "DICHIARAZIONE NON ANCORA COMPLETA" e ogni dato (stato di conformita, metodo e data della valutazione, data di pubblicazione, ultimo aggiornamento, indirizzo per le segnalazioni) e' "dato mancante". Le mie misure quindi non la contraddicono: non afferma nulla che io abbia potuto smentire.
- Confermo che per la messa in produzione e' un blocco (DEC-02 e DEC-04, gap B-02). Il modulo di feedback resta inattivo finche' l'ente non indica la casella.
- Quando l'ente la compilera' potra' indicare "parzialmente conforme": le mie misure darebbero come contenuti non accessibili almeno FO-5-04, FO-5-02, FO-5-06, FO-5-10, FO-5-12 e FO-5-15 (criteri 2.4.7, 3.1.1, 2.4.1, 3.3.1, 4.1.3, 2.1.1).

## 7. Cosa non ho potuto provare

- Uno screen reader vero (NVDA, JAWS, VoiceOver, TalkBack): gli annunci sono dedotti da regioni live, ruoli e nomi accessibili letti con CDP, non ascoltati.
- Un telefono con tocco reale, tastiera esterna su mobile, e uno zoom o una scalatura font del sistema operativo: ho emulato viewport e `font-size` (200%) con Chromium.
- Colori forzati di Windows e alto contrasto vero (emulazione Chromium `forced-colors: active`).
- Safari e Firefox (solo Chromium 1248).
- Stampa reale e carta (solo emulazione print e `page.pdf` A4).
- Invio finale del wizard (non ho creato segnalazioni), consegna posta (il mailer dell'istanza e' `array`) e il salvataggio della valutazione (B-05 letto, non riprodotto).
- I criteri WCAG 2.2 (2.4.11, 2.5.8) sono riportati ma a titolo informativo: AgID richiede ancora EN 301 549 con WCAG 2.1 AA.
- Le pagine dei moduli di back office (fuori perimetro) e il "tema scuro" come requisito.

## 8. Domande per l'utente

1. Banner cookie (FO-5-05): vuole che resti fisso in basso o posso proporre un avviso in flusso a fondo pagina, visto che i cookie sono solo tecnici?
2. Ricerca (FO-5-07): la decisione DEC-22 e' ancora aperta (collegarla alla ricerca ticket o togliere il pulsante). Quale delle due?
3. Password dimenticata (FO-5-02): riusare lo schema Filament del login con il layout Sixteen, e confermare che i testi vadano in it, en, de, es come il resto?
4. Livello di conformita richiesto: solo WCAG 2.1 AA (AgID) oppure anche 2.2 AA? Da questa risposta dipende se 2.4.11 e 2.5.8 sono QUALITA' o RIFINITURA.
5. Tema scuro (FO-5-33): completarlo o togliere le regole parziali?
6. Il freno DCP-149 sulle pagine di traccia ha risposto 429 sotto carico parallelo a mia sola sessione: che soglia vuole per utenti dietro lo stesso IP (biblioteche, scuole, reti comunali)?
7. "Le mie segnalazioni" elenca 13 voci, 12 delle quali con codice `DEMO-PUB-...`: sono dati di seed seguiti dalla cittadina o l'elenco mischia pratiche non sue? Lo passo all'auditor dell'area personale, ma vorrei sapere se e' voluto.
8. Chi puo' fare la prova con uno screen reader vero (NVDA con Chrome o VoiceOver su iPhone)? E' l'unica verifica che puo' promuovere FO-5-12 a BLOCCA.
