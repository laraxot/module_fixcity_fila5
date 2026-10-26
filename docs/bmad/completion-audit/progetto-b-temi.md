---
title: "Analisi di progetto B: temi"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "analisi di progetto b: temi audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Lacune dei temi: Sixteen, Barthelemy, Meetup, TwentyOne

Analista in sola lettura. Data della misura: 2026-10-26. Radice: `/mnt/nas07/var/www/_bases/base_fixcity_fila5`.
Nessun file del repository e' stato scritto, modificato o cancellato. Non ho eseguito Pest, PHPStan, npm, artisan, composer, il gate di go-live ne' i test browser: ogni numero viene da Read, grep, find, ls, wc e git in lettura.

Etichette usate in tutto il documento:

- VERIFICATO: l'ho letto o contato io in questa sessione, con il comando o il file indicato.
- RIPORTATO: lo dice un documento o una memoria di progetto e non l'ho ricontrollato.
- non determinato: non ho prove sufficienti.

I conteggi di chiavi di traduzione sono approssimati con una regex e valgono solo come ordine di grandezza.

---

## 1) Perimetro e ownership

| Tema | Percorso | Repo git | In uso | Contenuto reale |
|---|---|---|---|---|
| Sixteen | `laravel/Themes/Sixteen` | repo separato, remote `laraxot` -> `git@github.com:laraxot/theme_sixteen_fila5.git`, 223 commit quasi tutti con messaggio "." (VERIFICATO) | si: `pub_theme=Sixteen` in tutti i `xra.php` che impostano un tema; `public_html/themes/Sixteen` e' un link a `Themes/Sixteen/public` (VERIFICATO, coerente con `docs/README.md:48`) | 83 PHP in `app/`, 1068 Blade, 99 CSS, 21 JS, 305 story, 2056 md in `docs/` |
| Barthelemy | `laravel/Themes/Barthelemy` | tracciato direttamente nel repo radice (non submodule) | no (nessuna config lo imposta) | 1976 file, di cui 1716 md; 0 PHP, 0 Blade, 0 test; solo `package.json`, `.releaserc`, script CI e `docs/` |
| Meetup | `laravel/Themes/Meetup` | tracciato nel repo radice | no, ma citato da 3 test e 1 Action (vedi B-33) | 64 file: `package.json`, un `test-layout.html` per i layout mail, `docs/`. Nessun `resources/views/pages` |
| TwentyOne | `laravel/Themes/TwentyOne` | repo separato (in `gitmodules.ini`) | no, README dice "non usato"; importato da Predict | 851 file, 261 Blade, 2 PHP in `app/`, 2 test; riferimenti a `Modules\Predict` che non esiste |

Ownership dentro il perimetro di Sixteen (regole di progetto lette in `PRODUCT.md`, `docs/README.md:15`, `docs/page-directory-structure.md`):

- Sixteen possiede la presentazione: layout, blocchi Blade in `components/blocks`, CSS/JS, build Vite, shell Folio minima.
- Non possiede Action di dominio, Resource Filament, widget dei moduli. Il wizard di creazione segnalazione vive in `Modules/Fixcity` (`CreateTicketWizardWidget`); nel tema ci sono solo le viste. La logica dei documenti legali sta in `Modules/Fixcity/app/Actions/{BuildLegalDocumentAction,GetPublishedLegalDocumentAction}.php`.
- I contenuti pubblici sono JSON del tenant in `laravel/config/local/fixcity/database/content/{pages,sections}` e testi in `config/local/fixcity/lang/*/{policy,terms}.md`: appartengono al titolare/ente, non al tema.
- Dipendenze verso altri moduli (VERIFICATO da lettura codice): Fixcity (azioni legali, dettaglio pubblico `BuildPublicTicketDetailAction`, pagine errori `fixcity::errors.*`, 43 test di pagina in `Modules/Fixcity/tests/Feature/Pages`, tre `*PublicationGateTest`); Cms (`PageSlugMiddleware`, `ResolvePageAction`, `HasBlocks`); Blog (`ThemeComposer` per dettagli articolo/evento); Geo (`map-lit.js`, etichette mappa); User (form login/registrazione); Seo (sitemap, robots); Rating e Gdpr (esistono come moduli, il tema non li usa per valutazione e banner, vedi B-05 e B-19); Notify (layout mail legge `pub_theme`).
- Fuori dal mio perimetro ma che il tema eredita come lacuna: `Cms\ResolvePageAction` (soft-404 su blog/eventi, B-22), `config/security.php` e middleware `SecurityHeaders` (CSP, B-10), `laravel/phpstan.neon` (copertura PHPStan, B-12), `.github/workflows/ci.yml` (B-11).

---

## 2) Stato reale con evidenze

### 2.1 Inventario (VERIFICATO con find/wc/ls)

- Sixteen: 83 PHP in `app/`; 1068 Blade (907 in `components/`, 611 in `components/blocks/`, 126 cartelle di blocco); 99 CSS; 21 JS.
- Test: 25 Unit, 5 Feature, 12 spec browser in `tests/browser`. Il README dichiara 15, 6, 10 (`docs/README.md:58`).
- Documentazione: 2056 md in `docs/` (416 MB su disco, 429 md alla radice di `docs/`, 41 file `.lock`, 41 file `.old`); `.git` del tema 266 MB.
- Story: 305 file con frontmatter (indice in `scratchpad/stories-status.txt`). Cartelle: `docs/bmad/stories` 178, `docs/bmad/stories/catalog` 37, `docs/bmad/stories/pages` 20, `docs/stories` 51, `docs/bmad` 7, `front-office-design-comuni/stories` 6, `json-driven-homepage` 5, `docs/bmd` 1.
- Home: non e' piu una replica Design Comuni. `home.json` ha 4 blocchi x 4 lingue (hero, anteprima mappa, come funziona, azioni rapide); la replica DC e' `/it/tests/homepage`. Il CSS e' `16-homepage-civic-reframe.css`.

### 2.2 Stato delle story (VERIFICATO dal frontmatter, 305 file)

| Stato | N | Stato | N |
|---|---|---|---|
| ready-for-dev | 92 | open | 24 |
| in-progress | 42 | NONE (nessuno stato) | 26 |
| done | 38 | backlog | 17 |
| draft | 11 | todo | 9 |
| in-review | 8 | completed | 8 |
| review | 5 | superseded | 4 |
| needs-data | 3 | active | 3 |
| needs-decision | 2 | in_progress | 2 |
| done-with-open-decisions | 2 | archived | 2 |

Gli altri stati compaiono una volta sola (ready, planning, investigation, implemented-runtime-pending, done-pending-prod-sync, done-main, decided-by-orchestrator). Quindi 92 + 42 + 24 + 17 + 11 + 9 + altre = la gran parte e' lavoro non chiuso; solo circa 50 (done + completed + done-*) sono chiuse. Lo stato del tracker non e' pero' affidabile (B-25).

Campione di story chiuse, con verifica che codice e test esistano (VERIFICATO):

- Confermate: dcp-126 (`abort_if` presente nello shell, `UnknownTwoSegmentPageTest` esiste), dcp-147 (`PageDescription` + `PageDescriptionTest`), dcp-50 (guardia presente; elenca anche punti aperti e rimanda a DEC-38), dcp-110, 111, 120, 148, 52, 05, 06, 31, 40, 95, 268 (file citati trovati; `BuildPublicTicketDetailAction.php` e il suo test esistono).
- Parzialmente vere: dcp-222 (footer): la guardia `FooterJsonBrandAndLinksSsotTest` controlla solo `config/local/fixcity`, non le altre due copie (B-20).
- Non piu vere: dcp-28 (multa) e dcp-30 (vantaggi) sono "done" ma 11 su 27 e 13 su 30 dei file Blade citati non esistono piu (struttura consolidata da DCP-100, ancora in-review). Dcp-221 cita un file di build con hash che non c'e' piu.
- Non riproducibili: dcp-125, 186, 26, 23 citano strumenti di scratch (`dcp124-boxes.mjs`, `crawl.py`, `dcp26-explain.mjs`, `tokdiff.mjs`, `outline.mjs`) che non sono nel repo (B-26).
- Stato errato in senso opposto: dcp-102 e' "ready-for-dev" ma il corpo dice "Esito ... Fatto"; `tickets/track.json` ha `"middleware": ["throttle:10,1"]` e il test `Modules/Fixcity/tests/Unit/TicketTrackPageJsonGuardTest.php` esiste. Circa 65 story non chiuse hanno sezioni "Esito/Verifica" (esempi: dcp-104, 129, 143, 195, 94).

Story aperte importanti con stato reale (VERIFICATO dal frontmatter e dal corpo):

- DCP-145 (licenza/avvisi, LG-08): ready-for-dev, nessun AC attuato (B-04).
- DCP-220 (`social.php`, `notifications.php` assenti in tutte e 4 le lingue): ready-for-dev, il file manca ancora (B-17).
- DCP-32..35 (wizard): ready-for-dev con tutti gli AC non spuntati (B-24).
- DCP-158 e DCP-161 (cookiebar duplicati): ready-for-dev, i 6 file duplicati esistono ancora (B-19).
- DCP-166 (canonical/hreflang): in-progress; `components/layouts/seo-links.blade.php` esiste e funziona per costruzione, restano i punti di sitemap lato Seo.
- DCP-197 (repliche `/it/tests` in produzione): aperta (B-08).
- DC-001..037 (catalog): 37 stub generati da template, "ready-for-dev", criteri non spuntati, URL `192.168.1.40:8000` codificato; usano `bashscripts/design-comuni/verify-parity.mjs` che esiste.

### 2.3 Cosa dicono i gate di go-live sul tema

`bashscripts/tools/release/go-live-gate.py` (329 righe) + `bashscripts/tools/compliance/lg-design-scan.py` (66 righe) scansionano 12 pagine x 4 lingue = 48 combinazioni, solo GET (VERIFICATO).

- LEG-04 12/20: privacy (4) + terms (4) + note-legali (4) usano il blocco `legal_document`, che emette `data-element="legal-incomplete"` (`resources/views/components/blocks/legal-document/design-comuni.blade.php:47`). Il tenant ha solo `policy.md` (circa 600-700 byte, it/en/de/es); `terms.md` e' il segnaposto del template "Edit this file..." (it/en; assente in de/es, quindi cade sul segnaposto di radice); non esistono `legal-notes.md` ne' `media-policy.md`. `GetPublishedLegalDocumentAction` restituisce null per i segnaposto (VERIFICATO).
- Il gate controlla solo `legal-incomplete` (`go-live-gate.py:143-144`). Il blocco dichiarazione di accessibilita' usa un marcatore diverso, `accessibility-incomplete` (`components/blocks/accessibility-statement/design-comuni.blade.php:52`), non contato (B-02).
- LG-02 DPO, LG-06 data ultimo aggiornamento, LG-07 valutazione, LG-08 licenza: vedi B-03..B-06 (tutti VERIFICATI nel codice del tema).

### 2.4 Test del tema (non eseguiti)

- `tests/Unit`: 25 file di tipo "contratto" (cartelle Folio vietate, compilazione Blade, guardie JSON). `BladeTemplatesCompileTest` copre `pages` + `components/layouts` + `components/blocks` = 632 su 1068 template; restano 380 template vivi non coperti + 56 legacy. DCP-212 aveva misurato 529/963 il 2026-10-15, nel frattempo i blocchi sono cresciuti da 509 a 611.
- `tests/Feature`: 5 file. `ComunePagesTest` (25 test, 320 righe) usa `route('comune.homepage')` ai righe 21, 44, 65 e altre 7 rotte `comune.*`; quelle rotte non sono definite da nessuna parte (B-14).
- `tests/browser`: 12 spec Playwright; saltano senza `FIXCITY_STORAGE_STATE`. `a11y-axe.spec.mjs` copre 8 pagine ospite (/it, /en, login, register, track, faq, mappa-sito, tickets) a 375/768/1440 e le pagine autenticate, ma non le pagine legali, blog, eventi, servizi, dettaglio segnalazione, home de/es, 404; il banner cookie e' chiuso prima di axe (B-13).
- I test di pagina HTTP del front office stanno in `Modules/Fixcity/tests/Feature/Pages` (43 file: tre `PublicationGate`, `SeoLinksTest`, `ThemeAriaStructureTest`, `FooterLinksResolveTest`), non nel tema.
- `docs/bmad/verification-recovery.md` (2026-10-26) riporta Unit Sixteen 716 passati / 3422 asserzioni, browser 55 passati, suite radice 310 falliti / 4 saltati / 37 passati, PHPStan zero sui PHP modificati: RIPORTATO, non rieseguito. `docs/bmad/evidence/` contiene 36 file (VERIFICATO ls); il contenuto di ciascuno non e' stato confrontato col report.

### 2.5 Build e asset (VERIFICATO)

- `package.json`: Vite ^8.3.4, `laravel-vite-plugin` ^3.2.0, installati. Script reale: `npm run prepare:static-assets && vite build`; `build1` e' il vecchio. `prepare-static-assets.mjs` scarica da jsdelivr lo sprite se assente (sprite bootstrap-italia 2.8.8 contro CSS derivato da 2.9.0).
- CSS compilato `app-CpYZ0zpb.css` = 3.463.021 byte (277.592 gzip); nessun `.gz` o `.br` in `public/`. Contiene 3 regole `url(https://flowbite.s3.amazonaws.com/...)` che vengono da blocchi del modulo Blog non usati da nessun JSON.
- 6 file di build vecchi sono tracciati in `public/` e non sono nel manifest: `app-CF5BdshJ.js`, `app-oju-fezL.css`, `app-test-Idn7ahTr.css`, `app.css`, `app2.js`, `theme-map-C2GBZ8X-.js`, in totale 6.530.702 byte.
- Font locali (`fonts-manifest.json`, woff2); nel CSS compilato non ci sono host `googleapis` o `gstatic`.

### 2.6 Sicurezza e privacy nel tema

- CSP (`laravel/config/security.php:132-141`): modalita' report-only; `script-src` con `'unsafe-inline'` e `'unsafe-eval'`; `style-src` ancora con `https://fonts.googleapis.com`; `font-src` con `https://fonts.gstatic.com`, benche' i font siano ora locali. Nessun nonce.
- `public_html/info.php` contiene `phpinfo();` ed e' tracciato in git, con `test.txt` e `serve-root-test.txt` (VERIFICATO con `git ls-files`). Se `public_html` e' la radice web, e' esposto; non ho richiesto l'URL (esposizione effettiva non determinata).
- `/it/tests/*`: 83 JSON `tests.*.json`; `pages/tests/[slug].blade.php` non ha nessun controllo d'ambiente; l'unica protezione e' `Disallow: /it/tests` in `public_html/robots.txt` (VERIFICATO).
- Terze parti in viste legacy: Leaflet da `unpkg.com` in `tickets/index.blade.php` e `components/municipal/office-map.blade.php`; `adsbygoogle` in `components/footer-comune.blade.php`; picsum/imgur/error404.fun in viste vecchie; chiamate `gtag(...)` in `performance.js`, `municipal/breadcrumb`, `pagopa-button` senza script gtag caricato (VERIFICATO con grep).

### 2.7 Codice dormiente nel tema (VERIFICATO)

- `app/Providers/ThemeServiceProvider.php` (316 righe) non e' registrato in nessun punto: nessuna occorrenza in `Modules/Xot`, `Modules/Cms`, `app`, `bootstrap`, `config`; il `composer.json` di Laravel include col merge-plugin solo `Modules/*/composer.json`; le classi del tema si autocaricano per `Xot\...\RegisterRuntimePsr4NamespacesAction`. Conseguenza: `routes/auth.php` (SPID/CIE), `config/sixteen.php`, i singleton di menu e i comandi sono dormienti. I blocchi `events/index` e `blog/index` leggono `config('sixteen.*.parity')`.
- `routes/web.php` (8 righe) non ha `<?php` e inizia con un commento; non e' PHP valido.
- 4 controller dormienti (Cie, Spid, Base, Logout), un componente Livewire `Appointment`.
- Superficie morta: `pages_old` (32 Blade, 45 file tracciati), `pages_old1` (8 Blade, 16 tracciati, non nel README), `bootstrap-italia/` (16), `layouts/bootstrap-italia*.blade.php` (4), viste di radice `home.blade.php` (contiene "Nome del Comune"), `terms.blade.php`, `policy.blade.php`, `dashboard.blade.php`, `show.blade.php`, `404.blade.php` (morta: 404 e 429 reali usano `fixcity::errors.*` da `bootstrap/app.php`), `.old` in `pages/[container0]` e `pages/tests`, `.gitkeep` in `pages/`, `components/layouts/navigation.blade.php` (non compila, DEC-38) e `app01.blade.php`.

### 2.8 Temi secondari (VERIFICATO)

- Nessun `xra.php` imposta Barthelemy, Meetup o TwentyOne. Gli altri tenant usano `DirectoryBs5`, `One`, `KlekooPub`, temi che non esistono nel repo.
- Riferimenti a Meetup: `Modules/Seo/tests/TestCase.php:53` forza `config(['xra.pub_theme' => 'Meetup'])` (DEC-49 dice che Geo e' stato corretto il 2026-10-15 e Seo no); un test di Tenant fa un mock con nome modulo 'Meetup'; `Modules/Cms` `TranslationsTest.php:96` legge `Themes/Meetup` header se esiste; `Modules/Cms/.../ResolvePageAction.php:201` cita `Modules\Meetup\Models\Profile`.
- Riferimenti a TwentyOne: `Modules/Notify` `MailLayoutRenderTest` imposta `pub_theme=TwentyOne` e ritorna subito (test vacuo) perche' `Themes/TwentyOne/resources/mail-layouts` non esiste; Xot registra in PSR-4 TwentyOne, Sixteen, i test di Sixteen e `Themes\Two` (inesistente); un test UI di `DarkModeToggle` salta se il tema manca.
- Barthelemy: 1892 dei suoi file stanno in `docs/shared-components` (hub di documentazione del vecchio `Themes/docs`, rinominato nel commit `bc58be108`).

---

## 3) Lacune

Ordinate per gravita' (prima BLOCCA go-live, poi QUALITA', poi RIFINITURA). Taglia: S fino a mezza giornata, M fino a 3 giorni, L oltre. "Chi": sviluppo = team tecnico; ente / titolare = chi risponde del servizio e dei testi; fornitore = DPO, legale o consulente esterno. I numeri DCP delle story non sono assegnati: usare lo slug e prendere il numero con il controllo che blocca (memoria `story-number-race-check-must-gate`).

Totale: 40 lacune. BLOCCA go-live 9, QUALITA' 25, RIFINITURA 6.

### 3.1 BLOCCA go-live

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Taglia | Slug story |
|---|---|---|---|---|---|---|---|
| B-01 | Testi legali non pubblicati: privacy, condizioni d'uso e note legali mostrano l'avviso "testo non completo" in tutte le lingue. LEG-04 12/20. Il tema e' pronto, manca il contenuto (`policy.md` di 600-700 byte, `terms.md` segnaposto, `legal-notes.md` e `media-policy.md` assenti, de/es senza `terms.md`) | `legal-document/design-comuni.blade.php:47`; `go-live-gate.py:143-144`; `laravel/config/local/fixcity/lang/{it,en,de,es}/policy.md` e `terms.md`; `GetPublishedLegalDocumentAction.php` | BLOCCA go-live | titolare scrive/approva (fornitore legale); sviluppo carica i file. DEC-02, DEC-17 (terms contro note-legali) | Modules/Fixcity (azioni), DEC-01 dati ente | M | `sixteen-testi-legali-pubblicati-4-lingue` |
| B-02 | Dichiarazione di accessibilita' vuota e fuori dal gate: `accessibilita.index.json` ha stato, data di valutazione, email di feedback e URL dichiarazione AgID a null (solo `procedure_url` valorizzato). Il blocco mostra `accessibility-incomplete`, che LEG-04 non conta: il gate puo' risultare verde con la pagina incompleta. Solo 3 guardie Pest in Fixcity la vedono | `accessibility-statement/design-comuni.blade.php:52`; `go-live-gate.py:143-144`; `laravel/config/local/fixcity/database/content/pages/accessibilita.index.json`; `Modules/Fixcity/tests/Feature/Pages/AccessibilityDeclarationPublicationGateTest.php` | BLOCCA go-live | titolare compila (obbligo AgID di pubblicazione); sviluppo estende il gate | DEC-04 (contatto feedback, security.txt), audit a11y manuale (NVDA non verificato, RIPORTATO in memoria) | M | `sixteen-dichiarazione-accessibilita-e-gate-cieco` |
| B-03 | LG-02: nessun campo DPO in tutto il tema. `footer.json` ha `contact` tutto vuoto; solo chiavi lang in Fixcity (`privacy.php`, 'dpo') | `footer.json` (sections); `grep` senza esito nei blocchi del tema; DEC-06 | BLOCCA go-live | titolare designa il DPO e fornisce i dati; sviluppo espone il campo | DEC-06, B-01 (la privacy deve citarlo) | S | `sixteen-dpo-nel-footer-e-nella-privacy` |
| B-04 | LG-08: manca la licenza/attribuzione di terze parti. Nessun `LICENSE`/`NOTICE` in Sixteen e in Modules/Fixcity (Gdpr e Rating ce l'hanno), `composer.json` dichiara MIT, `components.css` ha solo un'intestazione BSD-3 parziale, `docs/compliance/` ha 4 file e nessun `third-party-notices.md`, il footer non rimanda a una pagina licenza/crediti. Sprite da bootstrap-italia 2.8.8 contro CSS 2.9.0 | assenza di file (ls); `resources/scripts/prepare-static-assets.mjs`; `docs/stories/` DCP-145 (ready-for-dev); DEC-05 | BLOCCA go-live | titolare sceglie la licenza del prodotto (DEC-05); sviluppo redige avvisi e pagina | DEC-05; B-30 per origine sprite | S | `sixteen-licenza-e-avvisi-terze-parti` |
| B-05 | LG-07: il widget "valuta questa pagina" non salva nulla. `dc-rating` lancia `document.dispatchEvent(new Event('feedback-submit'))` e nessun ascoltatore esiste in `resources` o `app`. La vista `rating/default.blade.php` ha anche testo italiano fisso. `Modules/Rating` esiste (polimorfico) e non e' usato | `resources/js/theme/design-comuni-home.js:108` (unica occorrenza di `feedback-submit`); `components/blocks/rating/default.blade.php`; `components/blocks/partials/dc-rating.blade.php` | BLOCCA go-live | titolare decide se la valutazione e' richiesta e dove conservare il dato; sviluppo collega Modules/Rating con una Action | DEC-20; Modules/Rating; B-36 (testi fissi); privacy B-01 (nuovo trattamento) | M | `sixteen-valutazione-pagina-persistita` |
| B-06 | LG-06: "ultimo aggiornamento" mostrato solo dai blocchi articolo e dichiarazione accessibilita'. Solo 5 dei 111 JSON di pagina hanno `updated_at` | `grep -l updated_at` sui JSON in `config/local/fixcity/database/content/pages`; DEC-21 | BLOCCA go-live | titolare decide la fonte della data (JSON, git, CMS); sviluppo applica al guscio | DEC-21; Modules/Cms | M | `sixteen-data-ultimo-aggiornamento-per-pagina` |
| B-07 | `public_html/info.php` (`phpinfo();`) e due file di prova sono tracciati in git nella radice web. Rivela versioni, percorsi, estensioni | `git ls-files public_html/info.php public_html/test.txt public_html/serve-root-test.txt` | BLOCCA go-live | sviluppo rimuove con commit in avanti; titolare conferma che non servono | nessuna (e' fuori dal tema ma e' il tema che gira su quella radice) | S | `rimuovere-info-php-e-file-di-prova-dalla-radice-web` |
| B-08 | Le ~83 repliche `/it/tests/*` sono servite senza controllo d'ambiente: `pages/tests/[slug].blade.php` non filtra; protezione solo `Disallow: /it/tests` in `robots.txt` (non e' un controllo d'accesso). In produzione sono pagine di prova pubblicamente indicizzabili da link esterni | `resources/views/pages/tests/[slug].blade.php`; `public_html/robots.txt`; DEC-18, DCP-197 | BLOCCA go-live | titolare decide se esistono in produzione (DEC-18); sviluppo mette il gate d'ambiente | DEC-18; Seo (sitemap deve escluderle) | S | `sixteen-repliche-tests-solo-in-ambienti-non-produttivi` |
| B-09 | Segnaposto visibili: `header.json` ha regione `name: "Demo"`, `url: ""`, titolo "Vai al portale {Nome della Regione}"; `pages/segnalazione-dettaglio.json` ha titolo "... - Nome del Comune"; la vista di radice `home.blade.php` contiene "Nome del Comune". Resa effettiva non verificata | `config/local/fixcity/database/content/sections/header.json`; `.../pages/segnalazione-dettaglio.json`; DEC-01, DEC-16 | BLOCCA go-live (resa: non determinata) | titolare fornisce nome ente, URL, dominio; sviluppo sostituisce | DEC-01, DEC-16 (nome/dominio) | S | `sixteen-segnaposto-ente-in-header-e-titoli` |

### 3.2 QUALITA'

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Taglia | Slug story |
|---|---|---|---|---|---|---|---|
| B-10 | CSP solo in report-only, con `'unsafe-inline'` e `'unsafe-eval'` negli script, senza nonce; `style-src` e `font-src` ancora aprono host Google benche' i font siano locali. Il livello di protezione da XSS e' di fatto zero | `laravel/config/security.php:132-141`; `app/Http/Middleware/SecurityHeaders.php` | QUALITA' | titolare/sicurezza decide la strategia (build CSP di Alpine, nonce); sviluppo esegue | Livewire/Alpine; B-18 (terze parti legacy) | L | `sixteen-csp-enforce-senza-unsafe` |
| B-11 | Nessuna CI esegue i test del tema: `.github/workflows/ci.yml` usa `php artisan test ... \|\| true` e `phpstan ... \|\| true`, costruisce soltanto il tema; il repo del tema ha `.github/workflows` vuoto. `page-directory-structure.md:129-131` lo ammette ("Nessuna CI lo esegue") | `.github/workflows/ci.yml`; `docs/page-directory-structure.md:129-131` | QUALITA' | sviluppo | decisione se rendere bloccante (cosa accade con la baseline rossa di 310 test, RIPORTATO) | M | `sixteen-ci-test-unit-e-axe-bloccanti` |
| B-12 | PHPStan non analizza i temi: `laravel/phpstan.neon:18` ha `# - ./Themes/` commentato (analizza solo `./Modules/`). Il tema ha 83 PHP. La config e' protetta (owner) | `laravel/phpstan.neon:16-19`; DEC su configurazione PHPStan (da proporre) | QUALITA' | titolare della config (owner); sviluppo corregge poi i residui (22 errori noti, RIPORTATO) | modifica di `phpstan.neon` vietata agli agenti | M | `sixteen-copertura-phpstan-temi` |
| B-13 | Copertura axe parziale: 8 pagine ospite + autenticate; mancano pagine legali, blog, eventi, servizi, dettaglio segnalazione, home de/es, 404; il banner e' chiuso prima della scansione, quindi non si testa il banner stesso. Verifica a mano con screen reader non fatta (RIPORTATO) | `tests/browser/a11y-axe.spec.mjs` | QUALITA' | sviluppo; fornitore a11y per la verifica manuale | B-02; B-11 | M | `sixteen-axe-su-tutte-le-pagine-pubbliche` |
| B-14 | Test che non possono passare: `tests/Feature/ComunePagesTest.php` (25 test) usa rotte `comune.*` definite da nessuna parte (righe 21, 44, 65 e altre); `BootstrapItaliaComponentsTest` (21 test) mira a componenti legacy; `SegnalazioniElencoPageTest` cerca su `/it` stringhe ('Elenco segnalazioni', 'nav-tabs', 'map-lit', 'Filtra') assenti da `home.json` (esito: non determinato). Saltano solo se manca la classe Ticket | `tests/Feature/ComunePagesTest.php:21,44,65`; `tests/Feature/SegnalazioniElencoPageTest.php`; `tests/Unit/BootstrapItaliaComponentsTest.php` | QUALITA' | sviluppo (porta i test al contratto reale o li ritira con commit in avanti; non si cancellano senza approvazione) | B-15 (rotte dormienti), B-32 | M | `sixteen-test-feature-allineati-al-contratto-json` |
| B-15 | `ThemeServiceProvider` (316 righe) non e' registrato: SPID/CIE (`routes/auth.php`), `config/sixteen.php`, menu e comandi del tema sono dormienti; 4 controller e un componente Livewire mai raggiunti; `routes/web.php` non e' PHP valido. Il README lo cita come il provider del tema (`docs/README.md:49`) | `app/Providers/ThemeServiceProvider.php`; `routes/web.php` (8 righe senza `<?php`); grep senza esito in Xot, Cms, app, bootstrap, config | QUALITA' | titolare decide se SPID/CIE sono requisiti (DEC da proporre: vedi domanda 4); sviluppo registra o rimuove | PRODUCT.md (login SPID/CIE promessi?); regola "no nuovi controller" | M | `sixteen-codice-dormiente-registra-o-ritira` |
| B-16 | `id="main-container"` duplicato: il layout `components/layouts/app.blade.php:19` lo mette su `<main>` e 66 file di blocchi/viste lo emettono di nuovo. Rompe HTML valido, il salto contenuto (`href="#main-container"`, riga 14) e il criterio WCAG 4.1.1 | `components/layouts/app.blade.php:14,19`; `grep -rl 'id="main-container"' resources/views` (66) | QUALITA' | sviluppo | B-13 (axe lo rileva) | S | `sixteen-id-main-container-unico` |
| B-17 | Traduzioni incomplete: it 43 file (~1607 chiavi), en 43 (~1537), de 17 (~645), es 17 (~572). A de/es mancano 26 file (`segnalazione.php`, `404.php`, `ticket.php`, molti enum). `social.php` e `notifications.php` non esistono in nessuna lingua (DCP-220). Il fallback `it` nasconde le mancanze (memoria `i18n-fallback-it-hides-missing-translations`) | `ls laravel/Themes/Sixteen/lang/*`; story DCP-220 | QUALITA' | sviluppo; fornitore traduzioni per de/es | DEC lingue attive (domanda 9); B-01 | M | `sixteen-lang-de-es-allineate-a-it` |
| B-18 | Terze parti e residui in viste legacy: Leaflet da `unpkg.com` (`tickets/index.blade.php`, `municipal/office-map.blade.php`), `adsbygoogle` (`footer-comune.blade.php`), picsum/imgur/error404.fun, `gtag()` senza script caricato (`performance.js`, `municipal/breadcrumb`, `pagopa-button`), URL fisso `https://fixcity.eu/it/tickets/create` in `hero/homepage.blade.php`. Se la vista e' raggiunta, il visitatore invia dati a terzi | `grep -rn` su `resources/views` e `resources/js` | QUALITA' | sviluppo ritira o sostituisce; titolare conferma dominio (DEC-16) | B-10, B-32 | S | `sixteen-rimuovere-terze-parti-da-viste-legacy` |
| B-19 | Cookiebar duplicati: `partials/cookie-bar.blade.php`, `components/feedback/cookiebar`, `components/ui/cookiebar`, `components/utilities/ui/cookiebar`, `components/bootstrap-italia/cookiebar`, `bootstrap-italia/cookiebar` (e riferimenti in `blocks/blog/detail`). DCP-158 e DCP-161 (due story sullo stesso tema, entrambe ready-for-dev) non eseguite. Il banner vero e' del modulo Gdpr (memoria DCP-153) | `grep -rln cookie-bar` su `resources/views`; frontmatter di dcp-158, dcp-161 | QUALITA' | sviluppo (una sola implementazione, quella Gdpr) | Modules/Gdpr; B-13 | S | `sixteen-cookiebar-unica-via-gdpr` |
| B-20 | Footer: 3 copie del JSON (`config/local`, `config/eu`, `Modules/Fixcity/config`) identiche oggi ma la guardia `FooterJsonBrandAndLinksSsotTest` controlla solo `config/local/fixcity`; la pagina `terms` non e' linkata nel footer (legal_links: privacy, note-legali, accessibilita; bottom: media-policy, mappa-sito, cookie-policy, gestisci cookie); strutture `columns` e `groups` duplicate; `social` e `eu_logo` vuoti (DEC-03 gestito per rimozione) | `laravel/config/*/fixcity/database/content/sections/footer.json`; test citato | QUALITA' | sviluppo; titolare per DEC-17 (terms contro note-legali) | DEC-17, DEC-03; Modules/Fixcity | S | `sixteen-footer-tre-copie-una-guardia` |
| B-21 | `components/layouts/main.blade.php`: la meta description ripiega sul nome dell'app se la pagina non ha descrizione. Quattro pagine non di prova non hanno descrizione completa in 4 lingue: `tickets/create`, `tickets/confirmation`, `area-personale/seguite`, `area-personale/pratiche` | `components/layouts/main.blade.php`; JSON in `config/local/fixcity/database/content/pages` | QUALITA' | sviluppo; titolare per i testi | DCP-147 (PageDescription) | S | `sixteen-description-su-ogni-pagina` |
| B-22 | Soft-404 su blog ed eventi: `/it/blog/slug-che-non-esiste` risponde 200 con titolo "Novita'" perche' ripiega sul template `blog.view`. Documentato come "difetto noto, non corretto". La correzione tocca `Cms\ResolvePageAction`, file condiviso | `docs/page-directory-structure.md:116-118` (RIPORTATO, non rieseguito) | QUALITA' | sviluppo Cms con story propria; il tema solo documenta | Modules/Cms; DCP-273 | S | `cms-404-reale-per-articolo-inesistente` |
| B-23 | Cosa significa "parity" oggi non e' deciso. Memoria di progetto: main ~99,8% al 2026-10-13. `docs/bmad/parity-status.md` (draft, 2026-10-13): pixel 48,3% sulla home, 75,6% su FAQ. `docs/00-IMPLEMENTATION-STATUS.md` (2026-04-02): parita' HTML 99,8%, copertura classi CSS 73%. Dopo il reframe la home non replica piu' il Design Comuni | `docs/bmad/parity-status.md`; `docs/00-IMPLEMENTATION-STATUS.md`; `home.json`; `16-homepage-civic-reframe.css` | QUALITA' | titolare/prodotto decide a cosa si misura la parita' (home reale o replica `/it/tests/homepage`) | domanda 5; B-08 | S | `sixteen-definizione-parita-design-comuni` |
| B-24 | Story del wizard DCP-32..35 ancora ready-for-dev con tutti gli AC non spuntati; la memoria riporta pixel 99,4-99,7% sul passo 2 (RIPORTATO, non rimisurato). Il codice del wizard sta in Modules/Fixcity (non nel tema) e c'e' stata un'altra sessione sul wizard | story DCP-32..35 in `docs/bmad/stories` e `docs/stories`; numeri ambigui Sixteen contro Fixcity | QUALITA' | sviluppo chiude con misura riprodotta | Modules/Fixcity; B-26 (tool di misura assenti) | M | `sixteen-wizard-chiusura-ac-con-misura` |
| B-25 | Igiene delle story: numeri DCP doppi in molte coppie e terne (dcp-07..26, 32, 158, 161, 162, 163 in tre copie, 166); 26 senza stato; 4 forme di "in corso" (`in-progress`, `in_progress`, `active`, `open`); circa 65 story non chiuse ma con esito scritto; dcp-28 e dcp-30 "done" con file inesistenti; 37 stub DC-001..037 generati; `bmad-remaining-work.md` dice che le story stanno in `docs/stories/` mentre 235 sono in `docs/bmad/stories`. Non cancellare ne' rinumerare: collegare o marcare superseded | `scratchpad/stories-status.txt` (305 righe); frontmatter dei file citati | QUALITA' | sviluppo (BMAD) | regola "non cancellare/rinumerare" (`bashscripts/docs/modular-bmad-story-policy.md`); memoria `sprint-status-semantics` | M | `sixteen-registro-story-stati-reali` |
| B-26 | Strumenti di misura citati dalle story ma non nel repo: `dcp124-boxes.mjs`, `crawl.py`, `dcp26-explain.mjs`, `tokdiff.mjs`, `outline.mjs`. Le misure di parita' non sono riproducibili. Esistono invece `bashscripts/design-comuni/verify-parity.mjs` e `laravel/compare-html.php` | story dcp-125, 186, 26, 23 | QUALITA' | sviluppo recupera o sostituisce con uno strumento in `bashscripts/tools/<area>/` e lo documenta | regola script root (`bashscripts/docs/root-script-policy.md`) | M | `sixteen-strumenti-di-misura-parita-nel-repo` |
| B-27 | Layout parallelo `components/layouts/design-comuni.blade.php` (22 righe, quasi gemello di `app`), usato da `[slug0]` e altri, e `design-comuni-authenticated`, `design-comuni-bare`: contraddice la regola `wiki/rules/no-parallel-design-comuni-layout.md`. La decisione e' dichiarata aperta nel README (`docs/README.md:72`) e nella story docs-studio | `components/layouts/design-comuni.blade.php`; `docs/wiki/rules/no-parallel-design-comuni-layout.md` | QUALITA' | titolare tecnico decide (nuova DEC); sviluppo unifica | B-16, B-28 | M | `sixteen-un-solo-layout-pubblico` |
| B-28 | Lo shell `pages/[container0]/[slug0]/index.blade.php` e' grosso e ha casi speciali (auth-login, auth-register, ticket-track, ticket-detail con chiamata a `BuildPublicTicketDetailAction`): contraddice il principio "shell sottile". Il soft-404 `abort_if($item === null && $cmsPage === null, 404)` c'e' (DCP-126 verificato) | `pages/[container0]/[slug0]/index.blade.php` | QUALITA' | sviluppo | B-27; regola Folio senza pagine semantiche | M | `sixteen-shell-folio-sottile` |
| B-29 | La guardia di compilazione copre 632 template su 1068; 380 vivi non coperti. Un errore Blade in un blocco nuovo si scopre a runtime (precedente: `@lang` dentro `{{ }}`, memoria `blade-lang-directive-inside-echo-breaks-compile`) | `tests/Unit/BladeTemplatesCompileTest.php`; DCP-212 | QUALITA' | sviluppo | B-32 (decidere quali legacy escludere), DEC-38 | S | `sixteen-compilazione-blade-tutta-la-superficie-viva` |
| B-30 | Artefatti di build: CSS da 3,46 MB senza precompressione `.gz`/`.br`; 6 file stantii tracciati in `public/` (6,5 MB) non nel manifest; 3 URL `flowbite.s3.amazonaws.com` nel CSS da blocchi Blog non usati; sprite da una versione diversa dal CSS | `public/assets/`; `git ls-files public`; manifest; `prepare-static-assets.mjs` | QUALITA' | sviluppo | B-04 (origine sprite); DEC-40 (build) | S | `sixteen-build-pulizia-e-precompressione` |
| B-31 | Build e documenti in contrasto: README dice `npm run build` = vite build + copy + `view:clear`, `package.json` fa `prepare:static-assets && vite build` (e `copy` lavora su `docs/Main_files`). DEC-41 propone di restare su Vite 7 ma Vite 8.3.4 e' gia' installato: la decisione e' superata dai fatti | `docs/README.md:63`; `package.json`; DEC-40, DEC-41 | QUALITA' | titolare tecnico chiude DEC-41 (presa d'atto); sviluppo riscrive la guida | build INDEX | S | `sixteen-guida-build-allineata-vite-8` |
| B-32 | Superficie morta mescolata a quella viva (vedi 2.7): `pages_old` 45 tracciati, `pages_old1`, `bootstrap-italia/`, layout legacy, viste di radice, `.old`, `navigation.blade.php` che non compila (DEC-38). Aumenta rumore, falsi positivi nei grep e costo del build | elenco in 2.7 | QUALITA' | titolare approva il ritiro; sviluppo con commit in avanti (vietato rollback come scorciatoia) | DEC-38; regola `git-forward-only`; test 56 legacy esclusi | M | `sixteen-ritiro-viste-legacy` |
| B-33 | Dipendenze dei test dei moduli da temi non usati: Seo `TestCase.php:53` forza `pub_theme=Meetup` (che non ha pagine Folio), Tenant mock 'Meetup', Cms legge `Themes/Meetup`, `ResolvePageAction.php:201` cita `Modules\Meetup\Models\Profile`, Notify `MailLayoutRenderTest` e' vacuo con TwentyOne. I test passano o falliscono per il motivo sbagliato | righe citate in 2.8 | QUALITA' | sviluppo dei moduli interessati | DEC-49 (Geo fatto, Seo aperto); B-38, B-39 | S | `moduli-test-senza-temi-inesistenti` |
| B-34 | Documentazione del tema in contrasto e pesante: README con numeri vecchi (vedi sezione 4), `phpstan-compliance-status.md` falso, `00-IMPLEMENTATION-STATUS.md` del 2026-04, 429 md alla radice di `docs/`, 41 `.lock` e 41 `.old` in `docs/`, 416 MB; esistono due indici (`INDEX.md`, `00-INDEX.md`) | `docs/README.md`; `docs/phpstan-compliance-status.md`; `ls docs` | QUALITA' | sviluppo; migration map per spostare (memoria `docs-organizer-safe-link-fix-and-baseline`) | nomi file senza date/`-1.md` | M | `sixteen-docs-riordino-con-migration-map` |

### 3.3 RIFINITURA

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Taglia | Slug story |
|---|---|---|---|---|---|---|---|
| B-35 | `layouts/main.blade.php` (vista legacy) ha un `<script>` dentro un `<script>` per il toast flash: errore JS se il flash e' presente. Non e' chiaro che la vista sia usata (quella usata da `<x-layouts.main>` e' `components/layouts/main`) | `resources/views/layouts/main.blade.php` | RIFINITURA | sviluppo (ritirare con B-32) | B-32 | S | `sixteen-layout-legacy-main-toast` |
| B-36 | `blocks/rating/default.blade.php` ha testo italiano fisso ("Rating", "Valuta da 1 a N stelle"), fuori da `__()` | `components/blocks/rating/default.blade.php` | RIFINITURA | sviluppo | B-05, B-17 | S | `sixteen-rating-testi-tradotti` |
| B-37 | PWA dormiente: `pwa.js` e `sw.js` non sono importati da `app.js`; nessuna funzione offline | `resources/js/`; `app.js` | RIFINITURA | titolare decide se la PWA e' un requisito (domanda 10) | PRODUCT.md | S | `sixteen-pwa-decisione` |
| B-38 | Barthelemy: guscio di 1976 file senza codice (1716 md, 1892 in `docs/shared-components`), mai configurato. Proposta di destinazione: RITIRARE (riduzione a un README breve; documentazione generale dei temi nell'hub `Themes/docs`), con commit in avanti. E' una decisione, non l'ho eseguita | `find laravel/Themes/Barthelemy`; `docs/README.md`, `completion-backlog.md` del tema | RIFINITURA | titolare tecnico (DEC da proporre) | `Themes/README.md`; commit `bc58be108` | S | `temi-ritiro-barthelemy` |
| B-39 | TwentyOne: 851 file (261 Blade, 2 PHP, 2 test), README "non usato", referenze a `Modules\Predict` inesistente, repo separato importato da Predict. Proposta: MANTENERE come archivio dichiarato (marcarlo non attivo nel README e togliere la registrazione PSR-4 in Xot di `Themes\Two` inesistente e di TwentyOne se nulla lo usa). Meetup: proposta RITIRARE o RIDURRE, dopo aver corretto il test Seo (B-33). Sono decisioni | `Themes/TwentyOne/README.md`, `theme.json`, `composer.json`; `RegisterRuntimePsr4NamespacesAction.php` | RIFINITURA | titolare tecnico (DEC da proporre) | B-33; gitmodules.ini | S | `temi-ritiro-meetup-archivio-twentyone` |
| B-40 | Igiene del repo del tema: 51 file alla radice del tema tracciati (molte sonde `*.cjs/*.mjs`), `ruvector.db` binario tracciato, 77 file `.lock` tracciati, `public/` con 132 file tracciati, messaggi di commit quasi tutti "." (223 commit): la storia non spiega il perche' | `git -C laravel/Themes/Sixteen ls-files` e `git log` | RIFINITURA | sviluppo (`.gitignore`, spostare sonde in `bashscripts/tools/visual`) | regola nome `.md`; `.lock` da non toccare nei miei compiti, qui solo da segnalare | S | `sixteen-igiene-repo-e-messaggi-commit` |

---

## 4) Contraddizioni tra documenti e codice

| N | Documento | Dice | Codice / altro documento | Esito |
|---|---|---|---|---|
| 1 | `docs/README.md:119` | "PHPStan level: max" | `laravel/phpstan.neon:18`: `# - ./Themes/` commentato, analizza solo `./Modules/` | il tema non e' analizzato |
| 2 | `docs/bmad/bmad-remaining-work.md` (2026-10-26) | "Level 10, 0 errors (verified 2026-10-26)" | stessa config: i temi non rientrano | affermazione non provabile per il tema; riferita probabilmente ai moduli |
| 3 | `docs/phpstan-compliance-status.md` | "NOT APPLICABLE (Theme) ... doesn't contain PHP code" | 83 PHP in `app/` | falso |
| 4 | story `phpstan-zero-themes-debt` (stato "done") | residuo 22 errori, cita `Themes/docs/phpstan-non-analizza-i-temi.md` | quel file non esiste (`Themes/docs` ha solo `INDEX.md`, `README.md`) | "done" con residuo e fonte mancante |
| 5 | `docs/README.md:54` | 957 Blade; `blocks/` 166 voci / 508 Blade | 1068 Blade; 126 cartelle di blocco; 611 Blade nei blocchi | numeri del 2026-10-14 superati |
| 6 | `docs/README.md:56` | 79 PHP, 24 enum, 5 controller | 83 PHP in `app/`; 4 controller dormienti | numeri superati |
| 7 | `docs/README.md:58` | 15 Unit, 6 Feature, 10 browser | 25 Unit, 5 Feature, 12 browser | numeri superati |
| 8 | `docs/README.md:63` | `npm run build` = vite build + copy + `view:clear` | `package.json`: `prepare:static-assets && vite build` | guida non allineata |
| 9 | `docs/README.md:49` | il provider `Providers\ThemeServiceProvider` estende `XotBaseThemeServiceProvider` (presentato come attivo) | non registrato in nessun punto (VERIFICATO) | tema dormiente nella parte provider |
| 10 | `docs/page-directory-structure.md:124-125` | il "layout contract" e' `layouts/app.blade.php` e `layouts/main.blade.php` | i layout vivi sono in `components/layouts/*`; i `layouts/*` sono legacy (`main.blade.php` ha script annidato) | link al layout sbagliato |
| 11 | memoria `dc-parity-v2-state` e `parity-claims-need-gate-output`: main ~99,8% | `docs/bmad/parity-status.md`: home 48,3%, FAQ 75,6%; `00-IMPLEMENTATION-STATUS.md` (2026-04): 99,8% HTML, 73% classi CSS | la home e' stata ridisegnata (B-23) | "parity" va ridefinita |
| 12 | `docs/bmad/stories/dcp-102` stato `ready-for-dev` | corpo "Esito ... Fatto" | `tickets/track.json` ha il `throttle:10,1` e il test esiste | stato tracker errato |
| 13 | dcp-28 e dcp-30 "done" | 11 su 27 e 13 su 30 Blade citati | non esistono piu' (struttura consolidata da DCP-100, in-review) | story chiuse senza essere piu' vere |
| 14 | DEC-41 (proposta: restare su Vite 7) | | Vite 8.3.4 installato | decisione superata dai fatti |
| 15 | regola `no-parallel-design-comuni-layout.md` | niente `components/layouts/design-comuni.blade.php` | il file esiste e lo usa `[slug0]` | violazione dichiarata come "decisione aperta" |
| 16 | `docs/bmad/bmad-remaining-work.md` | le story vivono in `docs/stories/` | 235 su 305 sono in `docs/bmad/...` | indice sbagliato |
| 17 | `go-live-gate.py:143-144` e story LEG-04 | LEG-04 misura i testi legali incompleti | la dichiarazione di accessibilita' incompleta usa un altro marcatore | il gate puo' dare verde con una pagina obbligatoria vuota |
| 18 | dcp-222 (footer "SSoT") | la guardia garantisce il footer | la guardia legge una sola delle 3 copie | garanzia parziale |
| 19 | `docs/verification-recovery.md` (2026-10-26): Unit 716 passati | | `ComunePagesTest` in Feature non e' contato; esito Feature del tema: non determinato | il numero vale per la suite Unit |
| 20 | `Themes/TwentyOne/README.md`: "non usato" | | Xot lo registra in PSR-4; Notify ha un test con `pub_theme=TwentyOne` | usato come nome, non come tema |
| 21 | tutta la documentazione "pagina di test" | `/it/tests` e' per i test | nessun gate d'ambiente (B-08) | repliche pubbliche |

---

## 5) Documentazione mancante o da riscrivere

Percorsi proposti dentro `laravel/Themes/Sixteen/docs/` (nomi senza date, senza suffissi numerici; la data va nel frontmatter; prima di creare un `.md`: `python3 -I bashscripts/tools/docs/md-filenames.py name <percorso>`).

| Percorso proposto | Perche' |
|---|---|
| `compliance/third-party-notices.md` | B-04: elenco e licenze (Bootstrap Italia BSD-3, Alpine, Lit, DaisyUI, Flowbite, Leaflet, font) con versione e origine dello sprite |
| `compliance/legal-pages-content-contract.md` | B-01: chi scrive `policy.md`/`terms.md`/`legal-notes.md`/`media-policy.md`, per lingua, e cosa il gate considera "completo" |
| `compliance/accessibility-statement-publication.md` | B-02: campi obbligatori AgID, chi li compila, come il gate li verifica |
| `wiki/concepts/dormant-code-register.md` | B-15, B-32: elenco di provider, controller, rotte, viste legacy con destinazione decisa |
| `wiki/concepts/csp-enforce-strategy.md` | B-10: percorso da report-only a enforce, nonce, build CSP di Alpine, host da togliere |
| `build/INDEX.md` (riscrivere) | B-31: script reali, ordine, `prepare:static-assets`, origine sprite |
| `README.md` (riscrivere la sezione "Stato verificato") | sezione 4 punti 1, 5-9: numeri generati da un comando, non copiati; togliere "PHPStan level: max" |
| `phpstan-compliance-status.md` (riscrivere) | contraddizione 3: dire che i temi non sono analizzati e perche' |
| `00-IMPLEMENTATION-STATUS.md` (marcare superata o aggiornare) | contraddizione 11 |
| `testing-convention.md` (aggiornare) | inventario reale dei test, cosa gira in CI (nulla) e cosa e' rotto (B-14) |
| `wiki/concepts/parity-definition.md` | B-23: cosa si misura, con quale strumento e soglia |
| story superseded/link per dcp-28, dcp-30, dcp-102 | B-25: non cancellare, collegare a DCP-100 |
| `Themes/Barthelemy/docs/README.md` e `Themes/README.md` (aggiornare) | B-38, B-39: stato "ritirato/archivio" dopo la decisione |

---

## 6) Domande aperte per l'utente

1. Quando arrivano i testi legali (privacy, condizioni, note legali, media policy) e chi li approva? Senza di essi LEG-04 resta 12/20. Si decide `terms` o `note-legali` come pagina canonica (DEC-17)?
2. Chi e' il DPO e quali sono i recapiti da pubblicare (DEC-06)? Chi e' l'organo che firma la dichiarazione di accessibilita' e dove va il feedback (DEC-04)?
3. Quale licenza ha il prodotto (DEC-05) e dove vuole la pagina crediti?
4. SPID e CIE sono requisiti del go-live? Se si, il tema ha il codice ma non e' registrato: registrare o riscrivere nel modulo User? Se no, ritiro con commit in avanti. Proposta di nuova decisione.
5. Che cosa significa "parity" per la home attuale, che non e' piu' una replica del Design Comuni? Si misura `/it` o `/it/tests/homepage`? Proposta di nuova decisione.
6. Le pagine `/it/tests/*` devono esistere in produzione (DEC-18)? Se no, gate per ambiente.
7. La valutazione di pagina a stelle e' richiesta (DEC-20)? Se si, la salvo con `Modules/Rating` (nessuna nuova tabella) o serve un'altra sede? Serve nuovo trattamento nella privacy.
8. Da dove viene la data "ultimo aggiornamento" (DEC-21): campo JSON, data dell'ultimo commit, o CMS?
9. Lingue attive al go-live: solo it, o it/en/de/es? Decide se de/es vanno completate (26 file mancanti) o dichiarate non disponibili.
10. La PWA/offline e' un requisito? Altrimenti ritiro di `pwa.js` e `sw.js`.
11. Posso proporre al proprietario della config di includere `./Themes/` in PHPStan (la config e' protetta)? Come si tratta il residuo noto di 22 errori?
12. Strategia CSP: si accetta di restare in report-only per il go-live (con rischio dichiarato) o si vuole l'enforce prima? Se enforce, accettate il lavoro su nonce e build CSP di Alpine?
13. Temi secondari (decisioni, non eseguite): Barthelemy ritirato, Meetup ritirato o ridotto dopo la correzione del test Seo (DEC-49), TwentyOne tenuto come archivio dichiarato non attivo. Confermate?
14. Il dominio del servizio (DEC-16) e' deciso? Serve per l'URL fisso `https://fixcity.eu/...` in `hero/homepage.blade.php` e per i titoli segnaposto.
15. Si vuole una CI bloccante sui test del tema (B-11) anche se la suite radice ha una baseline rossa (310, RIPORTATO)? Proposta: bloccare solo `Themes/Sixteen/tests/Unit` e `a11y-axe`.

---

Nota finale sul metodo: ho aperto circa 75 file con Read/cat/sed (documentazione di orientamento, gate, story campione, layout, shell Folio, blocchi, test, JSON del tenant, config, workflow CI) e ho fatto ricerche con grep/find/ls/wc su migliaia di file. Il numero di file aperti e' una stima, non un conteggio esatto. I file temporanei di appoggio (`stories-status.txt`, `allfiles.txt`) sono nella cartella scratchpad, fuori dal repository.
