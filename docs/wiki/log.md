---
title: "Fixcity Second Brain Log"
type: log
tags: [fixcity, second-brain, bmad, decisions]
created: 2026-09-26
updated: 2026-09-27
qmd: "Fixcity decisions implementation verification BMAD"
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues/572"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions/573"
---

# Modules Wiki Log

## [2026-09-27] Design Comuni — utility pubbliche e flusso CMS/Folio

Verificati i cataloghi v2.4.0: 35 template del sito e 44 schermate di workflow
(2 passaggi condivisi + 6 famiglie di servizi). I flussi di pagamento,
appuntamenti e pratiche amministrative non sono capability FixCity: il catalogo
serve a confrontare gerarchia e usabilità, non a presentare servizi simulati.

La verifica ha trovato `/it/domande-frequenti` e `/it/mappa-sito` con HTTP 200,
ma shell CMS priva di contenuto. Le FAQ demo includevano Lorem Ipsum e risposte
comunali estranee. Correzione: componenti Sixteen localizzati montati dal pattern
`[container0]/index` Folio + Volt, titoli/meta dedicati e link FAQ/sitemap nel
footer. Le view standalone top-level non prevalgono sul wildcard directory-index
Folio; preservare il router canonico ed evitare directory semantiche in `pages/`.

Evidenza: STORY-524; Playwright 2 test / 4 lingue / 4 larghezze, redirect guest
con locale conservata; view cache e quality gate wiki passano.

## [2026-09-27] demo | ticket ownership assigned to the citizen demo

`TicketDatabaseSeeder` no longer assigns demo reports to whichever user has
the lowest database ID. It resolves `DemoUsersSeeder::CITIZEN_EMAIL` through
`XotData`'s configured user model; without that account it skips seeding and
prints an actionable warning. It never falls back to another account.

Verification: isolated Pest 2 tests / 10 assertions cover preexisting unrelated
users, deterministic owner, idempotence, and missing-citizen skip. Playwright
on a seeded isolated SQLite database confirms the owner sees the capability,
guest-by-ID receives 403, and guest-by-code sees report status without the
secret code. A second signed-in account sees only public fields (no capability)
and gets 403 for an owner-private `pending` ticket (1 browser test passed).
PHPStan Modules has zero errors; targeted Pint and wiki quality gate pass. See
BMAD [STORY-521](../bmad/stories/STORY-521-demo-ticket-owner-assignment.md)
and [G-32](../bmad/gap-analysis.md).

## [2026-09-27] Design Comuni | full index audit and report-sheet navigation

Fetched both official indexes directly at release v2.4.0 and crawled their
linked HTML pages: 35 unique `/sito/` templates and 44 `/servizi/` workflow
templates returned HTTP 200. This corrects the prior workflow count of 45 and
the derived total of 81; STORY-517 and the Sixteen expected-experience document
now use 35 + 44 = 79 functional templates. The separate report-service detail
template is mapped to `/services/report-issue` and covers the FixCity domain.

Compared its actions with `segnalazione-dettaglio.html`: added the missing
localized secondary route from the report service sheet to `/tickets` in IT,
EN, DE and ES. Do not copy the sample municipality's URP contact, terms, or
appointments; those require approved tenant content. The service index remains
honest about the three implemented FixCity tasks. BMAD evidence: `STORY-517`,
`STORY-519`, and `services-catalog-gap-analysis-2026-09-27.md`. Playwright
passes 3/3 browser tests; the report-sheet route is checked in all four locales,
and the 390px screenshot confirms both action targets are at least 44px high
with no horizontal overflow (`/tmp/fixcity-design-comuni-report-detail-it-390.png`).
Laravel view cache, targeted Pint, the wiki gate, and `git diff --check` pass.

## [2026-09-27] citizen journey | private practice details

Audited the active Sixteen Folio route and confirmed the practice list already links
to `/tickets/{id}` through the localized Details label, while preserving code-based
tracking for records that have a capability. Extended
`CitizenMyTicketsDetailLinkTest` to prove an anonymous guest and a different signed-in
citizen both receive 404 for a private ticket; the owner still opens it, and the list
does not leak another citizen's title or code. Verified on isolated SQLite using
`APP_ENV=testing FIXCITY_TEST_SQLITE=1`: 2 tests / 14 assertions pass. An authenticated
Puppeteer smoke verified IT/EN empty states at 320/390/768/1440 px with no horizontal
overflow or JS/console errors. It found the “new report” CTA showed the raw `create`
translation key; IT and EN labels/tooltips are now localized and covered. The demo DB
has no owned tickets, so a populated browser detail still needs an isolated fixture.
The unresolved
`${FIXCITY_TEST_DB_USERNAME}` MariaDB placeholder is not changed; `.env.testing` stays
fail-closed. Browser IT/EN and the full wiki gate remain to be recorded in STORY-518.

## [2026-09-27] security | capability removed from public GeoJSON

The full Fixcity suite exposed a second capability-code disclosure outside the detail
API: `BuildTicketsGeoJsonAction` placed the bearer tracking code in public marker
properties, and the filter aggregate returned those features unchanged. Removed the
field; the aggregate, GeoJSON API, and private-practice authorization tests pass
together (8 tests / 35 assertions after CTA localization). The full suite run after
the GeoJSON fix but before CTA localization had 20 failures / 375 passes; remaining
failures include consent, route, privacy-expectation and Filament-icon issues, so suite
triage remains open.
The regression and boundaries are documented in STORY-510. Do not expose capability
codes through map data; map navigation uses the non-secret detail URL.

## [2026-09-27] design-comuni | services catalogue study

Studied the official Design-Comuni catalogue and service templates. Persisted the
complete three-dimension profile in `docs/bmad/design-comuni-design-dna-2026-09-27.json`
and the gap/correction record in `docs/bmad/design-comuni-services-comparison-2026-09-27.md`.
Implemented STORY-515: localized reusable service cards, accessible client-side
search and a category directory in the Sixteen Folio services page. The official
model remains the product reference for future persisted service/detail content.

## [2026-09-27] public-surface | legacy showcase routes closed

Added `App\Http\Middleware\BlockLegacyPublicPages` to the Folio theme/module
mounts. Internal showcase Blade sources remain for tests, but public guest URLs
now return 404 for test/prova/showcase/login-variant paths. Canonical demo routes
remain accessible and were verified in four locales with Playwright. This closes
the hardcoded Italian risk on reachable showcase pages without introducing a
controller or a service layer.

## [2026-09-27] demo | localized privacy policy

Replaced the tenant privacy placeholders with demo policy content for `it`, `en`,
`de` and `es`. The Folio privacy route now returns HTTP 200 with real localized
content instead of 503; Playwright verified all four locales at mobile width.

## [2026-09-27] ui/ux | home guest visual fix :8001

- Audit Puppeteer su `http://127.0.0.1:8001/it`: hero Tailwind fuori Design Comuni + CTA `btn-primary`/`bg-white` → box bianco vuoto; eyebrow `Laravel`.
- Home riscritta con markup BI (`bg-primary`, card, map-lit) + CSS `btn-hero-*`; `APP_NAME=FixCity`; i18n `pub_theme::home` / `navigation.site_title`.
- Screenshot v3 desktop/mobile OK; `/en` titolo tradotto. Doc: Themes/Sixteen `homepage-guest-visual-fix`; STORY-513 aggiornata.

## [2026-09-27] demo | seed + i18n FO + smoke guest

- `DemoUsersSeeder` + `FixcityDatabaseSeeder` (no UserDatabaseSeeder completo): 20 ticket `DEMO-*`, 13 categorie, 2 profili; utenti STI solo con alias validi (`master_admin` / `customer_user`).
- Blade FO guest senza italiano hardcoded: `home`/`index`, `segnalazioni/index`, `services` → `pub_theme::home.*` / `pub_theme::services.*` (it/en).
- `/it/segnalazioni` spostato a `pages/segnalazioni/index.blade.php` (Folio) per vincere su `[container0]`; lista pubblica via `BuildPublicTicketsQueryAction`.
- Vite: `server.watch.followSymlinks=false` + ignore `.claude` (ELOOP). Serve demo su `:8000`. Smoke HTTP 8/8; Playwright/Puppeteer MCP in `.mcp.json` ma Chromium host senza `libatk`.
- BMAD: [STORY-513](../bmad/stories/STORY-513-public-guest-visual-demo.md); seed [demo-tickets-presentation-seed](concepts/demo-tickets-presentation-seed.md).

## [2026-09-27] feature/notifications | email transazionali verificate

- `TicketStatusChangedNotification` e `TicketAssignedNotification` mantengono il canale database e aggiungono mail solo a `UserContract` con `hasVerifiedEmail()` e indirizzo non vuoto. Il corpo riusa il copy localizzato; l'azione apre tracking via capability code o il resource PA.
- Test Feature copre canali, contenuto/link email, destinatari verificati e non verificati. Rimangono opt-out/preferenze, push, retry queue/provider receipt e test SMTP reale.
- Suite Fixcity completa dopo questa tranche: 350 test / 1.440 asserzioni; PHPStan `analyse Modules` zero errori; Pint e test mirati verdi.
- Aggiornata gap analysis e journey sistema; STORY-003 è attualmente lockata da `fixcity-bmad-frontmatter` e non è stata modificata.

## [2026-09-27] security | rating owner check e doppio invio

- Audit di `SubmitCitizenTicketRatingAction`: `created_by`/`updated_by` autorizzavano impropriamente un voto; allineato il controllo a `owner_id` soltanto.
- Il submit ricarica e blocca il ticket (`lockForUpdate`) durante controllo duplicati e persistenza. Test diretto: owner audit-only respinto, rating valido persistito una sola volta e retry respinto.
- Pest rating mirato: 6 test / 16 asserzioni; suite Fixcity completa 346 test / 1.420 asserzioni; PHPStan Modules zero errori e Pint passano. Il lock va ancora verificato con due richieste concorrenti su MariaDB; nessuna migrazione/grant modificati.
- BMAD: [STORY-012](../bmad/stories/STORY-012-rating-system-flow.md); riepilogo gap [G-13](../bmad/gap-analysis.md).

## [2026-09-27] qa/ux | empty state KPI rating PA

- Esteso il test Feature Livewire del widget PA: con rating mostra media/conteggio; senza rating mostra `—` e `0` con le etichette localizzate. Test mirato: 2 test / 10 asserzioni; suite Fixcity 348 test / 1.430 asserzioni; PHPStan globale senza errori, Pint e quality gate wiki verdi.
- Aggiornata STORY-012 e G-13; la resa visuale reale, tastiera/accessibilità e la prova concorrente MariaDB restano gap distinti.

## [2026-09-27] architecture/qa | widget rating su base Xot

- `CitizenRatingOverviewWidget` ora estende `XotBaseStatsOverviewWidget` (regola Filament del progetto) ed è registrato nella coda PA dei ticket.
- Test Feature diretto del widget con un `RatingMorph` valorizzato: media `5/5` e conteggio `1`; verifica markup Livewire del widget, mentre il browser visuale/empty state resta da fare.
- Aggiornati STORY-012 e G-13. Suite Fixcity: 347 test / 1.425 asserzioni; PHPStan Modules zero errori; Pint e quality gate wiki verdi.

## [2026-09-27] security/ux | tracking per capability code

- Corretto il percorso notification che conteneva `ticket_id` sequenziale: notifiche database owner/follower ora portano `ticket_code` e `/tickets/track?code=...`; se un record storico non ha codice non si invia una notifica con link non sicuro.
- Anche “Segnalazioni seguite” punta al codice capability; per un ticket legacy senza codice mostra un messaggio esplicito senza URL vuoto. Tracking guest è testato al limite 10 richieste/minuto.
- Pest mirato: 6 test / 24 asserzioni. Suite Fixcity completa con `FIXCITY_TEST_SQLITE=1`, database condiviso e transazioni: 345 test / 1.416 asserzioni in 97,52 secondi; PHPStan Modules zero errori e Pint/view-cache passano. Report [tracking-links-capability](../bmad/tracking-links-capability-2026-09-27.md).
- Nessun grant MariaDB o database di produzione è stato modificato. Restano separati verifica schema MariaDB/staging e test visuale browser, non installato nel runtime corrente.

## [2026-09-26] impl | STORY-511 follow segnalazione FO

- Aggiunti `SetTicketSubscriptionAction` con autorizzazione `TicketPolicy::view`, operazioni idempotenti e row lock per evitare duplicati concorrenti sullo stesso ticket.
- `TicketSubscriber::user_id` viene evoluto a UUID tramite nuova migration `foreignIdFor(XotData::make()->getUserClass())`; i valori storici sono preservati.
- Il dettaglio CMS espone follow/unfollow con `aria-pressed`, live status, stato loading e CTA login guest; le notifiche database riguardano solo cambi stato pubblici.
- Test idempotenza/isolamento e destinatari-notifiche aggiunti. PHPStan Modules e view cache verdi; Pest e migration non eseguiti per MariaDB test negato (`1044`).
- Quality gate wiki fallisce per frontmatter/merge marker legacy di Xot e bashscripts; i file Fixcity di questa story non sono riportati tra i finding.
- I cambi stato pubblici inviano una notifica in-app post-commit al proprietario e ai follower deduplicati; le nuove assegnazioni avvisano il solo responsabile. Il link usa tracking opaco (cittadino) o resource PA (operatore); restano email/push e preferenze.
- `/area-personale/seguite` ora elenca i ticket seguiti dal solo utente autenticato, nasconde gli stati interni di terzi, mostra status label, conteggio/stato vuoto localizzati e link tracking; la pagina pratiche ha il collegamento di andata.
- Pest copre query isolata/ordinata e UI page compilation; il DB test resta bloccato da MariaDB 1044. Global account menu e prova browser responsive/accessibility restano aperti.

## [2026-09-26] security | STORY-510 owner id unico per visibilità privata

- `Ticket::isOwnedByAuthenticatedUser()` ora confronta solo `owner_id`; i campi di audit non danno accesso al FO privato.
- Test aggiunto per utente audit-only e owner effettivo; UI dettaglio CMS esistente usa `isVisibleOnPublicFrontoffice()`.
- `phpstan analyse Modules`, Pint e lint verdi; Pest mirato fallisce prima delle assertion con MariaDB `1044` su `fixcity_data_test`.
- Runtime HTTP/Pest a due account pending per accesso negato al DB test; non è stato cambiato il motore dati perché la policy Xot richiede MySQL/MariaDB.

## [2026-09-26] fix | STORY-509 TicketSubscriber FK

- `TicketSubscriber::ticket()` usava erroneamente `user_id`; ora risolve tramite `ticket_id`.
- PHPDoc user allineato a `UserContract`; aggiunto test che controlla la FK dichiarata.
- PHPStan Modules e Pint/lint verdi; Pest non raggiunge le asserzioni per MariaDB `1044` su `fixcity_data_test`.
- La relazione è corretta, ma follow/unfollow UI e invio notifiche sono ancora gap distinti.

## [2026-09-26] verification | PHPStan, wizard form e UI theme

- `cd laravel && ./vendor/bin/phpstan analyse Modules --no-progress` verificato: **0 errori**.
- Il wizard cittadino ora espone `priority` come campo hidden con default `low` e mostra la priorità nel riepilogo.
- `XotBaseWidget::resolveView()` conserva la vista risolta dal tema e usa il fallback solo se la risoluzione fallisce; il wrapper Sixteen è quindi effettivamente utilizzabile.
- Test verdi: enum priorità/stato/tipo, riepilogo wizard e view widget (27 test, 262 asserzioni + 6 test, 16 asserzioni).
- Blocker residuo: la suite con `Modules/Fixcity\Tests\TestCase` non parte per `Access denied` dell'utente MySQL `marco` su `fixcity_data_test`; serve il grant infrastrutturale, non uno skip nel codice.
- Quality gate wiki ancora rosso per frontmatter mancanti e merge-marker storici in `Modules/Xot`; non correlato alle modifiche Fixcity e da trattare con una storia dedicata.

## [2026-09-26] fix | vertical slice Ticket e test SQLite isolato

- `Ticket` non lascia più che `Spatie\ModelStatus\HasStatuses::__get()` nasconda il cast `TicketStatusEnum` della colonna `status`.
- La factory non valorizza più artificialmente `slug`: lo slug viene generato da `HasSlug` a partire dal nome.
- Il salvataggio normalizza coordinate e payload Nominatim, inclusa la chiave `region`, tramite `NormalizeTicketLocationDataAction`.
- `TicketTest` verificato in ambiente SQLite isolato: **26 test, 158 asserzioni verdi**.
- La migrazione categorie garantisce ora anche gli indici nominati quando aggiorna uno schema legacy.

## [2026-09-26] impl | STORY-508 conferma + tracking + FO reale

- CreateTicket: code + flash confirmation; Folio confirmation legge bag.
- CMS page merge: data bag pagina override blocco (sblocca `04-conferma`).
- Track Folio `/tickets/track`; payload pubblico con status/code/slug.
- Segnalazioni: query reale; pratiche: lista owner; Assign → Activity.
- PHPStan Actions OK; Pest bloccato da G-01 (DB test access denied).
- Story: `docs/bmad/stories/STORY-508-confirmation-tracking-fo.md`.

## [2026-09-26] docs | percorsi utente per tutti i tipi (guest→admin→sistema)

- Canon: `docs/wiki/concepts/user-journey-map.md` — dovrebbe/vede/può/fatto/manca.
- Verificato codice: confirmation vuota, segnalazioni demo, ChangeStatus+Activity OK,
  Assign senza activity, API dettaglio senza status, pratiche placeholder.
- Duplicati `docs/user-journey-maps.md` e `docs/bmad/actor-journeys.md` → puntatori.
- Aggiornato `actor-flow-map.md` (Activity su status non più “mancante”).

## [2026-09-26] bmad | set completo workflow process + attori

- Catalogo skill→workflow: `docs/bmad/workflow-catalog.md`.
- Process 00–10 arricchiti (gate, perché, output) in `docs/bmad/workflows/`.
- Attori eseguibili: `actor-citizen|operator|supervisor|admin|system.md`.
- Legacy uppercase in `docs/workflows/` sostituiti da puntatori minuscoli.
- Root `docs/bmad/` solo puntatori al canon FixCity.
- Sintesi: `docs/bmad/actor-flows.md`; indice: `docs/bmad/workflows/README.md`.

## [2026-09-26] bmad | workflow completi per attori e release

- Creato l'indice canonico `docs/bmad/workflows/` con bootstrap, discovery, product,
  architecture, story, implementation, QA, UI/UX, security, release e retrospective.
- Aggiunto `docs/bmad/actor-flows.md` per cittadino, operatore PA, supervisore e admin,
  con matrice delle prove.
- Sixteen documenta solo la boundary UI/UX in `Themes/Sixteen/docs/bmad/README.md`.
- Il Second Brain rimanda ai workflow senza duplicarne le regole.

## [2026-07-12] fix | PHPStan L10 — TicketLayoutViewModel trait types

- `PresentsTicketLayoutChrome`: `@property-read` per `$blockData`, `$selectedTypes`, `$liveTickets`; shape `list<array{id,label,active}>` su tab/breadcrumb
- `TicketLayoutViewModel`: `Collection<int, Ticket|object>` per demo Design Comuni + live query
- Scopo: VM FO elenco segnalazioni — dati CMS in `$blockData`, tab attiva per pannelli mappa/lista
- PHPStan: `vendor/bin/phpstan analyse Modules` → 0 errori
- GitHub: [#372](https://github.com/laraxot/base_fixcity_fila5/issues/372)

## [2026-06-13] docs | Gate chef — hub completamento + Activity/Xot test docs

- Hub: [platform-completion-roadmap](../Xot/docs/wiki/overviews/platform-completion-roadmap.md)
- Activity: 7 file test Assert; [completion-status](../Activity/docs/wiki/overviews/completion-status.md)
- Temi: Barthelemy, TwentyOne, Meetup completion roadmaps
- GitHub: [#372](https://github.com/laraxot/base_fixcity_fila5/issues/372)

## [2026-06-13] docs | PHPStan Pest sessione — helper test + roadmap completamento

- Fixcity: [phpstan-pest-testcase-helpers](../Fixcity/docs/wiki/concepts/phpstan-pest-testcase-helpers.md), [completion-roadmap](../Fixcity/docs/wiki/overviews/completion-roadmap.md)
- Notify: [phpstan-pest-test-doubles](../Notify/docs/wiki/concepts/phpstan-pest-test-doubles.md)
- Xot: aggiornato [phpstan-pest-bridge-discipline](../Xot/docs/wiki/concepts/phpstan-pest-bridge-discipline.md)
- UI, Tenant, Cms: `testing.md` aggiornati
- Sixteen: [theme-component-test-contract](../../Themes/Sixteen/docs/wiki/concepts/theme-component-test-contract.md), [completion-roadmap](../../Themes/Sixteen/docs/wiki/overviews/completion-roadmap.md)
- GitHub: [Fixcity#52](https://github.com/laraxot/module_fixcity_fila5/issues/52) / [D#53](https://github.com/laraxot/module_fixcity_fila5/discussions/53)

## [2026-06-05] docs | HackerNoon harness — tips 001-022 in wiki locale

- Stub/checklist: second-brain → canon Xot, ai-harness, [hackernoon map](../../../../docs/wiki/concepts/hackernoon-ai-coding-tips-fixcity-map.md), [llm-wiki.txt](../../../../bashscripts/tools/prompts/llm-wiki.txt)
- GitHub: [#272](https://github.com/laraxot/base_fixcity_fila5/issues/272) / [D#273](https://github.com/laraxot/base_fixcity_fila5/discussions/273)

# Modules Wiki Log

## [2026-06-05] docs | AI harness + HackerNoon propagati a tutti i moduli

- Creati `ai-harness-module-discipline.md`, aggiornato `second-brain-operating-model.md`
- Index wiki aggiornati (18 moduli) con sezione AI / second brain
- Stub `second-brain-local-discipline.md` allineati → canon Xot + mappa #272
- `index.md`: link `migration-update-timestamps-only` + `audit-migration-timestamp-redundancy.sh`

## [2026-06-05] architecture | parità N modelli per modulo (index cross-modulo)

- `wiki/index.md`: link a BMAD pilastro 3, script audit, snapshot root
- Canon: `docs/wiki/bmad/architecture-module-model-artifact-parity.md`

## [2026-04-28] docs | second brain operativo per i moduli

- creato `index.md` per rendere la wiki dei moduli navigabile come knowledge base.
- aggiunto `concepts/second-brain-operating-model.md` con modello operativo CODE + PARA adattato al repository.
- incluse sezioni best practices, bad practices, false friends e link verificati di approfondimento.

## [2026-07-12] phpstan | view-model chrome typed shapes

- `PresentsTicketLayoutChrome` usa shape PHPDoc su breadcrumb, tabs, CTA, contatti e collection live tickets.
- Motivo: PHPStan analizza il trait nel contesto di `TicketLayoutViewModel`; le shape devono essere visibili al consumer reale, non solo al file trait isolato.
# Decisione 2026-09-26 — notifiche follower Fixcity

- Un cambio stato genera una notifica database soltanto se Activity è `status_change`, visibilità `public` e lo stato di destinazione è nella allowlist pubblica SSoT.
- Il listener gira dopo il commit; owner e follower sono deduplicati. Errori di consegna vengono loggati senza annullare una modifica già persistita.
- Il link usa il codice opaco di tracking, mai l’ID sequenziale. Motivo e payload interni non vengono copiati nella notifica.
- Rimangono fuori da questa tranche: notifiche assegnazione, email/push, preferenze e pagina seguite. Il test DB/UI attende l’accesso MariaDB test secondo `docs/testing-database-strategy.md`.

### 2026-09-26 — FixCity: protezione capability code nel payload pubblico

`BuildTicketPublicDetailsPayloadAction` restituiva il codice bearer nei dettagli di qualunque ticket pubblico. Ora il campo è vuoto per guest/non owner e disponibile solo all’owner autenticato; test API coprono entrambi i casi. STORY-510 aggiornata. PHPStan e test sono da rieseguire; il DB test continua a negare l’accesso.

### 2026-09-26 — FixCity: link follower senza capability code

Decisione precedente da aggiornare: i link alle segnalazioni seguite e le notifiche non devono usare il codice opaco. Il codice è una capability e resta soltanto nel tracking anonimo avviato dal cittadino che lo possiede. Follower autenticati ricevono `/tickets/track?ticket_id=<id>`; la pagina carica il record solo dopo `Gate::authorize('view', $ticket)`, e la policy continua a negare ticket privati non posseduti. Rimossi codice da lista seguite e URL notifica. Test Feature aggiunti per link senza codice, lookup owner e guest negato; PHPStan e Blade cache passano. Esecuzione Pest ancora bloccata dal placeholder `${FIXCITY_TEST_DB_USERNAME}` (MariaDB 1045), quindi test runtime e controllo visuale browser non sono ancora provati.

### 2026-09-26 — Verifica lookup tracking autenticato

Correzione della nota sopra: con `APP_ENV=testing FIXCITY_TEST_SQLITE=1`, `TicketStatusNotificationTest` e `CreateTicketConfirmationFlowTest` passano (13 test, 32 asserzioni), incluso owner via ID, guest negato e URL follower privo del codice. PHPStan Modules: 0 errori; `pint` e `php artisan view:cache` passano. Smoke browser esterno non conclusivo: il server HTTP continua a puntare al DB MySQL `fixcity_data_test` e restituisce 500 con placeholder credenziali; il rendering route è comunque coperto dai test HTTP Laravel su SQLite.

### 2026-09-26 — STORY-012 rating nel dettaglio cittadino

Audit del flusso ha rilevato il prompt definito ma non montato nella pagina CMS `tickets.view`; inoltre `ViewWidget` non dichiarava la propria view XotBaseInfolistWidget. Integrato il prompt solo nel dettaglio del proprietario per ticket risolti/chiusi e corretto il binding della view. Nuovi Feature test: 5 test / 12 asserzioni per visibilità owner, stato, voto fuori range, persistenza RatingMorph e conferma. STORY-012 corretta: nessun endpoint POST, evento o notifica PA non presenti viene più dichiarato. Restano test di concorrenza/doppio invio, aggregato PA e verifica visuale browser.

### 2026-09-26 — Evidenza completa rating e PHPStan

Dopo l’integrazione del prompt rating nel detail: suite completa Fixcity 339 test / 1.389 asserzioni; PHPStan globale `analyse Modules` `[OK] No errors`; test mirato rating 5/12; Pint, Blade cache e `git diff --check` verdi. Rimane la verifica visuale browser impossibile finché il server test punta al MySQL con credenziali placeholder e Chromium manca.

## [2026-09-27] feature/notifications | preferenza email Fixcity

- STORY-013 implementa `preferences.fixcity.email_ticket_updates` con default true, Actions Get/Set e widget Livewire nella pagina Folio autenticata `area-personale.impostazioni`.
- Salvataggio conserva le altre preferenze del profilo. Le notifiche stato e assegnazione mantengono il canale database e inviano email soltanto a utenti verificati con indirizzo non vuoto e preferenza attiva.
- Suite Fixcity: 356 test / 1.458 asserzioni; PHPStan globale senza errori; quality gate wiki, Pint mirato, Blade cache e `git diff --check` verdi. Il `pint --test` globale evidenzia file legacy non formattati e parse error Sixteen preesistenti. La verifica visuale browser resta da eseguire in un ambiente con Chromium.
- Il test di navigazione ha inoltre rivelato e corretto un HTTP 500 preesistente in `area-personale.pratiche`: Folio non passava alla view la variabile creata nel file; il callback `render()` ora fornisce il dato esplicitamente. Test pagina impostazioni: 4 test / 14 asserzioni passati.

## [2026-09-27] guest-create-cta | CTA segnalazioni e metadata CMS

- Il CTA della lista `/it/segnalazioni` puntava a `/it/segnalazione-crea` e apriva il modal dettagli; ora è un unico link verso `/it/tickets/create`, localizzato per IT/EN. Il modal resta nel layout per i dettagli mappa. Il fallback del ViewModel ora usa la route canonica.
- Le pagine CMS Sixteen leggono title tradotto e description dalla pagina CMS prima del layout; la precedente proprietà Volt non era visibile fuori da `@volt` e generava HTTP 500. Il test HTTP copre title, destinazione, unicità CTA e separazione modal (2 test / 8 asserzioni).
- Browser Chromium reale: HTTP 200 e titolo `Segnalazioni`; responsive 320/768/1440 senza overflow; nessun errore JS. Click CTA guest → `/it/auth/login`, form password visibile. Screenshot temporanei `/tmp/fixcity-segnalazioni-{320,768,1440}-updated.png`.
- QA visuale non chiuso: a 320 px il testo “Il mio Comune” è troncato; con dataset vuoto l'area mappa desktop è grigia. Verificare con dati geolocalizzati prima di attribuire il difetto al rendering dei marker.

### 2026-09-27 — QA responsive header e mappa, verifica seconda passata

- Il brand mobile ellissava `Il mio Comune` a 320 px (testo 111 px in 104 px disponibili). Sixteen ora usa font 16 px fino a 359 px: browser Chromium misura 95/95 px a 320 e 111/111 px a 360; niente clipping, larghezza pagina invariata.
- L'area grigia mappa desktop non si riproduce dopo `networkidle` + stabilizzazione. A 1440 px la mappa è larga 1108 px, tile OSM caricati 15/15, nessun request failure/HTTP error; endpoint live `/api/tickets/geojson` restituisce FeatureCollection valida con zero record. Il file pubblico statico con 20 dati demo non è la fonte usata dalla pagina; non va riattivato al posto del GeoJSON live.
- Build Sixteen `npm run build:with-webroot` completata. Browser 320/360/768/1440: HTTP 200, CTA unico, zero overflow, zero errori console. Restano da provare i marker su ticket reali e l'E2E autenticato/PA.

### 2026-09-27 — FixCity tracking: cataloghi IT/EN e stato errore

- Browser guest su `/it/tickets/track?code=TCK-NOTFOUND000000` esponeva `fixcity::ticket.fields.code.label`: mancavano le chiavi `fields.code.label` e `fields.status.label` nei cataloghi owner. Aggiunte alle traduzioni IT e al partial EN aggregato dal loader `ticket.php`.
- Nuovi Feature test `TrackingTranslationTest`: recovery codice errato e risultato valido in IT/EN; 4 test / 23 asserzioni. Browser: IT 320 px ed EN 1440 px, codice conservato, `aria-invalid=true`, alert localizzato, nessuna chiave grezza, overflow o errore JS.
- Il browser del server locale non ha un ticket persistito; il successo con status e titolo è provato da Pest con factory/SQLite. Rimangono E2E con DB popolato e flusso autenticato/staging.

### 2026-09-27 — E2E tracking valido e privacy owner su SQLite temporaneo

- Per superare il limite del DB MySQL di test senza scrivere nei dati condivisi, `php artisan xot:build-test-sqlite --path=/tmp/fixcity-browser.sqlite --fresh` ha creato schema completo (114 tabelle). Un ticket e una activity pubblica sintetici sono stati inseriti solo lì e serviti da Artisan su `127.0.0.1:8095` con connessioni SQLite.
- Browser by-code guest a 320/768/1440: title “Traccia la segnalazione”, ticket, status “In lavorazione”, timeline pubblica, zero overflow/JS. La scheda guest non ripete il capability code. Login owner + `ticket_id`: codice visibile e stato label; browser isolato guest sul medesimo ID riceve 403.
- E2E ha trovato titolo fallback “Laravel”, status grezzo `in_progress` e riga code vuota per guest. La view ora passa titolo localizzato, usa `TicketStatusEnum::getLabel()` e rende il codice solo se il payload lo autorizza. Pest verifica IT/EN, status locale e branch privacy guest/owner.
- MariaDB, credenziali, DB condiviso e server esistente 8094 non sono stati modificati. La DB temporanea e il server 8095 vanno rimossi dopo aver terminato le verifiche della tranche.

### 2026-09-27 — BMAD UI/UX: sincronizzazione evidenze Sixteen/Fixcity

- Riallineati workflow `07-ui-ux` e indice BMAD Sixteen: lista pubblica e tracking valido sono verificati in browser; il tracking owner/guest usa SQLite isolato, mentre marker reali, wizard, rating, coda PA, accessibilità completa e staging restano gap espliciti.
- Creato report owner tema `Themes/Sixteen/docs/bmad/fixcity-ui-ux-runtime-audit.md` con matrice evidenze/limiti e link ai documenti Fixcity. Suite Fixcity: 363 test / 1.507 asserzioni; PHPStan, view cache, Pint mirato, gate wiki e diff check verdi.
- Second Brain QMD non è installato/eseguibile in questa checkout (`qmd: command not found`; gli script `llm-wiki-qmd.sh` e `second-brain-healthcheck.sh` non sono presenti nel workspace). La conoscenza è stata registrata qui e nel local chat board senza aggiornare l'indice bloccato.

### 2026-09-27 — FixCity wizard runtime IT/EN e regressioni metadata

- Il browser autenticato rilevava `<title>Laravel</title>` e una chiave grezza nel riepilogo stepper desktop. La pagina Folio ora legge il titolo localizzato dal record CMS; Sixteen usa la chiave active già posseduta dal catalogo `ticket`; aggiunto catalogo EN mancante `fixcity.ticket.*` per lo schema del wizard.
- Regressione Pest dedicata: 2 test / 10 asserzioni IT/EN; suite completa Fixcity 365 test / 1.517 asserzioni; PHPStan Modules zero, Pint mirato, `view:cache`, wiki quality gate e diff check passano.
- E2E con SQLite temporaneo e un utente sintetico: title, step label, privacy required e responsive a 320/768/1440 in IT/EN; `Space` sul checkbox consente l'avanzamento e il rifiuto del consenso blocca lo step. Il browser mostra però che, dopo la validazione negativa, il focus cade su `body` e l'input non riceve `aria-invalid`/`aria-describedby`: resta un fix accessibilità con test browser/Feature prima di chiudere wizard e release.
- Nessun ticket è stato inviato; gli upload e il flusso di persistenza/conferma richiedono una successiva prova completa su dati sintetici.

### 2026-09-27 — FixCity wizard: focus e semantica errore privacy

- Chiuso il gap di focus rilevato nel browser: `TicketForm` espone `aria-invalid` dinamico e collega il testo errore a `aria-describedby`; il wrapper Sixteen osserva l'errore del solo consenso e rifocalizza il checkbox una volta sul passaggio invalido.
- Chromium IT a 320 px: dopo click “Successivo” senza consenso il focus è `form.privacyAccepted`, `aria-invalid=true`, `aria-describedby=ticket-privacy-error`, messaggio presente, zero overflow e zero errori JS. La suite completa e PHPStan sono da rilanciare dopo questo fix.
- Submit, upload e conferma non sono stati invocati; il database temporaneo contiene solo l'utente sintetico e verrà rimosso alla chiusura del server.
- Verifica finale dopo il fix: suite Fixcity **365 test / 1.521 asserzioni**, PHPStan Modules zero errori, Pint mirato, Blade cache e wiki quality gate passano. Il browser è stato ripetuto dopo il controllo tipizzato sul `MessageBag`.

### 2026-09-27 — FixCity: rimosso input personale non persistito dal wizard

- Audit BMAD del flusso cittadino: lo step riepilogo renderizzava nome, codice fiscale, telefono ed email, ma `Ticket` non ha tali colonne e `CreateTicketAction` non li mappava; la compilazione dava l'impressione falsa di aver salvato quei dati.
- Rimossi gli input e il relativo stato iniziale. Il wizard richiede autenticazione, `owner_id` identifica il cittadino e la pagina di conferma legge l'email dall'account.
- Aggiunta regressione Feature su chiavi schema, default e render Livewire; aggiornati `docs/bmad/gap-analysis.md`, workflow cittadino e UI/UX.
- Restano da provare via browser l'invio effettivo, l'upload e la conferma a viewport mobile/desktop; nessun dato o allegato è stato creato in questa modifica.
- Verifica finale: Fixcity **366 test / 1.528 asserzioni**, PHPStan Modules 0 errori, Pint mirato, quality gate wiki, `view:cache` e `git diff --check` passano. Chromium autenticato conferma 320/768/1440 px senza overflow/campi personali/errori JS; il flusso submit/upload resta aperto.

### 2026-09-27 — FixCity: submit wizard persistito verificato

- Aggiunto test Feature Livewire che attraversa `submit()`, persiste ticket con owner e coordinate, verifica codice e stato `PENDING` e controlla l'email nel payload di conferma.
- Il database normalizza le coordinate numeriche; le asserzioni verificano il valore numerico persistito senza dipendere dal tipo string/float della connessione.
- Il browser submit/upload/conferma e lo smoke MySQL/staging restano gap espliciti; nessun ticket è stato creato nell'istanza persistente.
- Verifica completa: **367 test / 1.541 asserzioni** Fixcity, PHPStan Modules 0 errori, Pint mirato, `view:cache`, gate wiki e `git diff --check` verdi. Dopo la correzione di stile degli import, test Feature del wizard (4 test / 24 asserzioni) e PHPStan completo sono stati ripetuti e verdi.
- Corretto anche il flusso BMAD cittadino: il wizard chiama `CreateTicketAction` e la normalizzazione posizione avviene nel modello; `GetTicketFormDataForPersistAction` appartiene invece al lifecycle Create Resource PA.
- Esteso il Feature test fino alla successiva richiesta GET Folio: la conferma renderizza il codice generato e l'email owner. Dopo G-23, Pest mirato del widget: 4 test / 27 asserzioni verdi; manca la verifica Chromium post-submit con upload reale.

### 2026-09-27 — FixCity wizard: persistenza allegati

- La prova Livewire con immagine sintetica ha dimostrato che gli allegati non venivano persistiti: `getState()` chiamava `saveRelationships()` quando il form non aveva ancora il Ticket come record.
- Il widget valida e raccoglie lo stato senza hook relazionali, persiste il ticket, poi assegna il modello allo schema e salva le relazioni. Il test verifica media nella collection `attachments` e file esistente su `Storage::fake('public')`.
- Aggiornata la story map G-23 e i workflow attore/UI; browser con selezione file e conferma visuale post-submit restano da eseguire.
- Verifica dopo G-23: suite Fixcity **367 test / 1.544 asserzioni**, PHPStan Modules 0 errori, Pint mirato, `view:cache`, quality gate wiki e `git diff --check` verdi.

### 2026-09-27 — FixCity wizard: upload e conferma verificati in Chromium

- La prova browser con `APP_ENV=testing` selezionava per design Livewire `tmp-for-tests`; il test server è stato riavviato come `local` su DB SQLite isolato e l'upload è riuscito senza HTTP 500.
- Chromium ha riprodotto `this.editor.getCroppedCanvas is not a function` attivando l'editor opzionale. Rimosso `imageEditor()` dal campo allegati; la selezione diretta dell'immagine, il submit e la conferma ora completano il flusso senza errori JavaScript.
- Verificato nel DB temporaneo ticket sintetico `TCK-FQJ7YFEEBAKHLAYQ`, media nella collection `attachments` e file generato presente. UI finale mostra title IT, codice di tracking ed email account.
- L'E2E ha inoltre scoperto la chiave grezza `fixcity::segnalazione.steps.current_of_total.label`; aggiunti i cataloghi `lang/it/segnalazione.php` e `lang/en/segnalazione.php` con regressione IT/EN.
- Suite Fixcity: **369 test / 1.570 asserzioni** passati su SQLite isolato; PHPStan Modules zero errori, Pint mirato e quality gate wiki verdi. Restano MySQL/staging, browser PA e controlli accessibili manuali.

### 2026-09-27 — FixCity wizard: label riepilogo IT/EN

- L'ispezione della pagina di riepilogo E2E ha trovato `empty5` e label grezze `review_*`; il catalogo italiano conteneva i nomi delle chiavi e mancava il catalogo `ticket_form_review_infolist` inglese.
- Corrette le label in IT/EN e svuotata la heading della Section di warning senza titolo, conservando l'avviso. Regressione testata su entrambe le lingue: **4 test / 32 asserzioni** per `TicketFormWizardSummarySchemaTest`.
- La regressione G-24 è stata verificata nel browser sul percorso canonico a 320/768/1440 px per entrambe le lingue: titoli pagina corretti, label attese, nessuna chiave grezza, overflow orizzontale o errore JS.
- Suite completa Fixcity dopo G-24: **369 test / 1.570 asserzioni** passati. Il catalogo traduzioni summary ha anche 4 test / 32 asserzioni mirati; il widget FO 4 test / 28 asserzioni.
- Il percorso EN mostra ancora il footer Sixteen in italiano con contenuti demo e link `#`; aggiunto G-25, proprietario Theme Sixteen, da implementare e verificare.
- `qmd search` non è disponibile nel container; consultate le story/workflow BMAD e il Second Brain nel wiki del modulo.

### 2026-09-27 — Design Comuni services follow-up and actual task routes

- Followed the existing Design Comuni baseline and 81-template coverage audit; this increment is tracked separately as STORY-519 to avoid duplicating the full catalog inventory. Handoff: `docs/chat/services-catalog-design-comuni.md`.
- Replaced unverified municipal service claims and sample URP phone/email with the three implemented Fixcity journeys. `/services` now uses the shared public app shell (skip links, header, footer), real localized task URLs, four-locale search/count/empty feedback, and sections with valid in-page navigation.
- Browser route checks showed report submission redirects guests to login; the report card now states that sign-in is required in all four locales.
- Chromium production build: IT/EN/DE/ES × 320/390/768/1440, HTTP 200, no overflow or page errors, no sample contact values, all three links present, no-match query hides all tasks and announces an empty state. Screenshot: `/tmp/fixcity-services-final-it-390.png`.
- `npm run build`, `php artisan view:cache` and `bash bashscripts/quality-gates/verify-llm-wiki.sh` passed. Focused Pest is blocked before assertions by MariaDB `1045` using unresolved test placeholder credentials. Remaining: manual screen-reader review, fix Vite's unresolved theme logo runtime reference, and implement the authoritative report-service detail when tenant-owned service/contact data is defined.

### 2026-09-27 — Confirmation data bag and multilingual runtime

- The theme `x-page` now merges runtime page data into the nested block `data` payload.
- Confirmation code, tracking, visibility and personal-area links are rendered from
  `BuildTicketConfirmationDataAction`; stepper, breadcrumb, contacts and rating have
  IT/EN/DE/ES fallbacks.
- Chromium mobile verified the confirmation flow in all four locales; PHPStan and view
cache are green. See BMAD decision `design-comuni-confirmation-runtime-2026-09-27`.

### 2026-09-27 — Guest crawl, localized GDPR and demo asset serving

The final Chromium crawl covers the Design Comuni public journey in IT/EN/DE/ES,
including services, service detail, categories, tickets, privacy, auth and guest
redirects. Canonical pages have no UI/i18n anomaly, raw translation key, horizontal
overflow, console error or failed asset request. The GDPR notice no longer embeds
Italian copy or non-localized report links. `server.php` serves sibling `public_html`
assets with explicit JavaScript MIME handling when the demo server runs from the
Laravel directory. See `design-comuni-guest-crawl-2026-09-27.md`.

### 2026-09-27 — Demo seed repeatability and auth copy cleanup

`FixcityDatabaseSeeder` was executed successfully again (exit 0): three demo users,
13 categories, 20 `DEMO-*` tickets, activities/comments and regenerated GeoJSON.
`TicketDatabaseSeeder` now resolves the configured user class through `XotData` and
types it as `Model&UserContract`, without importing the concrete User model. The
Fixcity login component uses locale-owned `fixcity::auth.*` keys for all visible
labels and the obsolete module category Blade delegates to the canonical Sixteen
category directory. PHPStan remains zero-error and the final Chromium crawl remains
clean in all four locales.

`php artisan dev --inline --no-restart --no-interaction` was also started in a
controlled run: Vite selected 5174 after 5173 was occupied, Laravel selected 8005
after 8000–8004 were occupied, and no ELOOP/crash occurred. The temporary process
was stopped after verification.

### 2026-09-27 — Auth locale fallback hardening

The guest locale audit exposed fallback Italian in the active Sixteen auth widgets,
not in the Fixcity pages: the widgets referenced keys absent from the User locale
catalogue. The theme widgets now use the canonical `user::login`,
`user::registration` and `user::auth.login_page` keys; German login field metadata
and the Italian registration action catalogue were completed. The public crawler
now records `italianLeak` in its result model, so non-Italian UI leakage cannot be
silently omitted from its anomaly report. HTTP-rendered checks for EN/DE/ES auth
and report entry pages are clean; browser execution remains environment-dependent
when the host lacks GTK/ATK runtime libraries.

### 2026-09-27 — Design Comuni site catalogue vs service workflows

Read the official v2.4.0 root index and fetched the full `/servizi/index.html` source.
The root index describes municipal site sections and the disservice-report journey
(service sheet, privacy, data, review, confirmation, personal area, public list),
while the separate services index describes transactional service families such as
ranking applications, permits, benefits, pagoPA, IMU/F24 and paid services. They
are separate information architectures; the reference templates do not imply those
integrations exist in FixCity. The public service catalogue therefore stays limited
to verified reporting/list/tracking journeys and tenant-supplied facts.

Reconciled stale FixCity BMAD evidence: the local `/it/privacy` currently returns
HTTP 200 with tenant demo Markdown, so docs no longer claim the route is 503. This
is route evidence only, not legal approval. G-30/G-31 and release G4 remain blocked
on tenant/privacy-owner review of copy, contacts, checkbox semantics and evidence
retention. See `docs/bmad/services-catalog-gap-analysis-2026-09-27.md` and
`docs/bmad/release-plan.md`.

### 2026-09-27 — Canonical tickets route regression evidence

Fresh full Fixcity suite on isolated SQLite: 391 passed, 4 failed (1,705 assertions).
All four remaining failures are in `TicketPagesTest.php`, whose requests still target
legacy `/it/segnalazioni` and assert HTTP 200 although the route redirects 301 to the
canonical `/it/tickets`. That test file is held by a pre-existing lock, so it was not
modified. Updated the unlocked CTA regression test to use `/it/tickets`, the rendered
page title and current create link; focused result: 2 tests / 9 assertions passed.
Fresh `php -d memory_limit=2048M vendor/bin/phpstan analyse Modules --no-progress`
reports zero errors. `/it`, `/it/tickets`, `/it/services` and `/it/privacy` returned
HTTP 200 on the local runtime; `/it/privacy` contains demo policy only, not legal
sign-off.

### 2026-09-27 — Guest browser matrix and canonical route test cleanup

Used Playwright Chromium from the workspace with locally extracted shared libraries
under `/tmp` (no system package or project dependency changes). Checked home, ticket
list, services, report-service detail, privacy and tracking form for IT/EN/DE/ES at
320 and 1440 px: 48 route/viewport cases returned 200, with no horizontal overflow,
raw translation keys, missing anchor hrefs or page errors. Tracking form has the
required `code` field and localized submit label in all four locales. Reviewed the
Italian homepage at 390 px; screenshot is `/tmp/fixcity-home-current-it-390.png`.
This does not verify authenticated valid-code tracking, follower interactions, the
429 visual state, PA Filament, assistive technology or a staging deploy.

Corrected the unlocked CTA feature test to the actual canonical `/it/tickets` route,
rendered title and current CTA markup: 2 tests / 9 assertions pass. Full Fixcity
Pest remains 391 passed / 4 failed / 1,705 assertions; all four failures are stale
`/it/segnalazioni` requests in `TicketPagesTest.php` expecting 200, while the route
redirects 301 to `/it/tickets`. That file remains under a pre-existing lock and was
not changed. PHPStan Modules reports zero errors; focused Pint, wiki gate and diff
check pass. Privacy's local HTTP 200 still only proves the demo route/content loads,
not tenant/legal approval.

### 2026-09-27 — Tracking form responsive correction

The mobile tracking form had no horizontal overflow but left only 154 px for the
code because the submit button shared the row. Added a mobile stacked layout in
Sixteen below 576 px; each control now spans 240 px at the 320 px viewport with an
8 px gap. The Playwright regression covers 4 locales × 6 widths, geometry, native
required state, label/help association and keyboard focus: 1 test passed. Vite
production build passes (existing unresolved theme logo warning remains). BMAD
expected/observed/correction/evidence:
`Themes/Sixteen/docs/bmad/tracking-search-form-layout-2026-09-27.md`.
