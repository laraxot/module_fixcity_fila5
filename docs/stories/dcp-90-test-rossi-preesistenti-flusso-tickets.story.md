---
id: dcp-90
title: "DCP-90: test rossi preesistenti nel flusso tickets (salvataggio del wizard, redirect di track)"
type: story
epic: dc-parity-v2
status: done
priority: P1
owner: module:Fixcity
created: 2026-10-13
updated: 2026-10-13
qmd: "test rossi pest tickets wizard save redirect track oscurata pagina dinamica tema config cache sqlite"
related: [dcp-33, dcp-34, dcp-35]
---

# DCP-90: due test rossi che non dipendono dalla parity

## Fatti misurati (2026-10-13)
1. `Modules/Fixcity/tests/Feature/Filament/CreateTicketWizardWidgetTest.php` > `persists a valid wizard submission and prepares its confirmation` fallisce con `Component did not perform a redirect` (riga 85, `assertRedirectContains('/it/tickets/confirmation')`). Dopo `call('submit')` gli `effects` sono vuoti, nessun errore e 0 ticket creati. **Preesistente al cablaggio del riepilogo**: confermato ripristinando temporaneamente la riga `summary` di `TicketForm::getSteps()` (stesso esito) e rimettendo la modifica. Sospetto non verificato: `save()` ritorna prima del `Ticket::create` (modifiche del 12:34-13:04 a `TicketForm`/privacy/widget dell'altra sessione, o la privacy policy simulata nel test).
2. `Modules/Fixcity/tests/Unit/Actions/CreateTicketConfirmationFlowTest.php` > `track code path redirects to query form`: `GET /it/tickets/track/TCK-REDIR01` risponde 200 invece di un redirect. Causa: la pagina dinamica del tema `pages/[container0]/[slug0]/[container1]/[token]` oscura la pagina del modulo `tickets/track/[code]` (stesso meccanismo che oscurava `tickets/confirmation`, vedi DCP-34).

## Acceptance criteria
1. Indagare con `Livewire::test(...)->call('submit')` e leggere cosa fa `save()` (`GetPublishedPrivacyPolicyAction`, `Filament::auth()->id()`, `getState`) fino a una causa provata; correggere il codice o il test, mai silenziare.
2. Per il punto 2: o il redirect vive nella pagina del tema (con test) o il test cambia contratto (200 con il form del codice); decisione scritta qui.
3. Verdi con il comando sicuro `APP_ENV=testing DB_CONNECTION=sqlite APP_CONFIG_CACHE=/tmp/<inesistente> ./vendor/bin/pest <file>`; PHPStan a zero sui file toccati.
4. Un test di regressione per ciascun difetto.

## Note
Altri rossi preesistenti trovati dai sottoagent (non del flusso): in `Modules/Geo` il test `CoordinatePicker is not dehydrated by default` e 24 test che richiedono `database/fixcity_user_test.sqlite` (vedi DCP-93).

## Esito (2026-10-13 14:30 UTC, DCP-90)

### Causa provata 1: la route cache di sviluppo rendeva muti `set()`/`call()` di Livewire (non il codice del widget)
- `bootstrap/cache/routes-v7.php` (12:42, ambiente di sviluppo) contiene `POST /livewire-b075fdcb/update`; nell'ambiente di test l'URI di update e' `/livewire-53ac0bbe/update` (l'hash dipende da `APP_KEY`: `.env.testing` ne ha un altro). Il POST dei test non trova la rotta, risponde **405** e `Livewire\Features\SupportTesting\SubsequentRender` (`if (! $response->isOk())`) restituisce uno stato con componente `null`, snapshot ed `effects` **vuoti**. Misura: dopo il primo `set('data.privacyAccepted', true)` `$component->instance()` era `null` e `lastState->getResponse()` aveva status 405.
- Conseguenza: `save()` non e' mai stato eseguito, nessun errore (`assertHasNoErrors` passa a vuoto), 0 ticket, `Component did not perform a redirect`. Il sospetto della story (privacy policy simulata, `Filament::auth()->id()`, `getState`) e' **escluso**: con la route cache ignorata `GetPublishedPrivacyPolicyAction::execute()` ritorna la stringa simulata, `Filament::auth()->id()` e' l'UUID dell'utente (guard `web` = guard di default), `getState(shouldCallHooksBefore: false)` torna i campi senza eccezioni, redirect `?code=` compreso, 1 ticket creato.
- Correzione (minima, ambiente): `laravel/phpunit.xml` imposta `APP_ROUTES_CACHE` e `APP_CONFIG_CACHE` su percorsi inesistenti (cache ignorate). Cosi' i test non dipendono dal comando "sicuro" scritto a mano e non possono scrivere sul DB di sviluppo se qualcuno rigenera `config.php`. Il comando resta `cd laravel && APP_ENV=testing DB_CONNECTION=sqlite ./vendor/bin/pest <file>` (serve ancora `DB_CONNECTION=sqlite`: `.env.testing` punta a MariaDB `fixcity_data_test`).
- Test di regressione: `tests/Feature/Livewire/LivewireUpdateEndpointTest.php` (un round trip `set()` che deve rispondere 200; rosso con la route cache, verde senza).
- Effetto collaterale utile: ogni test Livewire del modulo che prima era un no-op ora esegue davvero (es. `it rejects a forged wizard consent while the tenant policy is unpublished` passa solo senza la cache).

### Causa provata 2: la pagina generica del tema oscura `tickets/track/[code]`
- Folio monta prima il percorso del tema e poi quelli dei moduli (`Modules/Cms/app/Providers/FolioVoltServiceProvider.php`, righe ~112-160). `/it/tickets/track/TCK-REDIR01` coincide con `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/[container1]/index.blade.php` (`container1.index`), che renderizzava la pagina CMS `tickets.view` con 200; `Modules/Fixcity/resources/views/pages/tickets/track/[code].blade.php` (`tickets.track.code`, quello che redirige) non veniva mai raggiunta. Verificato con `folio:list` (solo lettura) e con il test (200 anche senza route cache).
- Decisione: il contratto del test resta (la ricevuta della conferma, `BuildTicketConfirmationDataAction`, emette `/{locale}/tickets/track/{code}`: deve portare al modulo di ricerca). Il redirect vive **nella pagina del tema** (stesso comportamento della pagina del modulo, con `render()` Folio limitato a `tickets/track/{code}`; le altre pagine a 3 segmenti, es. `auth/password/reset`, non cambiano). Nessuno slug/JSON nuovo.
- Test di regressione: `tests/Feature/Pages/TicketTrackCodeRedirectTest.php` (it/en: 302 a `/{locale}/tickets/track?code=...`; `auth/password/reset` non redirige).
- Dev server: `/it/tickets/track/TCK-REDIR01` -> 302 `/it/tickets/track?code=TCK-REDIR01`; `/en/...` -> 302 `/en/tickets/track?code=...`; `/it/tickets/track?code=` e `/it/auth/password/reset` -> 200.

### File toccati
- `laravel/phpunit.xml` (due `<env>` + commento)
- `laravel/Themes/Sixteen/resources/views/pages/[container0]/[slug0]/[container1]/index.blade.php` (+19 righe: `render()` Folio e use; repo git separato del tema; backup `dcp90-container1-index.blade.php.bak` nello scratchpad)
- nuovi: `Modules/Fixcity/tests/Feature/Livewire/LivewireUpdateEndpointTest.php`, `Modules/Fixcity/tests/Feature/Pages/TicketTrackCodeRedirectTest.php`
- `CreateTicketWizardWidget`, `TicketForm`, i test esistenti e il file di test copia `Dcp90DebugTest.php` (cancellato): invariati/rimossi.

### Output reale
- `CreateTicketWizardWidgetTest` + `CreateTicketConfirmationFlowTest` + `TicketTrackCodeRedirectTest` + `tests/Feature/Livewire`: `Tests: 28 passed (110 assertions)`.
- PHPStan sui due test nuovi: `[OK] No errors`.
- Cartella intera `Modules/Fixcity/tests` (un processo, 398 s): `19 failed, 1 skipped, 462 passed (2188 assertions)`. Controllo a campione con la route cache originale (`CitizenMyTicketsDetailLinkTest`, `TicketPrivacyPublicationTest`, `GenerateTicketsActionTest`): falliscono identici, quindi non sono causati da questa modifica; gli altri rossi non sono stati ricontrollati uno a uno.

### Rossi rimasti (fuori dal wizard, non corretti)
1. Pagine attese dal modulo che in `Modules/Fixcity/resources/views/pages` non esistono (`area-personale/*`, `privacy`; verificato con `ls`): rispondono 200/404 dalla pagina generica del tema. Causa dei restanti test di elenco (`tickets`) non indagata a fondo (probabile stesso schema: contenuto CMS/tema al posto della pagina del modulo). `CitizenMyTicketsDetailLinkTest` (2), `FollowedTicketsPageTest` (2 + 1 throttle), `NotificationPreferencesTest` (3), `TicketListCreateCallToActionTest` (1), `TicketPagesTest` (4), `TicketPrivacyPublicationTest` (2: 404 invece di 503/200; il 3o, `forged wizard consent`, ora passa).
2. Rate limit del tracking (`TrackingTranslationTest` x2 + `FollowedTicketsPageTest` throttle: 429 atteso, 200): `tickets/track.blade.php` del modulo ha `middleware('throttle:10,1')` e la lookup per `ticket_id`, ma `/it/tickets/track` e' servita dalla pagina del tema `[container0]/[slug0]` (special page `ticket-track`) senza throttle. Rischio di sicurezza: i codici capability del tracking pubblico non hanno limite di richieste. Da sistemare con una story dedicata nel tema.
3. `TrackingTranslationTest` "authenticated owner ... ticket id": manca `<code>TCK-OWNERLOOKUP01</code>` nell'HTML della pagina del tema (markup diverso dal modulo): stessa causa del punto 2.
4. `GenerateTicketsActionTest > _creates_tickets_with_valid_states`: la factory crea ticket `PENDING`/`MEDIUM`, il test accetta solo `OPEN`/`RESOLVED` o `PENDING`+`URGENT`: contratto test/factory incoerente, preesistente (ultimi commit 2026-10-08).
5. Ambiente (non in questa cartella): `Modules/Geo` richiede `database/fixcity_user_test.sqlite` (DCP-93).
