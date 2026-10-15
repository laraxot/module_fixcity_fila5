---
id: dcp-139
title: "DCP-139: intestazioni di sicurezza HTTP e hardening per la produzione"
type: story
epic: dc-parity-v2
status: in-progress
priority: P1
owner: app:laravel + module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "security headers csp hsts x-frame-options nosniff x-powered-by securityheaders middleware permissions-policy geolocation debugbar produzione tls"
related: [dcp-112, dcp-138, dcp-137]
---

# DCP-139: intestazioni di sicurezza attive e ambiente di produzione verificato

Nata da DCP-112 (matrice normativa N-14).

## Lacuna e prova (2026-10-13)
- `curl -sI http://192.168.1.40:8000/it/auth/login` e `/it/tickets`: nessun `Content-Security-Policy`, `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy`, `Strict-Transport-Security`; presente `X-Powered-By: PHP/8.4.26`.
- `laravel/app/Http/Middleware/SecurityHeaders.php` esiste ma non e' registrato da nessuna parte (grep). Se lo fosse cosi' com'e', `Permissions-Policy: geolocation=()` (riga 57) disattiverebbe il pulsante «Usa la tua posizione» del passo 2, e la CSP `script-src 'self' 'unsafe-inline' https:` e' molto permissiva.
- Il vhost `laravel/config/vhost/fixcity.local-ssl.conf` imposta `nosniff` e `X-Frame-Options SAMEORIGIN`; HSTS e' commentato; nessuna CSP.
- `.env` locale: `APP_DEBUG=true`, `DEBUGBAR_ENABLED=true`; `.env.production.example` ha `false` per entrambi. Il proxy TLS `https://192.168.1.40:8080` non ha risposto: HTTPS non verificato.

## Norma
GDPR art. 32 (sicurezza del trattamento, testo nel registro fonti 02). Per un ente soggetto alle misure minime AgID (circolare 2/2017) o a NIS2 valgono i controlli del rispettivo ambito (da verificare con l'ente, N-20..N-22).

## Requisiti
1. Registrare un solo middleware di intestazioni (riusare `SecurityHeaders`, correggendo `geolocation=(self)`), con CSP elaborata sul risultato di DCP-137 (host reali) e test con Livewire, Filament e Alpine.
2. HSTS solo su HTTPS; rimuovere `X-Powered-By` (`expose_php=Off` o middleware).
3. Lista di controllo di produzione eseguibile: `APP_DEBUG=false`, `DEBUGBAR_ENABLED=false`, `SESSION_SECURE_COOKIE=true`, cookie `Secure`, redirect a HTTPS; verifica su ambiente con TLS.
4. Test d'integrazione che legge le intestazioni di tre rotte e fallisce se ne manca una.

## Criteri di accettazione
- [x] `curl -sI` su `/it`, `/it/auth/login`, `/it/tickets`: presenti CSP, `X-Frame-Options` o `frame-ancestors`, `nosniff`, `Referrer-Policy`; assente `X-Powered-By`. (Provato sul server di sviluppo, vedi "Esito". La CSP completa e' in `Report-Only`: `frame-ancestors` e' l'unica parte applicata.)
- [x] La geolocalizzazione del passo 2 funziona con le intestazioni attive (Playwright con permesso concesso): sonda `geo-picker-centering-probe.mjs` 39/39 prima e dopo, origine LAN non sicura e 127.0.0.1.
- [x] Nessun errore CSP nella console su home, login, elenco, wizard (passi 1, 2 e Riepilogo) e login di Filament: 0 eventi `securitypolicyviolation`. Non provato: le pagine di Filament dopo il login (vedi "Cosa manca").
- [x] Risultato della lista di controllo allegato alla story con data e ambiente (ambiente di produzione simulato con TLS: 19/19; sviluppo: 13/19 come atteso). L'ambiente di produzione vero non esiste in questo repository: NON VERIFICATO.

## Dipendenze e priorita'
DCP-137 (host terzi), DCP-138. P1.

## Esito dell'implementazione (2026-10-13, fase BMAD: sviluppo e verifica)

### Che cosa e' cambiato
| File | Cambio |
|---|---|
| `laravel/app/Http/Middleware/SecurityHeaders.php` | Riscritto: legge `config('security.headers')`. `nosniff`, `X-Frame-Options` (SAMEORIGIN, come il vhost), `Referrer-Policy`, `Permissions-Policy` con `geolocation=(self)` (prima `geolocation=()`, che spegneva «Usa la tua posizione»), HSTS solo se `$request->isSecure()` e `max_age > 0` (senza `includeSubDomains` e `preload` finche' il dominio non e' deciso), CSP in tre modi (`off`, `report-only`, `enforce`), `header_remove('X-Powered-By')`. Un'intestazione gia' impostata dalla risposta non viene toccata. Tolti `X-XSS-Protection` (deprecata, in browser vecchi crea XSS) e il `script-src https:` che ammetteva qualunque host. |
| `laravel/bootstrap/app.php` | `$middleware->append(SecurityHeaders::class)`: unico punto di registrazione (middleware globale, vale anche per Filament e Livewire). |
| `laravel/config/security.php` | Sezione `headers` riscritta (prima non la leggeva nessuno). Chiavi `SECURITY_HEADERS_ENABLED`, `SECURITY_CSP_MODE` (default `report-only`), `SECURITY_HSTS_MAX_AGE`, `SECURITY_HSTS_INCLUDE_SUBDOMAINS`, `SECURITY_HSTS_PRELOAD`. Corretto di passaggio un errore PHPStan preesistente alla riga 60 (`explode` su `env()`). |
| `laravel/.env.production.example` | Le chiavi `FORCE_HTTPS`, `SECURE_COOKIES`, `SECURE_HEADERS` non le legge nessun file di config: aggiunte quelle vere (`SESSION_SECURE_COOKIE`, `SECURITY_*`). Le tre vecchie restano, con nota. |
| `Modules/Fixcity/tests/Feature/Http/SecurityHeadersTest.php` | 8 test: tre rotte (`/it`, `/it/auth/login`, `/it/tickets`) con tutte le intestazioni; `geolocation=(self)`; host misurati nella CSP; HSTS solo su HTTPS e con `max_age` 0 spento; modi `enforce`/`off`; `enabled=false` e intestazione gia' presente. Provato: con `append` commentato 7 test su 8 falliscono. |
| `bashscripts/tools/visual/net-hosts-probe.mjs` (+ doc) | Misura le richieste di rete per direttiva e gli eventi `securitypolicyviolation`. |
| `bashscripts/tools/security/production-checklist.sh` (+ doc in `bashscripts/docs/tools/`) | Lista di controllo di produzione eseguibile. |

`Modules/Xot/app/Http/Middleware/SecurityMiddleware.php` non e' stato toccato (non e' registrato nelle risposte osservate: nessuna intestazione duplicata). Dopo `config/security.php` e' stata rigenerata la config in cache (`php artisan config:cache`, 22:15): confronto prima/dopo (script che visita gli array): oltre alle chiavi `security.headers.*` (5 tolte, 6 nuove) 29 differenze, tutte `social.items.*` e `permission.cache.expiration_time` per tenant, probabilmente oggetti non confrontabili con `!==` (nessun'altra chiave di configurazione cambiata); sentinelle 200 su `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale` e 302 su `/it/tickets/create`.

### Domini misurati (Playwright, 2026-10-13, `net-hosts-probe.mjs`)
Pagine reali: `/it`, `/it/tickets`, `/it/auth/login`, `/it/auth/register`, wizard passo 1, passo 2 (mappa), Riepilogo, `/fixcity/admin/login`. Con la sessione di prova isolata; nessuno stub.

| Direttiva | Origine | Richieste | Dove |
|---|---|---|---|
| `style-src` | `https://fonts.googleapis.com` | 8 | tutte le pagine con il layout |
| `font-src` | `https://fonts.gstatic.com` | 1 (le altre font da `'self'`) | `/it` |
| `img-src` | `https://{a,b,c}.tile.openstreetmap.org` | 36, 35, 34 | `/it`, `/it/tickets`, wizard |
| `connect-src` | `https://nominatim.openstreetmap.org` | 4 | wizard (indirizzo dal GPS, dal browser) |
| `script-src` | solo `'self'` | 88 | tutte |

Le tessere con tipo risorsa `other` (zoom 14 del wizard) non hanno dato nessuna violazione `connect-src` senza `*.tile.openstreetmap.org` in `connect-src`: la direttiva e' stata tolta. Su 98 pagine CMS (ospite) i domini terzi in piu' compaiono solo in `/it/tests/*` (pagine di prova): `picsum.photos` e `fastly.picsum.photos` (19 pagine), `images.unsplash.com`, `via.placeholder.com` e YouTube (`frame-src https://www.youtube.com`, `img-src https://i.ytimg.com`, solo `/it/tests/evento-dettaglio`). **Non sono in lista**: vedi decisioni.
Provato togliendo `'unsafe-inline'` e `'unsafe-eval'`: 304 violazioni `eval` (Alpine e Livewire), 28 script inline, 226 attributi `style` e 44 elementi `<style>` inline, su ogni pagina. Oggi servono tutti e quattro.

### Prove (comando e output)
| Criterio | Comando | Output |
|---|---|---|
| Intestazioni su 3 rotte | `curl -sI http://192.168.1.40:8000/it` (idem `/it/auth/login`, `/it/tickets`) | `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy: geolocation=(self), camera=(), microphone=(), payment=(), usb=(), accelerometer=(), gyroscope=(), magnetometer=()`, `Content-Security-Policy: frame-ancestors 'self'`, `Content-Security-Policy-Report-Only: default-src 'self'; script-src ...; frame-ancestors 'self'`; nessun `X-Powered-By` (prima `X-Powered-By: PHP/8.4.26`) |
| HSTS solo su HTTPS | `curl -skI https://127.0.0.1:8443/it` (proxy TLS di sviluppo davanti all'istanza scratch) e `curl -sI http://192.168.1.40:8000/it/tickets` | HTTPS: `strict-transport-security: max-age=31536000`; HTTP: nessuna `Strict-Transport-Security` |
| Console senza violazioni | `node bashscripts/tools/visual/net-hosts-probe.mjs --base http://127.0.0.1:8092 --state <auth.json> --pages /it,/it/tickets,/it/auth/login,/it/auth/register,/it/tests/homepage,/fixcity/admin/login` | `# Console (errori, avvisi, violazioni CSP): 0`; unica violazione `securitypolicyviolation`: `img-src https://picsum.photos x13` su `/it/tests/homepage` (pagina di prova) |
| Mappa e geolocalizzazione | `node bashscripts/tools/visual/geo-picker-centering-probe.mjs --state <dcp124-state.json>` | prima: 39/39; dopo (con le intestazioni, la config in cache e il build del tema): 39/39 |
| Test | `cd laravel && APP_ENV=testing DB_CONNECTION=sqlite ./vendor/bin/pest Modules/Fixcity/tests/Feature/Http/SecurityHeadersTest.php` | 8 passed (55 assertions); con la registrazione commentata: 7 failed, 1 passed |
| PHPStan | `./vendor/bin/phpstan analyse app/Http/Middleware/SecurityHeaders.php bootstrap/app.php config/security.php Modules/Fixcity/tests/Feature/Http/SecurityHeadersTest.php --no-progress --error-format=raw` | exit 0, nessun errore |
| Lista di controllo, produzione simulata con TLS | `bash bashscripts/tools/security/production-checklist.sh https://127.0.0.1:8443 --insecure --env-file <APP_ENV=production, APP_DEBUG=false, DEBUGBAR_ENABLED=false, SESSION_SECURE_COOKIE=true>` | 19/19 (intestazioni, HSTS, cookie `Secure`, sessione `HttpOnly`, nessuna debugbar, 404 senza stack trace, `http://` rimanda a `https://`) |
| Lista di controllo, sviluppo | stessa, con `.env` di sviluppo e proxy TLS davanti a `127.0.0.1:8000` | 13/19: falliscono `APP_ENV`, `APP_DEBUG`, `DEBUGBAR_ENABLED`, `SESSION_SECURE_COOKIE`, cookie senza `Secure`, debugbar nell'HTML (atteso: e' lo sviluppo) |
| `expose_php` | `rg -n '^expose_php' /etc/php/8.4/fpm/php.ini /etc/php/8.5/apache2/php.ini` | `Off` in FPM e Apache; `On` solo nella CLI (il server `php artisan serve`): li copre `header_remove` nel middleware |

### Decisioni che servono (non prese da me)
1. **Passare la CSP a `enforce`** (`SECURITY_CSP_MODE=enforce`): oggi nulla viene bloccato tranne `frame-ancestors 'self'`. Prima va deciso che cosa fare delle pagine di prova `/it/tests/*` (immagini da `picsum.photos`, `images.unsplash.com`, `via.placeholder.com` e video YouTube): o si aggiungono i domini (e il visitatore invia il suo IP a terzi: tema di DCP-137) o si tolgono dalle pagine. In produzione le pagine di prova andrebbero comunque escluse.
2. **`'unsafe-inline'` e `'unsafe-eval'` negli script**: con loro la CSP non ferma un XSS negli script. Toglierli richiede la build CSP di Alpine, nonce sugli script inline di Blade e un `style` senza attributi inline: lavoro da pianificare, non un ritocco.
3. **HSTS `includeSubDomains` e `preload`**: spenti. Sono impegni a lungo termine legati al dominio di produzione (che non conosco).
4. **Redirect a HTTPS**: `App\Http\Middleware\ForceHttps` non e' registrato (e dietro un proxy non fidato darebbe un ciclo): il redirect e' compito del vhost o del proxy. La lista di controllo lo verifica sulla risposta reale.
5. `config/vhost/fixcity.local-ssl.conf` imposta ancora `X-XSS-Protection "1; mode=block"` a livello Apache: andrebbe tolto (file di sviluppo, non toccato).

### Cosa manca per chiudere (perche' lo stato e' `in-progress`)
- Pagine di Filament **dopo il login**: provato solo `/fixcity/admin/login`. Con l'utente di prova isolato `/fixcity/admin` risponde 403 anche dopo aver dato il ruolo `fixcity::admin` nel DB scratch (`hasRole` e `canAccessPanel` sono `true` da tinker, il server risponde 403: causa non trovata). NON VERIFICATO.
- Verifica su un ambiente di produzione vero con TLS: non esiste qui; coperta solo la simulazione.
- Passaggio a `enforce` e l'eventuale lista di host per le pagine di prova (decisioni 1 e 2).
- Il limite di tentativi di login non e' stato toccato (DCP-138).

## Verifica del coordinatore e avatar locale (2026-10-14 23:30 UTC)
Controllato da me dopo il rapporto dell'agente: intestazioni presenti su 10 percorsi (`/it`, login, elenco, repliche, conferma, creazione, dettaglio, privacy e anche sul 404), `X-Powered-By` assente, CSP completa solo in `Report-Only`; `SecurityHeadersTest` + `ThemeAriaStructureTest` 29 passati; sonda mappa 39/39; sentinelle a 200.
**Trovato io, e non l'agente (non riusciva a entrare nel back office con l'utente di prova): Filament carica l'avatar da `ui-avatars.com` a ogni pagina di `/fixcity/admin`.** Con la sessione dell'utente master, 2 violazioni CSP in sola segnalazione (`img-src`) su ogni pagina del back office: oltre a rompersi con la CSP in vigore, un servizio terzo riceveva IP e iniziali di chi amministra (stesso tema di DCP-137).
Correzione alla radice: `Modules/Xot/app/Filament/AvatarProviders/InitialsAvatarProvider.php` (SVG con al massimo due iniziali, incorporato come `data:`, XML escapato, nessuna rete) e una riga `->defaultAvatarProvider(InitialsAvatarProvider::class)` in `XotBasePanelProvider` e `XotBaseMainPanelProvider`, cosi' vale per ogni pannello di ogni modulo. Prove: `Modules/Fixcity/tests/Unit/FilamentLocalAvatarTest.php` (8 test; i 7 sul provider non dipendono dal collegamento, l'ottavo "i pannelli usano il provider locale" era ROSSO prima del collegamento e verde dopo); PHPStan a zero su provider, pannelli e test; Playwright da master su `/fixcity/admin`, `/fixcity/admin/tickets`, `/fixcity/admin/tickets/create`: violazioni CSP 2 -> **0**, avatar `data:image/svg+xml` 64x64 visibile, **0 richieste esterne**. Il test sta nel modulo Fixcity perche' il TestCase di Xot cerca un file SQLite che qui non esiste.
Resta aperto: prova del back office con CSP in modalita' enforce, e il 403 dell'utente di prova scratch su `/fixcity/admin` (con l'utente master il back office si apre: e' un problema della fixture, non delle intestazioni).

