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
| `Lang/2026_11_05_100000_update_posts_table` (`bigint` -> `uuid`) | MariaDB rifiuta la conversione diretta anche a tabella vuota: 50 test Feature e 58 Geo | `XotBaseMigration::convertUserIdToUuidIfLegacyBigint` passa da `char(36)` su MariaDB |
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
| Accesso con SPID/CIE: i controller del tema cercavano l'utente per `fiscal_code` e scrivevano `auth_method`, `surname`, `birth_place`... colonne inesistenti: il primo accesso finiva in errore SQL | Migrazione User `add_digital_identity_columns_to_users_table` (solo `fiscal_code`, `auth_method`, `identity_provider`, `last_login_at`: minimizzazione GDPR) e `UpsertUserFromDigitalIdentityAction` condivisa dai due controller (riconosce per CF o email verificata, nome in `first_name`/`last_name`); test `User/tests/Feature/Auth/DigitalIdentityUserUpsertTest.php` |
| `app/Providers/FolioServiceProvider.php` rimontava le pagine del tema senza il gruppo `web` (sessione e CSRF persi se l'ordine dei provider cambiava) e `BlockLegacyPublicPages` non girava mai | Provider e middleware rimossi; i loro percorsi (`show`, `homepage`, `auth/login1`-`5`) sono in `BlockShowcasePages`, globale; test in `Fixcity/tests/Feature/Security/ShowcasePagesHiddenTest.php` |

## Aperto

- **Pagine dei moduli coperte dal tema** (Folio si ferma alla prima pagina che corrisponde): Fixcity `tickets/*`,
  `lista-categorie`, `home`, `prova`; User `area-personale/*`, `profile/edit`; Blog `blog/*`, `events/*`. Middleware e logica
  messi lì non girano. JSON CMS irraggiungibili: `segnalazione-dettaglio.json`, `segnalazioni.index.json`.
- Restano fallimenti nelle suite dei moduli informativi: si correggono a giri successivi, partendo dalle cause comuni.
