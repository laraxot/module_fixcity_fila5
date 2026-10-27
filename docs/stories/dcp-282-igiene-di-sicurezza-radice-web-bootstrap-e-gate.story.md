---
id: dcp-282
title: "DCP-282: igiene di sicurezza: radice web, bootstrap, rotta orfana, login API, proxy fidati e scrittore di traduzioni"
type: story
epic: go-live-security
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 282 info.php phpinfo public_html file di prova go-live-gate accessibility-incomplete bootstrap providers boost mcp no-dev rotta form-create-ticket api user login throttle trustProxies trustHosts save_missing_translations isLocal"
related: [dcp-139, dcp-154, dcp-186, dcp-197, dcp-202, dcp-208, dcp-275]
tags: [sicurezza, go-live, gate, bootstrap, throttle, bmad]
---

# DCP-282: igiene di sicurezza: radice web, bootstrap, rotta orfana, login API, proxy fidati e scrittore di traduzioni

## Stato

Fatta, con DA DECIDERE elencati in fondo all'Esito. Sette correzioni nate dall'audit dei gap (G-02, G-09, B-02, B-07, F-01, A-08, D-08, C-05), ciascuna con test rosso prima e verde dopo. Le affermazioni dei report sono state riverificate nel codice prima di toccare: sotto, ogni fatto e' marcato MISURATO (comando eseguito oggi, 2026-10-26) o DEDOTTO (letto nel codice, non eseguito).

Fase BMAD: Dev (le fasi Analisi e Plan sono i report dei gap). Nessun commit, nessun cambio di branch: solo working tree.

## Fatti misurati prima di toccare

| Gap | Affermazione del report | Verifica | Marcatura |
|---|---|---|---|
| G-02, B-07 | `public_html/info.php` e' tracciato e risponde 200 | `git ls-files public_html/info.php` stampa il file. `curl` su 127.0.0.1:8091, :8081 e :8000: `info.php` 200 (17 byte), `test.txt` 200 (11), `serve-root-test.txt` 200 (19), `data/tickets_test.json` 200 (3222). Il server di sviluppo (`php artisan serve`, `Content-type: text/x-php`) restituisce il SORGENTE `<?php phpinfo();`, non lo esegue. Sotto Apache con handler PHP lo eseguirebbe | MISURATO (200 e corpo); l'esecuzione sotto Apache e' DEDOTTA (CLAUDE di Sixteen E1, `.htaccess` manda a `index.php` solo se il file non esiste) |
| G-02 | DCP-197 e DCP-208 danno `info.php` per rimosso | Falso: il file e' tornato con il commit di sync `14c648dc5d "."` e oggi esiste (mtime 2026-10-17) | MISURATO |
| G-02 | altri file di prova tracciati | `git ls-files public_html` elenca: `info.php`, `test.txt`, `serve-root-test.txt`, `hot.stale-20260927`, `hot.stale2-20260927`, `data/tickets_test.json`, `data/tickets-test.json` (0 byte), `data/tickets_big.json` (500 feature sintetiche, 310 KB), `data/tickets-dense.geojson`, `data/tickets-30-points.geojson` (JSON non valido: virgola finale), `data/tickets.json` | MISURATO |
| G-02 | uso dei file di prova | `rg --no-ignore` su `bashscripts`, `tests`, `laravel/{app,config,routes,resources,database,tests,Modules,Themes}`, `public_html`, `docs`: ZERO riferimenti in codice, test, JSON del CMS e JS compilato. Il JS compilato usa solo `/data/tickets.json` (tenuto). Solo docs: `serve-root-test.txt` e' il marcatore che la ricetta `bashscripts/ai/wiki/memories/artisan-serve-public-html-server-php.md` crea da se con `echo`; `tickets_big.json` e' descritto in `Modules/Geo/docs/wiki/concepts/geo-map-data-source.md` come copia di `Modules/Geo/resources/json/cluster-test-data.json` (la fonte resta nel modulo). Nessun dato personale nei GeoJSON | MISURATO |
| G-02 | convenzione di ritiro | `.gitignore:13` ignora `*.old`: rinominando, i file spariscono da git (e quindi dal deploy) e restano sul disco. `.gitignore:119` ignora `*.lock` | MISURATO |
| B-02 | il gate non vede la dichiarazione di accessibilita' vuota | `curl /{it,en,de,es}/accessibilita`: 200 in tutte, `data-element="accessibility-incomplete"` presente 1 volta per lingua, `data-element="legal-incomplete"` presente 0 volte. `incomplete_legal()` cerca solo il secondo marcatore, quindi LEG-04 non conta la pagina. Il JSON `accessibilita.index.json` ha stato, date, email di feedback e URL AgID a `null` (solo `procedure_url` valorizzato) | MISURATO |
| F-01 | Boost e Mcp registrati a mano, pacchetti dev | `bootstrap/providers.php` righe 26-27 e 49-50. `composer.json` `require-dev`: `laravel/boost`; `composer.lock` `packages-dev`: `laravel/boost`, `laravel/mcp`; `packages` (produzione): nessuno dei due. Nessun altro uso di `Laravel\Boost` o `Laravel\Mcp` in `app`, `config`, `Modules/*/app` | MISURATO |
| F-01 | `composer install --no-dev` va in errore fatale al boot | `ProviderRepository` istanzia ogni classe elencata: una classe assente e' `Class not found` | DEDOTTO (non ho rifatto `composer install`: non si cambiano dipendenze) |
| A-08 | rotta orfana `POST /livewire/form-create-ticket` | `php artisan route:list --path=livewire/form-create-ticket` la mostra, solo middleware `web`, verso `CreateTicketWizardWidget@submit`. `rg --no-ignore` su tutto il repo (esclusi `node_modules`, `vendor`, `storage`): le uniche occorrenze di `form-create-ticket` sono `routes/web.php:27` e righe di docs/story. Nessun JS del tema, nessun test, nessun modulo Mobile la chiama | MISURATO |
| A-08 | cache delle rotte | `ls laravel/bootstrap/cache`: niente `routes-v7.php` ne' `config.php` (solo `blade-icons`, `event-handlers`, `events`, `modules`, `packages`, `services`). Dopo la rimozione NON serve `route:cache` in sviluppo | MISURATO |
| D-08 | `POST /api/user/login` senza limite | `route:list --path=api/user`: `api.login`, middleware `['api']`, nessun `throttle`. Nessun `RateLimiter::for` ne' `throttleApi` nel codice. Il limite G-07 e' solo in `Modules/User/app/Filament/Widgets/Auth/LoginWidget.php`: 5 tentativi errati per 60 secondi, chiave `lower(email)|ip` con `Str::transliterate`, messaggio `user::login.actions.login.throttle` | MISURATO |
| G-09 | `trustProxies(at: '127.0.0.1')` fisso, nessun `trustHosts` | `bootstrap/app.php:32`. `TrustProxies::setTrustedProxyIpAddresses` legge `$this->proxies() ?: config('trustedproxy.proxies')`. Il callback di `withMiddleware` gira alla risoluzione del kernel, PRIMA che `.env` sia caricato: `env()` li' non vedrebbe `.env`. `TrustHosts::shouldSpecifyTrustedHosts()` e' falso con `APP_ENV=local` e sotto test; `APP_ENV` di questo ambiente e' `local` (tinker) | MISURATO (config e ambiente) e DEDOTTO dal codice del framework v13.38.0 (temporizzazione) |
| G-09 | proxy HTTPS di sviluppo 192.168.1.40:8080 | In ascolto non c'e': `curl` su :8080 restituisce 000. Non posso provare il percorso reale attraverso il proxy | MISURATO |
| C-05 | l'auto-writer scrive anche in produzione | `SaveTransAction.php:30`: `Config::get('lang.save_missing_translations', ! app()->runningUnitTests())`. A runtime `config('lang.save_missing_translations')` e' NULL e `config()->has(...)` e' falso: la config del modulo vive sotto `lang.lang.*`, quindi vale sempre il default, che e' ACCESO ovunque tranne sotto test. `Modules/Lang/config/lang.php:211` dice `true` ma non e' letta con quel nome | MISURATO |

## Causa

1. File di prova nella radice web: un commit di sync (`14c648dc5d "."`) ha riportato in git file che DCP-186 aveva tolto; nessun controllo automatico li vede, quindi la rimozione non si e' mantenuta.
2. Gate cieco su B-02: il marcatore dell'avviso della dichiarazione di accessibilita' (`accessibility-incomplete`) e' diverso da quello dei testi legali (`legal-incomplete`) e il gate conosce solo il secondo.
3. Provider di sviluppo elencati a mano in `bootstrap/providers.php` come stringhe di classe: con `--no-dev` la classe non esiste e il boot cade.
4. Rotta di prova lasciata in `routes/web.php` quando il wizard e' passato a Livewire (`/livewire-<hash>/update`): nessun chiamante, pero' resta una superficie POST pubblica che istanzia un widget fuori da Livewire.
5. Il limite G-07 e' stato messo nel solo widget; l'endpoint API e' un secondo ingresso alla stessa verifica di password.
6. Proxy fidati scritti nel codice di avvio, dove `env()` non e' disponibile: l'unica via corretta e' la config `trustedproxy` che il framework legge a richiesta.
7. Default dell'auto-writer: pensato per "non rompere i test", non per "non scrivere in produzione".

## Decisioni

- D282-1 (ritiro): i file della tabella sotto sono rinominati con suffisso `.old` nello stesso percorso (regola `no-rm-rename-to-old`), con `mv` sul working tree (nessun `git mv`, nessun `git rm`). Essendo `*.old` ignorato da git, il prossimo commit li vedra' come cancellati.

  | File | Decisione | Motivo |
  |---|---|---|
  | `info.php` | ritirato | phpinfo, zero riferimenti |
  | `test.txt` | ritirato | zero riferimenti |
  | `serve-root-test.txt` | ritirato | la ricetta lo ricrea con `echo` |
  | `hot.stale-20260927`, `hot.stale2-20260927` | ritirati | residui di Vite con URL interni; Vite legge solo `hot` |
  | `data/tickets_test.json`, `data/tickets-test.json` | ritirati | zero riferimenti, uno e' vuoto |
  | `data/tickets_big.json` | ritirato | zero riferimenti nel codice; la fonte e' `Modules/Geo/resources/json/cluster-test-data.json` |
  | `data/tickets.json` | TENUTO | usato dal JS compilato della mappa |
  | `data/tickets-dense.geojson`, `data/tickets-30-points.geojson` | TENUTI, DA DECIDERE | zero riferimenti ma il nome non dice "prova"; proposta: ritirarli se nessuno li usa fuori dal repo |

- D282-2 (gate): nuovo controllo `REPO-03` che FALLISCE se nella radice web (`public_html`, senza seguire i symlink) esiste un file con estensione finale eseguibile (`.php`, `.phtml`, `.phar`, `.php3`-`.php8`) diverso da `index.php` della radice, un `.env*` (anche `.env.old`: contiene ancora i segreti ed e' servito come testo), o un `index.php` che chiama `phpinfo(`. Un file eseguibile ritirato con `.old` non conta. `LEG-04` conta ora anche `accessibility-incomplete`. Nessun controllo esistente cambia significato: `LEG-04` gia' dichiarava di coprire "testi legali" e la dichiarazione di accessibilita' era nella sua lista di pagine.
- D282-3 (providers): le due classi dev sono filtrate con `class_exists` in `bootstrap/providers.php`. In sviluppo la classe esiste e il comportamento e' identico.
- D282-4 (rotta orfana): nessun chiamante, quindi RIMOZIONE (non protezione). Il metodo `submit()` del widget resta (e' l'entrypoint Livewire).
- D282-5 (login API): middleware `throttle:user-login` sulla rotta e limiter nominato `user-login` nel provider del modulo User: 5 tentativi per 60 secondi per `lower(email)|ip`, stesso messaggio del widget (`user::login.actions.login.throttle`). Differenza dichiarata: il widget conta solo i tentativi errati e azzera al successo; il middleware conta tutte le richieste. Per un endpoint API di login e' lo schema standard. Un test blocca la coerenza delle soglie con quelle del widget. La logica sta nell'Action `BuildApiLoginLimitAction` (nessun nuovo controller).
- D282-6 (proxy e host): `trustProxies` legge `config('trustedproxy.proxies')` con default `127.0.0.1` (variabile `TRUSTED_PROXIES`): stesso comportamento di oggi. `trustHosts` e' agganciato ma OPT-IN: senza `TRUSTED_HOSTS` l'elenco e' vuoto e `Request::setTrustedHosts([])` non limita nulla; il framework lo salta comunque con `APP_ENV=local` e sotto test. Non uso il default del framework (host di `APP_URL` piu' sottodomini) perche' non posso provare che NativePHP, i controlli di salute e il proxy di sviluppo non usino altri `Host` con `APP_ENV` diverso da `local`. DA DECIDERE: rendere obbligatorio `TRUSTED_HOSTS` in produzione (proposta: si', con un controllo `ENV-10` nel gate dopo aver confermato con il fornitore gli host reali).
- D282-7 (auto-writer): il default diventa `app()->isLocal()`; l'override via config `lang.save_missing_translations` resta. DA SEGNALARE (non toccato per istruzione): `Modules/Lang/config/lang.php:211` dice `true` e, se un giorno la config del modulo fosse letta con quel nome, riaccenderebbe la scrittura in produzione. Proposta: portarla a `false` o rimuoverla in una story di Lang (`lang-translation-writes-and-probes` ha gia' il punto 1).

## Criteri di accettazione

- [x] AC1 `public_html/info.php`, `test.txt`, `serve-root-test.txt`, `hot.stale*`, `data/tickets_big.json`, `data/tickets_test.json`, `data/tickets-test.json` non esistono col nome originale e hanno la copia `.old`; `curl` su `/info.php` non restituisce piu' il sorgente di phpinfo.
- [x] AC2 `go-live-gate.py --self-test` e' verde con i casi nuovi (REPO-03 e marcatore di accessibilita'); `REPO-03` passa sul repo ripulito e fallirebbe se `info.php` tornasse; `LEG-04` segnala le 4 pagine `accessibilita` (misura reale).
- [x] AC3 `bootstrap/providers.php` non cita `Laravel\Boost` ne' `Laravel\Mcp` senza `class_exists`; `php artisan --version` funziona; un processo PHP con quei namespace bloccati carica il file senza errori.
- [x] AC4 `POST /livewire/form-create-ticket` risponde 404 e non e' in `route:list`; il wizard Livewire passa i suoi test.
- [x] AC5 il sesto `POST /api/user/login` dello stesso email e IP in 60 secondi risponde 429 con il messaggio tradotto; un altro email non e' bloccato.
- [x] AC6 con `TRUSTED_PROXIES` assente, un `X-Forwarded-Proto: https` da 127.0.0.1 e' fidato e da un altro IP no (come oggi); con la variabile impostata cambia; `TRUSTED_HOSTS` e' letto dalla config.
- [x] AC7 `SaveTransAction` non scrive con `APP_ENV=production` (default) e scrive con `APP_ENV=local`; l'override `false` e `true` vince in entrambi.
- [x] AC8 PHPStan a zero sui file PHP nuovi e toccati; Pint solo su quei file.

## Test

| Correzione | File di test | Posizione |
|---|---|---|
| gate | `--self-test` interno a `go-live-gate.py` | `bashscripts/tools/release/go-live-gate.py` |
| providers | `ProvidersDevOnlyGuardTest` | `Modules/Fixcity/tests/Unit/` |
| rotta orfana | `OrphanTicketFormRouteRemovedTest` | `Modules/Fixcity/tests/Feature/Http/` |
| login API | `UserApiLoginThrottleTest` | `Modules/Fixcity/tests/Feature/Api/` |
| proxy e host | `TrustedProxiesAndHostsConfigTest` | `Modules/Fixcity/tests/Feature/Http/` |
| auto-writer | `SaveTransActionDefaultTest` | `Modules/Lang/tests/Feature/` |

I test dei moduli girano con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso>`; mai `pest` diretto.

## Owned file scope

Toccati: `public_html/*` (solo rinomine `.old` dell'elenco D282-1), `bashscripts/tools/release/go-live-gate.py`, `laravel/bootstrap/providers.php`, `laravel/bootstrap/app.php`, `laravel/config/trustedproxy.php` (nuovo), `laravel/config/security.php` (solo la chiave `trusted_hosts`), `laravel/routes/web.php` (solo la riga della rotta orfana e il suo `use`; il file e' condiviso con la sessione sitemap), `laravel/.env.production.example`, `laravel/Modules/User/routes/api.php`, `laravel/Modules/User/app/Providers/UserServiceProvider.php`, `laravel/Modules/Lang/app/Actions/SaveTransAction.php`, i test nuovi della tabella, questa story e `Modules/Fixcity/docs/bmad/decision-log.md`.

NON toccati: Modules/Comment, blocchi Sixteen blog ed eventi, sitemap (Modules/Seo Actions/Sitemap, `SitemapPathsContract`, `FixcityServiceProvider`, `SeoLinksTest`), segreti e `.env` tracciati (DEC-28, DCP-275), dipendenze.

## Esito

Tutti i comandi sono stati eseguiti il 2026-10-26 dal working tree, senza commit ne' cambi di branch.

### 1. G-02 e B-07: file di prova nella radice web (FATTO e VERIFICATO)

- Prima (`curl`, 127.0.0.1:8091 e :8081): `info.php` 200 17 byte (sorgente `<?php phpinfo();`), `test.txt` 200, `serve-root-test.txt` 200, `data/tickets_test.json` 200 3222 byte.
- Dopo il ritiro con `.old` (8 file, elenco in D282-1): `info.php`, `test.txt`, `serve-root-test.txt`, `data/tickets_test.json`, `data/tickets_big.json` rispondono 404 (pagina a tema) su :8091 e :8081; `data/tickets.json` resta 200 `application/json` 24442 byte (la mappa lo usa). `git status --short public_html`: 8 file ` D`, nessun altro.
- Il server di sviluppo serve ancora `/info.php.old` come testo (200, 17 byte, sorgente non eseguito): `*.old` e' ignorato da git (`.gitignore:13`), quindi non viaggia col deploy. Se il web server di produzione usasse `AddHandler` (esegue `.php` in qualunque posizione del nome) e il deploy copiasse il disco invece di git, `info.php.old` andrebbe tolto a mano: il gate non lo segnala di proposito (vedi D282-2).
- DCP-197 (E1) e DCP-208 davano `info.php` per rimosso: era falso dal commit di sync `14c648dc5d`. Ora lo e', e il gate lo controlla.

### 2. Gate (FATTO e VERIFICATO)

- `python3 -I bashscripts/tools/release/go-live-gate.py --self-test`: prima dei casi nuovi era verde con 10 casi; scritti i casi nuovi, rosso (`AssertionError` su `incomplete_legal`); dopo il codice: `self-test OK: 10 configurazioni sbagliate segnalate, la configurazione corretta passa, avvisi dei testi legali e della dichiarazione di accessibilita' riconosciuti, marker di merge riconosciuti, file di debug nella radice web riconosciuti`.
- `REPO-03` sul repo reale: prima del ritiro `FAIL | 1 file in public_html: info.php`; dopo `PASS | nessuno`.
- `LEG-04` sulle pagine reali (:8081): con il solo marcatore vecchio 12 pagine su 20 con avviso (tutte e solo `privacy`, `terms`, `note-legali`); con il marcatore esteso 16 su 20, cioe' anche `it/en/de/es accessibilita`. Il gate non era mai stato verde per i testi legali; ora non puo' esserlo nemmeno con la dichiarazione di accessibilita' vuota.
- Esecuzione intera `go-live-gate.py --skip-pest` (exit 1): `[PASS] REPO-03 ... nessuno`, `[FAIL] LEG-04 ... 16 su 20 con avviso (es. de/accessibilita, de/note-legali, de/privacy, de/terms)`, `RISULTATO: NON pubblicabile: 12 controlli bloccanti falliti, 3 requisiti LG non soddisfatti, 2 avvisi, 17 superati su 34`. Gli altri bloccanti non sono di questa story. Il gate ha segnalato anche, fuori scope, un marcatore di merge in `laravel/Modules/Rating/package-lock.json` (REPO-01) e 4 nomi di `.md` con data (REPO-02).

### 3. F-01: provider di sviluppo (FATTO e VERIFICATO)

- `ProvidersDevOnlyGuardTest`: rosso (`Laravel\Boost\BoostServiceProvider e un pacchetto require-dev: con --no-dev il boot cade`), verde dopo: `2 passed (79 assertions)`. La simulazione `--no-dev` e' un processo PHP con i namespace `Laravel\Boost\` e `Laravel\Mcp\` nascosti all'autoloader (`vendor/` intatto, dipendenze invariate).
- In sviluppo: `php artisan --version` stampa `Laravel Framework 13.38.0`; `php artisan list --raw` mostra ancora 7 comandi `boost:` e `mcp:`; il secondo test controlla che i due provider siano ancora in elenco quando i pacchetti ci sono.
- NON provato: un vero `composer install --no-dev` (vieta il cambio di dipendenze).

### 4. A-08: rotta orfana (FATTO e VERIFICATO)

- `OrphanTicketFormRouteRemovedTest`: rosso (`Failed asserting that an array does not contain 'livewire/form-create-ticket'`; `Expected response status code [404] but received 302`), verde dopo: `3 passed (5 assertions)`. Il POST ospite ora risponde 405 (un'altra rotta solo GET risponde allo stesso percorso), non esegue nulla; dal vivo `POST :8081/livewire/form-create-ticket` risponde 405.
- `php artisan route:list --path=livewire/form-create-ticket`: `Your application doesn't have any routes matching the given criteria.`
- Il wizard Livewire funziona: `CreateTicketWizardWidgetTest`, `CreateTicketWizardTamperingTest`, `CreateTicketWizardDataStepTest`, `CreateTicketWizardInputValidationTest`: `25 passed (129 assertions)`.
- `routes/web.php`: tolti solo la riga della rotta e il suo `use` (la sessione sitemap ha modificato altre righe dello stesso file, intatte). Cache delle rotte: `ls laravel/bootstrap/cache` non contiene `routes-v7.php`, quindi in questo ambiente NON serve `php artisan route:cache`. Se una installazione la usa, va rifatta (non l'ho eseguita).

### 5. D-08: login API (FATTO e VERIFICATO)

- `UserApiLoginThrottleTest`: rosso (`5 failed, 1 passed`: 404 invece di 429, classe e middleware assenti), verde dopo: `6 passed (17 assertions)`.
- Dal vivo (:8081, email casuale): tentativi 1-5 `404 {"success":false,"message":"Unauthorised."...}`, tentativo 6 `429 {"success":false,"message":"Troppi tentativi di accesso. Riprova tra 56 secondi.","data":[]}`, tentativo 7 `429 ... 55 secondi`.
- Il `404` per credenziali errate e' preesistente (`XotBaseController::sendError` ha `404` come default): non lo cambio qui. Proposta: `401`.
- Il widget non e' stato toccato; `LoginThrottleTest` (G-07) resta verde.

### 6. G-09: proxy e host fidati (FATTO e VERIFICATO, con un DA DECIDERE)

- `TrustedProxiesAndHostsConfigTest`: rosso (`7 failed`), verde dopo: `7 passed (22 assertions)`. Default invariato: da 127.0.0.1 `X-Forwarded-Proto: https` e' fidato, da 203.0.113.9 no.
- Dal vivo, sviluppo (:8081, `APP_ENV=local`): `Host: evil.example` 200 (nessuna restrizione); con `X-Forwarded-Proto: https` da localhost il server invia `Strict-Transport-Security`, senza no: invariato.
- Simulazione di produzione senza toccare `.env` (`php -S` su :8095 con `APP_ENV=production TRUSTED_HOSTS=fixcity.example.it,www.fixcity.example.it`, poi chiuso): `Host: fixcity.example.it` 200, `WWW.fixcity.example.it` 200, `evil.example` 400, `127.0.0.1:8095` 400. Attenzione: con `TRUSTED_HOSTS` impostata anche i controlli di salute fatti per IP ricevono 400.
- `SecurityHeadersTest`, `AuthReturnsToRequiredPageTest`, `LivewireUpdateEndpointTest`: verdi con `TrustHosts` nello stack (15 passed con `LoginThrottleTest`).
- NON provato: il proxy HTTPS di sviluppo 192.168.1.40:8080 (non e' in ascolto, `curl` 000). Per questo `trustHosts` e' opt-in e vuoto di default.
- Variabili documentate in `laravel/.env.production.example` (`TRUSTED_PROXIES`, `TRUSTED_HOSTS`, valori d'esempio senza segreti).

### 7. C-05: auto-writer di Lang (FATTO e VERIFICATO)

- `SaveTransActionDefaultTest`: rosso (`2 failed, 4 passed`: in `production` e `staging` scriveva), verde dopo; insieme a `SaveTransActionRespectsSwitchTest`: `8 passed (16 assertions)`. Il test scrive su un percorso temporaneo: l'albero di Lang non e' stato toccato (`git status` di Lang: solo `SaveTransAction.php` modificato e il test nuovo).
- Ambiente di sviluppo (`APP_ENV=local`, verificato con tinker): l'auto-writer resta acceso. Il commento nel codice dice perche'.
- Trovata una debolezza del test preesistente `SaveTransActionRespectsSwitchTest` (non toccato, istruzione «non toccare altro in Lang»): «sotto test non scrive nemmeno senza configurazione esplicita» usa `Config::offsetUnset()`, che nel repository di Laravel imposta la chiave a `null` e non la rimuove; `Config::get()` restituisce `null` e non il default, quindi quel test non ha mai provato il default. Il mio test toglie davvero la chiave.

### Qualita'

- PHPStan (config unica, da `laravel/`): sugli 8 file di modulo (`BuildApiLoginLimitAction`, `UserServiceProvider`, `SaveTransAction` e i 5 test) `[OK] No errors`, exit 0; su `bootstrap/app.php`, `bootstrap/providers.php`, `config/trustedproxy.php`, `config/security.php`, `Modules/User/routes/api.php`, `routes/web.php` `[OK] No errors`, exit 0 (dopo un errore reale corretto: la closure di `trustHosts` doveva restituire una lista, ora `array_values`).
- Pint: eseguito sui due test che ne avevano bisogno; `pint --test` su tutti gli altri file toccati, `passed`. NON eseguito su `SaveTransAction.php` (Pint riscriverebbe il codice preesistente: `yoda_style`, `unary_operator_spaces`, `phpdoc_align`) ne' su `routes/web.php` (cambierebbe l'ordine degli `use` della sessione sitemap).
- Pest, tutti i test nuovi insieme: `6 file PASS, Tests: 26 passed (143 assertions)`; regressione: `15 passed (89 assertions)`.

### Da decidere

1. `data/tickets-dense.geojson` e `data/tickets-30-points.geojson`: tenuti (zero riferimenti, nome non marcato come prova; il secondo e' JSON non valido). Proposta: ritirarli con `.old` se nessuno li usa fuori dal repo.
2. `TRUSTED_HOSTS` obbligatoria in produzione e controllo `ENV-10` nel gate, dopo aver confermato con il fornitore gli host reali (anche NativePHP e controlli di salute).
3. `Modules/Lang/config/lang.php:211` dice `save_missing_translations => true` ma la config del modulo vive sotto `lang.lang.*`: oggi non e' letta con quel nome. Se lo diventasse, riaccenderebbe la scrittura in produzione. Proposta: `false` o rimozione, nella story `lang-translation-writes-and-probes` (punto 1).
4. `config/security.php` `rate_limit` (`LOGIN_RATE_LIMIT` 5 e `LOGIN_RATE_LIMIT_DECAY` 15 minuti) non e' letta da nessun codice e contraddice le soglie reali (5 per 60 secondi): rimuoverla o collegarla, e togliere le due variabili da `.env.production.example`.
5. Codice di risposta delle credenziali errate dell'API: `404` preesistente, proposta `401`.
6. Handler PHP del fornitore: se usa `AddHandler`, controllare che non resti `info.php.old` sul disco di produzione.
7. Le story DCP-197 (E1) e DCP-208 (riga `info.php`) affermano ancora «rimosso»: non le ho modificate (storie altrui); questa story e' il riferimento corretto.

### File toccati

`public_html/{info.php,test.txt,serve-root-test.txt,hot.stale-20260927,hot.stale2-20260927}` e `public_html/data/{tickets_test.json,tickets-test.json,tickets_big.json}` (rinominati `.old`); `bashscripts/tools/release/go-live-gate.py`; `laravel/bootstrap/providers.php`; `laravel/bootstrap/app.php`; `laravel/config/trustedproxy.php` (nuovo); `laravel/config/security.php`; `laravel/routes/web.php`; `laravel/.env.production.example`; `laravel/Modules/User/routes/api.php`; `laravel/Modules/User/app/Providers/UserServiceProvider.php`; `laravel/Modules/User/app/Actions/Auth/BuildApiLoginLimitAction.php` (nuovo); `laravel/Modules/Lang/app/Actions/SaveTransAction.php`; test nuovi: `Modules/Fixcity/tests/Unit/ProvidersDevOnlyGuardTest.php`, `Modules/Fixcity/tests/Feature/Http/OrphanTicketFormRouteRemovedTest.php`, `Modules/Fixcity/tests/Feature/Api/UserApiLoginThrottleTest.php`, `Modules/Fixcity/tests/Feature/Http/TrustedProxiesAndHostsConfigTest.php`, `Modules/Lang/tests/Feature/SaveTransActionDefaultTest.php`; questa story e `Modules/Fixcity/docs/bmad/decision-log.md`.
