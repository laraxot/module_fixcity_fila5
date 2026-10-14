---
id: dcp-149
title: "DCP-149: la ricerca per codice di tracciamento non aveva piu' limite di tentativi"
type: story
epic: dc-parity-v2
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-14
updated: 2026-10-14
qmd: "codice tracciamento capability token throttle rate limit tickets track confirmation GetPublicTicketByCodeAction brute force 429 DCP-138"
related: [dcp-102, dcp-104, dcp-138, dcp-148]
---

# DCP-149: freno ai tentativi sul codice di tracciamento

## Fase BMAD
Correzione (dev) di un difetto di sicurezza trovato da un agente durante DCP-148 e confermato dal coordinatore.

## Difetto misurato (curl, 2026-10-14 00:30 UTC)
`GET /it/tickets/track?code=<qualunque>` ripetuto 14 volte di fila: **14 risposte 200**, nessun limite.
- Il codice di tracciamento e' un **capability token**: chi lo conosce vede stato e cronologia anche di una segnalazione non ancora pubblica (`GetPublicTicketByCodeAction`, docblock).
- Il limite `throttle:10,1` stava nella pagina Folio del modulo `Modules/Fixcity/resources/views/pages/tickets/track.blade.php:19`, ma la rotta e' servita dal ramo `ticket-track` del tema (`pages/[container0]/[slug0]/index.blade.php`), che non ha throttle: la pagina del modulo e' oscurata (stessa causa documentata per `tickets/create`). Era l'ultima voce rimasta aperta di DCP-102.
- Anche `/it/tickets/confirmation?code=...` (`ResolveTicketConfirmationDataAction`) cerca per codice, senza limite.

## Correzione (alla radice, non sulla pagina)
Il freno sta in `GetPublicTicketByCodeAction`, l'unico punto da cui passano la pagina di tracciamento e quella di conferma:
- i tentativi **a vuoto** (codice sconosciuto) sono contati per indirizzo: 10 nella finestra di 60 secondi (`MAX_MISSES`, `DECAY_SECONDS`), poi `ThrottleRequestsException` = **429 con `Retry-After`**;
- i tentativi **riusciti non consumano nulla** (un cittadino con il codice giusto non e' mai rallentato);
- un codice vuoto non e' una ricerca e non consuma;
- a indirizzo bloccato anche un codice valido riceve 429 fino a fine finestra (altrimenti l'attaccante continuerebbe a provare).
Scelta: contare i soli miss invece di ogni richiesta (come faceva `throttle:10,1`) perche' il miss e' l'unica misura dell'attacco; il cittadino che ricarica la pagina con il codice giusto non si blocca da solo.

## Prove
- Pest `Modules/Fixcity/tests/Feature/Actions/GetPublicTicketByCodeThrottleTest.php` (5 test, 109 asserzioni), su DB scratch (`pest-scratch-db.sh`): rossi PRIMA della correzione (5 su 5, mancava `throttleKey`/`MAX_MISSES`), verdi dopo. Copre: codice noto mai limitato (25 prove), 429 con `Retry-After` dopo 10 miss, blocco anche del codice valido a finestra aperta, codice vuoto neutro, pagina `/it/tickets/track` 200 fino al decimo errore e 429 all'undicesimo.
- PHPStan (config unica) a zero su Action e test.
- Server di sviluppo (`php artisan serve`, cache `file`): 14 richieste di fila a `/it/tickets/track?code=TCK-PROVACOORDn` -> `200 x10, poi 429 x4`; `curl -i` mostra `HTTP/1.1 429 Too Many Requests` con `retry-after: 43`.
- Sentinelle `/it`, `/it/tickets`, `/it/tests/homepage`, `/it/tickets/confirmation`, `/it/tests/permessi-area-personale`, `/it/tickets/29` a 200.
- Backup della versione precedente: `scratchpad/dcp124-backup/GetPublicTicketByCodeAction.before`.

## Livello di pagina (contratto di DCP-102)
Esisteva gia' la story `Themes/Sixteen/docs/bmad/stories/2026-10-13-dcp-102-tracking-pubblico-senza-rate-limit-oscurato-dal-tema.story.md` (stesso difetto, che avevo dimenticato) e un test esistente che ne fissa il contratto: `FollowedTicketsPageTest` pretende 429 alla undicesima richiesta a `/it/tickets/track` anche senza codice. Il mio freno sui soli tentativi a vuoto non bastava a quel test. Aggiunto il livello di pagina con `tickets.track.json` (`"middleware": ["throttle:10,1"]`, 3 copie identiche) e la guardia `TicketTrackPageJsonGuardTest`: i due livelli si completano (la pagina limita ogni richiesta, l'Action limita ogni tentativo a vuoto anche sulla conferma `?code=`). Dettagli e prove nella sezione Esito di DCP-102.

## Pagina 429 con il layout del tema (stesso giro)
Il cittadino bloccato vedeva la pagina nuda di Laravel ("Too Many Requests", senza header, footer ne' lingua). Esisteva gia' una pagina 429 col layout del tema (`Modules/Fixcity/resources/views/errors/too-many-requests.blade.php`), ma il gestore in `bootstrap/app.php` scattava solo per `routeIs('tickets.track')`, cioe' per la pagina Folio del modulo, oscurata: la rotta servita dal tema si chiama `container0.view`. Ora il gestore riconosce anche i percorsi `*/tickets/track` e `*/tickets/confirmation` (la conferma con `?code=` passa dalla stessa Action). In tedesco e spagnolo mancavano le tre chiavi `track.rate_limited_*` in `lang/{de,es}/ticket.php` (cadeva sull'italiano): aggiunte.
Prova: dopo il blocco, `/{it,en,de,es}/tickets/track` rispondono 429 con header e footer Design Comuni e titolo "Troppe richieste", "Too many requests", "Zu viele Anfragen", "Demasiadas solicitudes"; test Pest nuovi (4, uno per lingua) ROSSI ripristinando il vecchio `bootstrap/app.php` e verdi con la correzione; in totale `GetPublicTicketByCodeThrottleTest` 9 test, 125 asserzioni. PHPStan a zero su `bootstrap/app.php` e sul test.

## Non coperto / seguiti
- **Pagina 429**: risolta per il tracciamento e la conferma (vedi sezione seguente). Restano nude le pagine 404, 403, 419, 500 e 503 e il 429 di altre pagine (login): pagina di errore col chrome Design Comuni in 4 lingue da aprire come story a parte.
- Il limite e' per indirizzo: dietro un proxy che non inoltra l'IP del client (`TrustProxies`) tutti i cittadini condividono il budget. Da verificare in produzione (DCP-139).
- Il resto di DCP-138 (login, registrazione, altre azioni) e' a carico di quella story.
- Due test dell'elenco sono rossi nello scratch (`TicketListPageTest` "searches text", `ListPublicTicketsActionTest` "searches title, description and address"): causa non indagata, segnalati dall'agente DCP-148, non toccano questo file.
