---
title: "Audit front office 6: lingue, SEO, prestazioni e sicurezza visibile"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "audit front office 6: lingue, seo, prestazioni e sicurezza visibile audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Audit FO trasversale: lingue, SEO, prestazioni e sicurezza visibile (fo-6)

Data della misura: 2026-10-26, tra le 11:50 e le 12:45 UTC. Istanza: `http://127.0.0.1:8091` (DB usa e getta, mai il DB dev). Ospite = senza cookie. Cittadino = account di prova `@example.test` (password non riportata). Dati: 12 ticket pubblici, 1 personale, 2 articoli, 2 eventi.

Legenda delle prove: MISURATO = visto con curl o con Chromium (playwright-core), con numero o testo. DEDOTTO = ricavato dal codice o dalla configurazione, non visto in esecuzione.

## 0. Sintesi

- 52 riscontri: 5 BLOCCA go-live, 32 QUALITA', 15 RIFINITURA (conteggio verificato nella sezione 3).
- Le traduzioni delle pagine pubbliche sono buone: nessuna chiave grezza e nessun segnaposto non risolto in 168 pagine scaricate, `html lang` corretto ovunque, canonical e hreflang coerenti. I problemi di lingua stanno in poche zone ben localizzabili: pagine legali incomplete, segnaposto dell'ente, blog e widget feedback con testo italiano fisso, contenuti redazionali solo in italiano.
- Il peso e' il difetto tecnico piu' grande: lo stesso CSS da 3,4 MB (270 KB compresso) e 1,4 MB di JS su ogni pagina, anche su `/privacy`. Su mobile lento, anche con compressione simulata, il primo contenuto arriva a 5,3-6,1 s e il contenuto principale a 5,3-10,8 s.
- Sicurezza visibile: intestazioni di base buone (nosniff, X-Frame-Options, Referrer-Policy, Permissions-Policy), nessun dato personale nelle API pubbliche, nessun id enumerabile. Da chiudere prima del go-live: `/pulse` aperto all'ospite, `APP_DEBUG` e Boost attivi, Host header riflesso nei link canonici.

I 5 piu' importanti:

1. FO-6-01 Le 4 pagine legali (privacy, note legali, termini, media policy) mostrano in tutte le lingue il riquadro "Testo non ancora completo" con le sezioni art. 13 GDPR "assenti o vuote".
2. FO-6-02 Segnaposto in pagina: ente "Demo" / "Name of the Region", numero verde "05 0505", CTA di assistenza con `href="#"`.
3. FO-6-33 `/pulse` risponde 200 con la dashboard completa a un ospite, nonostante il gate `viewPulse`.
4. FO-6-43 Dalla propria area personale il link alla propria pratica (`/it/tickets/13`) da' 404, anche al proprietario.
5. FO-6-26 CSS da 3,4 MB e JS da 1,4 MB su ogni pagina: LCP mobile 5-11 s anche con gzip simulato.

## 1. Metodo e limiti

- Scaricate e analizzate 168 pagine: le 124 URL della sitemap (31 percorsi x 4 lingue, tutte 200 dopo la correzione del 500 sul blog), 32 pagine da cittadino (area personale x4, tickets/create, tickets/track, tickets/confirmation, auth/login, tutti x4) e 12 pagine extra da ospite (auth/login, auth/register, tickets/confirmation x4). Altre richieste: 44 da cittadino e 44 da ospite sulle pagine riservate (302, 403, 404, 301 inclusi), 74 varianti di URL, 61 sonde su file e rotte, 228 link interni, 3 API, 18 misure di prestazione (8 pagine, 2 profili, mediana di 3 run) piu' una simulazione "produzione" con proxy gzip.
- Lingua: confronto riga per riga con la versione italiana (nodi di testo e attributi identici), euristica di parole italiane, ricerca di chiavi grezze (`::`, `txt.`, punti) e di segnaposto (`:nome`, `{x}`). Differenza statica dei file lang con uno script PHP di sola lettura.
- Prestazioni: Chromium con CDP. Desktop 1366 senza limiti. Mobile 390x844, 150 ms di latenza, 1,6 Mbps, CPU 4x. Il server e' `php -S` con 4 worker condivisi con altri agenti e debug acceso: TTFB e tempi assoluti sono indicativi, i pesi in byte e le proporzioni no.
- Solo richieste GET, nessun tentativo di sfruttamento. Nessuna scrittura nel repository. Nota: una GET puo' far scrivere il file lang all'auto-writer (C-05). Durante l'audit altre sessioni hanno modificato file nel repo (`Modules/Comment/lang/*`, `SaveTransAction.php`, `CommentWidget.php`, `info.php` rinominato in `info.php.old`); non sono attribuibili a me.
- I commenti (codice) non sono stati analizzati. Ho guardato il widget Comment solo come causa del 500 sul blog e per le sue stringhe a video.

## 2. Matrici misurate

### 2.1 Pagine x lingue (31 percorsi)

Cella it: stato HTTP e parole del testo. Celle en/de/es: stato, parole, e tre contatori: `s` = nodi di testo identici all'italiano, `i` = parole italiane riconosciute, `a` = attributi (title, alt, aria-label, placeholder) identici all'italiano. Molti `s` sono legittimi (FixCity, numeri, nomi di vie, contenuti dei cittadini). Il segnale vero e' `i`.

| Percorso | it | en | de | es |
|---|---|---|---|---|
| `/` | 200, 399 parole | 200, s6 i0 a0 | 200, s3 i2 a0 | 200, s3 i1 a0 |
| `/accessibilita` | 200, 517 parole | 200, s7 i0 a1 | 200, s3 i1 a1 | 200, s3 i0 a1 |
| `/blog` | 200, 454 parole | 200, s19 i6 a6 | 200, s15 i8 a6 | 200, s15 i8 a6 |
| `/blog/come-preparare-una-segnalazione-utile-w0A0n` | 200, 336 parole | 200, s23 i8 a2 | 200, s19 i9 a2 | 200, s19 i8 a2 |
| `/blog/cosa-succede-dopo-la-segnalazione` | 200, 303 parole | 200, s17 i5 a2 | 200, s15 i8 a2 | 200, s15 i7 a2 |
| `/cookie-policy` | 200, 395 parole | 200, s13 i0 a1 | 200, s7 i1 a1 | 200, s8 i0 a1 |
| `/domande-frequenti` | 200, 836 parole | 200, s7 i0 a1 | 200, s3 i3 a1 | 200, s3 i2 a1 |
| `/events` | 200, 458 parole | 200, s41 i13 a7 | 200, s37 i15 a7 | 200, s35 i13 a7 |
| `/events/incontro-pubblico-sulla-mobilita` | 200, 305 parole | 200, s17 i6 a2 | 200, s15 i9 a2 | 200, s15 i8 a2 |
| `/events/laboratorio-civico-segnalare-bene-in-cinque-minuti-WO192` | 200, 311 parole | 200, s17 i6 a2 | 200, s15 i9 a2 | 200, s15 i8 a2 |
| `/lista-categorie` | 200, 271 parole | 200, s7 i0 a0 | 200, s3 i1 a0 | 200, s3 i0 a0 |
| `/mappa-sito` | 200, 275 parole | 200, s9 i0 a0 | 200, s3 i1 a0 | 200, s3 i0 a0 |
| `/media-policy` | 200, 264 parole | 200, s9 i0 a1 | 200, s3 i1 a1 | 200, s3 i0 a1 |
| `/note-legali` | 200, 579 parole | 200, s7 i0 a1 | 200, s3 i1 a1 | 200, s3 i1 a1 |
| `/privacy` | 200, 669 parole | 200, s7 i0 a1 | 200, s3 i1 a1 | 200, s3 i0 a1 |
| `/services` | 200, 308 parole | 200, s7 i0 a0 | 200, s3 i1 a0 | 200, s3 i0 a0 |
| `/terms` | 200, 266 parole | 200, s7 i0 a1 | 200, s3 i1 a1 | 200, s3 i0 a1 |
| `/tickets` | 200, 1027 parole | 200, s43 i15 a0 | 200, s39 i18 a0 | 200, s41 i15 a0 |
| `/tickets/1` | 200, 341 parole | 200, s11 i1 a0 | 200, s8 i2 a0 | 200, s8 i1 a0 |
| `/tickets/10` | 200, 337 parole | 200, s11 i0 a0 | 200, s8 i1 a0 | 200, s8 i0 a0 |
| `/tickets/11` | 200, 336 parole | 200, s11 i3 a0 | 200, s8 i4 a0 | 200, s8 i3 a0 |
| `/tickets/12` | 200, 332 parole | 200, s11 i1 a0 | 200, s8 i2 a0 | 200, s8 i1 a0 |
| `/tickets/2` | 200, 347 parole | 200, s11 i2 a0 | 200, s8 i3 a0 | 200, s8 i2 a0 |
| `/tickets/3` | 200, 339 parole | 200, s11 i2 a0 | 200, s8 i3 a0 | 200, s8 i2 a0 |
| `/tickets/4` | 200, 339 parole | 200, s11 i4 a0 | 200, s8 i5 a0 | 200, s8 i4 a0 |
| `/tickets/5` | 200, 340 parole | 200, s11 i1 a0 | 200, s8 i2 a0 | 200, s8 i1 a0 |
| `/tickets/6` | 200, 339 parole | 200, s11 i1 a0 | 200, s8 i2 a0 | 200, s8 i1 a0 |
| `/tickets/7` | 200, 333 parole | 200, s11 i1 a0 | 200, s8 i2 a0 | 200, s8 i1 a0 |
| `/tickets/8` | 200, 335 parole | 200, s11 i3 a0 | 200, s8 i4 a0 | 200, s8 i3 a0 |
| `/tickets/9` | 200, 335 parole | 200, s11 i0 a0 | 200, s8 i1 a0 | 200, s8 i0 a0 |
| `/tickets/track` | 200, 277 parole | 200, s7 i0 a1 | 200, s3 i1 a1 | 200, s3 i0 a1 |

Totale: 124 su 124 in 200. Nessuna chiave grezza e nessun segnaposto non risolto in nessuna lingua. `html lang` = lingua dell'URL su 124 su 124 (e su tutte le pagine riservate che rispondono 200). Canonical uguale all'URL su 124 su 124. hreflang con 5 voci (it, en, de, es, x-default che punta a `/it`) su 124 su 124. `og:url` uguale all'URL su 124 su 124. Il selettore di lingua mantiene la pagina su 164 pagine (0 difetti).

### 2.2 Residui italiani nelle pagine en/de/es, per stringa

| Dove | Stringa italiana | Pagine (per lingua en/de/es) | Origine |
|---|---|---|---|
| `/blog` | "NOTIZIE", "N notizie trovate", "Cerca per parola chiave", "Invio", "Pagina precedente/successiva" | `/blog` | `Themes/Sixteen/resources/views/components/blocks/blog/index.blade.php` (righe 48, 61-64, 205, 213, 276) |
| `/events` | "Quanto sono chiare...", "Valuta N stelle su 5", "Vuoi aggiungere altri dettagli?" (circa 25 stringhe) | `/events` | `.../blocks/feedback/faq-rating.blade.php` e `feedback-section.blade.php` |
| 2 articoli + 2 eventi | `<h2>Commenti</h2>`, "Torna alle novità", "Torna agli eventi", credito immagine "Asset Design Comuni / tema Sixteen: uso demo FixCity" | 12 pagine (4 x 3 lingue) | `.../blocks/blog/detail.blade.php` riga 120, `.../events/detail.blade.php` riga 116 e 150 |
| Pagine con breadcrumb | `aria-label="breadcrumb"` non tradotto (anche in it e' in inglese) | 52 pagine (13 percorsi x 4 lingue) | componente breadcrumb del layout |
| `/cookie-policy` es, `/tickets` es | "Tipo", "periodo" | 2 | file lang es |
| Contenuti redazionali | titolo, sommario, corpo, alt di 2 articoli, 2 eventi, 12 ticket | 16 URL x 3 lingue = 48 URL | dato, non traduzione (FO-6-06) |

### 2.3 Pagine x SEO

Lunghezze in caratteri nell'ordine it/en/de/es. Canonical e hreflang come numero di lingue su 4 corrette. H1 = numero di H1 (sempre 1). OG immagine e meta robots sono assenti ovunque (0 su 124), quindi non hanno colonna.

| Percorso | Titolo it/en/de/es | Descrizione it/en/de/es | Canonical | hreflang | JSON-LD |
|---|---|---|---|---|---|
| `/` | 49/47/42/49 | 106/101/112/95 | 4/4 | 4/4 | WebSite |
| `/accessibilita` | 30/23/30/28 | 150/136/132/151 | 4/4 | 4/4 | nessuno |
| `/blog` | 6/4/11/9 | 88/68/73/75 | 4/4 | 4/4 | nessuno |
| `/blog/come-preparare-una-segnalazione-utile-w0A0n` | 37/37/37/37 | 63/63/63/63 | 4/4 | 4/4 | nessuno |
| `/blog/cosa-succede-dopo-la-segnalazione` | 33/33/33/33 | 63/63/63/63 | 4/4 | 4/4 | nessuno |
| `/cookie-policy` | 13/13/17/19 | 97/97/106/99 | 4/4 | 4/4 | nessuno |
| `/domande-frequenti` | 17/26/14/20 | 87/86/93/96 | 4/4 | 4/4 | nessuno |
| `/events` | 16/6/15/7 | 79/79/64/72 | 4/4 | 4/4 | nessuno |
| `/events/incontro-pubblico-sulla-mobilita` | 32/32/32/32 | 55/55/55/55 | 4/4 | 4/4 | nessuno |
| `/events/laboratorio-civico-segnalare-bene-in-cinque-minuti-WO192` | 51/51/51/51 | 55/55/55/55 | 4/4 | 4/4 | nessuno |
| `/lista-categorie` | 21/18/16/22 | 59/47/48/62 | 4/4 | 4/4 | nessuno |
| `/mappa-sito` | 14/7/7/14 | 50/52/55/56 | 4/4 | 4/4 | nessuno |
| `/media-policy` | 12/12/12/18 | 67/72/75/82 | 4/4 | 4/4 | nessuno |
| `/note-legali` | 11/11/19/13 | 60/67/69/68 | 4/4 | 4/4 | nessuno |
| `/privacy` | 19/14/20/19 | 112/109/107/119 | 4/4 | 4/4 | nessuno |
| `/services` | 7/8/7/9 | 77/66/69/84 | 4/4 | 4/4 | nessuno |
| `/terms` | 20/20/39/22 | 65/74/88/69 | 4/4 | 4/4 | nessuno |
| `/tickets` | 19/15/19/20 | 124/110/123/117 | 4/4 | 4/4 | nessuno |
| `/tickets/1` | 30/30/30/30 | 80/80/80/80 | 4/4 | 4/4 | nessuno |
| `/tickets/10` | 22/22/22/22 | 52/52/52/52 | 4/4 | 4/4 | nessuno |
| `/tickets/11` | 26/26/26/26 | 51/51/51/51 | 4/4 | 4/4 | nessuno |
| `/tickets/12` | 24/24/24/24 | 40/40/40/40 | 4/4 | 4/4 | nessuno |
| `/tickets/2` | 36/36/36/36 | 59/59/59/59 | 4/4 | 4/4 | nessuno |
| `/tickets/3` | 32/32/32/32 | 44/44/44/44 | 4/4 | 4/4 | nessuno |
| `/tickets/4` | 28/28/28/28 | 53/53/53/53 | 4/4 | 4/4 | nessuno |
| `/tickets/5` | 30/30/30/30 | 59/59/59/59 | 4/4 | 4/4 | nessuno |
| `/tickets/6` | 24/24/24/24 | 38/38/38/38 | 4/4 | 4/4 | nessuno |
| `/tickets/7` | 28/28/28/28 | 47/47/47/47 | 4/4 | 4/4 | nessuno |
| `/tickets/8` | 36/36/36/36 | 47/47/47/47 | 4/4 | 4/4 | nessuno |
| `/tickets/9` | 32/32/32/32 | 41/41/41/41 | 4/4 | 4/4 | nessuno |
| `/tickets/track` | 23/14/17/21 | 102/86/91/99 | 4/4 | 4/4 | nessuno |

Statistiche sulle 124 pagine (MISURATO):

| Misura | Valore |
|---|---|
| Titoli con il marchio "FixCity" | 4 su 124 (solo le home) |
| Titoli sotto i 15 caratteri | 25 su 124 (es. "News", "Servizi", "Novità") |
| Titoli sopra i 60 caratteri | 0 |
| Descrizioni sotto i 70 caratteri | 79 su 124 |
| Descrizioni sopra i 160 caratteri | 0; mancanti: 0 |
| Descrizioni duplicate nella stessa lingua | 2 coppie (i 2 articoli; i 2 eventi) |
| Descrizioni in italiano su pagine en/de/es | 48 su 93: 12 di articoli ed eventi, 36 dei ticket (testo dei cittadini) |
| `og:image` | 0 su 124 |
| `og:type` | `website` su 124 (anche articoli ed eventi) |
| `og:locale` | solo il codice lingua ("it"), non `it_IT` |
| `twitter:card` | `summary` su 124, senza immagine |
| JSON-LD | solo `WebSite` sulle 4 home; nessun Organization, BreadcrumbList, Event, Article, FAQPage, GovernmentService |
| `<meta name="robots">` | 0 su 124 (anche auth, area personale, create, confirmation, track) |
| `<link rel="icon">`, apple-touch-icon, manifest, theme-color | 0 su 124 |
| Elementi con attributo `lang=` | 0 su 124 |
| Immagini senza `alt` | 0 |

### 2.4 Richieste e peso (8 pagine, Chromium, mediana di 3 run, server di sviluppo senza compressione)

Tempi in ms. Peso in KB trasferiti. CSS, JS, immagini, font in KB.

| Pagina | Profilo | TTFB | FCP | LCP | CLS | TBT | Richieste | KB | CSS | JS | Immagini | Font |
|---|---|---|---|---|---|---|---|---|---|---|---|---|
| `/it` (home) | desktop | 671 | 1028 | 1028 | 0.001 | 243 | 76 | 6152 | 3432 | 1360 | 1099 | 76 |
| `/it` (home) | mobile | 1018 | 21504 | 22596 | 0.000 | 1980 | 51 | 4573 | 2610 | 1360 | 363 | 63 |
| `/it/tickets` (tickets) | desktop | 904 | 1196 | 1444 | 0.017 | 418 | 77 | 6109 | 3432 | 1360 | 996 | 52 |
| `/it/tickets` (tickets) | mobile | 965 | 21892 | 23772 | 0.000 | 4482 | 45 | 4548 | 2610 | 1360 | 256 | 63 |
| `/it/tickets/1` (ticket) | desktop | 800 | 1312 | 1392 | 0.002 | 159 | 40 | 5332 | 3432 | 1355 | 332 | 52 |
| `/it/tickets/1` (ticket) | mobile | 871 | 21284 | 21812 | 0.000 | 889 | 36 | 4467 | 2692 | 1355 | 208 | 52 |
| `/it/blog` (blog) | desktop | 717 | 1140 | 1140 | 0.002 | 137 | 31 | 5060 | 3432 | 1355 | 11 | 76 |
| `/it/blog` (blog) | mobile | 755 | 21404 | 22164 | 0.000 | 531 | 30 | 4214 | 2610 | 1355 | 11 | 52 |
| `/it/events` (events) | desktop | 672 | 1048 | 1180 | 0.001 | 71 | 31 | 5209 | 3432 | 1355 | 161 | 76 |
| `/it/events` (events) | mobile | 748 | 22152 | 22976 | 0.000 | 647 | 30 | 4363 | 2610 | 1355 | 161 | 52 |
| `/it/privacy` (privacy) | desktop | 856 | 1200 | 1272 | 0.017 | 60 | 30 | 5028 | 3432 | 1355 | 5 | 76 |
| `/it/privacy` (privacy) | mobile | 549 | 21080 | 21664 | 0.006 | 498 | 29 | 4182 | 2610 | 1355 | 5 | 52 |
| `/it/auth/login` (login) | desktop | 690 | 900 | 1488 | 0.001 | 197 | 30 | 5023 | 3432 | 1356 | 5 | 52 |
| `/it/auth/login` (login) | mobile | 788 | 21224 | 22084 | 0.001 | 537 | 30 | 4201 | 2610 | 1356 | 5 | 52 |
| `/it/tickets/create` (create) | desktop | 1168 | 1532 | 2000 | 0.146 | 414 | 32 | 5205 | 3432 | 1358 | 41 | 52 |
| `/it/tickets/create` (create) | mobile | 1060 | 21724 | 22596 | 0.000 | 2459 | 37 | 4549 | 2610 | 1358 | 205 | 52 |
| `/it/area-personale/pratiche` (pratiche) | desktop | 646 | 992 | 1156 | 0.013 | 83 | 33 | 5071 | 3432 | 1355 | 10 | 52 |
| `/it/area-personale/pratiche` (pratiche) | mobile | 959 | 21740 | 23056 | 0.002 | 727 | 32 | 4313 | 2674 | 1355 | 10 | 52 |

Rilievi: stesso bundle su ogni pagina (CSS 3432 KB desktop, JS 1355-1360 KB). Mobile throttled: FCP 21-22 s perche' i 3,4 MB di CSS non compresso passano a 1,6 Mbps. `create` ha CLS 0,146 desktop (soglia 0,1) e carica `sprites.svg` due volte (91,5 KB l'uno, uno da `/it/assets/bootstrap-italia/...`). `events` mostra un PNG 377x160 a 1366 px senza `width/height` (156 KB).

### 2.5 Simulazione "produzione" (gzip + cache immutable, 2 run, stesso Chromium)

Un proxy locale comprime in gzip i testi e mette `immutable` agli asset con hash. Isola quanto vale la configurazione del web server.

| Profilo | Pagina | FCP ms | LCP ms | TBT ms | KB trasferiti | KB propri | Richieste |
|---|---|---|---|---|---|---|---|
| mobile | `/it` | 5688 | 5688 | 2341 | 1165 | 806 | 112 |
| mobile | `/it/tickets` | 5328 | 9384 | 3801 | 1017 | 764 | 54 |
| mobile | `/it/tickets/1` | 6116 | 10848 | 1939 | 953 | 748 | 36 |
| mobile | `/it/privacy` | 5252 | 5252 | 801 | 770 | 770 | 30 |
| desktop | `/it` | 2184 | 2184 | 276 | 2472 | 806 | 154 |
| desktop | `/it/tickets` | 1364 | 1472 | 476 | 1760 | 764 | 78 |
| desktop | `/it/tickets/1` | 1460 | 1612 | 251 | 1078 | 748 | 40 |
| desktop | `/it/privacy` | 1436 | 1576 | 167 | 770 | 770 | 30 |

Lettura: con gzip il trasferimento proprio scende a 748-806 KB (da circa 5 MB), ma il primo contenuto resta a 5,3-6,1 s su mobile lento e l'LCP sale a 9-11 s su `/tickets` e sul dettaglio ticket (mappa e tabella). Il collo di bottiglia diventa il lavoro del browser (TBT 0,8-3,8 s) sui 3,4 MB di CSS e sul JS. La seconda pagina nella stessa sessione pesa 25 KB su mobile: la cache funziona, il costo e' la prima visita.

Altre misure: HTML mediano 57,7 KB (`/it/tickets` 150 KB). TTFB curl su 124 pagine: mediana 0,69 s, p90 0,86 s, massimo 1,34 s (debug acceso, nessuna config cache, worker condivisi). Copertura del codice: CSS principale usato al 4-5%, `livewire.js` 13%, Filament `support.js` 8%, `tables.js` 2%, `theme-map.js` 31-52%. Compressibilita': CSS 3.463.021 B grezzo, 270.749 B gzip, 186.223 B zstd; `livewire.js` 609 KB, 128 KB gzip; `theme-map.js` 316 KB, 90 KB gzip.

### 2.6 Intestazioni e cookie (124 pagine uguali)

| Voce | Valore misurato | Giudizio |
|---|---|---|
| X-Content-Type-Options | nosniff | ok |
| X-Frame-Options | SAMEORIGIN | ok |
| Referrer-Policy | strict-origin-when-cross-origin | ok |
| Permissions-Policy | geolocation=(self); camera, microphone, payment, usb, accelerometer, gyroscope, magnetometer negati | ok |
| Content-Security-Policy (applicata) | solo `frame-ancestors 'self'` | parziale |
| Content-Security-Policy-Report-Only | `default-src 'self'`, `script-src 'self' 'unsafe-inline' 'unsafe-eval'`, nessun `report-uri`/`report-to` | non applicata, senza raccolta (FO-6-35) |
| HSTS, COOP, CORP | assenti (HTTP) | non verificabile in produzione (FO-6-36) |
| Cache-Control sull'HTML | `no-cache, no-store, private, max-age=0, must-revalidate` + 2 `Set-Cookie` su ogni pagina pubblica | blocca CDN e bfcache (FO-6-29) |
| Cookie `XSRF-TOKEN` | non HttpOnly (normale), SameSite=Lax, 2 h | ok; `Secure` assente su HTTP |
| Cookie di sessione | HttpOnly, SameSite=Lax, 2 h, cifrata | ok; `Secure` assente su HTTP |
| Asset statici | `Cache-Control: public, max-age=3600`, nessuna compressione | FO-6-27 |
| Form | Livewire con `data-csrf`; solo il logout ha `_token` classico; nessun `<meta name="csrf-token">` | ok per Livewire; rifiuto CSRF non provato (solo GET) |

## 3. Riscontri

Gravita': BLOCCA = non si va online cosi'; QUALITA' = si va online ma il cliente se ne accorge; RIFINITURA = migliora, non urge. Dimensione: S = meno di mezza giornata, M = 1-3 giorni, L = piu' di 3 giorni. Link: C-xx = report gap-C-cms-lang-seo-tenant.md; DCP-xx = story; DEC-xx = registro decisioni aperte.

### 3.1 Lingue

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-01 | `/privacy`, `/note-legali`, `/terms`, `/media-policy` (e `/accessibilita` in es) | it en de es | Riquadro "Testo non ancora completo" con elenco di sezioni "assente o vuota": titolare, DPO, finalita' e base giuridica, destinatari, trasferimenti extra SEE, conservazione, diritti, reclamo al Garante, obbligatorieta' del conferimento. In `/accessibilita` es compare "Declaración aún incompleta". | MISURATO: attributo `data-element="legal-incomplete"` in 16 file su 16 (4 pagine x 4 lingue); testo "sezione assente o vuota" in `it_privacy`. | BLOCCA | Contenuto da scrivere insieme al Comune: JSON `laravel/config/local/fixcity/database/content/pages/{privacy,note-legali,terms,media-policy}.index.json` (blocco per lingua). Il riquadro e' reso da `Themes/Sixteen/resources/views/components/blocks/legal-document/design-comuni.blade.php` e sparisce da solo a sezioni piene. Tenere la guardia come cancello di pubblicazione. | M (testi e revisione legale) | DCP-112 |
| FO-6-02 | Barra sottile di ogni pagina; `/tickets`, `/blog`, `/events`; `/tickets/create`; `/tickets/confirmation` | it en de es | Segnaposto in produzione: ente "Demo" (it, 31 pagine) e "Name of the Region" / "Name der Region" / "Nombre de la Región" (93 pagine en/de/es); numero verde "05 0505" (`tel:050505`); `href="#"` su "Richiedi assistenza", "Chiama il numero verde 05 0505", "Prenota appuntamento", "Usa la tua posizione" (4 in create, 5 in confirmation, per lingua); nessuna pagina `/contatti` (404). | MISURATO: grep sull'HTML scaricato; `05 0505` in 6 file (`/tickets` x4, `/blog` it, `/events` it). | BLOCCA | Nome ente: `laravel/config/local/fixcity/database/content/sections/header.json` (blocchi it, en, de, es; copia in `Modules/Fixcity/config/database/content/sections/header.json`). Telefono e link: `Themes/Sixteen/resources/views/components/cmp-contacts.blade.php`, `blocks/content/contacts-card.blade.php`, `blocks/design-comuni/contacts-card.blade.php`, `blocks/segnalazioni-elenco/default.blade.php`. Meglio un solo dato di contatto in configurazione del tenant e i blocchi che lo leggono. | M | FO-6-48, DCP-112 |
| FO-6-03 | `/blog` | en de es | Chrome di ricerca e paginazione in italiano: "NOTIZIE", "N notizie trovate", "Cerca per parola chiave", "Invio", "Pagina precedente/successiva" (circa 12 nodi). | MISURATO: 4 file `*_blog.html`; contatori `i` 6-8 e `a` 6 in 2.1. | QUALITA' | Sostituire le stringhe fisse con `__('sixteen::blog.*')` nel blocco `components/blocks/blog/index.blade.php` (righe 48, 61-64, 205, 213, 276); aggiungere le chiavi in `Themes/Sixteen/lang/{it,en,de,es}/`. Controllo: `BASE=http://127.0.0.1:8091 python3 bashscripts/tools/i18n/audit-locales.py /blog`. | S | C-06 |
| FO-6-04 | `/events` (stesso widget in it su `/tickets`, `/blog`, `/domande-frequenti`) | en de es | Widget di valutazione in italiano: "Quanto sono chiare le informazioni", "Valuta N stelle su 5", "Vuoi aggiungere altri dettagli?" (circa 25 stringhe, 13-15 parole italiane per pagina). | MISURATO: `Quanto sono chiare` in `en/de/es_events.html`; contatore `i` 13-15 in 2.1. | QUALITA' | Stringhe fisse in `components/blocks/feedback/faq-rating.blade.php` e `feedback-section.blade.php`: portarle in lang del tema, riusare le chiavi del widget valutazione se esistono, un solo componente per i due blocchi. | S | C-06 |
| FO-6-05 | 2 articoli e 2 eventi (dettaglio) | en de es | `<h2>Commenti</h2>` fisso; "Torna alle novità" e "Torna agli eventi" fissi; credito immagine "Asset Design Comuni / tema Sixteen: uso demo FixCity" in italiano. Dopo la correzione del widget da parte di un'altra sessione, `/de/events/...` mostra sia "Commenti" sia "Kommentare". | MISURATO: ritest alle 12:40 UTC su `/{en,de,es}/events/incontro-pubblico-sulla-mobilita`; `Commenti` presente in 3 su 3. | QUALITA' | `components/blocks/blog/detail.blade.php:120` e `events/detail.blade.php:116,150`: `__()` con chiavi del tema; il titolo del widget Comment appartiene all'altra sessione. | S | C-06 |
| FO-6-06 | 2 articoli, 2 eventi, 12 ticket (corpo, titolo, meta description, alt) | en de es | Il contenuto esiste solo in italiano, ma l'URL en/de/es e' indicizzabile: canonical a se stesso, hreflang completo, titolo e meta description in italiano, `html lang` straniero. Sono 48 URL. Le 12 meta description di articoli ed eventi sono italiane. | MISURATO: `/de/tickets/1` titolo "Buca profonda in via Solferino" e descrizione italiana con `lang="de"`. | QUALITA' | Decisione di prodotto prima del codice: (a) tradurre articoli ed eventi nei JSON/modelli editoriali; (b) per ticket (testo dei cittadini) lasciare l'italiano ma con `lang="it"` sul contenuto (FO-6-07) e `noindex` o canonical verso `/it/...` sulle copie straniere; (c) togliere dalle copie non tradotte gli hreflang verso lingue senza traduzione. Logica in `components/layouts/seo-links.blade.php`. | M | C-21, DEC da aprire |
| FO-6-07 | Tutte le pagine con testo italiano dentro en/de/es | en de es | Nessun elemento ha `lang=`: lettori di schermo leggono l'italiano con voce tedesca. | MISURATO: 0 elementi con `lang=` su 124 pagine; la colonna `i` e' maggiore di zero su 68 delle 93 pagine non italiane (1-4 parole nei ticket, che sono testo dei cittadini; 6-15 in blog, eventi, `/tickets`). | QUALITA' | Avvolgere contenuti di utenti e testi redazionali in `<span lang="it">` o `<div lang="it">` nei blocchi `ticket-detail`, `blog/detail`, `events/detail`, liste; WCAG 3.1.2. | S | C-38 |
| FO-6-08 | `/it/auth/register`, `/{lg}/auth/login`, `/tickets/track` | it en de es | Errori di registrazione con nomi di attributo grezzi ("first name è richiesto", "last name", "password confirmation") in it, de, es, en; banner italiano "Si è verificato un errore durante la registrazione" dentro de; "Devi accettare la privacy policy" in italiano dentro en. Login: de con iniziale minuscola ("passwort ist erforderlich.", "e-Mail-Adresse ist erforderlich."). Il tracciamento con codice errato e' tradotto bene in 4 lingue. | MISURATO: invio a vuoto dei form in Chromium, 4 lingue. | QUALITA' | Aggiungere i nomi attributo (`first_name`, `last_name`, `password_confirmation`) nel blocco `attributes` di `Modules/User/lang/{lg}/validation.php` (oggi contiene solo `data.password`, che in it vale "zu": residuo da correggere) e le due frasi del banner nel lang del form di registrazione. Usare l'auto-writer solo per scoprire la chiave, poi tradurre a mano (nota C-05). | S | C-05, C-06 |
| FO-6-09 | Intero sito (file lang) | en de es | File lang mancanti rispetto all'italiano. Sixteen: en 82, de 551, es 549 chiavi mancanti su 1164. Comment: en 62, de 149, es 149 su 179. Blog: 356, 408, 408 su 409. Fixcity: 4498, 6031, 5129 su 7043 (in gran parte back office). Sixteen de/es non hanno 25 file, tra cui `ticket.php` (60 chiavi), `segnalazione.php` (76), `404.php` e gli enum. Esiste un residuo `ui.php.lock`. | MISURATO: differenza statica con script PHP di sola lettura, riproduce C-06 (551/549/82) al numero. Effetto visibile sulle pagine pubbliche controllate: piccolo, concentrato in blog, widget feedback, validazione. | QUALITA' | Per il front office basta il tema piu' User, Comment, Blog. Strumenti: `bashscripts/tools/i18n/audit-locales.py` (rileva le righe uguali all'italiano), BO `Modules/Lang` `TranslationFileResource` per modificare i file. Per il wizard (non coperto qui) i file `ticket.php` e `segnalazione.php` mancanti sono il rischio da verificare con l'agente del wizard. | L | C-06 |
| FO-6-10 | Selettore lingua in header | it en de es | Perde la query (`/it/tickets?q=buca&page=2` passa a `/de/tickets`); i link non hanno `lang`/`hreflang`; la voce spagnola ha etichetta "SPA". | MISURATO: 164 pagine controllate, la pagina viene mantenuta, la query no. | QUALITA' | Selettore nel layout del tema (`LaravelLocalization::getLocalizedURL` accetta la query: passare `request()->getQueryString()`); aggiungere `lang` e `hreflang`; etichetta "ES". | S | |
| FO-6-11 | Pagine pubbliche con data | de es | Data lunga `20 Oktober 2026`, `20 octubre 2026` invece di `20. Oktober 2026`, `20 de octubre de 2026`; l'area personale invece e' corretta. | MISURATO: `translatedFormat('j F Y')` in `blocks/ticket-detail/design-comuni.blade.php:76` e `blocks/blog/index.blade.php:44`. | RIFINITURA | Usare `isoFormat('LL')` (dipende dal locale) come fa l'area personale. | S | |
| FO-6-12 | Breadcrumb | it en de es | `aria-label="breadcrumb"` fisso in inglese su 52 pagine (13 percorsi x 4 lingue), anche nella versione italiana. La prima voce invece e' tradotta bene (Home, Startseite, Inicio su 13 su 13). | MISURATO: estrazione del `<nav aria-label="breadcrumb">` da 124 pagine. | RIFINITURA | Componente breadcrumb del layout: chiave lang per `aria-label` ("Percorso di navigazione", "Breadcrumb", "Brotkrümelnavigation", "Ruta de navegación"). | S | |
| FO-6-13 | Terminologia es | es | Incoerenza: nel footer "Señalamientos / Rastrear señalamiento / Enviar señalamiento", nel corpo "incidencia". | MISURATO: testo del footer e di `/es/tickets`. | RIFINITURA | Scegliere un termine ("incidencia") e correggere i file es del footer. Nessuna regola di glossario trovata nel repo. | S | |
| FO-6-14 | Slug | en de es | Gli slug restano italiani (`/en/accessibilita`, `/de/domande-frequenti`); gli equivalenti localizzati danno 404 (`/en/accessibility`, `/de/datenschutz`, `/es/privacidad`). | MISURATO: 74 varianti di URL. | RIFINITURA | Scelta di prodotto. Alternativa leggera: alias di slug per lingua nel JSON della pagina con redirect 301 all'URL canonico. Non risolvere con file in `Themes/*/pages`. | L | DEC da aprire |

### 3.2 SEO e condivisione

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-15 | Tutte | tutte | Nessuna `og:image`; `twitter:card=summary` senza immagine; `og:type=website` anche per articoli ed eventi; `og:locale` e' "it", non `it_IT`. Un link condiviso su WhatsApp o social appare nudo. | MISURATO: 0 su 124 `og:image`; 124 su 124 `website`/`summary`. | QUALITA' | `Themes/Sixteen/resources/views/components/layouts/main.blade.php`: immagine di default del tenant (1200x630 in `public/`), immagine specifica per articolo/evento, `og:type=article` per articoli, `og:locale` da mappa (`it_IT`, `en_GB`, `de_DE`, `es_ES`), `twitter:card=summary_large_image`. | S | |
| FO-6-16 | Tutte | tutte | Dati strutturati solo `WebSite` nelle 4 home. Mancano Organization/GovernmentOrganization, BreadcrumbList, Event (2 eventi), Article (2 articoli), FAQPage (`/domande-frequenti`), GovernmentService (`/services`); nessun `SearchAction`. | MISURATO: JSON-LD estratto da 124 pagine. | QUALITA' | Il layout stampa `WebSite` solo se `$bodyPage === 'homepage'`: spostare in un blocco `x-seo.jsonld` (un componente, usato dai blocchi `blog/detail`, `events/detail`, `faq`, breadcrumb). Convalida con lo strumento Rich Results di Google. | M | |
| FO-6-17 | Tutte | tutte | Titoli senza marchio (solo 4 su 124 hanno "FixCity"); 25 titoli brevissimi ("News", "Servizi", "Novità"); 79 descrizioni sotto i 70 caratteri; 2 coppie di descrizioni identiche (articoli, eventi); `/lista-categorie` ha titolo "Categorie di servizio" ma H1 "Esplora le segnalazioni". | MISURATO: statistiche in 2.3. | QUALITA' | Titolo = pagina + " | FixCity" (o nome ente) nel layout; descrizioni dai JSON `pages/*.json` e dal campo sommario di articoli ed eventi; allineare titolo e H1 di `lista-categorie.index.json`. | S | |
| FO-6-18 | Pagine riservate e di servizio; `robots.txt` | tutte | Nessun `<meta name="robots">` in alcuna pagina: `auth/*`, `area-personale/*`, `tickets/create`, `tickets/confirmation`, `tickets/track`, `/it/tests*` sono solo in `Disallow`, e `Disallow` non toglie dall'indice. `confirmation` e `track` non sono in `robots.txt`; `/tickets/create` e' linkata ma non bloccata. Manca la riga `Sitemap:`. Restano 7 righe per pagine che ora danno 404 (`/it/prova`, `/it/counter`, `/it/show`, `/it/genesis`, `/it/learn`, `/it/artisan-commands-manager`, `/it/dashboard`). Nessuna URL della sitemap e' bloccata (coerente). `/IT/tests` aggira il `Disallow` (le regole sono case sensitive). | MISURATO: 124 pagine senza meta robots; `robots.txt` 4615 B con 50 `Disallow`, nessun `Sitemap:`. | QUALITA' | Aggiungere `noindex,follow` nel layout per le pagine marcate "privata" (campo nel JSON della pagina o prefisso rotta); `Sitemap: https://dominio/sitemap.xml` in `public_html/robots.txt`; pulire le righe morte. Prima della riga `Sitemap:` serve il dominio (DEC-16). | S | C-23, C-24, C-07, DEC-16 |
| FO-6-19 | `/it/blog/inesistente`, `/it/events/inesistente`, `/it/blog/feed` | it en de es | Soft-404: rispondono 200 con la shell (55 KB), senza H1 e con canonical a se stesse. Le pagine inesistenti vere rispondono 404 a tema nelle 4 lingue (titoli "Pagina non trovata", "Page not found", "Seite nicht gefunden", "Página no encontrada"). | MISURATO: `curl -w %{http_code}` a 12:43 UTC (200, 55040 B / 55084 B / 54935 B). | QUALITA' | Il segmento `[slug]` di blog/eventi deve fare `abort(404)` quando il modello non c'e' (stesso meccanismo dei ticket, che danno 404 per `0`, `999`, `abc`, `1.5`). Dove: pagina Folio/Volt o blocco `blog/detail` e `events/detail`, ricerca per slug nel modulo Blog. | S | C-04 |
| FO-6-20 | `/IT`, `/It/blog`, `/it//blog` | it | Prefissi lingua in maiuscolo e doppio slash rispondono 200: `/IT`, `/IT/`, `/It/blog`, `/IT/tests` senza canonical e senza hreflang (il blocco canonical e' in un try/catch, C-21); `/it//blog` ha canonical `/it//blog`. `/it/Blog` e `/it/BLOG` sono 404 (corretto). | MISURATO: 74 varianti. | QUALITA' | Middleware o regola `.htaccess`: 301 verso la forma minuscola e senza doppio slash; canonical sempre calcolato dalla rotta normalizzata. Su Apache il 301 dello slash finale esiste gia' in `public_html/.htaccess`; su `php -S` no (DEDOTTO per produzione). | S | C-21, C-23 |
| FO-6-21 | Tutte | tutte | L'Host header e' riflesso: con `Host: evil.example`, canonical, `og:url` e `<loc>` della sitemap puntano a `evil.example`. `X-Forwarded-Host/Proto` sono accettati da 127.0.0.1. `TRUSTED_HOSTS` e' vuoto e con `APP_ENV=local` il framework salta il controllo. Diventa avvelenamento di cache o di sitemap dietro una CDN. | MISURATO su questa istanza (`curl -H 'Host: evil.example' /it` a 12:43 UTC); in produzione DEDOTTO: `bootstrap/app.php` usa `config('security.trusted_hosts')` da `TRUSTED_HOSTS`. Diventa BLOCCA se riproducibile in produzione. | QUALITA' | Impostare `TRUSTED_HOSTS` e `APP_URL` in produzione; provare con `curl -H 'Host: evil.example' https://dominio/it | grep canonical` (deve dare 400). | S | DCP-139 |
| FO-6-22 | Tutte | tutte | `/favicon.ico` esiste ma pesa 0 byte (`application/x-empty`); nessun `<link rel="icon">`, apple-touch-icon, `theme-color` o manifest in alcuna pagina. | MISURATO: 124 pagine senza icone; `/favicon.ico` 200, 0 B. | QUALITA' | Set di icone (ico, svg, 180 apple-touch, 192/512) in `public_html/` o in `Themes/Sixteen/public/`, `<link>` nel `<head>` di `main.blade.php`. Vedi anche FO-6-47. | S | |
| FO-6-23 | `/sitemap.xml` | tutte | La sitemap ha 124 URL (31 x 4) ma non ha `<lastmod>` ne' `xhtml:link` alternate; le URL dei ticket sono 12 e quelle degli articoli 2, non c'e' un indice per lingua. | MISURATO: `sitemap.xml` 68.969 B, 124 `<loc>`. | RIFINITURA | `Modules/Seo/app/Actions/Sitemap/BuildSitemapAction.php`: `lastmod` da `updated_at` e alternate hreflang per voce. | S | |
| FO-6-24 | `/` | tutte | `/` risponde 302 a `/it` per qualsiasi `Accept-Language` (de, en, es, fr, vuoto), anche con `useAcceptLanguageHeader => true`. Un utente tedesco atterra in italiano. | MISURATO: 5 richieste con intestazioni diverse. | RIFINITURA | `laravel/config/laravellocalization.php` e middleware di redirect lingua; decidere se vale rispettare il browser. Usare 302 (corretto) solo se la scelta e' variabile. | S | |
| FO-6-25 | `/it/tickets?page=2`, filtri | it | Il canonical scarta la query, quindi `?page=2` ha canonical a pagina 1: le pagine successive non sono indicizzabili come pagine. | MISURATO: canonical di `/it/tickets?q=buca&page=2` = `/it/tickets`. | RIFINITURA | Per la paginazione tenere `page` nel canonical; per i filtri libero (`q`) canonical alla pagina base. In `seo-links.blade.php`. | S | |

### 3.3 Prestazioni

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-26 | Tutte | tutte | Un solo CSS da 3,46 MB grezzo (270 KB gzip) con 18.101 regole di cui 3.307 `fi-` di Filament e le copie dello scope `data-dc` della PostCSS; il 4-5% e' usato dalla pagina. Mobile lento: FCP 21-22 s senza compressione, 5,3-6,1 s con gzip; LCP 5,3 s (`/privacy`), 5,7 s (home), 9,4 s (`/tickets`), 10,8 s (dettaglio ticket); TBT fino a 3,8 s. | MISURATO: 18 celle x 3 run piu' simulazione (2.4 e 2.5). | QUALITA' | Tre leve in ordine di resa: (1) separare il CSS dei pannelli Filament (non servono al front office) dal bundle `app.css` e caricare i CSS delle pagine "parity" solo dove servono; (2) CSS critico inline piu' resto con `media=print`/preload; (3) potare con content scanning di Tailwind. Verifica: `npm run build`, poi stessa misura (`perf.mjs` nella cartella di lavoro). PRODUCT.md indica la velocita' mobile come differenziatore. | L | |
| FO-6-27 | Server web | tutte | Nel repo non c'e' compressione ne' cache lunga: `public_html/.htaccess` ha solo le regole di rewrite; gli asset escono con `max-age=3600` (non `immutable`) e senza `Content-Encoding`. In produzione dipende da vhost non visibile. La misura gzip vale come stima (2.5). | MISURATO su dev (`php -S`); produzione DEDOTTA: nessuna regola `mod_deflate`/`expires` nel repo. | QUALITA' | Aggiungere in `.htaccess` (o nel vhost) deflate/brotli e `Cache-Control: public, max-age=31536000, immutable` per `/themes/Sixteen/assets/*-<hash>.*`; `Cache-Control` breve per il resto. Provare con `curl -sI -H 'Accept-Encoding: gzip' URL`. | S | |
| FO-6-28 | Tutte | tutte | Lo stesso JS ovunque: `livewire.js` (609 KB, usato al 13%), Filament `support.js` (8%), `tables.js` (2%), `theme-map.js` (316 KB) anche su `/privacy` e `/blog`. | MISURATO: copertura CSS/JS con CDP su 8 pagine. | QUALITA' | Caricare per pagina (mappa solo dove c'e' mappa; Livewire/Filament solo su wizard, area personale, auth). Vite: entry separate o import dinamici. | M | |
| FO-6-29 | Tutte le pagine pubbliche | tutte | L'HTML esce con `no-store` e con 2 `Set-Cookie` (`XSRF-TOKEN` e sessione) anche per l'ospite su pagine che non contengono form: niente cache di CDN, niente bfcache, e cookie dove la cookie-policy dovrebbe elencarli. | MISURATO: intestazioni di 124 pagine. | QUALITA' | Per le pagine statiche (CMS, legali) gruppo di rotte senza `StartSession`/`VerifyCsrfToken` e `Cache-Control: public, max-age=300`, oppure cache HTML a livello di web server. Il wizard e il login restano con sessione. | M | |
| FO-6-30 | `/events`, `/blog`, `/tickets/create` | tutte | Immagini senza `width/height`: hero PNG 377x160 mostrato a 1366x352 (sgranato, 156 KB), segnaposto PNG 48x48 mostrato a 374x192 nel blog; `/tickets/create` ha CLS 0,146 desktop (soglia 0,1). Alcune immagini senza `loading=lazy`. | MISURATO: dimensioni naturali contro dimensioni di render, CLS da PerformanceObserver. | QUALITA' | Immagini 2x delle dimensioni di rendering in WebP/AVIF, `width/height` sempre, `loading=lazy` sotto il fold; per `create` riservare l'altezza del blocco mappa/riepilogo. Blocchi `events/index`, `blog/index`, wizard `01-*.blade.php`. | S | |
| FO-6-31 | Home, `/tickets`, dettaglio ticket, create | tutte | Unico terzo servizio: tile OpenStreetMap (a/b/c.tile.openstreetmap.org), 42 immagini nella home. Senza terze parti il sito si renderizza, ma la mappa resta grigia (0 tile caricati) senza messaggio. Ogni visitatore invia IP e Origin a OSM, e le sezioni della privacy sono vuote (FO-6-01). La policy di OSM sconsiglia l'uso intenso dai server tile pubblici. | MISURATO: `net-hosts-probe` e prova con host bloccati (screenshot `shots/blocked-*.png`). Nominatim non toccato (wizard non guidato). | QUALITA' | Messaggio di fallback nel blocco mappa (`components/sections/map-lit.blade.php`) quando i tile non si caricano; tile provider con contratto o proprio tile server per il go-live; dichiarare OSM/Nominatim nell'informativa. | M | DCP-139 |
| FO-6-32 | Pagine con form e sprite | tutte | `sprites.svg` caricato due volte su `create` (91,5 KB l'uno); nessun `preload` dei font; 18 `@font-face` (Titillium duplicato v10/v19); font self-hosted con `font-display: swap` (bene). | MISURATO: elenco richieste di `create` e `<link>` del head. | RIFINITURA | Un solo percorso per lo sprite; `<link rel=preload as=font>` per Titillium Web 400/600 e Lora; togliere la versione v10. `vite.config.js` (plugin fonts). | S | |

### 3.4 Sicurezza visibile al cliente

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-33 | `/pulse` | n/a | Un ospite (senza cookie) ottiene la dashboard Laravel Pulse completa (200, 931 KB: Application Usage "Top 10 users", Queues, Cache, Slow Queries, Exceptions, Slow Requests), anche se `UserServiceProvider.php:152` definisce `Gate::define('viewPulse', ...hasRole('super-admin'))`. Con dati veri mostrerebbe nomi utente e query. | MISURATO: GET senza cookie, 200, 931.148 B. Causa DEDOTTA: il gate di default di Pulse e' "ambiente local" e vince sul gate dell'app, oppure il gate con `UserContract` tipizzato non nullable non si applica come atteso. | BLOCCA | Provare con `APP_ENV=production`: deve dare 403 per ospite e per utente senza ruolo. Se non lo da', mettere `Authorize` esplicito sulla rotta (`PULSE_PATH`/`config/pulse.php` `middleware`) e firma `?UserContract $user = null` per il gate. | S | |
| FO-6-34 | Ambiente | n/a | `APP_DEBUG=true`, `APP_ENV=local` e Laravel Boost attivi in questo ambiente: le pagine 500 mostrano lo stack trace con percorsi (1,1 MB), ogni pagina include lo script `browser-logger-active` che invia log del browser a `/_boost/browser-logs`; `|---LINE:151---|` visibile come testo in 40 pagine dopo un 500 (marcatori di BladeMapper dell'exception renderer, scompaiono con `view:clear`). | MISURATO: i 4 file `html_500_first/` (post del blog in 500, 4 lingue) e script in ogni pagina. Boost e' `require-dev` (`laravel/composer.json`): in produzione con `--no-dev` sparisce (DEDOTTO). | BLOCCA | Lista di controllo go-live: `.env` produzione con `APP_DEBUG=false`, `APP_ENV=production`, `composer install --no-dev`, `php artisan config:cache`; poi `curl -s URL-che-da-500` non deve mostrare tracce. Pagine 500/403/503 a tema (FO-6-39). | S | DCP-139 |
| FO-6-35 | Tutte | tutte | CSP applicata solo come `frame-ancestors 'self'`; la CSP completa e' in Report-Only con `script-src 'unsafe-inline' 'unsafe-eval'`, senza `report-uri`/`report-to` (nessuno raccoglie le violazioni), e consente `fonts.googleapis.com` anche se i font sono locali. | MISURATO: intestazioni `Content-Security-Policy` e `-Report-Only` (2.6). | QUALITA' | Aggiungere endpoint di report, osservare, poi togliere `unsafe-eval` (Alpine CSP build) e passare da Report-Only a enforce; togliere Google Fonts dall'elenco. Middleware `SecurityHeaders`, `config/security.php`. | M | DCP-139 |
| FO-6-36 | Produzione | tutte | Non verificabili qui (HTTP): HSTS, flag `Secure` dei cookie (la config lo imposta solo se `APP_ENV=production`), COOP/CORP, ridirezione http a https. | DEDOTTO da `config/session.php` (`secure` default true in produzione, `same_site=lax`, `http_only=true`, sessione cifrata). | QUALITA' | Dopo il deploy: `curl -sI https://dominio/it` deve mostrare `Strict-Transport-Security` e `Set-Cookie ... Secure`. | S | |
| FO-6-37 | Docroot `public_html/` | n/a | `info.php`, `test.txt`, `serve-root-test.txt` erano serviti: durante l'audit un'altra sessione li ha rinominati in `*.old` (12:17 UTC), ora `/info.php` e' 404, ma `/info.php.old` (17 B), `/test.txt.old`, `/hot.stale-20260927.old` e `/data/tickets_big.json.old` (310 KB di ticket sintetici) rispondono ancora 200. `/data/tickets.json` e' un'istantanea del 2026-10-09 (27 feature, 24 KB) servita come statico e referenziata da `map-lit.blade.php`, `GenerateTicketsJsonAction`, `LoadTicketsGeoJsonAction`, `LoadPublicTicketsGeoJsonAction`; `tickets-dense.geojson` e `tickets-30-points.geojson` sono dati di prova pubblici. `/index.php` risponde `text/x-php` solo con `php -S`. | MISURATO: curl a 12:43 UTC (`/info.php` 404, `/info.php.old` 200 17 B, `/data/tickets_big.json.old` 200). | RIFINITURA | Rimuovere i `.old` e i dati di prova da `public_html/` (non da questa sessione); decidere se `tickets.json` deve essere generato con l'azione BO "Esporta" o servito live. | S | C-02, DCP-197 |
| FO-6-38 | Foto dei ticket | n/a | Non verificabile: nel fixture non ci sono foto. Il front office non emette mai le immagini nella lista; `config/local/fixcity/media-library.php` rimuove i metadati (`jpegoptim --strip-all`) solo sulle conversioni; i binari `jpegoptim`, `optipng`, `pngquant`, `svgo`, `exiftool` non sono installati su questa macchina. L'originale potrebbe mantenere EXIF con posizione. | DEDOTTO dal codice di `ListPublicTicketsAction.php` e dalla config; non misurato. | QUALITA' | Prima del go-live: caricare una foto con GPS, scaricare la versione servita dal front office e leggere gli EXIF (`exiftool`); installare i binari sul server o togliere gli EXIF alla ricezione. | M | |
| FO-6-39 | Pagine di errore 403, 500, 503 | it en de es | Il tema ha 404 e 429 a tema (bene); 403/500/503 usano la vista minimale inglese del framework. Esempio: un cittadino loggato che apre `/{lg}/auth/register` vede "Forbidden 403" nudo in inglese. | MISURATO: 403 su `auth/register` loggato (x4 lingue); gestori solo per 404 e 429 in `bootstrap/app.php`. | QUALITA' | Aggiungere `resources/views/errors/{403,500,503}.blade.php` nel tema o i gestori in `bootstrap/app.php` come per 404/429. | S | |
| FO-6-40 | Blog (post `come-preparare-una-segnalazione-utile-w0A0n`) | it en de es | Il post, elencato in sitemap e linkato dall'indice del blog, rispondeva 500 in 4 lingue per un TypeError nel widget Comment (`CommentWidget::$uiConfig` array contro `CommentWidgetUiData`). Una modifica di un'altra sessione l'ha corretto; ora e' 200 in 4 lingue. Non esiste una guardia che lo avrebbe fermato. | MISURATO: 4 pagine 500 salvate in `html_500_first/`; ritest 200 x4. | QUALITA' | Uno smoke test che scarichi tutte le URL della sitemap in tutte le lingue e fallisca su status diverso da 200: puo' usare `urls.txt` e `fetch.sh` della cartella di lavoro come base, in `bashscripts/tools/`. | S | |
| FO-6-41 | `/api/tickets/list`, `/api/tickets/geojson` | n/a | Nessun dato personale (id, titolo, tipo, indirizzo, stato, coordinate a 3-4 decimali; niente email, owner_id, telefono, UUID), ma nessun limite: `per_page=1000` restituisce tutte le 12 voci; il geojson e' `no-cache` e senza bbox. Con migliaia di ticket sara' un'esportazione completa su richiesta. | MISURATO: JSON delle 2 API e confronto delle chiavi. | RIFINITURA | Limite massimo di `per_page` (ad es. 100), `Cache-Control: public, max-age=60` sul geojson, filtro bbox. `Modules/Fixcity/app/Actions/ListPublicTicketsAction.php`, `LoadPublicTicketsGeoJsonAction.php`. | S | |
| FO-6-42 | `/tickets/confirmation`, `/tickets/track` | it en de es | `confirmation` risponde 200 all'ospite senza un ticket e non e' in `robots.txt`; il codice di tracciamento viaggia in query (`?code=`, GET). Il freno funziona (429 dopo circa 7 richieste ravvicinate; il canonical toglie il codice). | MISURATO: pagine ospite e 40 risposte 429 durante il mio crawl (da non ripetere). | RIFINITURA | `confirmation` senza ticket: redirect a `/tickets/track` o 404; `noindex` (FO-6-18); `Referrer-Policy` attuale protegge gia' la query. | S | |

### 3.5 Funzioni e link che non funzionano

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-43 | `/{lg}/area-personale/pratiche` | tutte | Il link alla propria pratica (l'unico ticket personale del fixture) punta a `/{lg}/tickets/13`, che risponde 404 al proprietario e all'ospite. I ticket 13-40 sono tutti 404. Il cittadino non puo' aprire la propria segnalazione. Dipende dall'audit dell'area personale: se lo stato "non pubblico" e' voluto, il link deve cambiare. | MISURATO: 4 lingue x 2 ruoli, link in `links_status.txt`. | BLOCCA | Una pagina di dettaglio per il proprietario (stessa pagina con autorizzazione al proprietario) oppure un link `/{lg}/area-personale/pratiche/{codice}`; non una pagina nel tema. Dettaglio: `ListPublicTicketsAction` (solo pubblici) e il blocco `ticket-detail`. | M | DEC da aprire |
| FO-6-44 | Modale di ricerca in header | tutte | La ricerca invia `?autocomplete-two=buca` alla stessa pagina; non esiste una rotta di ricerca (`/it/search` e `/it/ricerca` sono 404) e il risultato e' la pagina senza filtro. | MISURATO: `shots/search-modal.png`, GET `?autocomplete-two=buca`. | QUALITA' | O collegare il campo a `/it/tickets?q=` (esiste il filtro `q` nell'elenco), o togliere il pulsante finche' non esiste. File: `components/sections/search-modal/design-comuni.blade.php`. | S | |
| FO-6-45 | `/tickets/create`, `/tickets/confirmation` | tutte | `href="#"`: "Richiedi assistenza", "Prenota appuntamento", "Chiama il numero verde" (+ "Usa la tua posizione" come ancora senza azione in HTML), 4 per lingua in create e 5 in confirmation. | MISURATO: grep di `href="#"`. | QUALITA' | Trasformare in `<button type="button">` quelli che sono azioni JS, o dare una destinazione reale ai tre contatti (FO-6-02, FO-6-48). | S | |
| FO-6-46 | `/{lg}/auth/register`, `/{lg}/auth/login` da loggato | tutte | `register` da loggato: 403 nudo in inglese; `login` da loggato mostra ancora il modulo. | MISURATO: cookie cittadino, 4 lingue. | QUALITA' | Trovare dove nasce il 403 (i JSON `pages/auth/auth.register.json` e `login.json` non dichiarano middleware) e sostituirlo con un redirect all'area personale per entrambe le pagine, quando l'utente e' autenticato. | S | |

### 3.6 Cosa manca per essere "perfetto"

| ID | Pagina o gruppo | Lingua | Difetto o mancanza | Prova | Gravita' | Come si fa | Dim. | Link |
|---|---|---|---|---|---|---|---|---|
| FO-6-47 | PWA e uso offline | tutte | Esistono `manifest.json` (nome FixCity, 8 icone tutte 404, screenshot 404), `sw.js` (precache `/css/app.css`, `/js/app.js`, `/images/logo.svg`: tutte e tre 404, esiste solo `/offline.html`) e `offline.html`, ma nessuna pagina collega il manifest o registra il service worker; `resources/js/pwa.js` non e' caricato. | MISURATO: `/manifest.json` 200, icone 404, `sw.js` 200, nessun `<link rel=manifest>`. | QUALITA' | Per segnalare "dal campo" conviene fare almeno: manifest collegato con icone vere, service worker che mette in cache gli asset hash e mostra `offline.html` quando manca la rete, e una bozza del modulo in `localStorage`. Entry in `Themes/Sixteen/resources/js/pwa.js`, `<link>` in `main.blade.php`. | M | |
| FO-6-48 | Contatti dell'ente | tutte | Non c'e' la pagina `/contatti` (404) con indirizzo, PEC, orari e recapiti; i blocchi contatti rimandano a segnaposto. Per un servizio PA e' un obbligo di trasparenza. | MISURATO: `/it/contatti` 404; vedi FO-6-02. | QUALITA' | Nuovo JSON `pages/contatti.index.json` con blocco `contacts-card` alimentato dai dati del tenant; link da footer. | S | DEC da aprire |
| FO-6-49 | `/.well-known/security.txt` | n/a | Assente (404). | MISURATO: `curl` a 12:43 UTC. | RIFINITURA | File in `public_html/.well-known/security.txt` con `Contact`, `Expires`, `Preferred-Languages`. | S | C-22 |
| FO-6-50 | Blog ed eventi | it en de es | Nessun feed RSS/Atom (`/rss`, `/feed`, `/feed.xml`, `/it/blog/feed` danno 404 o shell), nessun file iCal per gli eventi. | MISURATO: 4 URL sondate. | RIFINITURA | Rotta feed nel modulo Blog (`Modules/Blog`) con `<link rel=alternate type=application/rss+xml>` nel `<head>`; `.ics` per evento. | S | |
| FO-6-51 | Stato del servizio | tutte | Esiste solo `/up` (200, pagina HTML di Laravel). Non c'e' una pagina di stato leggibile dal cittadino. | MISURATO: `/up` 200, 1829 B. | RIFINITURA | Pagina `/stato` con esito di `/up`, versione e orario (JSON CMS + blocco). Per monitoraggio esterno usare `/up` come e' (non esporre dettagli). | S | |
| FO-6-52 | Newsletter / iscrizione | tutte | Nessuna pagina di iscrizione o avvisi per i cittadini oltre alle notifiche dell'area personale. | MISURATO: nessuna URL ne' modulo nel menu o nel footer. | RIFINITURA | Decisione di prodotto e di privacy (base giuridica, doppio opt-in) prima di costruire; se si', blocco `newsletter` che usa il modulo Notify. | M | DEC da aprire |

Conteggio per gravita' (verifica in fondo al documento): BLOCCA 5 (FO-6-01, 02, 33, 34, 43), RIFINITURA 15 (FO-6-11, 12, 13, 14, 23, 24, 25, 32, 37, 41, 42, 49, 50, 51, 52), QUALITA' 32 (tutti gli altri).

## 4. Confronto con C-06 e C-07 (gap-C-cms-lang-seo-tenant.md)

- C-06 (chiavi mancanti del tema): riprodotto al numero con la differenza statica: Sixteen it 1164 chiavi, mancano 551 in de, 549 in es, 82 in en. Aggiungo cio' che il report non diceva: sulle pagine pubbliche controllate (31 percorsi x 3 lingue) l'effetto e' piccolo e concentrato in blog, widget feedback e validazione dei form (FO-6-03, 04, 05, 08). Il resto delle chiavi mancanti riguarda back office, wizard e file `ticket.php`/`segnalazione.php` di de/es, che questo audit non ha esercitato a fondo. Peso reale per il cliente: 12 pagine (blog, eventi) e i form.
- C-07 (replica pubblica `/tests` e `tickets/create` che dipende da `blocks.tests.ticket-crea`): confermato che `/it/tests` risponde 200 all'ospite (79 KB), e' in `Disallow` ma senza `noindex`, e che `/IT/tests` aggira il `robots.txt` (FO-6-18, FO-6-20). Non ho enumerato le 83 repliche (non sono in sitemap).
- C-21 (hreflang per pagine non tradotte): confermato e quantificato: 48 URL straniere con contenuto italiano (FO-6-06).
- C-38 (`lang` sui frammenti italiani): misurato, 0 elementi con `lang` (FO-6-07).
- C-02 (`info.php` in docroot): risolto durante l'audit da un'altra sessione (rinominato `.old`); restano i `.old` serviti (FO-6-37).
- C-04 (oracolo di `ResolvePageAction`): non riprodotto con gli id provati (`/it/profile/1`, `2`, `13`, `abc`, `99999`, nome utente, UUID: tutti 404 per ospite e per cittadino). Non e' una prova che non esista.
- C-05 (auto-writer): non provato; vedi il limite nella sezione 1.
- C-22, C-23, C-24: confermati (FO-6-49, 18, 18).

## 5. Cosa funziona bene (con prova)

- Lingua e struttura: 124 pagine in 200, `html lang` giusto su tutte, canonical a se stesse 124 su 124, hreflang a 5 voci 124 su 124, `og:url` 124 su 124, un solo H1 per pagina 124 su 124, nessuna immagine senza `alt`, nessuna chiave grezza.
- Il selettore lingua mantiene la pagina su 164 pagine controllate.
- 404 veri e a tema in 4 lingue per pagine inesistenti, lingue non supportate (`/xx`, `/fr`) e ticket con id non validi (`0`, `999`, `abc`, `1.5`).
- Intestazioni di sicurezza di base presenti su tutte le risposte (2.6) e nessun `X-Powered-By` sulle risposte Laravel.
- Le API pubbliche non espongono dati personali; nessun id enumerabile oltre i 12 pubblici; `/api/user`, `/sanctum/csrf-cookie`, `/telescope`, `/horizon`, `/_debugbar`, `/.env`, `/.git/HEAD`, `/composer.json`, `/artisan`, `/phpinfo`, `/storage/` (directory), `/log-viewer`, `/nova` rispondono 404; `/storage/logs/laravel.log` 403.
- Il freno al codice di tracciamento funziona (429 dopo circa 7 richieste ravvicinate) e il messaggio per codice errato e' ben tradotto.
- Pagine riservate (`area-personale/*`, `tickets/create`, `/admin`) portano al login per l'ospite.
- Font self-hosted con `font-display: swap`, nessun Google Fonts, nessun analytics, un solo terzo servizio (tile OSM); cache calda: seconda pagina 25 KB su mobile.
- Sezione dei cookie: la pagina `/cookie-policy` esiste in 4 lingue e elenca i cookie.
- Il sito si usa con terze parti bloccate (h1, header, marker presenti).

## 6. Domande per l'utente

1. FO-6-06: gli articoli, gli eventi e i ticket stranieri devono restare indicizzabili in italiano sotto `/en`, `/de`, `/es`, o li marchiamo `noindex` finche' non sono tradotti? Chi traduce articoli ed eventi?
2. FO-6-02 e FO-6-48: chi fornisce i dati reali dell'ente (nome, numero verde, PEC, orari)? Fino ad allora preferite nascondere i blocchi con segnaposto o lasciare "Demo"?
3. FO-6-43: la segnalazione propria, non ancora pubblica, deve aprirsi in una pagina di dettaglio privata? In quale URL?
4. FO-6-26: la prestazione su mobile lento (LCP 5-11 s anche con gzip) e' un cancello di go-live o un obiettivo del trimestre? Cambia l'ordine con cui separare il CSS dei pannelli.
5. FO-6-14: gli slug devono essere tradotti (`/en/accessibility`) o l'URL italiano in tutte le lingue e' una scelta?
6. FO-6-52: serve una newsletter o un avviso al cittadino fuori dall'area personale? Con quale base giuridica?
7. FO-6-31: volete un provider di tile con contratto (o uno proprio) per il go-live, e dichiarare OSM/Nominatim nell'informativa?

## 7. Cosa non ho potuto provare

- Foto reali e loro EXIF (nessuna foto nel fixture).
- Rifiuto CSRF via POST e altre mutazioni (solo GET per vincolo).
- Comportamento di produzione: compressione, cache, slash finale, esecuzione di `info.php`, HSTS, cookie `Secure`, `APP_DEBUG=false`, pagine 500/403/503, `/pulse` con `APP_ENV=production`, Host header con `TRUSTED_HOSTS` impostato.
- Nominatim (la geocodifica del wizard non e' stata guidata), il resto della validazione del wizard oltre al login e alla registrazione a vuoto.
- Lighthouse come punteggio, lettori di schermo reali, CDN o proxy reali.
- Se le modifiche ai file lang del repo (Comment, `ticket_notification`) sono dell'auto-writer o di altre sessioni.
- Sonda con `fallback_locale` vuoto per trovare chiavi mancanti (richiederebbe cambiare la configurazione): sostituita dal confronto con l'italiano e dalla differenza statica dei file.

## 8. File di lavoro (fuori dal repository)

Cartella: `/tmp/claude-1000/-mnt-nas07-var-www--bases-base-fixcity-fila5/fa79ffe9-c6fb-4eb5-892f-259fcfdd19d2/scratchpad/fo-audit/fo-6-lingue-seo-prestazioni/`

- `html/`, `html_auth/`, `html_gx/`, `html_500_first/`: pagine scaricate con intestazioni.
- `analysis.json`, `lang_rows.json`, `perf/perf_final.json`, `perf/perf_proxy.json`: dati delle matrici.
- `fetch.sh`, `fetch2.sh`, `analyze.py`, `lang_matrix.py`, `seo_matrix.py`, `langdiff.php`, `perf.mjs`, `proxy.mjs`, `cov.mjs`: script di scaricamento e misura. `auth.json` e `cookie.hdr` contengono la sessione di prova: non condividerli.
- Un piccolo proxy (`node proxy.mjs`, porta 18091) puo' essere ancora attivo in background: innocuo, non tocca l'istanza.
