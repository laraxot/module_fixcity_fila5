---
title: "Fixcity — Audit di sicurezza"
type: audit
tags: [fixcity, audit, security]
created: 2026-11-05
updated: 2026-11-05
status: active
related:
  - ./README.md
  - ./COMPLETION_ROADMAP.md
---

> Analisi statica del branch `dev` del 2026-11-05: niente vendor/, PHPStan e Pest non eseguiti in questo ambiente.
> Lo stato aggiornato di ogni rilievo (risolto/aperto) è in [COMPLETION_ROADMAP.md](./COMPLETION_ROADMAP.md).

# Audit di sicurezza — Fixcity (base_fixcity_fila5, branch dev)

- Data: 2026-11-05
- Modalità: sola lettura, analisi statica (ripgrep, python, semgrep con regole locali). Nessun file del repository modificato, nessun test eseguito contro sistemi esterni.
- Ambito: `laravel/` (app, bootstrap, config, routes), `laravel/Modules/*`, `laravel/Themes/Sixteen` (TwentyOne solo a campione).
- Convenzione: **[F]** = fatto verificato leggendo il codice/file; **[D]** = deduzione da verificare a runtime.
- Segreti: per ogni segreto si riportano solo nome, file, riga e lunghezza. Nessun valore è riportato.

## Riepilogo per gravità

| ID | Titolo | Gravità | Tipo |
|----|--------|---------|------|
| SEC-01 | Segreti committati: APP_KEY condivisa, token Matomo, credenziali OAuth/FTP/Dropbox e chiavi API di altri tenant in `config/` | critica | bug dimostrato |
| SEC-02 | Callback SPID accetta una SAMLResponse senza verifica della firma (login con identità arbitraria) | alta (critica se esiste la colonna `fiscal_code`) | bug dimostrato (sfruttabilità condizionata) |
| SEC-03 | Tenant e database scelti dall'header Host, con TRUSTED_HOSTS vuoto negli env committati | alta | ipotesi da verificare |
| SEC-04 | Allegati (PDF compresi) esposti dalle API JSON pubbliche e file media su disco `public` con percorsi enumerabili | alta | bug dimostrato + problema altamente probabile |
| SEC-05 | Account cancellati (GDPR) ancora utilizzabili: login API, pagine di login legacy, token Passport | media | bug dimostrato |
| SEC-06 | Open redirect su `return_url` nei flussi SPID e CIE | media | bug dimostrato |
| SEC-07 | Collegamento di account OAuth per sola email, senza controllo dell'email verificata (Microsoft) | media | ipotesi da verificare |
| SEC-08 | Back office: `status`/`priority` sono campi Hidden modificabili dal client, aggirando il workflow e l'audit trail | media | problema altamente probabile |
| SEC-09 | Pagine demo/test e login legacy senza rate limit raggiungibili da mount Folio non filtrati | media | ipotesi da verificare (parzialmente verificata) |
| SEC-10 | Dati personali (codice fiscale, SAMLResponse, attributi SPID/CIE) scritti nei log | media | bug dimostrato |
| SEC-11 | APP_DEBUG=true e APP_ENV=local negli env committati; route con controller inesistente | media | problema altamente probabile |
| SEC-12 | Mappa pubblica: coordinate esatte e indirizzo, GeoJSON non paginato e senza throttle | bassa | ipotesi da verificare |
| SEC-13 | Codice morto pericoloso: policy permissiva `Models/Policies/TicketPolicy` e vista `tickets/show` con `{!! $ticket->content !!}` | bassa | problema altamente probabile (latente) |
| SEC-14 | Creazione ticket senza rate limit e senza email verificata | bassa | bug dimostrato |
| SEC-15 | Dipendenze: 78 vincoli `*`, `minimum-stability: dev`, nessun lock file, fork vendorizzati | bassa | bug dimostrato (configurazione) |
| SEC-16 | Modulo AI: base URL configurabile, prompt con testo utente, route verso controller inesistente | bassa | ipotesi da verificare |
| SEC-17 | Azioni artisan dal web (dead code) con `key:generate` dentro "clear" | bassa (informativa) | problema altamente probabile (latente) |

---

## SEC-01 — Segreti committati nel repository

- **Gravità:** critica
- **Tipo:** bug dimostrato
- **Evidenza [F]:** tutti i file sotto risultano tracciati (`git ls-files`).
  - `laravel/.env.fixcity:3` `APP_KEY` (len 51); `laravel/.env.latest:3` `APP_KEY` (len 51). L'hash SHA-256 della riga è **identico** nei due file: la stessa chiave è riusata. `laravel/.env.testing:7` `APP_KEY` (len 51, diversa).
  - `laravel/.env.fixcity:97` e `laravel/.env.latest:97` `MATOMO_AUTH_TOKEN` (len 34).
  - Negli stessi due file anche `DB_PASSWORD*` (len 5), `REDIS_PASSWORD` (len 4), `MAIL_PASSWORD` (len 4), `IMAP_PASSWORD` (len 6): valori corti, probabilmente di sviluppo, ma comunque committati.
  - **Nuovo rispetto al già noto**, segreti hardcoded in config di altri tenant presenti nello stesso repo:
    - `laravel/config/it/quaeris/manager/services.php:7-8` facebook `client_id` (len 16) / `client_secret` (len 32); `:12-13` twitter `client_id` (len 25) / `client_secret` (len 50); `:23` e `:27` `maps_key` google (len 39) e bing (len 64); `:30-31` dropbox `app_key` / `app_secret` (len 15 ciascuno). Le stesse chiavi sono replicate in `config/it/quaeris/manager/quaeris/services.php`, `config/it/quaeris/manager/quaeris/quaeris/services.php`, `config/it/quaerisofficina/manager2_old/services.php`.
    - `laravel/config/it/quaeris/filesystems.php:92` disco `photos` con `driver ftp` e `password` (len 13); replicata in `config/it/quaeris/manager/filesystems.php:92`, `config/it/quaeris/manager/quaeris/filesystems.php:85`, `config/it/quaerisofficina/manager2/filesystems.php:85`.
    - `maps_key` Google (len 39) in `config/localhost/google.php:8`, `config/net/futurely/google.php:8`, `config/it/qualitymonitor/*/google.php:8`, `config/it/quaerisofficina/*/google.php:8`.
- **Impatto [D]:** se la APP_KEY committata è (o è stata) usata in un ambiente raggiungibile:
  - si possono decifrare e forgiare cookie e sessione (`config/session.php:55` `encrypt` true);
  - si possono forgiare URL firmati (verifica email `signed`, `Modules/User/routes/auth.php:24`);
  - si possono forgiare snapshot Livewire (il checksum è un HMAC con APP_KEY): modifica di proprietà `#[Locked]` e invocazione di metodi di componenti non montati, ad esempio il `LoginComponent` senza throttle (SEC-09).
  - Le credenziali FTP/OAuth/Dropbox di altri progetti sono compromesse per chiunque abbia accesso al repo o ai suoi fork.
- **Correzione:** ruotare subito APP_KEY (con `APP_PREVIOUS_KEYS` per la transizione), token Matomo, secret Facebook/Twitter/Dropbox, password FTP; restringere per referrer/IP le chiavi Maps. Togliere i file dal repo (`git rm --cached`), aggiungere `.env.*` (tranne gli `.example`) a `.gitignore`, spostare i valori in `env()`, valutare la riscrittura della history (BFG/filter-repo) e un secret scanner in CI (gitleaks/trufflehog). Togliere dal repo Fixcity le config dei tenant `it/quaeris*`, `it/qualitymonitor`, `net/futurely`.
- **Test:** un test Pest/arch che fallisce se un file tracciato `config/**/*.php` contiene un letterale per chiavi `password|secret|client_secret|app_secret|maps_key|token`, e un job CI gitleaks sul repo.
- **Confidenza:** 98. La presenza dei segreti è verificata. Resta da capire se quei valori sono ancora validi.

## SEC-02 — SPID: SAMLResponse accettata senza verifica di firma

- **Gravità:** alta. Diventa critica se la tabella utenti ha la colonna `fiscal_code`.
- **Tipo:** bug dimostrato (manca la validazione); sfruttabilità condizionata dallo schema.
- **Evidenza [F]:**
  - Le route sono caricate: `Themes/Sixteen/app/Providers/ThemeServiceProvider.php:70` → `registerAuthRoutes()` → `loadRoutesFrom(routes/auth.php)`; `Themes/Sixteen/routes/auth.php:32` `GET auth/spid/login/{provider}`, `:36` `POST auth/spid/callback`.
  - `Themes/Sixteen/app/Actions/SpidAuthAction.php:117` `$responseDoc->loadXML($decodedResponse);`, `:119` `validateSamlResponse`, `:324-338`: controlla solo `//samlp:StatusCode/@Value == ...:Success`. Non verifica firma, Issuer, Audience, `InResponseTo`, `NotOnOrAfter` né il certificato dell'IdP. `getSigningCertificate()` (`:389`) non viene mai usato.
  - Il RelayState è confrontato con `spid.request_id` in sessione (`:106`), ma lo stesso valore torna all'attaccante nell'URL di redirect verso l'IdP (`getLoginUrl`, `:51-70`). I provider di default sono già cablati (`loadProviders`, `:207-243`), quindi il passo funziona senza configurazione.
  - `Themes/Sixteen/app/Http/Controllers/SpidAuthController.php:83-86`: `findOrCreateUser($userAttributes)` → `Auth::login($user, true)`. `:279` cerca l'utente per `fiscal_code` preso dalla risposta non firmata.
  - In nessuna migrazione dei moduli c'è una colonna `fiscal_code` (`rg fiscal_code Modules/*/database database`: 0 risultati). Oggi quindi la query molto probabilmente va in errore: l'eccezione viene catturata e il messaggio SQL finisce nel flash (`:117-118`, `'Errore ... SPID: '.$e->getMessage()`).
  - Nessuna esclusione CSRF per `/auth/spid/callback` e cookie `same_site=lax` (`config/session.php:208`). Il flusso SPID reale con POST dall'IdP **non può funzionare**, mentre l'attaccante usa la propria sessione e il proprio token CSRF.
- **Scenario [D]:** `GET /auth/spid/login/poste` → si legge `RelayState` dal `Location` → `POST /auth/spid/callback` con token CSRF della propria sessione, `RelayState` e una SAMLResponse base64 costruita a mano con `StatusCode Success` e `fiscalNumber` della vittima → login come l'utente con quel codice fiscale (o creazione di un nuovo utente) con "remember me".
- **Impatto:** se la colonna esiste in produzione: takeover di qualsiasi cittadino o operatore che abbia il codice fiscale valorizzato. Il codice fiscale si ricava da dati anagrafici pubblici.
- **Correzione:** disattivare le route SPID/CIE finché non c'è un'integrazione conforme (ad esempio `italia/spid-php` / `spid-cie-php`, o SATOSA come proxy). In alternativa usare `onelogin/php-saml` con verifica di firma (`wantAssertionsSigned`), Issuer, Audience, `InResponseTo`, finestre temporali e certificati dal metadata AgID. Escludere dal CSRF solo il callback ACS, con un controllo `InResponseTo` legato alla sessione.
- **Test:** feature test che fa `POST /auth/spid/callback` con una SAMLResponse non firmata, `StatusCode Success` e RelayState valido in sessione, e verifica `assertGuest()` e nessun utente creato.
- **Confidenza:** 95 sulla mancata verifica. 45 sulla sfruttabilità attuale: dipende dalla colonna `fiscal_code` nel DB reale, che non ho potuto vedere.

## SEC-03 — Header Host → scelta del tenant e del database

- **Gravità:** alta
- **Tipo:** ipotesi da verificare (dipende dal web server)
- **Evidenza [F]:**
  - `Modules/Tenant/app/Actions/GetTenantNameAction.php:132-144`: il tenant deriva da `SERVER_NAME`. Con Apache e `UseCanonicalName Off`, il default nei vhost name-based, `SERVER_NAME` è l'header Host.
  - `Modules/Tenant/app/Providers/TenantServiceProvider.php:60-72` `registerDB()` → `Config::set('database', $data)` con la config del tenant risolto.
  - `bootstrap/app.php:34-40` `trustHosts` vale solo se `security.trusted_hosts` è valorizzato. `.env.fixcity` e `.env.latest` non definiscono `TRUSTED_HOSTS`, e con `APP_ENV=local` il framework salta comunque il controllo (commento `:31-33`). Solo `.env.production.example:92` lo imposta.
  - Nel repo ci sono config di altri tenant con i rispettivi `database.php` (`config/it/quaeris/database.php`, `config/net/futurely/database.php`, ...). `config/eu/fixcity` è un symlink a `../local/fixcity/`.
  - Nessun modello Fixcity ha `tenant_id`. L'isolamento fra Comuni è solo "un database per host".
  - In più `Modules/Xot/app/Providers/XotBaseRouteServiceProvider.php:31` fa `Config::set('extra_conn', Request::segment(2))` ("va a prendere db diverso"). Non ho trovato altri usi di `extra_conn`.
- **Impatto [D]:** su un server che ospita più Comuni, un Host forgiato fa caricare la config, e quindi il DB, di un altro tenant. Inoltre i link di reset password o di verifica email si generano con l'host della richiesta (poisoning → takeover).
- **Correzione:** `TRUSTED_HOSTS` obbligatorio in produzione e controllo anche fuori da `APP_ENV=production`. Risolvere il tenant da una allow-list host → tenant, non dal filesystem. Fissare `APP_URL` per la generazione dei link nelle notifiche. Togliere le config di tenant estranei dal deploy. Rimuovere `extra_conn` da segmento URL.
- **Test:** feature test che invia `Host: evil.example` e si aspetta 400. Test unitario su `GetTenantNameAction` con `SERVER_NAME` non in allow-list che deve restituire il default.
- **Confidenza:** 60. Il codice è verificato; l'impatto dipende dalla configurazione di Apache/Nginx e da quanti tenant girano sullo stesso host.

## SEC-04 — Allegati e foto: esposizione pubblica e PDF nelle API

- **Gravità:** alta (dati personali, GDPR)
- **Tipo:** bug dimostrato (API) + problema altamente probabile (enumerazione file)
- **Evidenza [F]:**
  - `Modules/Fixcity/app/Models/Ticket.php:426` la collezione `attachments` accetta `application/pdf`.
  - La pagina HTML di dettaglio filtra le sole immagini: `Modules/Fixcity/app/Actions/BuildPublicTicketDetailAction.php:120-123` (`if (! str_starts_with(mime,'image/')) continue;`, doc `:32` "PDF ... restano esclusi").
  - Le API JSON pubbliche **non** filtrano: `Modules/Fixcity/resources/views/pages/api/tickets/list.blade.php:38-49` e `Modules/Fixcity/app/Actions/BuildTicketPublicDetailsPayloadAction.php:38-46` (usata da `pages/api/ticket-details/[ticket].blade.php:15-26`) restituiscono `getFullUrl()` di **tutti** i media, PDF inclusi, per ogni ticket pubblico.
  - Disco: `config/local/fixcity/media-library.php:34` `MEDIA_DISK` di default `public`; `:99` `DefaultPathGenerator`, quindi URL `/storage/{media_id}/{nome_file}` con id sequenziale. `config/filesystems.php:40-45` disco `public` con `visibility public`.
  - EXIF/GPS: rimossi solo per JPEG/PNG (`Modules/Media/app/Actions/Image/StripImageMetadataAction.php:29-33`, listener `Modules/Fixcity/app/Listeners/StripTicketPhotoMetadataListener.php:17-24`). I PDF restano invariati, metadati compresi. **Punto positivo verificato:** lo strip c'è ed è applicato a ogni percorso di upload dei ticket.
  - `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:337-345`: `maxFiles(5)`, mime jpeg/png/pdf, `maxSize` 10 MB. Gli upload temporanei Livewire seguono la config Livewire, non verificata.
- **Impatto:** i PDF caricati dai cittadini (spesso documenti, ricevute, dati anagrafici) sono leggibili da chiunque tramite `/api/tickets/list`. Con un nome file noto o indovinabile (nomi originali, `DefaultFileNamer`) e id sequenziali, sono raggiungibili anche i media di ticket non pubblici (pending/rifiutati) [D].
- **Correzione:** riusare `publicImageUrls()` di `BuildPublicTicketDetailAction` in entrambe le API. Spostare i media dei ticket su un disco privato e servirli con una route che applica `TicketPolicy::view`, oppure con URL temporanei firmati. Usare un PathGenerator con UUID. Rinominare i file in upload. Valutare la sanitizzazione o il divieto dei PDF per il pubblico.
- **Test:** feature test con un ticket pubblico che ha un PDF in `attachments`: `GET /api/tickets/list` e `GET /api/ticket-details/{id}` non devono contenere `.pdf` nell'array `images`. Test che un media di un ticket `pending` non sia raggiungibile via URL pubblico.
- **Confidenza:** 90 sul leak dei PDF via API. 65 sull'enumerazione, che dipende dal nome file reale e dal web server.

## SEC-05 — Account cancellati (GDPR) ancora utilizzabili

- **Gravità:** media
- **Tipo:** bug dimostrato
- **Evidenza [F]:**
  - `Modules/User/app/Models/BaseUser.php:150` `// use SoftDeletes;`: la cancellazione GDPR imposta solo `deleted_at` (`Modules/Gdpr/app/Actions/Account/DeleteAccountAction.php:75-77`) senza scope globale.
  - Il controllo `deleted_at` esiste solo in `Modules/User/app/Filament/Widgets/Auth/LoginWidget.php:92` e in `Modules/User/app/Actions/Socialite/LoginUserAction.php:31`.
  - Senza controllo: `Modules/User/app/Http/Controllers/Api/LoginController.php:24-29` (`POST /api/user/login`, rilascia un token Passport), `Modules/User/resources/views/pages/auth/login.blade.php:29`, `Modules/Cms/app/Http/Volt/LoginComponent.php:35` (usato da `Themes/Sixteen/resources/views/pages/counter.blade.php:19`), il login dei pannelli Filament (non verificato).
  - `DeleteAccountAction` non revoca token Passport né remember token. Inoltre fa il soft-delete dei log di activity dell'utente (`:80-83`), riducendo l'audit.
- **Impatto:** un account "eliminato" può ancora autenticarsi via API e ottenere un token. Contraddice l'informativa privacy e l'art. 17 GDPR.
- **Correzione:** `SoftDeletes` su `BaseUser`, o almeno uno scope globale o un `UserProvider` custom che escluda `deleted_at`. In `DeleteAccountAction` revocare token e refresh token Passport e azzerare `remember_token`.
- **Test:** utente con `deleted_at` valorizzato: `POST /api/user/login` con credenziali corrette → 401, e nessun record in `oauth_access_tokens`.
- **Confidenza:** 85. Che l'API sia davvero raggiungibile dipende da `Passport::routes`/guard `api`, il cui codice è presente (`Modules/User/routes/api.php`).

## SEC-06 — Open redirect su `return_url` (SPID/CIE)

- **Gravità:** media
- **Tipo:** bug dimostrato
- **Evidenza [F]:** `Themes/Sixteen/app/Http/Controllers/SpidAuthController.php:38` `$returnUrl = $request->query('return_url', ...)` → `SpidAuthAction::getLoginUrl` lo salva in sessione (`Themes/Sixteen/app/Actions/SpidAuthAction.php:63`) → `SpidAuthController.php:102-105` `redirect()->to($returnUrl)`. Lo stesso in `CieAuthController.php:37`, `:66`, `:137-140` e `:166` (logout).
- **Impatto:** phishing post-login con il dominio istituzionale del Comune.
- **Correzione:** accettare solo path relativi o host in allow-list (`url()->isValidUrl` + confronto con `APP_URL`), oppure usare `redirect()->intended()`.
- **Test:** `GET /auth/cie/login?return_url=https://evil.example` → la sessione non deve contenere un URL esterno; dopo il callback simulato il redirect resta sull'host dell'app.
- **Confidenza:** 85. Il redirect finale richiede un login riuscito, quindi è possibile solo con un IdP funzionante (vedi SEC-02).

## SEC-07 — OAuth: collegamento account per email non verificata

- **Gravità:** media
- **Tipo:** ipotesi da verificare
- **Evidenza [F]:** `Modules/User/app/Actions/Socialite/ProcessCallbackAction.php:49-52` cerca l'utente esistente per `email` restituita dal provider e lo collega (`RegisterSocialiteUserAction`), poi fa login (`:63`). Nessun controllo `email_verified` / `xms_edov`. Il provider `microsoft` è abilitato (`Themes/Sixteen/resources/views/pages/auth/login/[provider]/callback.blade.php:15`, `config/services.php:90-94`, senza tenant fisso). `IsUserAllowedAction` senza domini restituisce `true` (`:24-26`).
- **Impatto [D]:** attacco tipo "nOAuth". Un tenant Entra controllato dall'attaccante può emettere un'email non verificata uguale a quella di un operatore o admin, e ottenere il takeover dell'account esistente, ruoli compresi. Google restituisce email verificate; per GitHub dipende dal provider Socialite.
- **Correzione:** collegare per email solo se il provider attesta l'email come verificata (per Microsoft usare `oid`/`tid` + `xms_edov`, oppure limitare il tenant). Altrimenti chiedere il login locale prima di collegare. Escludere dal collegamento automatico gli account con ruoli PA.
- **Test:** mock Socialite con utente Microsoft senza `email_verified` e email di un admin esistente: il callback non deve autenticare e non deve creare `socialite_users`.
- **Confidenza:** 55. Dipende dalla configurazione del tenant Microsoft in produzione.

## SEC-08 — Back office: stato e priorità modificabili fuori dal workflow e senza audit

- **Gravità:** media
- **Tipo:** problema altamente probabile
- **Evidenza [F]:**
  - `Modules/Fixcity/app/Filament/Resources/TicketResource/Schemas/TicketForm.php:261-262` `Hidden::make('priority')->dehydrated()` e `Hidden::make('status')->dehydrated()`, inclusi nello schema del back office (`getFormSchema()`, `:135-142`) usato da `EditTicket` (`Pages/EditTicket.php`, nessun `mutateFormDataBeforeSave`).
  - Il wizard pubblico sovrascrive i due campi lato server (`CreateTicketWizardWidget.php:115-119`, commento "SEC-01, DCP-49"); il back office no.
  - Le transizioni "ufficiali" passano da `ChangeStatus`, `ApproveTicketAction` e `RejectTicketAction`, che registrano `TicketActivity` (`RecordTicketActivityAction`). `Ticket` non usa `LogsActivity` e `EditTicket`/`DeleteAction` non registrano nulla.
- **Impatto [D]:** chi può modificare un ticket nel pannello (operatori, `ticket.update`) può portare un ticket in qualsiasi stato con `$wire.set('data.status', ...)`, saltando le regole di transizione, le notifiche ai cittadini e la timeline pubblica. Modifiche a contenuto, posizione e responsabile non lasciano traccia.
- **Correzione:** togliere `status`/`priority` dallo schema di modifica (o `->dehydrated(false)` + `disabled()`), forzare in `mutateFormDataBeforeSave` i valori originali, e tracciare update e delete (spatie activitylog `LogsActivity` su `Ticket` o un observer che scrive `TicketActivity`).
- **Test:** Livewire test su `EditTicket` che fa `set('data.status','closed')` + `call('save')` e verifica che lo stato non cambi. Test che un update da back office crei una riga di activity.
- **Confidenza:** 70. La logica è verificata; non ho eseguito il test Livewire.

## SEC-09 — Pagine demo/test e login legacy raggiungibili, senza rate limit

- **Gravità:** media
- **Tipo:** ipotesi da verificare (parzialmente verificata)
- **Evidenza [F]:**
  - `Themes/Sixteen/resources/views/pages/counter.blade.php:16-20` monta `Modules\Cms\Http\Volt\LoginComponent`, che fa `Auth::attempt` senza throttle e senza controllo `deleted_at` (`Modules/Cms/app/Http/Volt/LoginComponent.php:31-50`). `Modules/User/resources/views/pages/auth/login.blade.php:25-38`: stessa cosa. `Modules/Fixcity/resources/views/pages/prova.blade.php`. `Themes/Sixteen/resources/views/pages/tests/index.blade.php`, `tests/[slug].blade.php` e `tests/pages-view-reference.blade.php` non hanno auth. `Modules/Xot/resources/views/pages/artisan-commands-manager.blade.php` è una vista Filament senza componente: va in errore, e con debug attivo mostra lo stack.
  - `app/Http/Middleware/BlockLegacyPublicPages.php:32-44` blocca `counter` e `prova`, ma non `tests` ed è applicato **solo** al mount del tema in `app/Providers/FolioServiceProvider.php:33-40`. Lo stesso path tema viene montato una seconda volta da `Modules/Cms/app/Providers/FolioVoltServiceProvider.php:141-149` (riga file ~141) senza quel middleware. Le pagine dei moduli (`Modules/*/resources/views/pages`, quindi `prova`, `User/auth/login`, `artisan-commands-manager`) sono montate solo da Cms (`:165-189`), senza blocco.
  - Il login principale (`Modules/User/app/Filament/Widgets/Auth/LoginWidget.php:77-109`) è invece limitato per email+IP e per IP. Registrazione e password dimenticata usano `ConsumeAuthSubmissionSlotAction`.
- **Impatto [D]:** quale mount Folio vince per lo stesso URI dipende dall'ordine di registrazione, che non ho potuto verificare senza `route:list`. Se `/it/counter` o `/it/auth/login` della pagina User sono serviti, è possibile un brute force di password senza limiti. Con APP_KEY nota (SEC-01) il `LoginComponent` è comunque invocabile via `/livewire/update` con snapshot forgiato.
- **Correzione:** cancellare `counter.blade.php`, `prova.blade.php`, `tests/*`, la pagina User `auth/login` e `artisan-commands-manager.blade.php` (o metterli sotto `app()->environment('local')`). Applicare `BlockLegacyPublicPages` a tutti i mount. Aggiungere un throttle in `LoginComponent` o rimuoverlo.
- **Test:** feature test `GET /it/counter`, `/it/prova`, `/it/tests`, `/it/artisan-commands-manager` → 404. Test di 6 tentativi errati sulla pagina User login → errore di throttle.
- **Confidenza:** 55. Codice verificato; routing effettivo non verificato.

## SEC-10 — Dati personali nei log

- **Gravità:** media
- **Tipo:** bug dimostrato
- **Evidenza [F]:** `Themes/Sixteen/app/Actions/SpidAuthAction.php:123-126` `Log::info(... 'user_attributes' => $attributes)` (codice fiscale, nome, data di nascita, indirizzo, ...). `Themes/Sixteen/app/Http/Controllers/SpidAuthController.php:95-99` logga `fiscal_code`, `:108-112` logga `'request_data' => $request->all()` (SAMLResponse intera) e il trace. `CieAuthController.php:130-134` e `:143-147`: stessa cosa. `Log::info` per ogni login con IP e user agent (`SpidAuthController.php:51-56`, `CieAuthController.php:39-43`). I log sono scaricabili da `/api/log-download` (protetto da `super-admin`/`log.viewAny`, `Modules/Activity/app/Actions/Log/AuthorizeLogAccessAction.php`).
- **Impatto:** trattamento di dati identificativi fuori dal registro dei trattamenti, con retention dei log non governata.
- **Correzione:** loggare solo id interno e hash del codice fiscale; mai `$request->all()` su callback di identità.
- **Test:** test con `Log::spy()` sul callback CIE/SPID in errore: nessun contesto contiene `SAMLResponse`, `fiscal_code` o `request_data`.
- **Confidenza:** 90.

## SEC-11 — Debug attivo negli env committati, errori verbosi

- **Gravità:** media
- **Tipo:** problema altamente probabile
- **Evidenza [F]:** `laravel/.env.fixcity:2,4` e `laravel/.env.latest:2,4` `APP_ENV=local`, `APP_DEBUG=true`. `.env.testing:8` `APP_DEBUG=true`. `.env.production.example:2,4` è corretto (`production`/`false`), come `config/app.php:96` (default `false`). Il nome `.env.fixcity` fa pensare al file del deploy del tenant [D]. `Modules/AI/routes/api.php` registra 10 route verso `Modules\AI\App\Http\Controllers\Api\AIController`, che non esiste nel repo, con il guard `auth:sanctum` che non risulta configurato: ogni chiamata genera un'eccezione. `SpidAuthController.php:117-118` e `CieAuthController.php:152-153` mettono `$e->getMessage()` nel flash per l'utente.
- **Impatto:** con debug attivo, pagine di errore con stack trace, variabili d'ambiente e query. `APP_ENV=local` disattiva anche `trustHosts` (SEC-03) e `secure` dei cookie (`config/session.php:178`).
- **Correzione:** non tenere env "reali" nel repo; check in CI o boot che blocca l'avvio se `APP_DEBUG=true` e host non locale. Rimuovere le route AI orfane. Messaggi generici all'utente.
- **Test:** test di configurazione che carica `.env.production.example` e verifica `debug=false`, `env=production`, `trusted_hosts` non vuoto. Feature test `POST /api/ai/classify` → 404 (route assente).
- **Confidenza:** 70.

## SEC-12 — Mappa pubblica: posizione esatta e GeoJSON non paginato

- **Gravità:** bassa
- **Tipo:** ipotesi da verificare (scelta di prodotto)
- **Evidenza [F]:** `Modules/Fixcity/app/Actions/BuildTicketsGeoJsonAction.php:43` `->latest()->get()` su tutti i ticket pubblici, senza limite. `:86-99` coordinate esatte + `address` + `city`. Le pagine `Modules/Fixcity/resources/views/pages/api/*` sono montate con il solo middleware `web` (`FixcityServiceProvider.php:61-66`), senza throttle. L'autore, l'email e `owner_id` **non** sono esposti (verificato in `BuildTicketsGeoJsonAction`, `BuildTicketPublicDetailsPayloadAction`, `list.blade.php`), e il codice di tracciamento esce solo al proprietario (`BuildTicketPublicDetailsPayloadAction.php:54-56`). La ricerca `q` usa binding (`ApplyPublicTicketFiltersAction.php:33-40`): nessuna SQL injection.
- **Impatto:** segnalazioni fatte da casa espongono l'indirizzo del cittadino. Con molti ticket, l'endpoint senza paginazione è un vettore di DoS.
- **Correzione:** arrotondare le coordinate pubbliche (ad esempio 3 decimali) o permettere al cittadino di non pubblicare il civico; aggiungere `throttle:60,1`, paginazione o bounding box, e cache della risposta.
- **Test:** test che le coordinate in `/api/tickets/geojson` abbiano al massimo N decimali e che la risposta abbia un limite di feature.
- **Confidenza:** 60.

## SEC-13 — Codice morto pericoloso (policy permissiva, XSS latente)

- **Gravità:** bassa
- **Tipo:** problema altamente probabile (latente)
- **Evidenza [F]:**
  - `Modules/Fixcity/app/Models/Policies/TicketPolicy.php:19-45`: tutte le abilità restituiscono `true`. Oggi è inattiva perché `FixcityServiceProvider.php:40` registra esplicitamente `Modules\Fixcity\Policies\TicketPolicy`, che è corretta: `view` limitata a PA, pubblico o proprietario; `delete` solo admin. Ma la classe permissiva sta nel percorso che Laravel indovina automaticamente (`Models\Policies\{Model}Policy`): se la registrazione esplicita venisse rimossa, diventerebbe attiva.
  - `Modules/Fixcity/resources/views/tickets/show.blade.php:33` `{!! $ticket->content !!}`, dove il contenuto è testo libero del cittadino. Non ho trovato riferimenti alla vista (`rg fixcity::tickets.show`: solo test che ne vietano l'uso). La vista pubblica reale fa l'escape (`Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php:132` `nl2br(e(...))`).
  - Altri `{!! !!}` verificati su contenuti di admin o già sanificati: CMS (`Modules/Cms/resources/views/livewire/page/show.blade.php:28-52`), commenti (`Modules/Comment/.../comment.blade.php:80`, testo sanificato da `SanitizeCommentHtmlAction` con `allowSafeElements`), `HtmlString` in `TicketForm.php:395-405` con `e()`.
- **Correzione:** cancellare la policy legacy e la vista `tickets/show.blade.php`.
- **Test:** test arch: nessun file `Modules/*/app/Models/Policies/*Policy.php` con metodi che fanno solo `return true`; test che vieta `{!! $ticket->` nelle viste.
- **Confidenza:** 75.

## SEC-14 — Creazione ticket senza rate limit e senza email verificata

- **Gravità:** bassa
- **Tipo:** bug dimostrato
- **Evidenza [F]:** `Modules/Fixcity/resources/views/pages/tickets/create.blade.php:14-17` usa solo il middleware `auth`, non `verified`. In `CreateTicketWizardWidget::save()` (`:85-129`) `SubmitTicketOnceAction` garantisce l'idempotenza dello stesso form, ma non c'è un limite per utente o IP. Fanno eccezione follow (`routes/web.php:37` `throttle:30,1`), track e conferma (`throttle:10,1`) e il lookup per codice (`GetPublicTicketByCodeAction.php:42-55`).
- **Impatto:** spam di segnalazioni con allegati fino a 5×10 MB, saturazione del disco e della moderazione.
- **Correzione:** `RateLimiter` per utente in `save()` (ad esempio 5 ticket/ora), richiesta di `verified` e quota disco per utente.
- **Test:** Livewire test che crea N+1 ticket in un minuto e si aspetta un errore di throttle.
- **Confidenza:** 80.

## SEC-15 — Gestione dipendenze

- **Gravità:** bassa
- **Tipo:** bug dimostrato (configurazione)
- **Evidenza [F]:** `laravel/composer.json` usa `merge-plugin` su `Modules/*/composer.json`, `minimum-stability: dev` con `prefer-stable: true`, e non c'è `composer.lock`. Ho trovato **78 vincoli `*`** (elenco completo in `/tmp/claude-0/-home-claude/c5e605f6-c060-5a89-a098-1f94319d5901/scratchpad/deps.tsv`). Tra i pacchetti di runtime:
  - `Modules/User/composer.json`: `laravel/passport *`, `jenssegers/agent *`, `socialiteproviders/auth0 *`, `spatie/laravel-personal-data-export *`, `flowframe/laravel-trend *`;
  - `Modules/Xot/composer.json`: 42 pacchetti a `*`, tra cui `laravel/tinker` in `require` di produzione, `spatie/laravel-permission`, `livewire/livewire`, `guzzlehttp/guzzle`, `spipu/html2pdf`, `predis/predis`;
  - `Modules/Media`: `intervention/image *`; `Modules/AI`: `openai-php/laravel *`; `Modules/Comment`: `symfony/html-sanitizer *`, `livewire/livewire *`; `Modules/Lang`: `mcamara/laravel-localization *`, `lara-zeus/spatie-translatable *`.
  - `Modules/Comment/packages/spatie/laravel-comments*` sono fork vendorizzati di pacchetti spatie: non ricevono aggiornamenti di sicurezza upstream.
  - `jenssegers/agent`: per quanto ne so non è più mantenuto attivamente (nessuna release recente). Non l'ho verificato online.
- **Impatto:** build non riproducibili. Un `composer update` può installare major nuove o versioni dev. Impossibile fare audit CVE senza lock.
- **Correzione:** committare `composer.lock` (e `package-lock.json`), sostituire `*` con `^x.y`, togliere `minimum-stability: dev` o limitarlo a pacchetti specifici, spostare `tinker` in `require-dev`, `composer audit` in CI.
- **Test:** job CI `composer validate --strict && composer audit --locked`, più uno script che fallisce se un `composer.json` contiene `"*"`.
- **Confidenza:** 90.

## SEC-16 — Modulo AI (openai-php)

- **Gravità:** bassa
- **Tipo:** ipotesi da verificare
- **Evidenza [F]:** la chiave arriva da `config('ai.openai_api_key')` o `config('services.openai.api_key')` (`Modules/AI/app/Actions/MakeAIRequestAction.php:28`) e da `config/openai.php:15` `env('OPENAI_API_KEY')`: non è hardcoded. Il base URL arriva da settings (`AIConfigData::openai_base_url`, `MakeAIRequestAction.php:30`) e riceve l'header `Authorization: Bearer <key>` (`:43-48`). I prompt includono titolo e descrizione dei ticket (`SuggestSolutionsAction.php:50-52`). Nessun modulo fuori da AI richiama il modulo AI (`rg "Modules\\AI\\"`: 0 risultati), e le route API puntano a un controller inesistente (SEC-11).
- **Impatto [D]:** chi può modificare le impostazioni AI può puntare il base URL a un host interno (SSRF) o esterno ed esfiltrare la chiave. Se il modulo venisse collegato ai ticket, il testo del cittadino entrerebbe nel prompt (prompt injection su classificazione e priorità) e i dati personali andrebbero a un fornitore extra-UE.
- **Correzione:** allow-list del base URL (solo `https://api.openai.com` o un endpoint approvato), validazione in `AIConfigData`. Trattare l'output del modello come non fidato (enum chiusi). DPIA prima di inviare dati dei ticket.
- **Test:** unit test che `AIConfigData` rifiuta `openai_base_url=http://169.254.169.254`.
- **Confidenza:** 50.

## SEC-17 — Azioni artisan dal web (dead code)

- **Gravità:** bassa (informativa)
- **Tipo:** problema altamente probabile (latente)
- **Evidenza [F]:** `Modules/Xot/app/Actions/Artisan/HandleArtisanActRequestAction.php`: `clearAll()` include `key:generate` (rigenera APP_KEY: invalida sessioni e dati cifrati). `module:disable '.$moduleName` usa `Request::input('module')` in concatenazione. Secondo il commento in `Handlers/ModuleCommandHandler.php:14` non ha chiamanti, e non ho trovato route che la invochino. `ArtisanHeaderAction.php:31` e `RunArtisanCommandAction.php:32` sono raggiungibili solo dai pannelli admin.
- **Correzione:** togliere `key:generate` da `clearAll`, validare `module` contro `Module::all()`, cancellare il codice se inutilizzato.
- **Test:** unit test di `HandleArtisanActRequestAction` con `Artisan::spy()`: `clear` non deve invocare `key:generate`.
- **Confidenza:** 70.

---

## Altre verifiche con esito positivo o neutro

- **Login/registrazione/reset [F]:** login FO con throttle per email+IP e per IP (`LoginWidget.php:77-109`) e `session()->regenerate()`. Registrazione e password dimenticata con `ConsumeAuthSubmissionSlotAction` (10 tentativi/IP in 10 minuti, `AuthThrottleScope.php:16-19`); la registrazione forza `email_verified_at=null` (`RegisterWidget.php:90-93`). Verifica email con `signed` + `throttle:6,1` (`Modules/User/routes/auth.php:24`). Logout via POST con CSRF (`routes/web.php:28-31`). API login con limiter `user-login` (`UserServiceProvider.php:156`). Socialite usa `state` (nessun `stateless()`, `RetrieveOauthUserAction.php:32`).
- **SPID/CIE [F]:** esistono solo come implementazione "fatta in casa" nel tema (SEC-02). CIE con `state` e `nonce` controllati e `id_token` ottenuto dal token endpoint (`CieAuthAction.php:77-99`, `:242-269`). La firma JWT non viene verificata, che è accettabile solo se il token arriva via back-channel TLS. Nessuna libreria `italia/spid-*` o OIDC federation tra le dipendenze.
- **Autorizzazione ticket [F]:** `Modules/Fixcity/app/Policies/TicketPolicy.php` è corretta. Il dettaglio JSON e il follow usano `BuildPublicTicketsQueryAction` (solo stati `canViewByAll` o proprietario), quindi nessun IDOR sui ticket non pubblici via `/api/ticket-details/{id}` e `/{lang}/tickets/{id}/follow`. Il codice di tracciamento ha 16 caratteri casuali (`AllocateTicketCodeAction.php`) e il lookup per codice ha un limite sui tentativi falliti.
- **Mass assignment [F]:** `Ticket::$fillable` comprende `status`, `owner_id` e `responsible_id`, ma il wizard pubblico li sovrascrive lato server (`CreateTicketWizardWidget.php:115-119`). `BaseUser::$fillable` comprende `email_verified_at`, `type`, `is_active` e `id`: nei percorsi visti (`RegisterWidget`) passano solo i campi dello schema. `$guarded = []` solo su modelli Comment/Media/Rating (`Modules/Comment/app/Models/Comment.php:63`, `Modules/Media/app/Models/TemporaryUpload.php:92`): da tenere sotto controllo, nessun percorso pericoloso trovato.
- **SQL raw [F]:** nessun `whereRaw`/`DB::raw` con input concatenato (rg + semgrep locale). `orWhereRaw` in `ApplyPublicTicketFiltersAction.php:40` usa binding.
- **Path traversal [F]:** `ServeThemeAssetAction` rifiuta `..`; download log via `ResolveLogFilePathAction`, confinato a `storage/logs` e protetto da ruolo.
- **CSRF [F]:** nessuna esclusione configurata. Cancellazione account GDPR: GET → solo redirect, POST con conferma `cancella` (`DeleteAccountAction.php:43-51`).
- **Sessione [F]:** `encrypt` true, `http_only` true, `same_site` lax, `secure` e `expire_on_close` true in produzione, lifetime 60 minuti in produzione (`config/session.php`). Header di sicurezza con HSTS e CSP (`app/Http/Middleware/SecurityHeaders.php`, `config/security.php:109-129`).
- **CORS [F/D]:** non c'è `config/cors.php`, quindi valgono i default del framework (`api/*`, origini `*`, senza credenziali). Accettabile per dati già pubblici.
- **Monitoraggio [F]:** Pulse protetto da `viewPulse` = `super-admin` (`UserServiceProvider.php:138`). Telescope, Horizon e log-viewer non sono tra le dipendenze. Debugbar è solo in `require-dev` e `DEBUGBAR_ENABLED=false` negli env.
- **Multi-tenant [F]:** non esiste un partizionamento per riga (`tenant_id`) sui ticket. L'isolamento fra Comuni si regge solo sul DB per host (SEC-03). In uno stesso tenant gli operatori PA vedono tutti i ticket, come da policy: è la scelta di prodotto attuale.
- **Audit trail [F]:** le transizioni di workflow (approva, rifiuta, chiudi, assegna, cambio stato, comunicazioni) scrivono `TicketActivity` con visibilità. Modifiche libere e cancellazioni da back office non sono tracciate (SEC-08). La cancellazione GDPR fa il soft-delete dei log activity dell'utente (SEC-05).

## Cosa non ho potuto verificare e perché

- **Esecuzione dell'app** (`route:list`, test Pest, PHPStan, `composer audit`): niente `vendor/`, PHP 8.3 contro il 8.4 richiesto, nessun lock file. Quindi:
  - l'ordine di risoluzione dei mount Folio duplicati (SEC-09);
  - se il mount tema di `app/Providers/FolioServiceProvider.php` riceve davvero il gruppo `web`;
  - il comportamento effettivo di Livewire e Filament (SEC-08);
  - la presenza delle route Passport.
- **Schema DB reale:** non ho visto il database. La colonna `fiscal_code` (SEC-02) e il nome file reale dei media (SEC-04) restano da verificare.
- **Infrastruttura:** configurazione di Apache/Nginx (`UseCanonicalName`, vhost, quanti tenant per server), `.env` reale di produzione, servizio di `/storage` (SEC-03, SEC-04, SEC-11).
- **CVE delle dipendenze:** senza lock non si conoscono le versioni installate. Non ho dichiarato CVE.
- **Semgrep:** il registry `semgrep.dev` è bloccato dal proxy. Ho usato solo regole locali (exec/unserialize/redirect/file-op): 28 risultati, tutti in comandi console o pagine admin, valutati in SEC-17. Blade non è supportato da semgrep: i `{!! !!}` li ho controllati con ripgrep (160 occorrenze, analizzate a campione sui moduli rilevanti).
- **Frontend JS** (`camera-image.js`, mappe Leaflet/Google) e tema TwentyOne: solo a campione.

## Copertura stimata

| Area | Copertura | Motivazione |
|------|-----------|-------------|
| Autenticazione (password, Socialite, SPID/CIE, Passport API, reset, verifica) | 80% | letti tutti i flussi principali; non verificati il login dei pannelli Filament e le route Passport effettive |
| Autorizzazione / IDOR ticket e API mappa | 85% | policy, query pubbliche, API Folio, follow, tracking letti integralmente |
| Multi-tenant | 60% | logica di risoluzione letta; manca l'infrastruttura |
| Upload/media | 75% | componenti, collezioni, strip EXIF, disco e path generator; non verificati limiti Livewire tmp e config del web server |
| XSS | 60% | `{!! !!}` sui moduli rilevanti e `HtmlString` Fixcity; non tutti i 160 casi, nessun controllo JS |
| Mass assignment / SQL | 70% | modelli sensibili e grep raw SQL; non tutti i percorsi `create/fill` di tutti i moduli |
| AI | 60% | chiavi, base URL, chiamanti; non tutti gli action del modulo |
| CSRF / rate limit / sessione / CORS | 80% | config e middleware letti |
| Pagine demo/debug | 70% | file letti; routing effettivo non verificabile |
| Audit trail | 60% | azioni workflow e observer; spatie activitylog non approfondito |
| Dipendenze | 70% | tutti i `composer.json` analizzati; nessun lock, quindi nessuna analisi CVE |
| **Totale ponderato** | **~70%** | analisi statica ampia sui percorsi critici; i limiti principali sono l'assenza di runtime e di infrastruttura |
