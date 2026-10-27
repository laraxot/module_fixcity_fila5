---
title: "Analisi di progetto E: Geo, Media, Rating, Comment, Event, Blog"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "analisi di progetto e: geo, media, rating, comment, event, blog audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

---
title: "Lacune gap-E: Geo, Media, Rating, Comment, Event, Blog"
type: analysis
tags: [gap-analysis, geo, media, rating, comment, event, blog]
created: 2026-10-26
---

# Lacune gap-E: Geo, Media, Rating, Comment, Event, Blog

Analisi in sola lettura del 2026-10-26. Nessun file del repository e' stato scritto o modificato, non ho eseguito
Pest, PHPStan, artisan, composer, npm, migrate ne' git che scrive. Ho usato solo Read, grep, find, ls, `git ls-files`,
`git log` e `git status`.

Legenda delle prove:

- VERIFICATO: l'ho letto o eseguito io in questa sessione (comando o file:riga indicato).
- RIPORTATO: lo dice una fonte (documento, story, registro) che non ho controllato io, oppure e' conoscenza generale di
  una politica esterna senza accesso di rete.
- non determinato: non sono riuscito a provarlo con i mezzi in sola lettura.

I numeri di altri documenti (percentuali, conteggi di test, "PHPStan a zero") non sono copiati: dove compaiono sono
marcati RIPORTATO e confrontati con quello che ho contato.

Nessun valore segreto e' riportato nel report (le credenziali FTP sono indicate solo per file e riga).

## 1. Perimetro e ownership per modulo

Il codice vive in `laravel/Modules/<Modulo>`. Geo, Blog e Comment sono repo git separati; Media, Rating ed Event sono
cartelle del repo radice o di altri repo, non determinato per ciascuno.

| Modulo | Cosa possiede | Chi lo consuma davvero (VERIFICATO) | Conteggi che ho misurato |
|---|---|---|---|
| Geo | Componenti mappa Lit (`map-lit`, `coordinate-picker-lit`, `map-filter-lit`), azioni geocoding, modelli Comune/Place, widget Filament mappa | Solo Fixcity: `Rules/FilterCoordinatesInRadius.php`, `TicketForm.php` (CoordinatePicker), `GeocodeTicketAddressAction.php`, `DemoMunicipalityProvider`, `blocks/ticket_list/map.blade.php`. Il tema Sixteen importa solo `map-lit.js`, `map-filter-lit.js`, `coordinate-picker-lit.js` (`Themes/Sixteen/resources/js/app.js` righe 13-15). Nessun uso da Cms, User, Tenant, Notify, Seo, Mobile | php 608 (app 246, test 106), md 2173 (docs 2056), 32 story, js/vue ~19 varianti `*-lit*` |
| Media | Spatie MediaLibrary, upload temporanei, conversioni, FFmpeg, UI admin libreria media | Fixcity (`Ticket` con collezioni `attachments` e `ticket`), Blog (upload immagini articolo). Nessuna rotta propria (`routes/web.php` e `api.php` vuoti) | php 251 (app 119, test 49), md 544, 36 story |
| Rating | Tabelle `ratings` (criterio) e `rating_morph` (voto), HasRating | Fixcity: voto cittadino sul ticket (`SubmitCitizenTicketRatingAction`), Blog `Article` (HasRating). Nessun altro modulo del repo | php 175 (app 73), 29 file Test (181 chiamate it/test), md 603, 161 story |
| Comment | Commenti su modelli commentabili, widget Filament, sanificazione, policy | Ticket (solo se `update` e' consentito), Blog `Article` e eventi (`Article` con `content_type=event`) nel tema Sixteen | php 296 (app 114), md 192, 28 story, 16 file test (44 test) |
| Event | Segnaposto dormiente | Nessuno: `module.json` ha `"active": 0` e `"providers": []` (riga 10), assente da `modules_statuses.json` | php 2, md 13, 0 story, 0 test |
| Blog | Articoli, categorie, banner, i contenuti editoriali di blog, eventi e notizie (`content_type`) | Tema Sixteen (blocchi `blog`, `events`, `news`), Comment, Rating, Seo (sitemap, lavoro non committato di un'altra sessione) | php 440 (app 187), md 181, 18 story, 6 file test |

Confini da rispettare (regole di progetto): logica in Actions (niente Services, niente nuovi Controller), dipendenze a
senso unico (Geo non deve conoscere Fixcity), pagine front office = JSON CMS + blocchi. Gli eventi sono per regola
`Article` con `content_type=event` (`bashscripts/ai/wiki/rules/fixcity-inbound-editorial.md`).

Lavoro non committato di altre sessioni da non duplicare (VERIFICATO da `git status` iniziale): Blog
`BlogServiceProvider.php`, `ThemeArticleQueries.php`, nuova `Actions/Article/ListPublishedArticlePathsAction.php`
(sitemap DCP-273); story `dcp-275-credenziali-in-chiaro-nei-docs-e-env-tracciati.story.md` nuova e non tracciata,
che non ho letto e che puo' sovrapporsi a E-01.

## 2. Stato reale con evidenze

### Geo

- Funziona e arriva in produzione: `map-lit` (cluster con leaflet.markercluster, popup ticket con `escapeHtml`,
  controlli con aria-label, marker `keyboard:true`), `coordinate-picker-lit` con catena di centratura (punto, centro
  esplicito, GPS solo in contesto sicuro, fuso orario con zoom 6, `geo.config.map.default_view` Italia 42.5/12.5/6,
  fallback), blocco del click sotto zoom 10. VERIFICATO: file `timezone-centers.js`, test
  `CoordinatePickerLocationChainTest.php`, `tests/js/default-view.test.mjs`, sonda
  `bashscripts/tools/visual/geo-picker-centering-probe.mjs` esistono. Il risultato "39/39" della sonda e' RIPORTATO.
- I dati della mappa vengono dal file statico `public_html/data/tickets.json`, generato da
  `Fixcity/app/Actions/GenerateTicketsJsonAction.php` (vedi E-08).
- Le Actions PHP di Geo sono per larga parte scheletri senza chiamanti in produzione: `GetMapMarkersAction.php:25`
  e `GetGeocodingSuggestionsAction.php:23` restituiscono `[]`; `ExportMapDataAction` restituisce stringa vuota/
  FeatureCollection vuota; nessun chiamante fuori dai test (grep). Le Actions Nominatim (`ReverseGeocodeAction`,
  `SearchPlacesAction`) non hanno chiamanti fuori dai test.
- La configurazione e' letta con chiavi sbagliate (E-13). Heatmap: `leaflet.heat` e' importato (`map-lit.js:288`)
  ma nessun `heatLayer` viene creato (E-37).
- Test: 106 file; 10 `skip` (per lo piu' chiamate `->skip(1)` su collezioni), 9 `todo`; `GeoApiTest` prova 7 rotte
  `/api/geo/*` che non esistono (`routes/api.php` e' interamente commentato). Non ho eseguito nulla.
- Documentazione: 2056 md sotto `docs/`, 544 al primo livello, 102 nomi base duplicati (ignorando maiuscole e
  separatori), 57 file `.old`; 32 story. La massa nasconde l'essenziale (E-35).

### Media

- Per le foto dei ticket il modulo e' di fatto solo Spatie MediaLibrary: `Ticket.php:423` dichiara due collezioni
  (`attachments` jpeg/png/pdf, `ticket` jpeg/png), nessuna conversione, nessun controllo antivirus, nessuna rimozione
  EXIF. Upload nel form: `TicketForm.php:307` (`maxFiles(5)`, `maxSize(10*1024)`).
- Disco: `config/local/fixcity/media-library.php` `disk_name = env('MEDIA_DISK','public')`, `MEDIA_DISK` non impostata
  nel `.env` (ho letto solo i nomi delle chiavi). Quindi disco `public`, cartelle per id sequenziale.
- Il README promette HLS, AVIF, invalidazione CDN, bitrate adattivo, "sanificazione dati EXIF" (riga 54): HLS, AVIF,
  invalidazione e bitrate adattivo hanno 0 corrispondenze in `Media/app` (VERIFICATO grep). Il README si dichiara
  gia' "da verificare" per HLS/AVIF/CDN/bitrate (righe 67-69) ma lascia l'EXIF come promessa.
- Test: 41 file `*Test.php`, 2 `skip` (DB `media` non disponibile), 2 `todo`; tre file minuscoli duplicati non vengono
  mai raccolti da phpunit (E-24).

### Rating

- Reale: il voto di soddisfazione del cittadino sul ticket. `SubmitCitizenTicketRatingAction.php` crea un
  `RatingMorph` dopo i controlli (valore 1-5, autenticato, proprietario, stato RESOLVED o CLOSED, `lockForUpdate`
  riga 57, gia' votato). Test: `SubmitCitizenTicketRatingActionTest`, `TicketCitizenRatingPromptWidgetTest`,
  `CitizenRatingOverviewWidgetTest` (esistono; esito non verificato).
- Il README descrive un altro prodotto: "10 classi fuori dal modulo estendono BaseRating" (Ptv, Performance,
  Progressioni...) e `Modules/Ptv/...`. VERIFICATO: nel repo non esistono quei moduli; solo `Rating` e `RatingMorph`
  estendono. Numeri del README (137 php, 20 file test, 125 casi) non coincidono con quelli misurati (73 php in app,
  29 file Test, 181 chiamate).
- 161 story: 59 done, 24 senza stato, 19 ready-for-dev, 12+3 in-progress, 12 draft, 12 backlog, 8+3 superseded,
  4 review (conteggio mio da frontmatter). 33 nomi contengono "phpstan"; l'Epic 1 "Scheda Management Workflows"
  (1.1, 1.3, 1.4, 1.5, 1.8, 1.9, 1.10) e' tutto `backlog` e riguarda flussi di valutazione di un altro prodotto,
  non le segnalazioni civiche. Quanta parte delle 161 e' realmente utile al progetto: solo il voto cittadino e' provato.
- Migrazioni: 3 che creano `ratings` (2023_01_01_000000, 2026_03_12_180000, 2026_06_16_000003) e 3 che creano
  `rating_morph` (2023_01_01_000005 con `foreignIdFor(User)`, 2026_07_15_120003 con `nullableUuidMorphs('user')`,
  2026_07_27_100005 di nuovo `foreignIdFor`): lo schema finale dipende dall'ordine, non determinato.

### Comment

- Il motore funziona e ha basi sane: `SanitizeCommentHtmlAction` usa Symfony HtmlSanitizer; policy `see()` mostra solo
  i commenti approvati; test su policy, azioni di approvazione, sanificazione, widget (16 file, 44 test, non eseguiti).
- Tutto e' auto-approvato (E-06). Il widget Filament espone proprieta' pubbliche modificabili (E-09).
- FO: commenti nei dettagli di ticket (solo se `update` consentito), blog (`blog/detail.blade.php:119-120`) ed eventi
  (`events/detail.blade.php:115-138`), con titolo "Commenti" scritto a mano in italiano.
- Le story cmt-010..013 ("FO blog/eventi/ticket") sono `ready-for-dev` ma i blocchi esistono: stati vecchi.

### Event

- Placeholder: un solo modello (`Models/Event.php`, 261 righe, con campi di scommessa `is_wagerable`,
  `bet_end_date`), una migrazione (`20260101000000_create_events_table.php`, FK `category_id` verso la tabella
  `categories` mentre le categorie Blog sono `blog_categories`), 13 documenti. Il README dice che e' dormiente e che
  per attivarlo mancano `EventModuleServiceProvider`, `RouteServiceProvider`, `EventServiceProvider`.
- Gli eventi pubblici reali vivono in Blog (`Article.content_type=event`, migrazione
  `2026_10_15_000001_add_content_type_to_articles_table.php`), come impone la regola editoriale.

### Blog

- Reale: indice blog e indice eventi come pagine CMS (`blog.index.json` 5809 byte, `events.index.json` 3143 byte),
  dettagli via `container0.view` + `ResolvePageAction`, blocchi Sixteen per blog, eventi, notizie, seeder demo,
  test `BlogFrontOfficeEventsTest` (99 righe).
- Mancano: location/indirizzo dell'evento, ICS, JSON-LD, iscrizione (grep 0 in Blog e nei blocchi), policy Filament
  (nessun `ArticlePolicy`, `CategoryPolicy`, `BannerPolicy` nel repo), sanificazione HTML dei paragrafi.
- Residui di un altro dominio: scommesse e mercato predittivo (`is_wagerable`, `wagers*`, `brier_score*`,
  `volume_play_money`, ordinamenti `paginatedByWagers`, `paginatedByVolume`), seconda tabella e modello `Comment`.

## 3. Lacune

Ordinate per gravita': 8 BLOCCA, 28 QUALITA', 4 RIFINITURA (totale 40). Dimensioni: S meno di mezza giornata,
M da mezza giornata a due giorni, L oltre. "Chi": sviluppo (codice), ente (Comune o titolare del servizio),
fornitore (terzo esterno), titolare (decisione di prodotto o privacy).

### Gravita' BLOCCA go-live

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Dim. | Modulo | Story proposta |
|---|---|---|---|---|---|---|---|---|
| E-01 | Credenziali FTP del disco `photos` (host, utente, password) in chiaro in 7 file di config tracciati in git. Nessun codice usa `disk('photos')`, quindi sono residui | VERIFICATO `git ls-files config` + grep: `config/com/geekpiu/filesystems.php`, `config/it/quaeris/filesystems.php`, `config/it/quaeris/manager/filesystems.php`, `config/it/quaeris/manager/quaeris/filesystems.php`, `config/it/quaeris/manager/quaeris/quaeris/filesystems.php`, `config/it/quaerisofficina/manager2/filesystems.php`, `config/test/fixcity/filesystems.php` (righe 81-93, host a riga 83). Ultimo commit sul file 2026-10-26 (`acf993edf5`). Config attiva `config/local/fixcity/filesystems.php` ha `photos` commentato. Valori non riportati | BLOCCA | Titolare (ruotare o chiudere l'account del servizio) poi sviluppo (rimozione dei file, valutare riscrittura storia) | Story `dcp-275` non tracciata (non letta); nuova DEC-53; `go-live-gate` non controlla segreti (E-36) | S (rimozione), M (pulizia storia) | Media (disco) / config radice | `credenziali-ftp-disco-photos-in-config-tracciati` |
| E-02 | Le foto dei ticket stanno sul disco `public` con cartelle a id sequenziale: chiunque conosca il pattern `/storage/{id}/{file}` le raggiunge, anche per ticket non pubblici (in moderazione, respinti). `MediaPolicy` e' solo a permessi (`media.view`), senza proprietario | VERIFICATO: `config/local/fixcity/media-library.php` `disk_name` = `env('MEDIA_DISK','public')`; `MEDIA_DISK` non impostata (solo nome chiave letto); symlink `public_html/storage` verso `laravel/storage/app/public`; cartelle `10`, `100`, `101`... in storage. Nome file originale conservato: non determinato | BLOCCA | Titolare (le foto sono visibili prima della moderazione?), sviluppo | DEC-37, nuova DEC-55; modulo Fixcity (Ticket); E-03 | M | Media | `foto-ticket-su-disco-privato-con-url-firmati` |
| E-03 | Il dettaglio pubblico espone gli originali con `getFullUrl()`; nessuna conversione/miniatura, nessuna rimozione di EXIF e GPS. Il commento in `ListPublicTicketsAction` ammette il problema ("finche' non c'e' una miniatura senza EXIF") ma l'endpoint di dettaglio lo aggira | VERIFICATO: `Fixcity/app/Actions/BuildTicketPublicDetailsPayloadAction.php:45`; `Ticket.php:423` `registerMediaCollections` senza `registerMediaConversions`; pagina Folio `pages/api/ticket-details/[ticket].blade.php` (`name('api.ticket-details')`, senza auth); `ListPublicTicketsAction.php:22-23`; nessun codice EXIF in `Media/app` e `Fixcity/app` (grep). jpegoptim `--strip-all` vale solo per le conversioni. Presenza reale di GPS nelle foto: non determinato (storage di sviluppo: 182 PNG, 0 JPEG) | BLOCCA | Titolare (privacy), sviluppo | DEC-37, DEC-55; Fixcity | M | Media | `miniature-senza-exif-per-foto-ticket` |
| E-04 | Nessuna attribuzione OpenStreetMap sulla mappa delle segnalazioni e sui picker: `attributionControl:false` e tile OSM senza opzione `attribution` | VERIFICATO: `Geo/resources/js/components/map/events.js:200`; `coordinate-picker.js:158`; `map-location-input-lit.js:111`; `geo-latlng-input.js:143`; `map-picker-lit.js:180`; `map/layers.js:4-6` (nessuna opzione attribution); bundle `public_html/themes/Sixteen/assets/theme-map-*.js` contiene `attributionControl:!1` e non contiene "OpenStreetMap contributors" (assenza verificata). Altre mappe del tema hanno il credito (`tickets/index.blade.php:198`, `pages/home.js:16`). Nota legale `Fixcity/resources/templates/legal-notes.en.md:27` cita OSM ODbL | BLOCCA | Titolare (dove mostrare il credito), sviluppo | DEC-14 (parzialmente coperta); E-05 | S | Geo | `attribuzione-osm-su-mappa-e-picker` |
| E-05 | Il browser di ogni visitatore parla direttamente con Nominatim (ricerca mentre si digita, reverse a zoom 18 con coordinate esatte) e con i tile pubblici OSM: nessun contratto, nessuna cache, nessun controllo di Referer/User-Agent, nessun limite lato client. La policy d'uso pubblica di Nominatim e OSM tile non prevede l'autocompletamento e limita l'uso intenso (RIPORTATO, conoscenza generale della policy, nessun accesso di rete). Dati personali (IP, testo cercato, posizione) ai terzi | VERIFICATO: `map/controls/search.js` (MIN_QUERY_LENGTH 3, debounce 350 ms, `limit=5`, senza countrycodes/viewbox; Escape/Enter alle righe 61-68), `map/reverse-geocode.js`, `coordinate-picker.js:214`, `map-picker-search.js`, `map-positioner-lit.js:394`, `geo-map-picker-field.js:79`; CSP `laravel/config/security.php:142-143` ammette tile OSM e `nominatim.openstreetmap.org`. Lo User-Agent a `coordinate-picker.js:215` non e' impostabile dal browser (comportamento generale, non provato) | BLOCCA | Titolare (provider: self-host, contratto, o OSM con limiti), fornitore, sviluppo | Nuova DEC-52; DEC-14; Gdpr (informativa); E-14, E-19 | L | Geo | `geocoder-e-tile-provider-politica-uso-e-privacy` |
| E-06 | Tutti i commenti sono auto-approvati e non esistono premoderazione, limite di frequenza, antispam o segnalazione abuso, su blog ed eventi pubblici e sui ticket | VERIFICATO: `laravel/config/comments.php:61`, `Modules/Comment/config/comments.php:91`, `laravel/config/local/fixcity/comments.php:60` tutte `'automatically_approve_all_comments' => true`; `ShouldAutomaticallyApproveCommentAction` restituisce true con quella chiave; grep di throttle, RateLimiter, honeypot, spam, report in `Comment/app` e `Comment/packages` = 0 file; `CommentPolicy::create` richiede solo utente autenticato; commenti mostrati in `blog/detail.blade.php:119-120` e `events/detail.blade.php:115-138`. Se i commenti non saranno attivi al go-live la gravita' scende a QUALITA' | BLOCCA (condizionato) | Ente/titolare (politica di moderazione, responsabile), sviluppo | Nuova DEC-54; Notify (avvisi ai moderatori); story cmt-019 | M | Comment | `moderazione-commenti-premoderazione-e-segnalazione` |
| E-07 | I widget "stelle" di valutazione pagina dicono "Grazie, il tuo parere ci aiutera' a migliorare" ma, per quanto verificato, non salvano nulla. Sono almeno 6, non 2 come nel registro | VERIFICATO per presenza (grep): `Themes/Sixteen/resources/views/components/blocks/segnalazioni-elenco/default.blade.php:106,202`, `search-results/default.blade.php:24`, `feedback/faq-rating.blade.php:5`, `feedback/survey.blade.php:3`, `service/detail.blade.php:120`, `feedback-section.blade.php`. Il salvataggio per ciascuno non e' provato. Il gate lo conosce come LG-07 (RIPORTATO, gate non rieseguito) | BLOCCA | Titolare (nascondere o salvare davvero) | DEC-20; DCP-169; Rating (solo `SubmitCitizenTicketRatingAction` e' reale) | S (nascondere), M (salvare) | Rating (+ tema Sixteen) | `voto-pagina-salvare-o-nascondere-dec-20` |
| E-08 | La mappa pubblica legge il file statico `public_html/data/tickets.json`, rigenerato solo da un pulsante admin e da seeder: nessun evento, schedulazione o osservatore. Un ticket nuovo non compare, e uno ritirato o reso non pubblico resta (con coordinate) nel file finche' qualcuno non rigenera | VERIFICATO: unici chiamanti di `GenerateTicketsJsonAction` in `Modules`, `app`, `routes`: `TicketResource/Pages/ListTickets.php:54`, `GeoJsonUpdateSeeder.php:21`, `InvestoGeoJsonSeeder.php:23`, `TicketDatabaseSeeder.php:41`; nessun riferimento in `routes`, `bootstrap`, `app/Console`. Un cron di sistema esterno che chiami altro: non determinato. Lettura: `LoadPublicTicketsGeoJsonAction.php:18` | BLOCCA (condizionato a E-02/E-03 e al trigger esterno) | Sviluppo; titolare per la regola di ritiro | Fixcity (genera), Geo (consuma); privacy dei ticket ritirati | M | Geo / Fixcity | `tickets-json-rigenerazione-automatica-e-ritiro` |

### Gravita' QUALITA'

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Dim. | Modulo | Story proposta |
|---|---|---|---|---|---|---|---|---|
| E-09 | `CommentsWidget` ha proprieta' Livewire pubbliche modificabili dal client (`$model`, `$commentableType`, `$commentableKey`, `$readOnly`, `$uiConfig`) senza `#[Locked]`; l'autorizzazione e' fatta solo se il modello ha una policy, ma Article ed Event non ne hanno; il testo ha solo `required,string,min:1` (nessun massimo). Sfruttabilita' non provata | VERIFICATO: `Comment/app/Filament/Widgets/Commentable/CommentsWidget.php:36-51` (proprieta'), `:293` (`Gate::getPolicyFor($commentable) !== null`); regole di validazione lette in precedenza nello stesso file. Livewire notoriamente accetta il valore dal client per le proprieta' pubbliche (RIPORTATO, comportamento del framework) | QUALITA' (sicurezza, alta) | Sviluppo | E-10 (policy Blog); `memory/filament-hidden-fields-are-client-controlled` | S | Comment | `commentswidget-proprieta-bloccate-e-autorizzazione` |
| E-10 | Nessuna policy per `Article`, `Category`, `Banner`: nelle risorse Filament di Blog l'autorizzazione dipende solo dall'accesso al pannello (senza policy Filament consente). Chi ha accesso al pannello puo' modificare e cancellare articoli (ruoli con accesso: non determinato) | VERIFICATO: `find` di `*Policy*` per Article/Category/Banner in tutto il repo = vuoto; `Blog/app/Models/Policies` non esiste. Story IM-BLOG-002 AC4 (autorizzazione, tenant, audit) non verificata | QUALITA' (sicurezza, alta) | Sviluppo; titolare per matrice ruoli | User (permessi), Tenant; IM-BLOG-002 | M | Blog | `policy-article-category-banner-blog` |
| E-11 | I paragrafi dei contenuti sono stampati con `{!! $paragraph !!}` senza sanificazione; Blog non ha sanitizzatore; `ImportArticlesFromByJsonTextAction` importa JSON esterno | VERIFICATO: `Themes/Sixteen/resources/views/components/blocks/events/detail.blade.php:95`, `blog/detail.blade.php:95`; in Blog solo `strip_tags` per conteggio parole. Esiste per altri contesti una regola C-3 con allowlist (RIPORTATO da memoria) non applicata qui | QUALITA' (sicurezza) | Sviluppo | Comment ha gia' `SanitizeCommentHtmlAction` da riusare | S | Blog | `sanificazione-html-contenuti-editoriali` |
| E-12 | Eventi: la data `published_at` futura e' ignorata (visibili subito) e l'indice mostra anche gli eventi passati; la story `events-public-index` (done) dice che l'indice mostra solo eventi con `event_start_date > now()` | VERIFICATO: `Blog/app/View/Composers/Queries/ThemeArticleQueries.php:175-180` (`paginatedEvents` usa `published()`), mentre i post usano `->publishedUntilToday()` (righe 28-30, 50-51, 83-84, 100-101, 163-164); `Themes/Sixteen/.../blocks/events/index.blade.php:109` chiama `paginatedEvents(9)`; `paginatedComingSoon` esiste ma non e' usato dal blocco; `BlogFrontOfficeEventsTest` non ha alcun caso passato/escluso (grep 0) | QUALITA' | Sviluppo; titolare per la regola "eventi passati" (archivio o nascosti) | IM-BLOG-003 (validazione date) | S | Blog | `eventi-pubblicazione-programmata-e-passati` |
| E-13 | La configurazione di Geo e' letta con chiavi inesistenti: il loader registra `geo.config.*`, il codice legge `geo.cache.*`, `geo.http_client.*`, `geo.rate_limits.*`, `geo.markers.*`, `geo.google_maps.*`. Cache mai attiva, limiti e timeout mai letti | VERIFICATO: `Geo/app/Actions/GoogleMapsAction.php:50,82`, `Actions/GoogleMaps/GetGeocodingDataAction.php:82-84`, `Models/Place.php:291`, `Adapters/GeoHttpClientBase.php`; solo `HasCoordinatePicker.php:180` legge correttamente `geo.config.map.default_view`; loader `XotBaseServiceProvider::registerConfig()` (RIPORTATO da memoria precedente, riletto) | QUALITA' | Sviluppo | Nessuna | S | Geo | `config-geo-chiavi-geo-config-vs-geo` |
| E-14 | Circa 14 chiamate `Http::get(...)` ai provider (Google, Mapbox, OpenCage, Bing, Here...) senza timeout, 6 `new Client()`, nomi delle chiavi incoerenti (`services.opencage.key` e `.api_key`, `services.google.maps_api_key`, `services.bing.maps_api_key`, `services.here.key`); il driver predefinito in `config.php` e' `google_maps` (a pagamento). Solo `GeoHttpClientBase` e le Actions Google impostano timeout/retry | VERIFICATO con grep (conteggi approssimati, non rieseguiti in questa pagina); `Geo/config/config.php` | QUALITA' | Sviluppo; titolare per provider e chiavi | E-05, E-13; README promette "9+ provider" | M | Geo | `geo-http-client-unico-timeout-e-chiavi` |
| E-15 | Azioni Geo mai usate e test che ne certificano il vuoto: `GetMapStatsAction`, `GetMapMarkersAction` (`return []` riga 25), `ExportMapDataAction` (CSV/GeoJSON/KML vuoti), `GetGeocodingSuggestionsAction` (`return []` riga 23, nemmeno il parametro `limit`), `GeocodeAddressAction` ("stub"). Il testo di DEC-47 e' in parte vecchio (la fusione e' chiusa, il repo Geo e' pulito) | VERIFICATO: nessun chiamante fuori dai test (grep); `tests/Unit/Actions/Map/MapActionsTest.php` asserisce `[]`/vuoto e ha un `->todo()` su "MERGE_HEAD" ormai falso; `git status --short` nel repo Geo pulito, nessun `MERGE_HEAD` | QUALITA' | Titolare decide (implementare o rimuovere) | DEC-47 | M | Geo | `azioni-geo-stub-implementare-o-rimuovere` |
| E-16 | Geocoding duplicato: Fixcity ha un proprio reverse Nominatim con cache e retry (`GeocodeTicketAddressAction`, UA `Fixcity/1.0`) mentre l'azione di Geo non ha timeout, usa `User-Agent: Xot/1.0`, legge `$data['display_name']` senza guardia, non ha parametro zoom; `SearchPlacesAction` lancia `RuntimeException` se non trova nulla | VERIFICATO: `Geo/app/Actions/Nominatim/ReverseGeocodeAction.php`, `SearchPlacesAction.php`; `Fixcity/app/Actions/GeocodeTicketAddressAction.php` | QUALITA' | Titolare/architetto (chi e' proprietario), sviluppo | DEC-46, DEC-48 | M | Geo (+ Fixcity) | `geocoding-unico-in-geo-fixcity-lo-riusa` |
| E-17 | Geo conosce Fixcity, contro la regola a senso unico: chiavi di lingua `fixcity::ticket.map.image.alt` e `fixcity::segnalazione.*`, selettore `#modal-disservizio`, percorso fisso a `/themes/Sixteen/design-comuni/assets/images/img-disservizio-thumbnail.png`, uso di `window.bootstrap?.Modal` con ripiego manuale (nessuna trappola del focus). Nessun import PHP `Modules\Fixcity` nel codice di Geo (pulito) | VERIFICATO: `Geo/resources/views/components/map-lit.blade.php:11`; `components/geolocation/address-field.blade.php`, `partials/address-field-geolocation-script.blade.php`; `map-lit.js` righe ~604-662; `map/map-lit-ticket-layer-ui.js:14,53,62` | QUALITA' | Sviluppo | Sixteen (modale), lang Fixcity | M | Geo | `geo-indipendente-da-fixcity-etichette-e-modale-iniettate` |
| E-18 | Accessibilita' della mappa incompleta: l'elemento `<map-lit>` ha `aria-label` ma nessun `role`; il campo ricerca non ha `role="combobox"`, `aria-expanded`, `aria-activedescendant` e i risultati (`role="listbox"/"option"`) non sono scorribili con le frecce (gestiti solo Escape ed Enter); modale ticket senza trappola del focus; alternativa testuale alla mappa e posizionamento del marker da tastiera nel picker: non determinati. Contrasto popup e marker con tastiera risultano trattati (story done) | VERIFICATO: `map/controls/search.js:61-68, 255-300`; `render-controls.js:35` (`role="status" aria-live="polite"`); `map-lit.blade.php:11`; marker `keyboard:true` in `map-lit.js` (~577); strumento `map-popup-contrast.cjs` e le story contrasto esistono, misura non rieseguita | QUALITA' | Sviluppo; ente per test con tecnologie assistive | `memory/a11y-audit-dcp143`, E-17 | M | Geo | `accessibilita-mappa-tastiera-ricerca-e-alternativa-testuale` |
| E-19 | Layer terzi non coperti dalla CSP e dalla privacy: ArcGIS (`World_Imagery`, `World_Topo_Map`), `tile.openstreetmap.fr/hot`, `tile.opentopomap.org` (picker). La CSP ammette solo `*.tile.openstreetmap.org`; oggi la CSP e' in sola segnalazione, quindi in modalita' bloccante quei layer smetterebbero di caricare. Se il selettore layer e' visibile agli utenti: non determinato | VERIFICATO: `map/layers.js:4-6`; `laravel/config/security.php:142` (img-src); `script-src` contiene `unsafe-inline` e `unsafe-eval` (VERIFICATO in precedenza) | QUALITA' | Titolare (quali layer tenere) | E-05, DCP-139 | S | Geo | `layer-mappa-terzi-csp-e-informativa` |
| E-20 | Dati dei comuni: `resources/json/comuni.json` e' una lista piatta di 4 comuni (Palermo...) mentre `LoadGeoDataAction` (`GeoDataConfig::JSON_PATH`, riga 44) richiede `{"regions":[...]}`; il README promette "ANPR Italia, dati Italia pronti". Quale dataset comuni c'e' davvero nel DB: non determinato | VERIFICATO: lettura di `comuni.json` e `LoadGeoDataAction`; `Geo/README.md` righe 38 e 44 | QUALITA' | Titolare/ente (fonte dati ufficiale e licenza), sviluppo | DEC-48 | M | Geo | `dataset-comuni-fonte-e-importazione` |
| E-21 | Test Geo poco affidabili: `GeoApiTest` prova 7 rotte `/api/geo/*` che non esistono; 9 `todo` (es. `GeoMapWidgetTest`, `RegionBusinessLogicTest`, `ReverseGeocodeActionTest` x2, `GetGeocodingSuggestionsActionTest` x2, `GoogleMapsClientTest`) e 10 `skip`; i 16 spec Playwright e il test JS esistono ma non li ho eseguiti; il flusso mappa-dati-popup (E-08) non ha un test end-to-end noto | VERIFICATO per esistenza e conteggi (grep `todo`/`skip`); esito dei test: non determinato | QUALITA' | Sviluppo | E-15 | M | Geo | `test-geo-allineare-a-rotte-e-azioni-reali` |
| E-22 | Cruft nel front-end e nel modulo Geo: circa 19 varianti `*-lit*.js`, molti `.vue`, tre file che definiscono lo stesso custom element (`coordinate-picker-lit` in tre file, `geo-map-lit` in tre), `popup-ticket-styles.js` (417 righe, mai importato), `geo-map-lit.js.bak-1777577793`, widget con suffisso `.disabled/.wip/.fila3`, migrazioni `.bak1`, file a 0 byte nella radice (`owner`, `record,`, `schedas`). Fixcity ha copie dei picker non incluse nella sua build. La story 12.1 "Ritiro Geo Http/Livewire Test" e' done ma `Geo/app/Http/Livewire/Test.php`, `Test.test`, `test.test` esistono ancora | VERIFICATO: `ls Geo/app/Http/Livewire/`; `find` e grep dei file elencati; vite del tema importa solo 3 componenti | QUALITA' | Sviluppo (con regola git forward-only: marcare superseded, non cancellare story) | Sixteen build | M | Geo | `geo-pulizia-varianti-js-e-file-morti` |
| E-23 | Upload senza antivirus e con regole larghe: nessuna scansione (ClamAV o simile) in Media/Fixcity; le regole di Livewire per l'upload temporaneo sono `null` (default: qualunque file fino a 12 MB), `preview_mimes` include svg, throttle predefinito 60,1; limiti reali solo nel form (jpeg/png/pdf, 10 MB, 5 file) | VERIFICATO: grep antivirus/ClamAV/virus = 0; `laravel/config/livewire.php:131`; `TicketForm.php:307`; `config/local/fixcity/media-library.php` (max 10 MB) | QUALITA' | Titolare (rischio accettato?), sviluppo | Gdpr, sicurezza | M | Media | `upload-foto-limiti-tipi-e-scansione` |
| E-24 | Test Media: due coppie di file duplicati per maiuscole/minuscole (`tests/filament/resources` e `tests/Filament/Resources`, `MediaConvertResourceTest` e `mediaconvertresourcetest`) con i minuscoli mai raccolti (suffisso `Test.php`); 3 test rossi e "ripresa di Pest" sono solo RIPORTATI dalla story `media-test-mai-eseguiti-e-doppioni-maiuscole`, che dice anche che `app/conversions` esiste ma oggi non c'e' piu' | VERIFICATO: `find` dei file duplicati; assenza di `app/conversions`; esito rosso: non determinato | QUALITA' | Sviluppo | Nessuna | S | Media | `media-test-doppioni-e-raccolta` |
| E-25 | Immagini editoriali senza testo alternativo e senza dichiarazione dei diritti: `Article` non ha un campo alt per l'immagine principale (nelle schede evento l'alt e' il titolo); la story inbound `IM-MEDIA-001` (alt, licenza, decorativa o informativa) e' in `backlog` | VERIFICATO: `Themes/Sixteen/.../blocks/events/index.blade.php` (~riga 136, `'alt' => $title`); assenza di campo alt in `Article` (grep); stato story letto | QUALITA' (accessibilita') | Titolare (policy di licenza), sviluppo | Regola `fixcity-inbound-editorial`; Blog; Media | M | Media (+ Blog) | `immagini-editoriali-alt-e-diritti` |
| E-26 | Rating: ambito e documentazione non coincidono con la realta'. README descrive consumatori inesistenti (Ptv, Performance...), numeri vecchi, badge "PHPStan 0 errori verificato 2026-09-01" (RIPORTATO); 161 story di cui molte igiene (33 "phpstan") e un intero Epic 1 su workflow di schede di un altro prodotto; il voto cittadino e' l'unico uso provato | VERIFICATO: `Rating/README.md:39-41`, `:96-106`; lista story e stati (conteggio mio); `Rating/app/Models/{Rating,RatingMorph}.php` | QUALITA' | Titolare (cosa resta di Rating nel progetto), sviluppo | E-07, E-27, E-38 | M | Rating | `rating-ambito-reale-e-triage-delle-story` |
| E-27 | Migrazioni Rating in conflitto: tre che creano `ratings`, tre che creano `rating_morph` con chiave utente diversa (`foreignIdFor(User,'user_id')` contro `nullableUuidMorphs('user')`); in piu' cartelle `_archive_redundant` e `_bak`. Lo schema finale dipende dall'ordine e dal tipo di id utente (rischio simile a quello gia' noto di `ticket_hours`, RIPORTATO da memoria) | VERIFICATO: nomi dei file in `Rating/database/migrations`; contenuto dei tre `rating_morph` letto; schema effettivo in produzione: non determinato | QUALITA' | Sviluppo; titolare per eventuale migrazione dati | Fixcity (`Ticket.citizen_rating`), User | M | Rating | `rating-migrazioni-una-sola-verita-utente-uuid` |
| E-28 | Commenti nel front office non localizzati e poco accessibili: titolo "Commenti" scritto in italiano nel blocco; etichetta del campo solo per screen reader (`sr-only`), id fissi `new-comment-text`, nessun `aria-describedby`/`aria-live` per gli errori; lingue del modulo: it 8 file, en 3, de 0, es 0 | VERIFICATO: `blog/detail.blade.php:120`, `events/detail.blade.php:116`; `new-comment.blade.php:2`, `comments.blade.php:15`; conteggio file lang. Story cmt-020 (a11y) in `ready-for-dev` | QUALITA' | Sviluppo | Lang, Sixteen | S | Comment | `commenti-fo-lingue-e-accessibilita-errori` |
| E-29 | Hygiene del modulo Comment e story: `cmt-014` "completed" dice che `packages/spatie/` e' stato eliminato ma ne restano 127 file tracciati; `cmt-018` AC1 dice "Http/Livewire assente" ma `app/Http/Livewire/` esiste (solo `_components.json`); `module.json` richiede `Notification` (riga 23), modulo che non esiste (esiste Notify); mancano test su manomissione, limiti, invio end-to-end (cmt-019, cmt-021 `ready-for-dev`) | VERIFICATO: `git ls-files packages` = 127; `ls Comment/app/Http/Livewire`; `Comment/module.json:23`; elenco test | QUALITA' | Sviluppo (marcare le story superseded, non cancellarle) | Notify | M | Comment | `comment-pulizia-pacchetti-requires-e-test-sicurezza` |
| E-30 | Modulo Event: placeholder dormiente (`active:0`, 2 file php) che contraddice la regola editoriale (gli eventi sono `Article` di Blog, "niente modulo Eventi duplicato"); modello con campi di scommessa, FK verso `categories` invece di `blog_categories`, NOT NULL su `bet_end_date`, `closed_at`, `published_at`; documentazione che promette iscrizione, calendario, indicizzazione pubblica | VERIFICATO: `Event/module.json:10,23`, `Event/app/Models/Event.php`, migrazione `20260101000000_create_events_table.php`, `Event/docs/bmad-remaining-work.md`, `fixcity-inbound-editorial.md` | QUALITA' | Titolare (ritirare o tenere) | Nuova DEC-56; Blog | S | Event | `event-ritiro-o-archivio-del-modulo` |
| E-31 | Mancano all'evento pubblico i dati essenziali: luogo/indirizzo/coordinate (nessun legame con Geo), file ICS, JSON-LD `Event` (c'e' solo la nota `Seo/docs/task-schema-org-eventi.md`), iscrizione, validazione `fine >= inizio` (IM-BLOG-003), stato annullato/rinviato. Questa e' la definizione di cosa manca a Event (0 story, 13 docs) | VERIFICATO: grep di venue/location/address/iCal/ICS/registrazione/JSON-LD nei blocchi evento e in Blog = 0; story IM-BLOG-003 `backlog` | QUALITA' | Titolare (quali servizi offrire), sviluppo | Geo (picker), Seo, DEC-56 | L | Blog (+ Geo, Seo) | `evento-pubblico-luogo-ics-jsonld-e-date` |
| E-32 | Residui di un altro dominio (scommesse, mercato predittivo) nel modello `Article` e nelle query, piu' una seconda tabella e modello `Comment` in Blog che duplicano il modulo Comment. Le colonne potrebbero non esistere in DB: non determinato | VERIFICATO: `Article` fillable/props `is_wagerable`, `wagers*`, `brier_score*`, `volume_play_money`, `rewarded_at`; `ThemeArticleQueries.php` ~righe 240-292 (`paginatedByWagers`, `orderedByWagers`, `paginatedByVolume`, `orderedByVolume`); `Blog/app/Models/Transaction` + migrazione `2024_05_02_185212_create_transactions_table.php`; `Blog\Models\Comment` + `2023_11_23_000001_create_comments_table.php` | QUALITA' | Titolare (rimuovere?), sviluppo | Nuova DEC-57; Comment | M | Blog | `blog-rimozione-residui-scommesse-e-comment-duplicato` |
| E-33 | Cartelle di database duplicate per maiuscole in Blog, entrambe tracciate e con contenuti diversi: `migrations` 21 e `Migrations` 17, `seeders` 20 e `Seeders` 5, `factories` 17 e `Factories` 11; rischio di doppia esecuzione o file persi su filesystem insensibili a maiuscole. Presenti anche `Models/BaseModel.php.backup-20251015-092511` e `posts_table.to_article*` | VERIFICATO: conteggi con `git ls-files` e `diff` (i file differiscono) | QUALITA' | Sviluppo | Migrazioni gia' applicate in produzione: non determinato | M | Blog | `blog-database-cartelle-duplicate-per-maiuscole` |
| E-34 | Governance editoriale bloccata: `IM-BLOG-001` e' `blocked` (nomina del responsabile, privacy, calendario); `IM-BLOG-002` (authoring), `IM-BLOG-004` (qualita' e CTA) e taxonomy/playbook in backlog. Nessuno puo' dire chi pubblica | VERIFICATO: stato delle story in `Blog/docs/stories` | QUALITA' | Titolare/ente (nominare il responsabile) | Nuova DEC-58 | S | Blog | `governance-editoriale-responsabile-e-calendario` |
| E-35 | Massa documentale di Geo: 2056 md in `docs/`, 544 al primo livello, 102 nomi base ripetuti, 57 `.old`; i due documenti del 2026-10-26 (`bmad-remaining-work.md`, `completion-backlog.md`) sono generici, affermano "PHPStan Level 10, 0 errors verified" senza prove e "Needs module-specific story files" pur esistendo 32 story. README con frontmatter ripetuto a meta' testo, badge Laravel 12 | VERIFICATO: conteggi; lettura dei due file; `Geo/README.md` righe 28-35 | QUALITA' | Sviluppo; titolare per cosa archiviare | Regola nomi `.md` (niente `-1.md`, date) | L | Geo | `geo-docs-mappa-di-migrazione-e-indice-essenziale` |
| E-36 | Il gate di messa online non vede i rischi principali di questo ambito: nessun controllo di segreti nei file di config tracciati, di attribuzione sulla mappa, di host terzi a runtime (tile, ArcGIS, Nominatim non sono nel regex `BAD_HOST`, che analizza HTML/CSS statici della home), di EXIF nelle foto, di moderazione dei commenti | VERIFICATO: `bashscripts/tools/release/go-live-gate.py` (controlli SEC-01/02, SITE, LEG, LG-01..08, REPO-01/02); il gate non e' stato rieseguito | QUALITA' | Sviluppo | E-01, E-04, E-05, E-06 | M | Radice (bashscripts) | `go-live-gate-segreti-attribuzione-host-runtime` |

### Gravita' RIFINITURA

| ID | Descrizione | Evidenza | Gravita' | Chi decide / esegue | Dipendenze | Dim. | Modulo | Story proposta |
|---|---|---|---|---|---|---|---|---|
| E-37 | Heatmap non implementata: `leaflet.heat` viene caricato (`map-lit.js:288`) ma nessun `heatLayer` esiste; `console.log` di debug lasciati (8 `console.*`, es. righe 236, 301, 424) | VERIFICATO: grep in `map-lit.js`. Se la heatmap sia richiesta dal prodotto: non determinato (cluster implementato) | RIFINITURA | Titolare (serve la heatmap?), sviluppo | E-22 | S | Geo | `mappa-heatmap-richiesta-o-togliere-dipendenza` |
| E-38 | Rating: lingue solo `it` (24 file, en/de/es 0); titolo della definizione "soddisfazione" scritto in italiano nell'azione `EnsureTicketCitizenRatingDefinitionAction`; modello `Like` con `$table='likes'` senza migrazione; file `RatingTrait.php.old`, `composer.json.tmp`, `changelog.md.old`, `license.md.old` | VERIFICATO: conteggi lang; grep `likes`; `ls` della radice del modulo | RIFINITURA | Sviluppo | E-26 | S | Rating | `rating-lingue-like-e-file-residui` |
| E-39 | README di modulo vecchi o generici: Media (scheda tecnica 127/46/6 contro 119/49/5 attuali; EXIF "sanificato" non vero), Geo (Laravel 12, "9+ provider", frontmatter duplicato), Comment (testo modello, frontmatter dentro il testo), Blog (modello generico, "Superpoteri"), Rating (vedi E-26) | VERIFICATO: lettura dei README e confronto con i conteggi | RIFINITURA | Sviluppo | Regola BMAD: docs solo in moduli/temi | S | Tutti | `readme-moduli-riscrivere-con-numeri-verificabili` |
| E-40 | Popup ticket con stili `!important` in linea e attributi `onerror="this.remove()"`: oggi tollerati da `script-src 'unsafe-inline'`, ma incompatibili con una CSP piu' stretta | VERIFICATO: `popup-ticket.js` ~righe 258-275; `security.php` script-src | RIFINITURA | Sviluppo | DCP-139 | S | Geo | `popup-ticket-senza-handler-inline` |

## 4. Contraddizioni tra documenti e codice

1. Story Geo 12.1 "Ritiro Geo Http/Livewire Test" (done): `Geo/app/Http/Livewire/Test.php`, `Test.test`, `test.test` esistono (VERIFICATO).
2. DEC-47 (Geo): descrive azioni stub con valori `['total'=>0,'categories'=>0]` e una fusione aperta; nel codice `GetMapStatsAction` restituisce `[]` (anche in HEAD) e il repo Geo non ha fusioni in corso (VERIFICATO). Nessun chiamante di produzione.
3. Test `MapActionsTest` ha un `->todo()` su "MERGE_HEAD" mentre il repo e' pulito (VERIFICATO).
4. `GeoApiTest` verifica 7 rotte `/api/geo/*`; `routes/api.php` e' tutto commentato (VERIFICATO).
5. `Geo/README.md`: "9+ provider", "ANPR Italia, dati Italia pronti", badge Laravel 12 contro `comuni.json` di 4 comuni e Laravel 13 installato (VERIFICATO).
6. `Geo/docs/completion-backlog.md` dice che servono story del modulo ma esistono 32 story; dichiara PHPStan a zero senza prova (VERIFICATO nel testo; il dato PHPStan e' RIPORTATO e non rieseguito).
7. Documento "Geo non deve conoscere Fixcity" contro `fixcity::ticket.map.image.alt` in `map-lit.blade.php:11` e le altre dipendenze di E-17 (VERIFICATO).
8. Nota legale `legal-notes.en.md:27` cita OSM ODbL e il contatto diretto del browser con servizi terzi, mentre la mappa non mostra il credito (VERIFICATO).
9. `Media/README.md` riga 54 "Preservazione e sanitizzazione dati EXIF" contro nessun codice EXIF (VERIFICATO); la scheda tecnica 127/46/6 contro 119/49/5 (VERIFICATO).
10. Story `media-test-mai-eseguiti-e-doppioni-maiuscole` dice che `app/conversions` esiste; oggi la cartella non c'e' (VERIFICATO).
11. Commento in `ListPublicTicketsAction.php:22-23` ("niente foto nell'elenco finche' manca una miniatura senza EXIF") contro `BuildTicketPublicDetailsPayloadAction.php:45` che pubblica gli originali nel dettaglio (VERIFICATO).
12. `Rating/README.md`: "10 classi fuori dal modulo estendono BaseRating" (Ptv, Performance, Progressioni...) e uso in `Modules/Ptv`: moduli non presenti nel repo; 137/20/125 contro 73/29/181 (VERIFICATO).
13. Story Comment `cmt-014` ("pacchetti spatie eliminati", completed) contro 127 file tracciati; `cmt-018` AC1 contro `Http/Livewire` presente; `cmt-010..013` "ready-for-dev" contro blocchi FO gia' esistenti (VERIFICATO).
14. Story Blog `events-public-index` (done) AC2 contro `events/index.blade.php:109` e `ThemeArticleQueries.php:175-180` (VERIFICATO).
15. Regola `fixcity-inbound-editorial` (niente modulo Eventi, niente migrazioni inutili) contro la presenza del modulo Event con una propria migrazione (VERIFICATO).
16. `Event/docs/bmad-remaining-work.md` promette iscrizione, calendario e indicizzazione pubblica; il codice ha solo un modello e non ha provider (VERIFICATO).
17. Registro DEC-20 cita il widget di valutazione pagina in modo ristretto; le occorrenze sono almeno 6 (VERIFICATO per presenza).
18. DEC-49 (TestCase con tema forzato) per Geo e' risolta: `Geo/tests/TestCase.php` non forza `pub_theme` (VERIFICATO). Da chiudere nel registro per quanto riguarda Geo.

## 5. Documentazione mancante o da riscrivere

Percorsi proposti (nomi senza date e senza suffissi numerici, come impone la regola dei nomi `.md`). Le story vanno in
`docs/stories/<slug>.story.md` del modulo owner esistente; non creo ne' sposto nulla in questo compito.

| Modulo | Documento | Perche' |
|---|---|---|
| Geo | `laravel/Modules/Geo/docs/architecture/map-runtime-and-third-party-services.md` | Quali servizi terzi tocca ogni pagina (tile OSM, ArcGIS, OSM-fr, OpenTopoMap, Nominatim), con CSP e limiti (E-04, E-05, E-19) |
| Geo | `laravel/Modules/Geo/docs/privacy/geocoding-and-tiles-data-flows.md` | Flussi di dati personali verso terzi, per l'informativa (Gdpr) |
| Geo | `laravel/Modules/Geo/docs/architecture/config-keys-and-providers.md` | Chiavi vere `geo.config.*`, provider supportati davvero, chiavi `services.*` (E-13, E-14) |
| Geo | `laravel/Modules/Geo/docs/inventory/docs-consolidation-map.md` | Mappa di migrazione per i 2056 md: cosa e' essenziale, cosa archivio, senza cancellare story (E-35) |
| Geo | Riscrittura di `README.md` e dei due file 2026-10-26 | Numeri verificabili, nessun badge non provato (E-39) |
| Media | `laravel/Modules/Media/docs/privacy/ticket-photos-exif-and-exposure.md` | Cosa e' pubblico, dove, quali miniature, EXIF, URL firmati (E-02, E-03) |
| Media | `laravel/Modules/Media/docs/architecture/public-vs-private-disks.md` | Dischi, `MEDIA_DISK`, dischi FTP residui da rimuovere (E-01) |
| Media | Riscrittura di `README.md` | Togliere HLS, AVIF, CDN, EXIF finche' non esistono (E-39) |
| Rating | `laravel/Modules/Rating/docs/scope-citizen-rating.md` e riscrittura `README.md` | Scopo reale: voto cittadino sul ticket; resto marcato fuori ambito (E-26) |
| Rating | `laravel/Modules/Rating/docs/stories/rating-story-triage.story.md` | Collegare o marcare superseded le story non pertinenti, senza cancellare |
| Comment | `laravel/Modules/Comment/docs/moderation-policy.md` | Premoderazione, segnalazione, tempi, responsabile (E-06) |
| Comment | `laravel/Modules/Comment/docs/accessibility-comment-form.md` | Etichette, errori annunciati, lingue (E-28) |
| Event | `laravel/Modules/Event/docs/decision-module-retirement.md` | Decisione su ritiro o archivio del modulo e rimando alla regola editoriale (E-30) |
| Blog | `laravel/Modules/Blog/docs/events-lifecycle-and-publication.md` | Ciclo di vita dell'evento: bozza, programmato, passato, annullato (E-12, E-31) |
| Blog | `laravel/Modules/Blog/docs/editorial-html-sanitization.md` | Quali tag, chi sanifica, import JSON (E-11) |
| Blog | `laravel/Modules/Blog/docs/editorial-governance.md` | Responsabile, calendario, privacy (E-34) |

Vecchi o da marcare: `Event/docs/bmad-remaining-work.md` e `completion-backlog.md` (promesse non vere), i due file
2026-10-26 di Geo, README di Rating, Media, Geo, Comment, Blog, e le story elencate nella sezione 4.

## 6. Domande aperte per l'utente

1. Provider di tile e geocoder per il go-live: auto-ospitato, a contratto, oppure OSM pubblico con attribuzione e
   limiti (proposta DEC-52)? Finche' manca la risposta E-04 ed E-05 non si chiudono.
2. Foto dei ticket: devono essere visibili al pubblico prima della moderazione? Si accettano solo miniature senza EXIF
   e un disco privato con URL firmati (proposta DEC-55, collegata a DEC-37)?
3. Le credenziali FTP nei config tracciati sono ancora valide? Si chiude l'account e si riscrive la storia git, o basta
   toglierle dai file e ruotarle (proposta DEC-53)? La story `dcp-275` non tracciata copre gia' questo caso?
4. Commenti: attivi al go-live su ticket, blog ed eventi? Premoderazione obbligatoria? Chi modera (proposta DEC-54)?
5. Widget "voto pagina" (almeno 6): nasconderli o salvare davvero i voti (DEC-20)?
6. Modulo Event: ritirarlo/archiviarlo, dato che gli eventi sono `Article` (proposta DEC-56)? I residui di scommesse in
   Blog si rimuovono (proposta DEC-57)?
7. `tickets.json`: chi lo rigenera e quando? Cosa deve succedere alle coordinate di un ticket ritirato (E-08)?
8. Dataset dei comuni: fonte ufficiale (ANPR/ISTAT), licenza e responsabile dell'importazione (DEC-48)?
9. Azioni Geo stub (DEC-47): si implementano o si eliminano? Heatmap richiesta dal prodotto?
10. Quali dei 161 story di Rating si considerano fuori ambito? Possiamo marcarle superseded in blocco?
11. Responsabile editoriale per blog ed eventi (proposta DEC-58, sblocca `IM-BLOG-001`)?

I numeri DEC-52..58 sono provvisori: vanno assegnati nel registro DCP-200 al prossimo numero libero.

### Limiti di questa verifica

- Non ho eseguito test ne' strumenti di analisi: l'esito di qualunque test citato e' non determinato.
- Non ho interrogato il database: gli schemi reali (rating, eventi, comuni, colonne di scommessa) sono non determinati.
- Non ho usato la rete: le politiche d'uso di OSM e Nominatim (E-05) sono conoscenza generale, RIPORTATA.
- La gestione degli errori di caricamento di `_loadGeoJson` in `map-lit.js` non e' stata letta per intero.
- Il gate di messa online non e' stato rieseguito; la sua copertura e' dedotta dal codice.
- La story `dcp-275` e i file non committati di altre sessioni sono stati solo individuati da `git status`, non letti.
