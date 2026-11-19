---
id: dcp-281
title: "DCP-281: rinforzo del back office ticket e ritiro delle pagine legacy"
type: story
epic: go-live-backoffice
status: in-progress
priority: P1
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 281 ChangeStatus AssignTicketAction user_id uuid attore audit stati consentiti assegna operatori PA hasPermissionToOrCreate pagine legacy tickets slug id TicketList CreateTicketScreen"
related: [dcp-139, dcp-186, dcp-200, dcp-202, dcp-208]
tags: [back-office, audit, workflow, legacy, test, bmad]
---

# DCP-281: rinforzo del back office ticket e ritiro delle pagine legacy

## Stato

Fase BMAD: Dev (TDD). Origine: gap A-02, A-09, A-13, A-14, A-23, A-36, A-37, A-38 del report `gap-A-fixcity-mobile`. Ogni
affermazione del report e' stata riverificata nel codice prima di toccare: due righe avevano una causa o un'estensione diversa
(A-23 e la raggiungibilita' di A-09/A-37, vedi sotto). L'esito per riga e' in fondo (sezione Esito).

## Fatti misurati prima di toccare

1. **A-02, tipo reale della colonna.** `ticket_activities.user_id` e' `uuid` nel database di sviluppo (`information_schema.columns`
   di `fixcity_data`), e la migrazione `2026_09_26_151800_create_ticket_activities_table.php` la riallinea a uuid
   (`alignUserIdColumn`). Il database usa-e-getta clona la struttura da quel database, quindi anche i test vedono `uuid`. Quindi
   NON serve alcuna migrazione. `RecordTicketActivityAction::execute` accetta gia' `int|string|null`. Sette Action su nove usano
   `is_int($actorId) || is_string($actorId)`; solo due usano `is_int`: `Actions/ChangeStatus.php:49` e
   `Actions/AssignTicketAction.php:41`. Con utenti UUID (`users.id` e' `varchar(36)`) l'attore diventa sempre `null`.
2. **A-02, il lock su `Actions/ChangeStatus.php` era orfano.** `Actions/ChangeStatus.php` e `Models/TicketActivity.php` avevano
   un `.lock` del 2026-09-26 (task `fixcity-story-500-501`, agent `codex`). All'inizio la regola della sessione («se e' LOCKED salta»)
   mi ha fatto saltare `ChangeStatus.php` e marcare il test come `skip`. Il coordinatore ha poi autorizzato la rimozione perche' il
   lock e' orfano. Prove del 2026-10-26: (a) eta' del lock 43056 minuti (circa 30 giorni, letta dal marker); (b) il PID registrato nel lock e' quello di una subshell di `lock.sh` gia' terminata, non di una sessione
   viva; (c) `ChangeStatus.php` e' stato modificato dopo il lock (commit del 2026-10-15, mtime 2026-10-22), quindi chi lo teneva non
   lo stava piu' usando; (d) secondo il coordinatore nessuna sessione in corso lo aveva preso: i pari attivi (d0, 29, f5) sono
   partiti da poche ore, dopo il lock. Procedura seguita: `check.sh`, `unlock.sh` solo su quel lock, poi il mio `lock.sh ... DCP-281 w1a`, modifica minima,
   `unlock.sh`. Il lock di `Models/TicketActivity.php` NON l'ho toccato (non serve: il file non e' stato modificato).
3. **A-13.** `Filament/Actions/ChangeStatus.php:38` offre `->options(TicketStatusEnum::class)`, cioe' tutti i nove stati. La
   matrice legale e' in `TicketStatusEnum::allowedTransitions()` (SSoT, con `allowedTargets()` e `assertCanTransitionTo()`); l'Action
   `Actions/ChangeStatus` la fa rispettare con una `DomainException`. L'azione e' montata solo in `ViewTicket` (header).
4. **A-14.** `ViewTicket.php:59` fa `->pluck('name','id')` su tutti gli utenti. I ruoli PA sono definiti una volta sola in
   `Policies/BasePolicy::PA_ROLES = ['operator','supervisor','admin']` (`protected const`, usata da `isPaOperator`). Nel database di
   sviluppo esiste il ruolo `operator`; `supervisor` e `admin` non sono ancora righe (esistono nei test e nei ruoli pannello
   `fixcity::admin`, `super-admin`). `User::role([...])` di Spatie lancia `RoleDoesNotExist` per un ruolo mancante, quindi la
   selezione va fatta con `whereHas('roles', ...)` sui nomi.
5. **A-23, la causa del report e' sbagliata.** Il report dice «`findOrCreate` a ogni controllo»: `Spatie\Permission\Models\Permission::
   findOrCreate` (v8.3.2, `Permission.php:139-150`) cerca prima nella cache del registrar e fa `INSERT` solo se la riga manca.
   Quindi si scrive una volta per nome di permesso, non a ogni controllo. Nel database di sviluppo ci sono gia' 5 righe `ticket.*`
   (`assign`, `changeStatus`, `update`, `view`, `viewAny`) create in questo modo. L'auto-registrazione e' il comportamento voluto di
   un metodo che si chiama `hasPermissionToOrCreate` e ha oltre quaranta chiamanti in `Modules/User` e `Modules/Fixcity`. Il file e'
   in `Modules/User` (altro modulo e altro repo), non in Fixcity.
6. **A-09 / A-37, raggiungibilita'.** Ho risolto gli URL con il router di Folio (stesso ordine di `RequestHandler`: il primo percorso
   montato che combacia vince; il tema e' montato prima dei moduli in `Cms/FolioVoltServiceProvider::registerFolioPaths`):
   `/it/tickets/29`, `/it/tickets/hhh`, `/it/tickets/create`, `/it/tickets/track` combaciano tutti con
   `Themes/Sixteen/resources/views/pages/[container0]/[slug0]/index.blade.php`, e `/it/tickets/track/ABC123` con
   `[container0]/[slug0]/[container1]/index.blade.php`. Le pagine del modulo `tickets/[slug].blade.php` (auth senza autorizzazione,
   `withTrashed`, nome ed email dell'utente corrente come autore, Nominatim con User-Agent segnaposto) e `tickets/[id].blade.php`
   (testi italiani fissi, modello intero) sono quindi oscurate dal tema in tutte e quattro le lingue. Anche tra loro due, `[id]` e
   `[slug]` nella stessa cartella non possono coesistere: ne risponde una sola. I nomi di rotta `ticket.view` e `tickets.detail`
   non sono usati da nessun file (grep su Modules, Themes, app, routes, config, resources). Il dettaglio vivo e' nel tema ed e' gia'
   `BuildPublicTicketDetailAction` (DCP-148). Esiste gia' un `[slug].blade.php.old` (versione precedente, diversa): la regola
   `.old` senza sovrascrivere impone un suffisso numerato (precedente nel repo: `.old3`, `.old4`). La prova del router non dipende
   dagli id dei dati (`[slug0]` accetta qualunque valore). Il dettaglio pubblico del tema funziona: misurato con curl il
   2026-10-26 su entrambe le istanze con gli id che esistono in ciascuna (200 per 29, 30, 31, 32 su `:8081`, database di sviluppo;
   200 per 1, 2, 3 su `:8091`, fixture), vedi Esito.
7. **A-38.** `app/View/Components/Blocks/TicketList.php` non ha alcun punto di istanza: nessun `<x-fixcity::blocks.ticket-list>`
   ne' `Blocks\TicketList` in views, JSON o PHP (le uniche citazioni sono documenti). I blocchi CMS `ticket_list` risolvono per
   `data.view` (`pub_theme::components.blocks.ticket-list.design-comuni` in `tickets/index.json`) e la view Volt
   `components/blocks/ticket_list/ticket_list.blade.php` calcola le proprie categorie da `TicketTypeEnum`, senza leggere
   `$categories` ne' `$tickets` della classe. I conteggi «(21)», «(208)», «(321)», «(302)» non sono quindi mai mostrati.
8. **A-36.** `Modules/Mobile/app/Native/Screens/CreateTicketScreen.php` (299 righe) e `TicketDetailScreen.php` (328 righe)
   dichiarano entrambe `class TicketDetailScreen` nello stesso namespace: con l'autoload PSR-4 `CreateTicketScreen` non esiste come
   classe. Nessun codice istanzia `CreateTicketScreen`; i riferimenti sono solo in documenti (`docs/00-INDEX.md` e
   `docs/wiki/nativephp-build-and-run.md` dicono gia' che `CreateTicketScreen` rende la vista `ticket-detail`).

## Causa

- A-02: due Action copiate da una versione in cui gli id utente erano interi non hanno seguito il passaggio a UUID, mentre le altre
  sette si'.
- A-13 e A-14: i campi del form sono stati scritti sull'insieme intero (enum, utenti) invece che sull'insieme legale (matrice
  delle transizioni, ruoli PA).
- A-23: ipotesi del report non verificata (vedi fatto 5).
- A-09/A-37/A-38/A-36: codice duplicato rimasto dopo lo spostamento del dettaglio pubblico nel tema (DCP-148) e dopo la
  rimozione delle schermate Mobile (story 1.12).

## Decisioni

- D-1 (A-02): corregge le Action con lo stesso schema delle altre sette (`is_int || is_string`); nessuna migrazione, nessuna
  modifica di schema.
- D-2 (A-13): le opzioni dell'azione derivano da `TicketStatusEnum::allowedTargets()` dello stato corrente del record. Nessuna
  matrice duplicata. Stato corrente vuoto o non riconosciuto: stessa regola dell'Action (nessun vincolo), quindi tutte le
  opzioni. L'azione e' nascosta se non ci sono target (stati terminali: `closed`, `draft`).
- D-3 (A-14): nuova Action `ListAssignableTicketOperatorsAction` che restituisce `id => nome` degli utenti con un ruolo di
  `BasePolicy::PA_ROLES` (costante resa `public`, unica fonte). `super-admin` non e' incluso perche' `isPaOperator` non lo include.
  La difesa e' nel form (regola `in` della Select); non aggiungo un controllo dentro `AssignTicketAction`, che romperebbe il test
  esistente `TicketAssignmentNotificationTest` (assegna a un utente senza ruolo) e cambierebbe la semantica per altri chiamanti.
- D-4 (A-23): NON modifico `HasSpatiePermission`. Vedi sezione «Da decidere».
- D-5 (A-09/A-37): ritiro con rinomina `.old` nello stesso percorso, senza `rm` ne' cartelle archive.
  `[slug].blade.php` diventa `[slug].blade.php.old2` perche' `.old` esiste gia'; `[id].blade.php` diventa `[id].blade.php.old`.
- D-6 (A-38): `TicketList.php` e' codice morto con dati inventati: ritirato con `.old`.
- D-7 (A-36): rinomino la classe in `CreateTicketScreen`. Il corpo resta quello (una copia del dettaglio): la schermata di creazione
  vera non esiste e non la invento.

## Da decidere

- DD-1 (A-23): il metodo `hasPermissionToOrCreate` scrive una sola volta per nome di permesso, non a ogni controllo. Se si vuole
  comunque togliere la scrittura dal percorso di lettura, la proposta e': un comando/seeder idempotente `permissions:sync` che
  registra i nomi dichiarati dalle policy, e nel trait `try { return hasPermissionTo(...) } catch (PermissionDoesNotExist) { return false; }`.
  Costo: i permessi nuovi non compaiono piu' da soli nell'interfaccia dei ruoli finche' non si lancia il comando. Tocca tutti i
  moduli e il repo `Modules/User`: decisione di prodotto/architettura, non un fix locale.
- DD-2 (A-14): includere `super-admin` tra i responsabili assegnabili? Oggi no (coerente con `isPaOperator`).
- DD-3 (A-14): far rispettare «solo operatori PA» anche dentro `AssignTicketAction` (difesa a livello dominio per chiamanti
  diversi dal form). Richiede di aggiornare `TicketAssignmentNotificationTest`.
- DD-4 (A-36): quale schermata di creazione per l'app nativa (modulo nativo oppure rimando al wizard web).

## Criteri di accettazione

1. `AssignTicketAction` registra in `ticket_activities.user_id` l'UUID dell'utente autenticato (prima: `null`).
2. `Actions/ChangeStatus` idem (lock orfano rimosso con autorizzazione del coordinatore, vedi Fatti 2; nessun `skip` residuo).
3. L'azione Filament `changeStatus` rifiuta un target non consentito dalla matrice e accetta uno consentito; l'elenco delle opzioni
   e' uguale a `allowedTargets()` dello stato corrente; per uno stato terminale l'azione non e' offerta.
4. Il campo «assegna a» di `ViewTicket` elenca solo utenti con ruolo in `BasePolicy::PA_ROLES` (un cittadino non compare e non
   e' accettato come valore).
5. Un test misura che `hasPermissionToOrCreate` scrive una sola volta per permesso (documenta la causa corretta di A-23).
6. In `Modules/Fixcity/resources/views/pages/tickets/` nessun `[id]*.blade.php` ne' `[slug]*.blade.php` attivo; il router di
   Folio risolve `/{lang}/tickets/{x}` sempre con il tema.
7. Nessun file PHP attivo di `Modules/Fixcity/app` contiene i conteggi inventati delle categorie.
8. Ogni file in `Modules/Mobile/app/Native/Screens` dichiara la classe con il proprio nome.
9. PHPStan (config unica, senza `--level`) a zero sui file PHP creati o modificati; Pint solo sui miei file.

## Test

- `Modules/Fixcity/tests/Feature/Actions/TicketActivityActorUuidTest.php` (A-02)
- `Modules/Fixcity/tests/Feature/Filament/ChangeStatusActionTargetsTest.php` (A-13)
- `Modules/Fixcity/tests/Feature/Filament/AssignTicketOperatorsOnlyTest.php` (A-14)
- `Modules/Fixcity/tests/Feature/Policies/PermissionLookupWritesOnceTest.php` (A-23, caratterizzazione)
- `Modules/Fixcity/tests/Feature/Pages/LegacyTicketPagesRetiredTest.php` (A-09, A-37, A-38)
- `Modules/Mobile/tests/Unit/NativeScreenClassNamesTest.php` (A-36)

Esecuzione solo con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso relativo a laravel/>`.

## Owned file scope

Elenco esatto dei file che tocco (creati C, modificati M, rinominati R). Se ne tocco altri, li aggiungo qui.

- C `laravel/Modules/Fixcity/docs/stories/dcp-281-rinforzo-back-office-e-pagine-legacy.story.md`
- C i sei file di test elencati nella sezione Test (la cartella `tests/Feature/Policies/` e' nuova)
- M `laravel/Modules/Fixcity/app/Actions/AssignTicketAction.php`
- M `laravel/Modules/Fixcity/app/Actions/ChangeStatus.php` (una riga, la 49; lock orfano rimosso, vedi Fatti 2)
- C `laravel/Modules/Fixcity/app/Actions/ListAssignableTicketOperatorsAction.php`
- M `laravel/Modules/Fixcity/app/Policies/BasePolicy.php` (solo visibilita' di `PA_ROLES`)
- M `laravel/Modules/Fixcity/app/Filament/Actions/ChangeStatus.php`
- M `laravel/Modules/Fixcity/app/Filament/Resources/TicketResource/Pages/ViewTicket.php`
- R `laravel/Modules/Fixcity/resources/views/pages/tickets/[slug].blade.php` -> `[slug].blade.php.old2`
- R `laravel/Modules/Fixcity/resources/views/pages/tickets/[id].blade.php` -> `[id].blade.php.old`
- R `laravel/Modules/Fixcity/app/View/Components/Blocks/TicketList.php` -> `TicketList.php.old`
- M `laravel/Modules/Mobile/app/Native/Screens/CreateTicketScreen.php` (solo il nome della classe)
- M `laravel/Modules/Fixcity/docs/bmad/decision-log.md` (una nota)

Non toccati per regola: `Modules/Comment`, blocchi Sixteen blog/events, sitemap (`Modules/Seo/app/Actions/Sitemap`,
`Contracts/SitemapPathsContract.php`, `FixcityServiceProvider`, `tests/Feature/Pages/SeoLinksTest.php`).

## Esito

Stato della story: parziale (restano da decidere A-23 e la schermata di creazione Mobile, DD-1 e DD-4), quindi resta `in-progress`. Tutti i comandi sono stati lanciati il 2026-10-26.
I test girano solo con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso>`; l'ultima riga di ogni esecuzione e'
`scratch eliminati: 0 rimasti`.

| Riga | Esito | Rosso prima | Verde dopo |
|---|---|---|---|
| A-02 `AssignTicketAction` | FATTO e VERIFICATO | `Tests: 2 failed (2 assertions)` (attore `null` invece dell'UUID) | `Tests: 1 skipped, 4 passed (10 assertions)` (con `TicketAssignmentNotificationTest`) |
| A-02 `Actions/ChangeStatus` | FATTO e VERIFICATO (lock orfano, rimosso con autorizzazione) | `Tests: 1 failed, 1 passed (2 assertions)` (`Failed asserting that null is identical to '61d259bb-f338-417c-82a7-3e596196f22f'`) | `Tests: 10 passed (25 assertions)` (con `TicketStatusNotificationTest` e `TicketAssignmentNotificationTest`) |
| A-13 | FATTO e VERIFICATO | `Tests: 6 failed, 2 passed (24 assertions)` | `Tests: 8 passed (29 assertions)` |
| A-14 | FATTO e VERIFICATO | `Tests: 4 failed, 1 passed (14 assertions)` | `Tests: 5 passed (19 assertions)` |
| A-23 | NON NECESSARIO come descritto; resto DA DECIDERE (DD-1) | non applicabile (la tesi del report e' falsa) | `Tests: 2 passed (8 assertions)` (caratterizzazione) |
| A-09 / A-37 (`[slug]`, `[id]`) | FATTO e VERIFICATO (ritirate) | `Tests: 3 failed, 5 passed (18 assertions)` | `Tests: 8 passed (20 assertions)` |
| A-38 `TicketList` | FATTO e VERIFICATO (ritirata) | nello stesso test A-09 | nello stesso test A-09 |
| A-36 Mobile | FATTO e VERIFICATO | `Tests: 2 failed, 3 passed (5 assertions)` | `Tests: 7 passed (12 assertions)` (intera suite Unit di Mobile) |

### Dettagli

- **A-02, schema.** Colonna `uuid`: nessuna migrazione, nessun file di migrazione creato o modificato. Corretto
  `AssignTicketAction.php:41` (`is_int($actorId) || is_string($actorId) ? $actorId : null`, come le altre sette Action).
- **A-02, `Actions/ChangeStatus.php`.** Il lock era orfano (prove in Fatti 2) e il coordinatore ne ha autorizzato la rimozione.
  Tolto `->skip(...)` dal secondo test di `TicketActivityActorUuidTest`, rosso misurato: `Tests: 1 failed, 1 passed (2 assertions)`,
  `Failed asserting that null is identical to '61d259bb-f338-417c-82a7-3e596196f22f'`. Corretta la sola riga 49, da
  `$userId = is_int($actorId) ? $actorId : null;` a `$userId = is_int($actorId) || is_string($actorId) ? $actorId : null;`
  (stessa forma delle altre Action: nessun helper condiviso esiste, quindi nessun helper nuovo). Verde:
  `TicketActivityActorUuidTest` + `TicketStatusNotificationTest` + `TicketAssignmentNotificationTest` = `Tests: 10 passed (25 assertions)`.
  PHPStan su `Actions/ChangeStatus.php` e sul test: `[OK] No errors`; Pint: `{"tool":"pint","result":"passed"}`. Lock del file
  rilasciato con `unlock.sh`. Resta solo il PHPDoc `@property int|null $user_id` di `Models/TicketActivity.php` (stale, non
  bloccante, file non toccato perche' ha ancora il suo lock vecchio e non serve a questo fix).
- **A-13.** `Filament/Actions/ChangeStatus.php`: opzioni e visibilita' derivano da `TicketStatusEnum::allowedTargets()`. Un
  target non consentito ora da' un errore di validazione sul campo `status` (prima: `DomainException` non gestita) e lo stato
  non cambia. Non ho aggiunto un `try/catch` con notifica per la corsa (stato cambiato da un altro operatore mentre il modale e'
  aperto): resta un residuo noto.
- **A-14.** `ListAssignableTicketOperatorsAction` + `BasePolicy::PA_ROLES` resa `public`. Un cittadino inviato a mano
  (`callAction('assign', ['responsible_id' => <cittadino>])`) viene rifiutato dalla regola `in` della Select. Residuo: chi chiama
  `AssignTicketAction` direttamente (non dal form) puo' ancora assegnare a chiunque (DD-3). Se il responsabile attuale non e' piu'
  un operatore, il campo si apre vuoto.
- **A-23.** Misura del test: la prima chiamata emette un solo `INSERT INTO permissions`, la seconda e la terza zero scritture;
  l'esito resta `false` senza permesso e `true` dopo `givePermissionTo`.
- **A-09 / A-37 / A-38.** Rinominati (nessun `rm`): `pages/tickets/[slug].blade.php` in `[slug].blade.php.old2` (esisteva gia'
  `.old`, diverso), `pages/tickets/[id].blade.php` in `[id].blade.php.old`, `View/Components/Blocks/TicketList.php` in
  `TicketList.php.old`. Prova di non regressione: la prova forte e' quella del router (test `LegacyTicketPagesRetiredTest`): le
  pagine ritirate non erano mai servite perche' il tema le oscura, quindi prima e dopo sono identici per costruzione. Controllo HTTP
  dopo la modifica, rifatto il 2026-10-26 su entrambe le istanze con gli id giusti (nota: una versione precedente di questa story
  riportava `/it/tickets/29` 404 «prima e dopo»; era misurato su `:8091`, dove il 29 non esiste, e non provava nulla; la misura
  «prima» non e' piu' ripetibile perche' i file sono gia' ritirati):

  | URL | `:8081` (database di sviluppo) | `:8091` (fixture) |
  |---|---|---|
  | `/it/tickets`, `/en/tickets`, `/it/tickets/track` | 200 | 200 |
  | `/it/tickets/create` | 302 | 302 |
  | `/it/tickets/hhh` | 404 | 404 |
  | `/it/tickets/29`, `/30`, `/31`, `/32` e `/en/tickets/29` | 200 | 404 (id assente) |
  | `/it/tickets/1`, `/2`, `/3` e `/en/tickets/1` | 404 (id assente) | 200 |
  | `/it/tickets/13` | 404 (id assente) | 404 (stato `pending`, non pubblico) |
  | `/it/tickets/64` | 404 (stato `pending`, non pubblico) | 404 (id assente) |

  Stati letti dal database: `:8081` 29 `in_progress`, 30 `in_progress`, 31 `on_hold`, 32 `resolved`, 64 `pending`; `:8091` 1 `open`,
  2 `in_progress`, 3 `resolved`, 13 `pending`. `TicketStatusEnum::canViewByAll()` esclude `pending`, `draft`: il 404 anonimo di una
  segnalazione non pubblica e' il comportamento voluto di `BuildPublicTicketDetailAction`. Il titolo della risposta 200 del 29 e'
  quello reale della segnalazione («Buca in via Solferino»). Effetto collaterale noto e innocuo: la classmap ottimizzata di Composer cita ancora
  `Modules\Fixcity\View\Components\Blocks\TicketList` finche' non si rilancia `composer dump-autoload`; nessun codice usa la
  classe. `docs/components.md` cita ancora il vecchio componente (documento non toccato).
- **A-36.** Solo `class CreateTicketScreen` in `CreateTicketScreen.php`. Il classmap di Composer non conosceva
  `CreateTicketScreen` (il file era trattato come duplicato di `TicketDetailScreen`), ora lo risolve PSR-4.

### Controlli finali

- PHPStan (config unica, senza `--level`), per ogni file creato o modificato: `[OK] No errors`. Poi su
  `analyse Modules/Fixcity Modules/Mobile`: 5 errori, tutti in 3 file di test di un'altra sessione
  (`TicketLifecycleNotificationTest`, `TicketReceivedNotificationTest`, `TicketLifecycleNotificationLangTest`); zero nei miei file.
- Pint solo sui miei file: `{"tool":"pint","result":"passed"}`.
- Pest su tutte le cartelle toccate (Feature/Actions, Filament, Policies, Pages; Unit/Views, Policies, Filament; Mobile/Unit):
  `Tests: 9 failed, 1 risky, 13 skipped, 529 passed (3307 assertions)`, 688 s. I 9 rossi non dipendono da questa story:
  `PublicPagesStructureTest` x4 (`/{lang}/auth/password/reset: 0 elementi <h1>`), `UnknownFourSegmentPageTest` (404 su
  `/it/auth/password/reset/abc123`), `TicketListPageTest` x2 (link delle card alla lista), `FrontofficeTicketLinksContractTest` x2
  (`ticket-card.blade.php` non contiene `route('container0.`; `BuildPersonalAreaNotificationsDataAction.php` e
  `TicketStatusChangedNotification.php`, modificati nel working tree da un'altra sessione, non contengono `route('container0.view'`).
  Nessuno di quei file e' tra i miei. Questa esecuzione combinata e' precedente alla correzione di `Actions/ChangeStatus.php`
  (i 13 `skipped` comprendevano il test A-02 di `ChangeStatus`); dopo la correzione il test e' stato rieseguito da solo con i due
  test di notifica: `Tests: 10 passed (25 assertions)`. La suite combinata non e' stata rilanciata per intero dopo.

### Osservato e NON toccato

1. **RITIRATA, 2026-10-26: «il dettaglio pubblico `/{lang}/tickets/{id}` risponde 404 per segnalazioni pubbliche» era falso.** Una
   versione precedente di questa story lo affermava (misure su 29, 30, 31, 32) e ne dava una causa (`abort_if` nel Volt del tema
   prima del ramo `ticket-detail`, regressione dopo il 2026-10-13/15). La misura era sbagliata: avevo interrogato `:8091` (fixture,
   id da 1 a 13, dove 29..32 non esistono) invece di `:8081` (database di sviluppo, dove 29..32 esistono). Rimisurato su entrambe
   le istanze con gli id giusti (tabella in Esito, sezione A-09): 200 per le segnalazioni pubbliche (`:8081` 29, 30, 31, 32; `:8091`
   1, 2, 3), 404 per id assenti e per `pending`. La causa che avevo scritto non e' dimostrata e la ritiro: il 200 del 29 contiene il
   titolo reale della segnalazione, quindi nel processo web `mount()` non aborta, mentre la mia risoluzione da riga di comando
   (`ResolvePageAction('tickets','29')` con `item` e pagina CMS nulli) non rappresenta il percorso web; perche' le due strade
   divergano non l'ho indagato e non serve a questa story. Nessun difetto da prendere in carico per questo punto.
   Resta valido, da altri audit e non misurato da me (richiede un utente autenticato): il PROPRIETARIO di una segnalazione non
   pubblica non vede il dettaglio della propria segnalazione (404; coerente con il codice: `BuildPublicTicketDetailAction` parte da
   `BuildPublicTicketsQueryAction`, chiamata senza il visitatore, che e' usato solo per il flag «segui»). Non e' un fix di questa story e non l'ho toccato; il 404 anonimo per `pending` misurato qui
   (`:8081` id 64, `:8091` id 13) e' coerente con quel comportamento.
2. Per le altre pagine orfane del modulo, misura rifatta su entrambe le istanze: `pages/prova.blade.php` (`/it/prova`) 404 su
   `:8081` e su `:8091`; `pages/home.blade.php` (`/it/home`) 404 su entrambe; `pages/lista-categorie.blade.php`
   (`/it/lista-categorie`) 200 su `:8091` ma 500 su `:8081` (`View [pages.services.categories-content] not found`, errore gia'
   presente nel log dal 2026-10-23, prima di questa story; il file della pagina e' del 2026-10-22 e non e' tra i miei). Il 500 di
   `:8081` e' un difetto del tema/cache viste, non mio e non indagato oltre. Candidate per una story successiva insieme al widget
   legacy `CreateTicketWidget` (A-37).
3. Il lock di `Actions/ChangeStatus.php` era orfano: rimosso su autorizzazione del coordinatore (vedi Fatti 2). Resta il lock
   vecchio su `Models/TicketActivity.php`, non toccato; il README di `bashscripts/lock` prevede di rimuovere i lock morti, da
   fare al prossimo giro con il coordinatore.
