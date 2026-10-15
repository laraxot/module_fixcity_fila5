---
id: dcp-54
title: "Scopo del progetto e conformita' normativa: norme, evidenza, lacune"
type: story
epic: dc-parity-v2
status: in-progress
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-13
updated: 2026-10-13
tags: [compliance, gdpr, accessibilita, cad, agid]
related: [Modules/Fixcity/docs/wiki/concepts/fixcity-scopo-e-target.md]
qmd: "dcp-54 Scopo del progetto e conformita' normativa: norme, evidenza, lacune"
---

# DCP-54: scopo del progetto e conformita' normativa

## Richiesta (2026-10-13, utente)
Andare a fondo sullo scopo del progetto: perche' lo si fa in questo modo, quali norme di legge si devono rispettare e come le si sta rispettando. Prodotto professionale e accademico.

## Impostazione
Lo scopo e il target sono gia' nel documento SSoT `Modules/Fixcity/docs/wiki/concepts/fixcity-scopo-e-target.md`. Questo lavoro aggiunge la matrice norma -> requisito -> come e' rispettato -> PROVA nel repo (file o risposta HTTP verificata) -> lacuna -> gravita'. Regola di onesta': nessuna riga «conforme» senza prova; le interpretazioni giuridiche sono segnalate come tali (non e' un parere legale: la validazione spetta al DPO e al responsabile della trasformazione digitale dell'ente).

## Studio (subagent LEGAL)
Studio del 2026-10-13, sola lettura (letture del repo, curl, Playwright, axe-core, tinker senza scritture; test Pest non rieseguiti). Documento completo con matrice, output e story proposte: [norme-e-conformita-fixcity](../wiki/concepts/norme-e-conformita-fixcity.md). **Non e' un parere legale**: le righe con sigla L attendono DPO, RTD o legale; nessuna riga e' dichiarata "conforme" (la tabella "Che cosa regge" elenca i soli fatti provati: cookie solo tecnici, payload pubblico a allowlist, fail-closed senza informativa, 0 violazioni axe nel DOM del prodotto).

Esito: 31 riferimenti normativi (N-01..N-31), 19 lacune (G-01..G-19), 17 story proposte (DCP-54.1..DCP-54.17). Nota: il SSoT citato in `related` non esiste in `Modules/Fixcity/docs/wiki/concepts/`; esiste in `bashscripts/ai/wiki/concepts/fixcity-scopo-e-target.md`.

Bloccanti per la messa in produzione:
- **G-01 informativa privacy**: `/it/privacy` risponde 404 (e `/en/privacy`, `/privacy`; nessun nome plausibile risponde 200). Il testo `lang/it/policy.md` (713 byte) indica "FixCity" come titolare e non ha RPD, base giuridica, destinatari, trasferimenti, durata, diritti, reclamo (art. 13 GDPR); conclude "acconsenti" mentre la casella dice "ho letto".
- **G-02 dichiarazione di accessibilita'**: `/accessibilita`, `/it/accessibilita`, `/it/accessibility` danno 404; nessun meccanismo di feedback nella home (Dir. UE 2016/2102 art. 7, L. 4/2004).

Alte:
- **G-03 footer**: nel browser tutti i 10 link del footer finiscono in 404 perche' gli URL sono senza prefisso di lingua (`footer.json:136-177`), compreso "Segnalazione disservizio"; contatti, P. IVA e PEC sono segnaposto.
- **G-04 base giuridica**: semantica consenso/presa visione non decisa; in registrazione l'informativa non ha link e i termini sono un segnaposto (`terms.md`, 88 byte).
- **G-05 SPID/CIE**: solo email e password; `/it/tickets/create` impone il login (302 a `/it/auth/login`); nessun SPID/CIE/app IO (CAD artt. 64, 64-bis). Bloccante se il Comune lo eroga come canale ufficiale: decide l'ente.
- **G-06 terze parti**: ogni pagina contatta `fonts.googleapis.com` (`app.css:9`, il font esiste gia' in locale); home, `/en` ed elenco contattano `*.tile.openstreetmap.org`; Nominatim e' chiamato dal server con il testo digitato (`HasCoordinatePicker.php:226,277`); nessuno e' nell'informativa.
- **G-07 nessun freno ai tentativi**: `LoginWidget.php:36-62` usa `Auth::attempt` senza `RateLimiter`; `config/security.php` `rate_limit` non e' letta da nessun codice; 30 GET sul tracciamento: tutte 200.
- **G-08 diritti e conservazione**: `DeleteUserAction.php:29-38` fa solo `$user->delete()`, `tickets.owner_id` senza chiave esterna; nessun export; `Modules/Gdpr/config/config.php` e' copiato da un progetto assicurativo e nessun codice lo legge.
- **G-09 WCAG non dimostrata**: axe 4.13.0 su 8 pagine = 0 violazioni nel prodotto, ma nessun audit su tastiera, lettore di schermo, zoom, wizard autenticato.

Medie, con prova: consensi non scritti se manca il `Treatment` (DB di sviluppo: `{"treatments":[],"consents":0,"users":9}`) e nessuna prova di presa visione nel wizard (SEC-12); le stelle di valutazione della conferma non inviano nulla (0 richieste di rete dopo il clic, `design-comuni-home.js:64-108`); nessun termine/responsabile (L. 241); nessuna intestazione di sicurezza (CSP, HSTS, X-Frame-Options, nosniff, Referrer-Policy) e `APP_DEBUG`/Debugbar attivi (`/_debugbar/open` 200); proxy TLS 8080 non raggiungibile (HSTS non verificabile).

## Aperto
Lacune da trasformare in story. Gia' note: `/it/privacy` risponde 404 (l'informativa linkata dal footer e dal wizard); accesso solo con email e password (nessuno SPID/CIE).

## Verifica del lead sulle prove (17:30 UTC)
Ricontrollate con `curl` e `grep` le affermazioni piu' pesanti del documento `wiki/concepts/norme-e-conformita-fixcity.md`:
| Affermazione (LEGAL) | Esito della verifica del lead |
|---|---|
| G-01 informativa privacy non raggiungibile | CONFERMATO: `/privacy`, `/it/privacy` -> 404 |
| G-02 dichiarazione di accessibilita' assente | CONFERMATO: `/accessibilita`, `/it/accessibilita`, `/it/accessibility`, `/it/dichiarazione-accessibilita` -> 404 |
| G-03 link del footer in 404 | CONFERMATO e preciso: dei 12 link interni del footer (home, FAQ, appuntamento, segnalazione, assistenza, trasparenza, privacy, note legali, accessibilita', RSS, media policy, mappa del sito) **11 rispondono 404**. Tutti hanno l'URL senza prefisso di lingua (`/tickets/create`, non `/it/tickets/create`), anche quelli che con il prefisso esistono (`/it/domande-frequenti` 200, `/it/tickets/create` 302 al login). |
| G-07 login senza limite di tentativi | CONFERMATO nel codice: `LoginWidget.php:52` `Auth::attempt(...)` senza `RateLimiter` ne' `throttle` (non provato con tentativi reali: regola di sola lettura) |
| Nessuna intestazione di sicurezza | CONFERMATO: nella risposta di `/it/` non compaiono CSP, HSTS, X-Frame-Options, X-Content-Type-Options, Referrer-Policy (`curl -I`) |
| `/_debugbar/open` risponde 200 | NON RIPRODOTTO: a un ospite risponde 500 senza parametri. `APP_DEBUG` e `DEBUGBAR_ENABLED` sono presenti in `.env` (valori non letti in questo controllo): da verificare che in produzione siano spenti |
Non verificato dal lead: gli articoli e i commi delle norme (marcati "da verificare" nel documento), il banner cookie, SPID/CIE, il contenuto dei diritti dell'interessato (G-08).
Correzione immediata applicata dal lead (solo codice, nessun testo legale inventato): prefisso di lingua nei link interni del footer, vedi sotto. Le pagine di privacy e accessibilita' richiedono il TESTO del titolare del trattamento (RPD, base giuridica, destinatari, durata, diritti, reclamo): non si scrivono senza di lui.

## Correzione G-03 applicata (17:50 UTC)
`Themes/Sixteen/resources/views/components/sections/footer/design-comuni.blade.php`: un helper `$href` prefissa la lingua corrente ai percorsi assoluti senza prefisso (`/tickets/create` -> `/it/tickets/create`, `/en/...`, `/de/...`, `/es/...`), lasciando invariati URL esterni, `mailto:`, `tel:` e percorsi che hanno gia' un prefisso di lingua; applicato a link di gruppo, contatti e social. Test: `Modules/Fixcity/tests/Feature/Pages/FooterLocalizedLinksTest.php` (4 lingue), ROSSO prima (`/domande-frequenti` senza prefisso) e VERDE dopo: `22 passed (74 assertions)` insieme alla guardia dei template. Sentinelle (stesso footer, `/it/tests/*`): HTML replica homepage 95,07% e amministrazione 93,03% identici prima e dopo; pixel homepage replica 99,88% con `--stub-images 'picsum\.photos'` (96,03% senza: artefatto delle immagini casuali), regione `footer.it-footer` 100%, altezze 4629 = 4629.
Effetto: `/it/domande-frequenti` (200) e `/it/tickets/create` (302 al login) ora sono raggiungibili dal footer. Restano 404 e RICHIEDONO una decisione o un contenuto: privacy, accessibilita', note legali, assistenza, amministrazione trasparente, prenotazione appuntamento, mappa del sito, media policy, RSS. Le prime due sono obblighi di legge (G-01, G-02) e vanno scritte dal titolare; le altre sono voci del riferimento che FixCity non eroga: o si creano le pagine o si tolgono dal footer del SITO REALE. Vincolo: il footer e' condiviso con le repliche `/it/tests/*`, quindi la rimozione richiede un footer dedicato al sito reale (JSON a parte), non la modifica di `sections/footer.json`.

## Correzione G-07 applicata (17:30 UTC)
`Modules/User/app/Filament/Widgets/Auth/LoginWidget.php`: limite di 5 tentativi errati per email e IP in 60 secondi (`RateLimiter`), con controllo PRIMA di `Auth::attempt` (anche la password giusta e' rifiutata durante il blocco), messaggio con i secondi da attendere (`user::login.actions.login.throttle`, tradotto in it, en, de, es) e azzeramento del contatore dopo un accesso riuscito. Test `Modules/Fixcity/tests/Feature/Pages/LoginThrottleTest.php` (3 casi: blocco con password giusta dopo 5 errori; accesso prima del limite con azzeramento; limite per email, un altro account non e' bloccato). ROSSO prima, VERDE dopo: `17 passed (155 assertions)` insieme a header e footer; PHPStan sul widget 0 errori.
Incidente di metodo (stessa classe di `tickets.create.json`): il file e' contesa da due scrittori che alternano `redirect()` e `redirectIntended()`; alle 17:25:49 una riscrittura da una copia vecchia ha cancellato la mia prima modifica. Riapplicata alle 17:28:42 in modo ancorato e idempotente; presente dopo 45 secondi e a ogni controllo successivo. Se riscompare, il codice da cercare e' `RateLimiter` e `throttleKey`.
Limiti dichiarati: (1) la chiave usa l'IP della richiesta, dietro un proxy senza `TrustProxies` configurato tutti i visitatori condividerebbero lo stesso IP (verificare in produzione); (2) il limite protegge il widget `LoginWidget`, non l'endpoint API `api/user/login` ne' il back office Filament, che hanno il proprio login (da verificare, non toccati); (3) non provato con tentativi reali contro il sito di sviluppo (regola di sola lettura); (4) il blocco dura 60 secondi: valore da validare con il titolare in base al rischio.
