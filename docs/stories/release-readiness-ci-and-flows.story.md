---
title: "Fixcity - Prontezza al rilascio: CI reale e flussi end-to-end"
type: story
bmad: true
status: in-progress
created: 2026-11-10
owner: Modules/Fixcity (coordinamento); file toccati anche in User, Xot, Event, Lang, Themes/Sixteen, .github
related:
  - ./ticket-data-integrity.story.md
  - ./public-api-media-and-geojson.story.md
  - ../../../Xot/docs/stories/ci-deploy-ops-hardening.story.md
  - ../audit/COMPLETION_ROADMAP.md
---

# Prontezza al rilascio: CI reale e flussi end-to-end

- Fase BMAD: Quick Flow ripetuto (CI -> causa -> correzione -> CI)
- Branch: `dev`

## Come si verifica

L'ambiente degli agenti non ha PHP 8.4 né `vendor/` (Packagist e i repository PHP sono bloccati dal proxy; gli artifact di
GitHub Actions non si scaricano). La verifica vera è la CI su GitHub: ogni push su `dev` esegue PHPStan e le suite Pest, e i
log si leggono con `gh api repos/laraxot/base_fixcity_fila5/actions/jobs/<id>/logs`. PHPStan usa `--error-format=raw` perché
il formato `github` mostra solo 10 annotazioni per step.

## Cosa ha bloccato i test finora

| Causa | Effetto | Correzione |
| --- | --- | --- |
| `Modules/Event/.../20260101000000_create_events_table.php` con classe nominata e nome file fuori formato | Il migrator la caricava due volte: "Cannot redeclare class CreateEventsTable", **tutte** le suite morivano prima del primo test | Classe anonima, stesso nome di file |
| Stessa migrazione con FK verso `users` e `categories` di altri moduli (modulo disattivato ma migrazione eseguita) | La migrazione Fixcity di `categories` non poteva ricreare la tabella: 266 test User e 58 Geo falliti | Niente FK, solo colonne e indici; idempotente |
| `2024_03_14_create_reports_table` con FK su `categories` | Stesso blocco su un database nuovo: **anche l'installazione per un nuovo Comune falliva** | FK tolta alla creazione (solo installazioni nuove) |
| `Lang/2026_11_05_100000_create_posts_table` (`bigint` -> `uuid`) | MariaDB rifiuta la conversione diretta e i valori legacy non possono essere mappati automaticamente | `XotBaseMigration::convertUserIdToUuidIfLegacyBigint` conserva i valori legacy come stringhe (`preserveLegacyValues: true`) |
| `mockeryExpect()` e helper di Activity non caricati | "Call to undefined function" | `tests/Pest.php` li include; in Activity la dichiarazione e' condizionale |
| `tests/Unit/Modules/*`: 57 copie vecchie di test dei moduli | 273 test della suite Unit falliti per l'ambiente | Escluse dalla suite (non cancellate) |
| `uses()` doppio in `tests/Feature/Pages/ShowcasePagesHiddenTest.php` | `TestCaseAlreadyInUse`, suite Feature ferma | Tolto |
| Test nella cartella `laravel/tests/` (regola: i test vivono nei moduli e nei temi) | Suite Unit/Feature della radice con copie doppie e test saltati di funzioni rimosse | Spostati: `ListaCategorieTest` e `ShowcasePagesHiddenTest` in Fixcity, `ListingComponentsTest` in Sixteen, integrazione User/Blog in Blog (riscritta: le annotazioni `@test` non giravano). Eliminati: 53 copie con gemello nel modulo, test di Chart (modulo inesistente), Breeze e `GeocodeTicketAddressJob`. Suite Unit/Feature tolte da `phpunit.xml` e dalla CI |

PHPStan su tutti i moduli: da 46 errori a 0 attesi (errori nei test della sessione, `Filter::schema()` deprecato).

## Flussi: problemi trovati e corretti

Analisi statica dei percorsi cittadino, operatore e API (rotte, pagine Folio, JSON CMS, traduzioni).

| Problema | Correzione |
| --- | --- |
| `/tickets/create` è servita dal tema: il middleware della pagina Folio del modulo non gira, quindi chi non ha confermato l'email compilava tutto e veniva respinto solo all'invio | Controllo dell'identità anche in `CreateTicketWizardWidget::mount()`; test `CreateTicketWizardVerifiedIdentityTest` |
| `/api/ticket-details/{id}` serviva ancora titolo e indirizzo di ticket cancellati (ricadeva sul GeoJSON statico) | `withTrashed()` nel controllo |
| "Elimina" nella scheda del ticket: `action()` sostituiva quella di Filament, niente notifica né redirect | `using()` con `DeleteTicketAction` |
| "Elimina" nella modifica del ticket non usava `DeleteTicketAction` (niente `deleted_by`, niente transazione) | Stessa `using()` |
| "Invia comunicazione" registrava l'id dell'utente come destinatario | Le opzioni usano il nome |
| Messaggio "account eliminato" mai mostrato dal layout in uso | Avviso `role="status"` nel layout `components/layouts/main` |
| Dopo la conferma dell'email si finiva in home | Si va al modulo di segnalazione (o alla pagina "intended") |
| SPID/CIE rimandavano a `route('dashboard')`, 404 | `GetPostLoginUrlAction` |
| Chiave `fixcity::ticket.owner.auth_required` inesistente | `fixcity::ticket_submit.auth_required` nelle 4 lingue |
| Mappa dell'operatore (`filament/infolist/ticket-location-map.blade.php`) leggeva `/data/tickets.json`, aggiornato solo col pulsante di esportazione: dati vecchi | Legge `/api/tickets/geojson`: stesse segnalazioni pubbliche (`BuildPublicTicketsQueryAction`, la stessa query del file), sempre aggiornate |
| Accesso con SPID/CIE: i controller del tema cercavano l'utente per `fiscal_code` e scrivevano colonne inesistenti: il primo accesso finiva in errore SQL | Migrazione owner User `2026_11_12_120000_create_users_table` (solo `fiscal_code`, `auth_method`, `identity_provider`, `last_login_at`: minimizzazione GDPR) e `UpsertUserFromDigitalIdentityAction` condivisa dai due controller; test `User/tests/Feature/Auth/DigitalIdentityUserUpsertTest.php` |
| `app/Providers/FolioServiceProvider.php` rimontava le pagine del tema senza il gruppo `web` (sessione e CSRF persi se l'ordine dei provider cambiava) e `BlockLegacyPublicPages` non girava mai | Provider e middleware rimossi; i loro percorsi (`show`, `homepage`, `auth/login1`-`5`) sono in `BlockShowcasePages`, globale; test in `Fixcity/tests/Feature/Security/ShowcasePagesHiddenTest.php` |

## Aperto

- **Pagine dei moduli coperte dal tema** (Folio si ferma alla prima pagina che corrisponde): Fixcity `tickets/*`,
  `lista-categorie`, `home`, `prova`; User `area-personale/*`, `profile/edit`; Blog `blog/*`, `events/*`. Middleware e logica
  messi lì non girano. JSON CMS irraggiungibili: `segnalazione-dettaglio.json`, `segnalazioni.index.json`.
- Restano fallimenti nelle suite dei moduli informativi: si correggono a giri successivi, partendo dalle cause comuni.

## Controllo completo del front office (12/11/2026)

Ambiente locale avviato da zero: PHP 8.4.27 compilato, MariaDB, `vendor/` dal `composer.lock` versionato, `migrate` + `db:seed`,
server su `fixcity.local:8000` con document root `public_html` e asset per symlink. Percorso con Playwright (desktop 1366 px e
mobile 390 px) su tutte le pagine raggiungibili dalla home, da anonimo e da cittadino registrato; wizard inviato con foto
(geocoder e tile simulati: la rete del sandbox non arriva a OpenStreetMap). Segnalazioni del tester (WhatsApp) in
laraxot/module_fixcity_fila5#70-#74 e laraxot/theme_sixteen_fila5#116-#117.

| Trovato | Correzione |
| --- | --- |
| Registrazione impossibile su database nuovo: `consents.metadata` mancante (migrazione 000006 cancellata dal commit 483080dca) | Migrazione Gdpr `2026_11_12_000001_add_metadata_to_consents_table` |
| Tema senza `resources/views/components` dopo 483080dca | Tema riallineato al suo repository (`theme_sixteen_fila5`) |
| Campo "Dettagli" nero con testo scuro su telefono in tema scuro (#70) | `data-theme="light"`, `color-scheme: light`, sfondo bianco dei campi del wizard |
| Foto rotta e link non raggiungibile in Traccia (#71), foto assente nel popup (#72) | In locale funzionano (URL relativi firmati, 200 `image/jpeg`); URL senza host anche per i dischi `local`. Da verificare la configurazione del server |
| Traccia senza elenco delle proprie segnalazioni (#73) | Sezione "Le tue segnalazioni" per l'utente autenticato, invito ad accedere per l'anonimo |
| Testata con l'email al posto del nome | `name` salvato alla registrazione; ripiego su nome e cognome |
| Indicatore di caricamento di accesso e registrazione largo ~470 px | Dimensioni fisse, regole valide anche sulle pagine `data-dc` |
| Voce di menu attiva sbagliata (Traccia, Segnala, Novità, Eventi) | `header_active` corretti, `element` news/live, vince il percorso più lungo |
| Piede con 300 px vuoti e copyright su fondo bianco | Tolto `min-height: 775px`, riga del copyright nella fascia scura |
| Cronologia del ticket appena inviato: solo "Allegato aggiunto" | Prima voce "In attesa" registrata alla creazione (solo autore e ufficio) |
| Notifica "ha un nuovo stato: Ricevuta" (Ricevuta e' un evento, lo stato e' "In attesa") | Testo "Segnalazione TCK-…: Ricevuta." nelle 4 lingue |
| Home del tenant `localhost` con un widget inesistente (500) | Blocco della mappa del tema |
| 210 test Fixcity a 404: i contenuti CMS nei test vanno in una copia usa-e-getta mai riempita (483080dca) | `GetTenantFilePathAction` copia i JSON del tenant una volta per processo |
| hreflang di ogni pagina = home della lingua (`/de`) nei test | `ResolveHreflangAlternatesAction` costruisce gli URL dal percorso della richiesta |

Restano aperti: prestazioni e cache (#74); Gravatar caricato prima del consenso (theme_sixteen_fila5#106); test Cms informativi
che falliscono (login FO di modulo).
