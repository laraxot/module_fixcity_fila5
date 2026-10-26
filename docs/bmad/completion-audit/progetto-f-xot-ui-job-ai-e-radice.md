---
title: "Analisi di progetto F: Xot, UI, Job, AI e radice del repository"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "analisi di progetto f: xot, ui, job, ai e radice del repository audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Gap F: Xot, UI, Job, AI e radice applicativa laravel/

Data analisi: 2026-10-26. Analista in sola lettura. Nessun file del repository e' stato modificato, nessun comando di scrittura e' stato eseguito (solo lettura, `php -l`, git di sola lettura). Non ho eseguito Pest, PHPStan, composer, artisan, npm, migrate.

Convenzioni:
- VERIFICATO: letto o misurato da me in questa sessione (file:riga o comando indicato).
- RIPORTATO: affermato da un documento o da una nota di memoria, non ricontrollato da me.
- non determinato: serve eseguire qualcosa che mi era vietato, o conoscere l'ambiente di produzione.
- I valori dei segreti non sono riportati: solo prefissi redatti o lunghezze.

Percorsi: `R` = `/mnt/nas07/var/www/_bases/base_fixcity_fila5`, `L` = `R/laravel`.

---

## 1. Perimetro e ownership per modulo

| Ambito | Ruolo dichiarato | Ruolo reale (VERIFICATO) | Owner decisioni |
| --- | --- | --- | --- |
| Xot | Base condivisa, contratti, infrastruttura di test | Base con 592 file PHP in `app/`, 203 `*Test.php`, 1.924 `.md` su disco. Non e' una vera base: importa User (8 file), Lang (7), Tenant (7), UI (3), Notify (2), Media (1). Contiene anche codice morto (SecurityMiddleware, route seo) e duplicati di AI (Ollama). | titolare + sviluppo |
| UI | Componenti condivisi, "project-agnostic" | Usato da Fixcity, Blog, Cms (Blocks, IconPicker, SliderData, GetAllBlocksAction). Importa `Modules\User\Models\Profile` (`Actions/GetUserDataAction.php:10`). Contiene modelli e migrazioni di dominio non usati da nessuno (Category, Collection, FieldOption). | sviluppo |
| Job | Code, worker, scheduler (DEC-27) | Contiene solo il codice applicativo e la UI Filament. Nessuna definizione di worker, supervisor, cron nel repo fuori dai docs. Registrazione dello scheduler da DB e hook di coda interamente commentati (`JobServiceProvider.php:35-42, 51-70`). | titolare (hosting), fornitore, sviluppo |
| AI | Integrazioni LLM, FreeLLMAPI | Modulo attivo (`AI: true` nelle due mappe moduli). Consumatori reali: pagine Filament `Completion` e `FineTuning`, `AiActionProposalResource`, provider in `config/app.php:32`. Le altre Action (Classify, Suggest, Sentiment, Predict) non hanno chiamanti oltre la pagina Completion. FreeLLMAPI NON e' nel modulo: e' strumentazione locale degli agenti (`tools/freellmapi` ignorato da git e assente). | titolare (privacy), sviluppo |
| Radice `laravel/` | Composer, routes, bootstrap, config, tests, CI, env, tool QA | Vedi sezione 2. Contiene configurazioni di altri clienti, segreti tracciati, file di debug e un deploy pericoloso. | titolare + fornitore + sviluppo |

Grafo reale delle dipendenze (VERIFICATO, calcolato da me su `use`/`Modules\X\` nei file `app/` PHP, commenti esclusi):

- Nessun `composer.json` di modulo dichiara require tra moduli. I `module.json` hanno `requires` per lo piu' vuoti (Xot, UI, Job, AI, Cms, Tenant, Lang: tutti `[]`). Il grafo dichiarato e' quindi vuoto, quello reale no.
- Una sola componente fortemente connessa di 14 moduli: Activity, Blog, Cms, Comment, Job, Lang, Media, Notify, Rating, Seo, Tenant, UI, User, Xot. Singoli (senza ciclo): AI, Event, Geo, Fixcity, Gdpr, Mobile.
- Direzioni sbagliate (la base importa i moduli di dominio):
  - Xot -> User: `Contracts/UserContract.php:21-24`, `Contracts/ProfileContract.php:11`, `Datas/XotData.php:13-14,69-75`, `Actions/Panel/ApplyTenancyToPanelAction.php:8-9`, `Filament/Builders/FilterBuilder.php:15`, `Providers/Filament/XotBaseMainPanelProvider.php:22`.
  - Xot -> Tenant: `Datas/XotData.php:12`, `Datas/MetatagData.php:13-14`, `Filament/Pages/MetatagPage.php:15`, `Models/InformationSchemaTable.php:10`.
  - Xot -> UI: `Filament/Traits/HasXotTable.php:39-41`.
  - Xot -> Notify: `States/Transitions/XotBaseTransition.php:11-12`, `Actions/Mail/SendMailByRecordAction.php:8-9`.
  - Xot -> Media: `Filament/Resources/XotBaseResource.php:23`.
  - UI -> User: `Actions/GetUserDataAction.php:10`. Job -> User: `Models/Policies/*Policy.php`, `Models/TaskComment.php:10`. Media -> Job (1 file).
  - Il `composer.json` di Xot ha un path repository `../Tenant`.
- Riferimenti a moduli inesistenti: `User/app/Listeners/AssignFreeCreditsListener.php:9` importa `Modules\Predict\Models\Profile`; `User/app/Datas/DeviceData.php:80` cita `Modules\Egea`; Tenant ha riferimenti commentati a Dental/Patient.

---

## 2. Stato reale con evidenze

### 2.1 Radice applicativa

- Versioni (VERIFICATO da `composer.lock`): Laravel 13.38.0, Filament 5.10.3, Livewire 4.4.8, Pest 5.x. `laravel/boost` e `laravel/mcp` sono pacchetti DEV. `codewithkyrian/transformers` e' DEV. `openai-php/laravel` 0.20.0 e' in produzione. Nessun Horizon, Telescope, Sentry, Scout, pacchetto CSP.
- `bootstrap/providers.php:26-27` registra `Laravel\Boost\BoostServiceProvider` e `Laravel\Mcp\Server\McpServiceProvider` (VERIFICATO). Con `composer install --no-dev` la classe non esiste e il boot va in errore fatale.
- `bootstrap/app.php`: `trustProxies(at: '127.0.0.1')` (riga 32), nessun `trustHosts`, nessuna eccezione CSRF, middleware `SecurityHeaders` aggiunto, gestori 404 e 429 a tema (VERIFICATO).
- `routes/web.php` (file modificato nel working tree da altra sessione):
  - riga 27: `POST /livewire/form-create-ticket` -> `CreateTicketWizardWidget::submit`. Nessun chiamante nel codice (grep). Nessun throttle a livello route. L'autenticazione e' controllata dentro il widget.
  - `logout.post` con auth; `tickets.follow` con `auth` e `throttle:30,1`; `/sitemap.xml` via `Modules\Seo\Actions\Sitemap\BuildSitemapAction`.
  - `routes/ai.php` e' solo commenti; `routes/console.php` ha solo `inspire`.
- Middleware (`app/Http/Middleware/`): solo `SecurityHeaders` e' registrato. `ForceHttps` e `ThrottleAuthentication` non hanno riferimenti (morti). `ThrottleAuthentication` e' anche sbagliato: legge la chiave inesistente `auth.rate_limit`, incrementa prima di controllare e scrive "minuti" per secondi. `BlockLegacyPublicPages` e' usato da `FolioServiceProvider`.
- `config/security.php`: solo le chiavi `headers.*` sono lette da codice (grep). Le sezioni `session`, `rate_limit`, `password` (12 caratteri, 90 giorni), `file_upload` (`scan_for_malware=true`), `cors`, `api`, `two_factor`, `encryption`, `logging`, `database` sono decorative. CSP: modalita' di default `report-only` (riga 126), `script-src` con `'unsafe-inline'` e `'unsafe-eval'` (riga 136), nessun `report-uri` / `report-to` (grep vuoto). `frame-ancestors 'self'` e' sempre applicata. HSTS solo se la richiesta e' sicura.
- Sessione: `config/session.php` default redis in produzione, `secure` legato a `SESSION_SECURE_COOKIE`, `encrypt` true (VERIFICATO). `.env` di sviluppo: `APP_ENV=local`, `APP_DEBUG=true`, `DEBUGBAR_ENABLED=true`, `QUEUE_CONNECTION=sync`, `SESSION_DRIVER=file`.
- Avatar di terzi: `XotBaseMainPanelProvider.php:50-51` e `XotBasePanelProvider.php:73-74` applicano `InitialsAvatarProvider` (locale): ui-avatars.com non e' piu' usato dai pannelli. Resta hard-coded solo in un blade di `Themes/TwentyOne` (fuori ambito).
- `config/logging.php:22`: `'default' => config('logging.default','stack')` ignora `LOG_CHANNEL`. Il canale `stack` usa `single` (nessuna rotazione). `storage/logs/laravel.log` pesa 4,6 MB. Il controllo ENV-09 del go-live gate (`go-live-gate.py:92`) convalida `LOG_CHANNEL`, che il codice non legge: il controllo e' vuoto.
- `config/queue.php`: default `env('QUEUE_CONNECTION','database')`; il driver `database` usa `DB_QUEUE_CONNECTION` che non e' definita in nessun file env; i job falliti usano `database-uuids` su `DB_CONNECTION`.
- `phpstan.neon` (non modificato): `level: max`, `paths: ./Modules/` soltanto; `./Themes/` e `./app` commentati. Root `app`, `routes`, `bootstrap`, `tests` radice e Themes NON sono analizzati. Baseline: due file da 0 byte.
- Lint: `php -l` su Xot, UI, Job, AI, app, bootstrap, config, routes, tests, database: 0 errori di sintassi, 1 warning (`Xot/tests/Unit/XotPageFormSchemaResolutionTest.php:9`, `use ReflectionMethod` inutilizzato). Nessun marcatore di conflitto nei quattro moduli e in radice.
- Conteggi (VERIFICATO): 9.232 file PHP sotto Modules (ambito PHPStan), 278 sotto Themes, 16 in `laravel/app`. La cifra "9196 file" dei documenti non coincide e dice "20 moduli + temi" mentre le directory modulo sono 21 e i temi non sono analizzati.
- Log recenti (VERIFICATO): 2026-10-22 classe mancante `Themes\Sixteen\Support\*`; 2026-10-26 `Modules/Seo/app/Actions/BuildSitemapAction.php` mancante (classmap vecchia dopo lo spostamento da Fixcity a Seo).

### 2.2 Segreti e file tracciati (valori NON riportati)

- `config/test/fixcity/filesystems.php:85`: password FTP letterale per il disco `photos` (inizia con "tM", redatta). Altre password e utenti FTP letterali in 7 file `filesystems.php` tracciati: `config/com/geekpiu`, `config/it/quaeris` (4 varianti), `config/it/quaerisofficina/manager2`.
- File env tracciati in `laravel/`: `.env.fixcity`, `.env.latest`, `.env.local.bak`, `.env.locale.bak`, `.env.localz.bak`, `.env.testing`, `.mcp.json`. `laravel/.gitignore` ignora solo `.env`, `.env.backup`, `.env.production`.
- Nei `.bak`: chiave Google Maps in formato reale (39 caratteri, prefisso `AIzaSy`) in `GOOGLE_MAPS_API_KEY` e `FILAMENT_GOOGLE_MAPS_SERVER_API_KEY`; `APP_KEY` in formato `base64:` (diversa dalla live); `IMAP_PASSWORD` e `MATOMO_AUTH_TOKEN` uguali a quelli del `.env` live; in `.env.fixcity`, `.env.latest`, `.env.testing` le password DB sono uguali a quelle live.
- `config/telegram.php:48`: riga commentata con un token (valore non verificato).
- Altri file di debug tracciati in `laravel/`: `error.html` (78 KB), `server.pid`, `.phpstan-r5-live.json`, 7 `phpstan_*.json`, 5 `PHPSTAN_*.md`, script `fix_*.php`/`analyze_*.php`/`rename_md_files.sh`, 8 varianti neon, `_ide_helper_models.php` (circa 40 file). Nella radice del repo altri 90 file tracciati, molti "FINAL/SESSION/SUCCESS/REPORT".
- Configurazioni di altri clienti tracciate in questo repository della PA: `config/it/quaeris` (71 file), `quaerisofficina` (74), `qualitymonitor` (34), `net/futurely` (25), `com/geekpiu` (22), `localhost` (30), piu' chiavi legacy dentro `local/fixcity` (`quaeris.php`, `limesurvey.php`, `chart.php`, `exchanges.php`, `totem.php`).
- `config/local/fixcity/modules_statuses.json`: contiene la chiave segnaposto `"<nome progetto>"` e moduli inesistenti (LU, Chart, Limesurvey, BarberShop, RealEstate, Booking, Food, Forum, Predict, Shop, Ticket, Setting); omette Mobile ed Event. Il `modules_statuses.json` di radice elenca Setting, Chart, Ticket (nessuna directory) e omette Event. Quale dei due sia letto in produzione: non determinato (`config/modules.php:275` legge quello di radice).

### 2.3 Deploy e CI

- `deploy.sh` (radice, 59 righe, VERIFICATO): riga 31 `composer.phar selfupdate`; riga 35 `composer.phar update -W` (ignora il lock, `minimum-stability: dev`); riga 37 `vendor:publish --all`; riga 38 `rm -rf database/migrations/*`; riga 41 `migrate --force` senza backup; riga 47 `optimize` seguito da riga 49 `route:clear` (annulla la route cache); riga 54 `sudo chown -R www-data:www-data .`. Nessuna maintenance mode, nessun `queue:restart`, nessun health check, nessun rollback. Righe 10 e 14 contengono il segnaposto `<nome progetto>`.
- CI: i workflow sono in `R/.github/workflows` (44 file; `laravel/.github` non esiste). `ci.yml:45` PHPStan con `--level=10 ... || true`; `ci.yml:81` Pint `|| true`; `ci.yml:145` `php artisan test --no-interaction || true`; `ci.yml:91,142` database `laravelpizza_test` (avanzo di un altro progetto). `ci-cd.yml` ha `continue-on-error: true` sulle righe 53-133 e `actions/upload-artifact@v3` (righe 80, 136), usa Laravel 12/Filament 4/Node 18/PHP 8.2 secondo la lettura iniziale; i job `deploy-staging` e `deploy-production` sono `echo`. `phpstan.yml` esegue `Modules/$mod` con variabile non definita, dalla radice dove manca `vendor/bin/phpstan`. 8 workflow contengono `|| true`. Nessun workflow richiama `go-live-gate`, `check-conflict-markers` o `production-checklist` (grep: 0 file).
- Go-live gate `bashscripts/tools/release/go-live-gate.py` (329 righe, letto per intero): controlli ENV-01..09, SEC-01/02, DATA-01, SITE-00..04, LEG-01..04, LG-01..08, NET-01, REPO-01/02. `SEC-02` (riga 94) accetta `report-only`. `ENV-07` verifica solo che `APP_KEY` non sia vuota. NON controlla: CSP in enforce, worker di coda, scheduler, backup/restore (DEC-27), provider dev in `bootstrap/providers.php`, segreti tracciati, `composer audit`, `trustHosts`/`trustProxies`, `QUEUE_CONNECTION`. `LEG-01` richiede Pest e un DB (`pest-scratch-db.sh`).
- `production-checklist.sh` (66 righe, letto per intero): controlla intestazioni, cookie e redirect HTTP->HTTPS su un URL HTTPS vivo; non controlla coda, scheduler, backup.

### 2.4 Test

- `phpunit.xml`: solo le suite `tests/Unit` e `tests/Feature`, esclusa `tests/Unit/Modules/Chart`. I test dei moduli non sono in nessuna suite radice.
- `tests/Pest.php`: `uses(TestCase::class)->in('Feature')` piu' segnaposto `something()` e `toBeOne`. `tests/TestCase.php` usa `DatabaseTransactions` su `['mariadb','user','fixcity']`.
- 77 file di test in radice. `tests/Feature/Auth/*` e `ProfileTest.php` sono test dello starter kit Breeze/Volt (`/login`, `/register`, `/dashboard`, `pages.auth.login`, `App\Models\User::factory`) che non corrispondono alle rotte Folio localizzate. `tests/Unit/Modules/{Geo 25, Notify 12, Tenant 10, Chart 4, ...}` duplicano test dei moduli (molti nomi uguali) e Chart e' un modulo inesistente. `tests/Unit/ExampleTest.php` ha `expect(true)->toBeTrue()`. Nessuna cartella `tests/Architecture`.
- Guardie architetturali in Xot: `Unit/Architecture/{NoServicesTest,NoJobsTest,DatasFolderConventionTest}.php`, `Unit/Docs/StoryNumbersAreUniqueTest.php`, `Unit/CaseCollisionsDoNotGrowTest.php`, `Unit/Support/TestDatabaseGuardTest.php`. Sono controlli di filesystem, ma fanno `uses(Modules\Xot\Tests\TestCase)` che ha `DatabaseTransactions` su sqlite/user/tenant/xot: quindi richiedono un DB (lo conferma la story `dcp-222` T1, ancora aperta, e `StoryNumbersAreUniqueTest.php:11`). `StoryNumbersAreUniqueTest` usa la regex `/^(\d+\.\d+)\./` (riga 59) e quindi copre solo le story `N.M.` e NON le `dcp-NNN`; la baseline e' 74 collisioni (2026-09-08).
- `pest-plugin-arch` e' installato ma `arch()` non e' mai usato (grep).
- Misura di oggi (find): nessuna cartella `Services`, `Jobs` diretta, `Data`/`DTOs`/`Support` e nessun `*Service.php` nelle radici di produzione dei quattro moduli. Quindi le regole "no Services", "no Jobs", "Datas" oggi sono rispettate nei quattro moduli.
- Controller presenti (violano la regola "mai creare Controller"): Media 2, Notify 1, User 10; in Xot `Http/Controllers/XotBaseController.php` (56 righe, base legittima) piu' i rifiuti `Http/Http/Controllers/{XotBaseController,xotbasecontroller}.php` (3 righe, gemelli per maiuscole, tracciati); in radice `app/Http/Controllers/Controller.php` e `MyController.php` (0 byte).
- Harness di copertura: 3.192 righe (`Module{Deep,Execute,Remaining,Business}Coverage.php`, `FilamentSchemaCoverage.php`, `Unit/ModuleCoverageBoostTest.php`, `XotAggressiveSweepAbstractStubsTest.php`) con sweep per reflection, 92 `catch (\Throwable)`, 31 `assertGreaterThanOrEqual(0`, 14 skip e 11 todo/incomplete nei test Xot, circa 20 file non raccolti perche' senza `*Test.php`, 33 file `.old`. Le story `xot-test-harness-truth` e `dcp-222` sono `ready-for-dev`.
- Numeri della suite (310 falliti / 4 skipped / 37 passati sulla radice, 27 rossi in Fixcity): RIPORTATO dalla memoria, non rieseguiti. Stato attuale: non determinato.

### 2.5 Documentazione

- Xot: 1.924 `.md` su disco, di cui 1.190 vuoti (0 byte, 62%). Nel commit HEAD, 941 su 1.633 `.md` tracciati sono il blob vuoto `e69de29...` (57%). Causa (VERIFICATO con `git diff-tree`): il commit `24e86ba1f` ("." di Marco Xot, 2026-10-18 15:54:43 UTC) ha toccato 1.922 file md (+42.563 / -123.299), svuotando circa 1.227 file non vuoti e aggiungendone 406 vuoti. Il contenuto e' recuperabile da `24e86ba1f~1` (esempio: `docs/XOTBASE_QUICK_REFERENCE.md` oggi 0 byte, 172 byte in `1ad60d0f7`).
- UI: 276 su 467 `.md` vuoti (269 su 456 in HEAD); commit `3e2d9188` ("." del 2026-10-18 15:54:31), per esempio `docs/algolia-docsearch.md` (prima 5,8-16,7 KB). Job: 0 vuoti. AI: 0 vuoti. Le story sopravvivono.
- Link relativi rotti (mio script, blocchi di codice esclusi, sensibile alle maiuscole): Xot 1.262 su 1.901 (66%), UI 281 su 420 (66%), Job 459 su 1.048 (43%), AI 144 su 593 (24%).
- Punti di ingresso: `Xot/docs/index.md` e' marcato `superseded_by: INDEX.md` ma `docs/INDEX.md` non esiste. `Xot/README.md` (88 righe) e' standard, con titolo emoji e "nessuna logica di dominio" (smentita dagli import). `UI/README.md` (229 righe) ha blocchi duplicati ("La visione" x3, "Scheda tecnica" x2), badge non verificabili, elenca aree (`Data, Services, Livewire, Forms`) che non esistono, e linka `./docs/{purpose,architecture,readme-en}.md` mancanti. `Job/README.md` linka `./docs/architecture.md` mancante, elenca `Services` che non esiste; `Job/ARCHITECTURE.md` (38 righe) linka 5 file `docs/wiki/...` mancanti. `AI/docs/README.md` e `Job/docs/README.md` linkano `./00-INDEX.md` mancante. Il README di AI e' generico con emoji e non cita FreeLLMAPI.

### 2.6 Xot, UI, Job, AI: stato del codice

- Xot (VERIFICATO): file oltre 400 righe: `MetatagData.php` 756, `HasXotTable.php` 744, `XotBaseMigration.php` 630, `XotData.php` 513, `SecurityMiddleware.php` 474, `ColumnBuilder.php` 452, `XotBaseResource.php` 447. `app/Http/Middleware/SecurityMiddleware.php` (474 righe) e' usato solo da `XotSecurityHandlersCoverageTest`, mai registrato; accanto ci sono file `.wip`. `routes/seo.php` e `routes/web_seo.php` puntano a controller inesistenti (`RssFeedController`, `SiteMapController`, `SitemapXmlController`); `routes/api.php` e `web.php` sono vuoti. Resti in `app/`: `.oo`, `.zero_used`, `.kalnoy`, `.tnt`, `.to_blog`, `.to_rating`, `.to_activity`, `.stub`, `PanelMixin.php.md`. `app/Filament/Actions/{export,Export}` sono due file da 0 byte. `Actions/AI/Ollama/{Chat,Generate}OllamaAction.php` duplicano `AI/app/Actions/Ollama/*`. 40 percorsi tracciati in collisione di maiuscole (UI 2, Job 2, AI 0).
- UI: `app/Http/Kernel.php` e' morto (0 riferimenti, estende un HttpKernel che in Laravel 11+ non esiste piu'). `app/Models/{Category,Collection,FieldOption}` con 3 migrazioni non sono usati da nessun modulo (crea tabelle di dominio in produzione). `app/Traits/TableLayoutTrait.php.bak`.
- Job: file orfani tracciati `Job/Untitled` (contiene "~"), `app/Models/MonitoredScheduledTask.aaa`, `MonitoredScheduledTaskLogItem.aaa`, `app/Models/BaseModel.php.backup-20251015-092511`, `app/Console/Commands/queue.pid`, molti `.md.old`; `app/Http/Livewire/*` ancora presente mentre il README dice che e' stato ritirato. Due modelli di scheduler sovrapposti (`Task/Frequency/Parameter/Result` e `Schedule/ScheduleHistory`). `Models/BaseModel.php:34` ha `$connection = 'job'` mentre `config/queue.php` non usa quella connessione; con `QUEUE_CONNECTION=redis` (esempio produzione) le tabelle `jobs`, `job_batches`, `failed_jobs` del modulo possono non riflettere la coda reale (inferenza statica, impatto a runtime non determinato).
- AI (VERIFICATO): `MakeAIRequestAction.php:28` legge `config('ai.openai_base_url','https://api.openai.com/v1')`, riga 49 modello fisso `gpt-4`, righe 81 e 88 logga contesto e corpo della risposta in caso di errore, riga 98 `sleep()` bloccante per il backoff. Il prompt contiene titolo e descrizione della segnalazione (testo del cittadino) senza rimozione di dati personali. `CompletionAction.php:22` ha `gpt-3.5-turbo-instruct` fisso, ignorando `config/ai.php`. Chiavi di config lette ma definite da nessuna parte: `ai.openai_api_key`, `ai.openai_base_url`, `ai.timeout`, `ai.retry_attempts`, `ai.backend_api.fine_tuning_url` (`FineTuning.php:53` fa un assert di stringa e fallira'). `OPENAI_API_KEY` manca in `.env.example` e `.env.production.example`. `Modules/AI/config/ai.php` usa `config('services.openai.*')` dentro un file di config. Nessun documento di flusso dati o DPIA (8 doc citano solo parole di privacy). Il codice di predizione (`GeneratePredictionsAction` 320 righe e 3 Action duplicate di template di fallback da 137/96/82 righe) e' legacy di un altro progetto (Predict).
- AI e UUID (VERIFICATO): le migrazioni usano `unsignedBigInteger` per `created_by_user_id` (`2026_07_24_000001_create_ai_threads_table.php:28`), `user_id` (`...000004_create_ai_tool_logs_table.php:27`, `...000002_create_ai_messages_table.php:25`), `proposed_by_user_id` e `confirmed_by_user_id` (`...000003_create_ai_action_proposals_table.php:30,40`) mentre gli utenti hanno id UUID stringa (`BaseUser` con `HasUuids`). `AiActionProposalsTable.php` chiama `execute($record, (int) Auth::id())` e `ConfirmAiActionProposalAction::execute(..., int $confirmedByUserId)`. Il test esistente `tests/Feature/UserIdColumnsAcceptUuidTest.php` asserisce colonne stringa e sarebbe rosso per costruzione (non eseguito).

### 2.7 Campione di story Xot segnate "done" (14 verificate su 154)

| Story | Esito (VERIFICATO) |
| --- | --- |
| `test-database-guard` | Guardia cablata in `CreatesApplication.php:64`: ok. Il test dichiarato in `laravel/tests/Architecture/TestDatabaseGuardTest.php` non esiste; vive in `Modules/Xot/tests/Unit/Support/TestDatabaseGuardTest.php`. |
| `dcp-270-no-services-ratchet` | `NoServicesTest` esiste: ok. |
| `remove-typedhasrecursiverelationships` (done, criteri spuntati) | FALSA: `Xot/app/Models/Traits/TypedHasRecursiveRelationships.php` esiste ancora ed e' usato da `Models/BaseTreeModel.php:12` e `XotBaseTreeModel.php:12`. |
| `xot-morph-pivot-factory-abstract` | Factory astratta (`XotBaseMorphPivotFactory.php:18`): ok. |
| `services-to-actions-xot-route` | 12 file in `Actions/Route`, `RouteService`/`RouteDynAction` spariti: ok. |
| `sidebar-collapsed-groups-null-store` | `GetSidebarStateScriptAction` esiste: ok. |
| `auditcoverage-guard-hardening` | Due script e 5 file harness esistono; `audit-coverage/` ha 0 file tracciati in Xot e UI localmente (stato remoto non determinato). |
| `psr-4-morph-many-spy-fixture` | `tests/Fixtures/MorphManyRelationSpy.php` esiste; l'esecuzione Pest e' bloccata dalla guardia DB (lo dice la story). |
| `gettable-methods-cleanup-independent-verification` | Cita il modulo `Ptv`, non presente: non verificabile. |
| `phpstan-modules-zero-gate` | Dichiara PHPStan a zero: RIPORTATO, non verificato da me. |
| `5.176-export-xls-formula-injection-sanitize` | Stato `ready-for-dev`; `Exports/XlsxCellFactory.php:49` trasforma ancora `=...` in `FormulaCell`: non sanitizzato. |
| `fix-navigationicon-violations` | `draft`. |

Vocabolario degli stati incoerente (conteggio mio): done 48, ready-for-dev 27, in-progress 23, active 13, review 11, in_progress 10, ready 5, draft 4, backlog 3, blocked 2, altri; 2 senza stato. Ci sono story duplicate o con suffisso `-xot-story-md` tra `stories/` e `bmad/stories/`, e numerazioni strane (`10000-phpstan-modules-fix`).

### 2.8 Registri aperti

- DEC-30 (162 `.merge_file_*` tracciati in Xot): oggi 0 tracciati e 0 su disco. Voce superata.
- DEC-50 (merge in corso): nei quattro moduli in ambito non c'e' `MERGE_HEAD`; gli altri 7 moduli non verificati da me. `git status` pulito in Xot, UI, Job, AI.
- DEC-28 dice che la password FTP e' stata tolta da "4 config": ne ho trovate almeno 8 con credenziali letterali (vedi 2.2).

---

## 3. Lacune

Legenda severita': BLOCCA = blocca la messa online; QUALITA' = degrada correttezza, sicurezza o manutenibilita' ma non impedisce il rilascio da solo; RIFINITURA = pulizia.
Dimensione: S = meno di mezza giornata, M = 1-3 giorni, L = piu' di 3 giorni o piu' persone.
Ogni lacuna e' VERIFICATO salvo diversa indicazione.

### BLOCCA (8)

| ID | Descrizione | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Story proposta |
| --- | --- | --- | --- | --- | --- | --- | --- |
| F-01 | Provider di sviluppo (Boost, Mcp) registrati a mano in `bootstrap/providers.php` ma pacchetti `require-dev`: `composer install --no-dev` produce un errore fatale al boot. | `L/bootstrap/providers.php:26-27`; `composer.lock` (laravel/boost, laravel/mcp in packages-dev) | sviluppo | nessuna; poi aggiungere controllo al gate (F-07) | S | root | `root-provider-dev-solo-in-locale` |
| F-02 | Credenziali tracciate: password FTP letterale in `config/test/fixcity/filesystems.php:85` e in altri 7 `filesystems.php`; file `.env.*`/`.bak` tracciati con chiave Google Maps in formato reale, `IMAP_PASSWORD`, `MATOMO_AUTH_TOKEN`, password DB uguali a quelle live; `.gitignore` copre solo `.env`, `.env.backup`, `.env.production`. Contraddice DEC-28 (parla di 4 config). La storia git non puo' essere riscritta (regola forward-only): resta solo ruotare. | vedi 2.2; `L/.gitignore` | titolare (ruotare le credenziali, decidere se riscrivere la storia); sviluppo (untrack, `.gitignore`, `.env.example`) | decisione sul fornitore FTP/Matomo/IMAP/Google; F-24 | M | root | `root-segreti-tracciati-rotazione-e-gitignore` |
| F-03 | `deploy.sh` non adatto alla produzione: `composer update -W` ignora il lock con `minimum-stability: dev`, `selfupdate` senza controlli, `vendor:publish --all`, `rm -rf database/migrations/*`, `migrate --force` senza backup, `route:clear` dopo `optimize`, `chown -R` con sudo, nessuna maintenance mode, nessun `queue:restart`, nessun health check, nessun rollback, segnaposto `<nome progetto>`. | `R/deploy.sh:10,14,31,35,37,38,41,47,49,54` | fornitore/titolare (ambiente di destinazione); sviluppo (script) | F-05 (backup), F-26 | M | root | `root-deploy-sicuro-e-riproducibile` |
| F-04 | La CI non blocca nulla e non esegue il gate: PHPStan, Pint e test hanno `|| true`; `ci-cd.yml` ha `continue-on-error` su 10 passi e i job di deploy sono `echo`; nessuno dei 44 workflow richiama `go-live-gate`, `check-conflict-markers`, `production-checklist`; il DB e' `laravelpizza_test`. Un rosso non ferma mai un rilascio. | `R/.github/workflows/ci.yml:45,81,91,142,145`; `ci-cd.yml:53-133`; grep gate = 0 file | sviluppo; titolare decide quali controlli sono bloccanti | F-07, F-16, F-18 | M | root | `ci-bloccante-con-go-live-gate` |
| F-05 | Nessuna definizione di worker di coda, scheduler e backup nel repository (DEC-27 senza esito): niente supervisor/cron/compose per l'app; `laravel/docker/` e' solo Sail standard; `routes/console.php` pianifica solo `inspire`; la registrazione dello scheduler del modulo Job e' commentata. `QUEUE_CONNECTION` e' `database` o `sync` per default, `redis` solo nell'esempio produzione. Se le notifiche o gli export usano la coda, in produzione non verrebbero eseguiti. | `L/Modules/Job/app/Providers/JobServiceProvider.php:35-42,51-70`; `L/routes/console.php`; `L/config/queue.php`; grep worker/cron = 0 fuori dai docs | titolare e fornitore (hosting, driver coda, backup); sviluppo (file di servizio, runbook) | DEC-27; F-03; F-17 | L | Job | `job-worker-scheduler-backup-runbook` |
| F-06 | CSP solo in `report-only` di default, con `'unsafe-inline'` e `'unsafe-eval'` negli script e senza `report-uri`/`report-to`: le violazioni non vengono ne' bloccate ne' raccolte. Il gate (SEC-02) considera `report-only` un esito valido, quindi il passaggio a enforce non e' mai richiesto. | `L/config/security.php:125-136`; `go-live-gate.py:94`; `.env.production.example` (`SECURITY_CSP_MODE=report-only`) | titolare (data di enforce); sviluppo (nonce al posto di inline, endpoint di report) | F-07; compatibilita' Livewire/Alpine | L | root | `csp-enforce-con-nonce-e-report` |
| F-07 | Il go-live gate puo' dare esito verde con i blocchi sopra presenti: non controlla CSP enforce, worker, scheduler, backup, provider dev, segreti tracciati, `composer audit`, `trustHosts`/`trustProxies`, `QUEUE_CONNECTION`; ENV-07 controlla solo che `APP_KEY` non sia vuota; ENV-09 verifica `LOG_CHANNEL` che `config/logging.php:22` ignora (controllo vuoto). | `go-live-gate.py:92,94`; `L/config/logging.php:22` | sviluppo; titolare approva l'elenco dei controlli | F-01..F-06 | M | root | `go-live-gate-controlli-mancanti` |
| F-08 | AI e privacy: l'azione invia titolo e descrizione delle segnalazioni (testo del cittadino) a `api.openai.com` senza rimozione dei dati personali, con modello fisso `gpt-4`, logga contesto e corpo in caso di errore, usa `sleep()` bloccante; chiavi di config non definite; modulo attivo in entrambe le mappe moduli; nessun documento di flusso dati/DPIA. Raggiungibile oggi solo dai pannelli admin (pagina Completion e Action collegate), quindi diventa BLOCCA se AI resta attivo in produzione; se viene disattivato scende a RIFINITURA. | `AI/app/Actions/MakeAIRequestAction.php:28,49,81,88,98`; `CompletionAction.php:22`; `FineTuning.php:53`; `L/modules_statuses.json` | titolare (attivare o no, base giuridica, DPIA, responsabile del trattamento OpenAI); sviluppo | decisione D-AI-1 (sezione 6) | L | AI | `ai-privacy-flusso-dati-e-disattivazione` |

### QUALITA' (25)

| ID | Descrizione | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Story proposta |
| --- | --- | --- | --- | --- | --- | --- | --- |
| F-09 | `trustProxies` limitato a `127.0.0.1` e nessun `trustHosts`: dietro un proxy o load balancer non loopback la richiesta risulta non sicura (HSTS non inviato, URL `http`), e nessun controllo sull'host. Topologia di produzione: non determinato. Diventa BLOCCA se il proxy non e' in loopback. | `L/bootstrap/app.php:32` | fornitore (topologia); sviluppo | F-03 | S | root | `root-trust-proxies-e-trust-hosts` |
| F-10 | Colonne utente di AI di tipo `bigint` contro utenti UUID: la scrittura delle proposte/log/messaggi con un utente reale fallirebbe (inferenza statica, esecuzione non provata); `(int) Auth::id()` converte una stringa UUID in 0 o in un numero sbagliato; il test esistente e' rosso per costruzione. | migrazioni `2026_07_24_00000{1,2,3,4}_*`; `AiActionProposalsTable.php`; `ConfirmAiActionProposalAction.php`; `tests/Feature/UserIdColumnsAcceptUuidTest.php` | sviluppo | F-08 (se AI viene disattivato la priorita' scende) | M | AI | `ai-user-id-uuid-colonne-e-tipi` |
| F-11 | Esportazioni Excel: un testo che inizia con `=`, `+`, `-`, `@` viene scritto come formula (`FormulaCell`). Se un operatore esporta segnalazioni con testo del cittadino, e' un vettore di injection. La story 5.176 e' `ready-for-dev` da 2026-09-23. Dove venga usato l'export per i ticket: non determinato (nessun riferimento diretto in `Fixcity/app`). | `Xot/app/Exports/XlsxCellFactory.php:49`; story 5.176 | sviluppo; titolare conferma la policy (prefisso apice) | nessuna | S | Xot | `xot-export-xls-sanifica-formule` |
| F-12 | Rotta `POST /livewire/form-create-ticket` senza chiamanti e senza throttle a livello route; la protezione e' tutta dentro il widget. Il rate limit del login e' solo nel widget (G-07, RIPORTATO dalla memoria); per login Filament e API: non determinato. `config/security.php` dichiara `rate_limit` mai letto. | `L/routes/web.php:27`; `config/security.php` (sezione `rate_limit`) | sviluppo | F-21 | S | root / Fixcity | `root-throttle-rotte-post-e-rimozione-orfane` |
| F-13 | Ciclo di dipendenze a 14 moduli con Xot come "base" che importa 6 moduli di dominio; nessun grafo dichiarato (`composer.json` e `module.json` senza require tra moduli). Impedisce l'indipendenza per modulo proclamata da `NWIDART_MODULAR_ARCHITECTURE` e rende ogni modulo non estraibile. | sezione 1 (elenco file:riga); `module.json` requires vuoti | titolare + sviluppo (piano a ondate: contratti in Xot, implementazioni nei moduli) | nessuna; precede F-31 | L | Xot | `xot-dipendenze-direzione-e-grafo-dichiarato` |
| F-14 | Documentazione svuotata: 1.190 `.md` vuoti in Xot (62%) e 276 in UI (59%) per i commit `24e86ba1f` e `3e2d9188` del 2026-10-18; contenuto recuperabile dal commit precedente. Nessuno ha deciso se ripristinare o eliminare. | `git diff-tree` sui due commit; conteggio file a 0 byte | titolare decide (ripristinare / eliminare / migrare); sviluppo esegue in avanti (nuovo commit, non revert) | F-15 | L | Xot, UI | `xot-ui-docs-svuotati-recupero-o-eliminazione` |
| F-15 | Link relativi rotti nei docs e punti di ingresso mancanti: Xot 66%, UI 66%, Job 43%, AI 24%; `index.md` superseded verso un `INDEX.md` inesistente; README di UI e Job linkano docs mancanti; coppie trattino/sottolineatura (49 in Xot, 80 in UI) e cartelle con nome che inizia per trattino. | misura mia sul filesystem; sezione 2.5 | sviluppo (riparazione sicura dei link + migration map); titolare per le eliminazioni | F-14 | L | Xot, UI, Job, AI | `docs-moduli-link-ingresso-e-indice` |
| F-16 | Le guardie architetturali (`NoServicesTest`, `NoJobsTest`, `DatasFolderConventionTest`, `StoryNumbersAreUniqueTest`, `CaseCollisionsDoNotGrowTest`) richiedono un DB perche' estendono il TestCase di Xot con `DatabaseTransactions`: senza DB non girano, quindi non possono essere un gate veloce. `StoryNumbersAreUniqueTest` non copre le story `dcp-NNN`. | `Xot/tests/Unit/Architecture/*`; `StoryNumbersAreUniqueTest.php:11,59`; story `dcp-222` T1 | sviluppo | F-04 | M | Xot | `xot-guardie-architettura-senza-db` |
| F-17 | Modulo Job incompiuto: registrazione dello scheduler e hook di coda commentati; due modelli di scheduler sovrapposti; `$connection = 'job'` non allineata a `config/queue.php`; `DB_QUEUE_CONNECTION` mai definita; `Http/Livewire` presente nonostante il README; README/ARCHITECTURE con link e aree inesistenti. | `Job/app/Providers/JobServiceProvider.php:35-70`; `Job/app/Models/BaseModel.php:34`; `L/config/queue.php` | titolare decide quale scheduler tenere; sviluppo | F-05 | L | Job | `job-scheduler-unico-e-connessione-coda` |
| F-18 | Suite radice non affidabile: le suite phpunit coprono solo `tests/Unit` e `tests/Feature`; i test Breeze/Volt non corrispondono alle rotte reali; `tests/Unit/Modules/*` duplica test dei moduli (con Chart inesistente); tautologia in `ExampleTest.php`; nessuna cartella Architecture. Le cifre della memoria (310 falliti) sono RIPORTATE e non rieseguite. | `L/phpunit.xml`; `L/tests/**`; `tests/Pest.php` | sviluppo; titolare approva la cancellazione dei test (regola: non eliminare test senza approvazione) | F-04 | M | root | `root-suite-test-verita-e-pulizia` |
| F-19 | Harness "coverage floor" da 3.192 righe con sweep per reflection, 92 `catch (\Throwable)`, 31 `assertGreaterThanOrEqual(0` e simili: gonfia i numeri senza provare comportamento (contraddice la filosofia "un obiettivo di coverage non e' una misura"). Le story `xot-test-harness-truth` e `dcp-222` sono aperte. | sezione 2.4 | sviluppo | F-16 | M | Xot | `xot-test-harness-verita-e-rimozione-padding` |
| F-20 | Regola "mai Controller": esistono Controller in Media (2), Notify (1), User (10), il file vuoto `MyController.php` in radice e i rifiuti `Xot/app/Http/Http/Controllers/*` (gemelli per maiuscole); nessun test d'architettura li blocca (`pest-plugin-arch` installato e mai usato). | sezione 2.4 | sviluppo; titolare decide le eccezioni legittime | F-16 | M | Xot, User, Media, Notify | `guardia-no-controller-e-pulizia` |
| F-21 | Codice di sicurezza morto o decorativo: `SecurityMiddleware` (474 righe) mai registrato, `ForceHttps` e `ThrottleAuthentication` (con bug) senza riferimenti, `config/security.php` con 9 sezioni mai lette (password 12 caratteri/90 giorni, `scan_for_malware`, `two_factor`, `rate_limit`...) che danno una falsa impressione di protezione. | sezione 2.1 e 2.6 | sviluppo; titolare decide se implementare o eliminare ogni sezione | F-12 | M | root, Xot | `sicurezza-codice-morto-config-decorativa` |
| F-22 | Ambito PHPStan: `phpstan.neon` analizza solo `./Modules/`; `app`, `routes`, `bootstrap`, `tests` radice e Themes restano fuori; i documenti di fine lavoro dicono "9196 file, 20 moduli + temi, zero errori", cifra che non coincide con il conteggio misurato (9.232 file PHP, 21 moduli, temi esclusi). Lo stato effettivo di PHPStan: non determinato. | `L/phpstan.neon`; `docs/second-brain/project/{missing-work-index,bmd-brainstorming-remaining-work}.md`; `AI/docs/bmad-remaining-work.md` | l'utente (solo l'utente modifica `phpstan.neon`) | nessuna | S | root | `phpstan-ambito-app-temi-e-cifre` |
| F-23 | Story contrassegnate "done" ma false o con riferimenti sbagliati: `remove-typedhasrecursiverelationships` (il trait esiste ancora ed e' usato), `test-database-guard` (percorso del test sbagliato); vocabolario degli stati con oltre 10 valori e due stati uguali scritti in modo diverso (`in-progress`, `in_progress`). | sezione 2.7 | sviluppo (correggere stato o completare); titolare per il vocabolario | nessuna | S | Xot | `xot-story-stato-e-vocabolario` |
| F-24 | Configurazioni di altri clienti (Quaeris 71+74 file, QualityMonitor 34, Futurely 25, Geekpiu 22, localhost 30) e chiavi legacy dentro `local/fixcity` sono tracciate nel repository della PA: dati di terzi, rischio di fuga di segreti altrui, e rumore per chi cerca la config reale. | sezione 2.2 | titolare (autorizza l'eliminazione); sviluppo | F-02 | M | root | `root-config-altri-clienti-fuori-dal-repo` |
| F-25 | Mappe dei moduli incoerenti: `modules_statuses.json` di radice elenca Setting, Chart, Ticket (inesistenti) e omette Event; `config/local/fixcity/modules_statuses.json` ha la chiave `"<nome progetto>"` e 12 moduli inesistenti, omette Mobile ed Event; quale file sia letto in produzione non e' determinato. `Event` ha `active: 0` e 0 test. | sezione 2.2 | titolare decide quali moduli sono attivi in produzione | nessuna | S | root | `root-modules-statuses-una-sola-verita` |
| F-26 | `composer.json`: `minimum-stability: dev` con `policy.advisories.block=false`; lo script `go` esegue `composer update -W`, `vendor:publish --all`, `rm -rf database/migrations/*` e `rm -rf app/View/Components`; `coolsam/panel-modules` e' `dev-dev` da path; nessun `composer audit` in CI. | `L/composer.json`; `composer.lock` | titolare (accettare i rischi di stabilita' dev); sviluppo | F-03, F-04 | M | root | `root-composer-stabilita-audit-e-script-go` |
| F-27 | Workflow CI obsoleti o rotti: Laravel 12/Filament 4/Node 18/PHP 8.2 in `ci-cd.yml`, php-cs-fixer e psalm non usati dal progetto, `upload-artifact@v3` (dismesso da GitHub), `phpstan.yml` con `$mod` indefinita e senza `vendor` in radice, `quality.yml` solo per Activity; 44 file in totale. | `R/.github/workflows/*` | sviluppo | F-04 | M | root | `ci-workflow-obsoleti-pulizia` |
| F-28 | Tracciabilita' del repository: circa 35.000 file sotto `laravel/Modules` sono tracciati sia dal repo radice sia dai repo annidati dei moduli (rischio di divergenza); `gitmodules.ini` esiste ma non `.gitmodules`; quasi tutti i messaggi di commit sono "." (Xot 592/1.038, Job 3.648/4.489, UI 1.505/2.313, AI 1.187/1.532), quindi la storia non spiega nulla. | misura mia con `git log` e `git ls-files` | titolare decide il modello (monorepo o sottomoduli) | nessuna | L | root | `repo-modello-di-versionamento-e-messaggi` |
| F-29 | Codice AI orfano e duplicato: `ClassifyTicketAction`, `SuggestSolutionsAction`, `SentimentAction` senza chiamanti oltre la pagina Completion; 3 Action di template di fallback duplicate; `GeneratePredictionsAction` (320 righe) e le Action Predict/Prediction sono eredita' del progetto Predict; Ds4 e Ollama non collegate; Xot contiene `Actions/AI/Ollama/*` che duplica quelle di AI. | sezione 2.6 | titolare decide cosa tenere; sviluppo | F-08, F-10 | M | AI, Xot | `ai-pulizia-orfane-predict-e-ollama` |
| F-30 | Pannelli Filament orfani: `app/Filament/AdminPanelProvider.php` che estende `PanelProvider` esiste in AI, Cms, Gdpr, Blog, Comment accanto al provider reale in `Providers/Filament/AdminPanelProvider` (XotBasePanelProvider). In AI l'orfano ha id `AI_admin`, path `AI/admin`, `->login()` e non e' in `module.json`. Se sia raggiungibile: non determinato. | sezione 2.6 e riepilogo | sviluppo | F-29 | S | AI, Cms, Gdpr, Blog, Comment | `moduli-pannelli-filament-orfani` |
| F-31 | Logging: `config/logging.php:22` ignora `LOG_CHANNEL` (rende vuoto ENV-09); il canale di default e' `single`, senza rotazione; nessun canale di produzione definito (niente Sentry o simili). | `L/config/logging.php:22` | titolare (dove vanno i log di produzione); sviluppo | F-07 | S | root | `root-logging-canale-e-rotazione` |
| F-32 | `.env.production.example` con chiavi legacy o inesistenti (`BROADCAST_DRIVER`, `CACHE_DRIVER`, `MIX_*`, `SCOUT_DRIVER=meilisearch` e `TELESCOPE_ENABLED` senza pacchetti; `RATE_LIMIT_ENABLED`, `LOGIN_RATE_LIMIT`, `FORCE_HTTPS`, `SECURE_COOKIES`, `SECURE_HEADERS` non lette da nessuno) e senza `OPENAI_API_KEY`, `APP_TIMEZONE`, indicazioni su worker/scheduler. | `L/.env.production.example` | sviluppo | F-05, F-08 | S | root | `root-env-produzione-esempio-allineato` |
| F-33 | Percorsi in collisione di maiuscole tracciati (Xot 40, UI 2, Job 2) e file `.old` (33 nei test Xot): su filesystem insensibili alle maiuscole il checkout si corrompe. | sezione 2.6; `CaseCollisionsDoNotGrowTest` (non cala, solo "non cresce") | sviluppo | F-16 | M | Xot, UI, Job | `moduli-collisioni-maiuscole-azzeramento` |

### RIFINITURA (7)

| ID | Descrizione | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Story proposta |
| --- | --- | --- | --- | --- | --- | --- | --- |
| F-34 | README da riscrivere: UI (duplicati, aree inesistenti, badge non verificabili), Job (`Services`, link rotti), AI (generico, senza FreeLLMAPI ne' flusso dati), Xot (titolo emoji, "nessuna logica di dominio" smentita). | sezione 2.5 | sviluppo | F-14, F-15 | M | UI, Job, AI, Xot | `moduli-readme-riscrittura` |
| F-35 | File di debug e residui tracciati in `laravel/` e nella radice del repo (circa 40 + 90): `error.html`, `server.pid`, `phpstan_*.json`, `PHPSTAN_*.md`, `fix_*.php`, varianti neon, `_ide_helper_models.php`. | sezione 2.2 | sviluppo (git rm in avanti); titolare per i documenti storici | F-02 | S | root | `root-pulizia-file-di-debug` |
| F-36 | Residui nei moduli: `Job/Untitled`, `*.aaa`, `queue.pid`, `BaseModel.php.backup-*`, `.oo/.zero_used/.kalnoy/.tnt/.to_*` e `.stub` in Xot, `PanelMixin.php.md`, due file vuoti `Actions/{export,Export}`, `.wip` nei middleware. | sezione 2.6 | sviluppo | F-33 | S | Job, Xot | `moduli-residui-e-file-orfani` |
| F-37 | UI: `Http/Kernel.php` morto, modelli `Category/Collection/FieldOption` con 3 migrazioni non usate da nessun modulo (creano tabelle in produzione), `TableLayoutTrait.php.bak`. | sezione 2.6 | sviluppo; titolare conferma la rimozione delle migrazioni gia' eseguite | nessuna | S | UI | `ui-rimozione-codice-non-usato` |
| F-38 | Igiene della radice applicativa: `app/Models/ModulesFixcityAppModelsTicket.php` classe vuota, `app/Models/User.php` scheletro usato dal modulo Mobile mentre `auth.php` usa `Modules\User\Models\User`, `env('NATIVEPHP_RUNNING')` fuori da config in `AppServiceProvider` (con `config:cache` vale null), `routes/seo.php` e `routes/web_seo.php` di Xot verso controller inesistenti. | sezione 2.1 e 2.6 | sviluppo | F-20 | S | root, Xot | `root-igiene-app-modelli-e-env` |
| F-39 | Registri e riepiloghi superati: DEC-30 (0 file contro 162) e DEC-50 (nessun merge nei 4 moduli) da chiudere o aggiornare; documenti di chiusura che dicono "PHPStan zero su 9196 file" da marcare come non verificati. | sezione 2.8 | titolare | F-22 | S | Fixcity (registro) | `registro-decisioni-allineamento-dec-30-50` |
| F-40 | Proliferazione di classi base di test: Xot `TestCase`, `XotBaseTestCase`, `XotBasePest`, shim `app/Testing/XotBaseTestCase` in radice, Geo `LightTestCase`, 20 `TestCase` di modulo; `User/tests/Support/CreatesApplication.php` senza guardia DB. | sezione 2.4 | sviluppo | F-16 | S | Xot | `xot-test-classi-base-unificate` |

Totale: 40 lacune (BLOCCA 8, QUALITA' 25, RIFINITURA 7). Il limite di 40 e' raggiunto: raggruppate voci vicine (per esempio ambito PHPStan e cifre 9196 in F-22; residui nei moduli in F-36).

---

## 4. Contraddizioni tra documenti e codice

| Documento | Afferma | Codice o misura | Etichetta |
| --- | --- | --- | --- |
| `docs/second-brain/project/{project-confidence-achievement,missing-work-index,bmd-brainstorming-remaining-work}.md`, `Modules/AI/docs/bmad-remaining-work.md`, `Modules/Fixcity/docs/bmad-remaining-work.md` | PHPStan a zero su 9196 file, 20 moduli + temi | 9.232 file PHP in Modules, 21 directory modulo, Themes esclusi da `phpstan.neon` | VERIFICATO il conteggio; zero errori NON verificato (non ho eseguito PHPStan) |
| `laravel/AGENTS.md` (regola 5) | "ZERO errors across all 16 Modules (June 2026)" | I moduli sono 21; data e conteggio diversi dagli altri documenti | VERIFICATO il conteggio moduli |
| Story Xot `remove-typedhasrecursiverelationships` (done) | Trait rimosso | `Xot/app/Models/Traits/TypedHasRecursiveRelationships.php` esiste ed e' usato da `BaseTreeModel.php:12` | VERIFICATO |
| Story `test-database-guard` | Test in `laravel/tests/Architecture/TestDatabaseGuardTest.php` | Il file non esiste; il test e' in `Modules/Xot/tests/Unit/Support/` | VERIFICATO |
| DEC-28 | Password FTP tolta da "4 config" | Almeno 8 `filesystems.php` tracciati con credenziali letterali, piu' chiave Google nei `.bak` | VERIFICATO |
| DEC-30 | 162 `.merge_file_*` tracciati in Xot | 0 tracciati, 0 su disco | VERIFICATO (voce superata) |
| DEC-50 | Merge in corso | Nessun `MERGE_HEAD` nei 4 moduli in ambito; altri 7 non controllati | VERIFICATO per i 4 moduli |
| `Xot/README.md` | "nessuna logica di dominio" | Xot importa User, Lang, Tenant, UI, Notify, Media (sezione 1) | VERIFICATO |
| `UI/README.md` | Aree `Data, Services, Livewire, Forms`; "project-agnostic"; badge "PHPStan Level 10" | Non esistono le prime tre cartelle; UI importa `User\Models\Profile`; il badge non e' verificabile | VERIFICATO |
| `Job/README.md` | Area `Services`; `Livewire` ritirato | `Services` assente; `app/Http/Livewire/*` presente | VERIFICATO |
| `Xot/docs/index.md` | "superseded by INDEX.md" | `docs/INDEX.md` non esiste | VERIFICATO |
| `.env.production.example` e `config/security.php` | Chiavi di rate limit, HTTPS forzato, 2FA, scansione upload | Nessun codice le legge (grep) | VERIFICATO |
| Go-live gate ENV-09 | Verifica `LOG_CHANNEL` | `config/logging.php:22` non lo legge | VERIFICATO |
| Story 5.176 / `XlsxCellFactory` docblock | "decisione condivisa in follow-up" per le formule | Follow-up mai fatto (`ready-for-dev`) | VERIFICATO |
| Memoria: 310 falliti / 4 skipped / 37 passati, 27 rossi in Fixcity | Stato della suite | Non rieseguito | RIPORTATO |
| `CLAUDE.md` di progetto | "PHPStan si corregge nel codice" | `ci.yml:45` passa `--level=10` e termina con `|| true` | VERIFICATO |

---

## 5. Documentazione mancante o da riscrivere

Percorsi proposti (nome senza date ne' suffissi numerici, come da regola; i `.md` vanno nel modulo owner, non in `bmad-output/`):

| Percorso proposto | Contenuto minimo che serve a chi arriva | Collegata a |
| --- | --- | --- |
| `laravel/Modules/Xot/docs/getting-started.md` | Cosa e' Xot (e cosa non e'), gerarchia TestCase, come si esegue una guardia senza DB, le 5 classi base da estendere, dove sta cosa | F-13, F-16, F-34 |
| `laravel/Modules/Xot/docs/architecture/module-dependency-graph.md` | Grafo reale dei moduli (generato), direzioni consentite, elenco delle violazioni e piano di rientro | F-13 |
| `laravel/Modules/Xot/docs/INDEX.md` | Indice unico che sostituisce `index.md` superseded; elenco dei 10 documenti che contano | F-15 |
| `laravel/Modules/Xot/docs/restore-emptied-docs.md` | Esito della decisione F-14 (ripristinate o eliminate), elenco e mappa di migrazione | F-14 |
| `laravel/Modules/Xot/docs/testing-strategy.md` | Cosa e' una guardia, cosa e' un test di comportamento, perche' il padding di copertura e' vietato, come isolare il DB | F-16, F-19, F-40 |
| `laravel/Modules/Job/docs/ops/queue-worker-scheduler.md` | Driver di coda in produzione, comando del worker, scheduler, riavvii dopo il deploy, monitoraggio, cosa fa il modulo Job e quale scheduler vale | F-05, F-17 |
| `laravel/Modules/Job/docs/ops/backup-restore.md` | Cosa si salva, ogni quanto, dove, prova di ripristino (DEC-27) | F-05 |
| `laravel/Modules/AI/docs/privacy-data-flow.md` | Quali dati lasciano il sistema, verso chi, base giuridica, conservazione, cosa si logga, interruttore di disattivazione; base per la DPIA | F-08, F-10 |
| `laravel/Modules/AI/docs/README.md` (riscrittura) | Scopo, stato (attivo o dormiente), configurazione richiesta, relazione con gli strumenti locali (FreeLLMAPI) | F-34 |
| `laravel/Modules/UI/docs/README.md` (riscrittura) | Elenco reale dei componenti riusabili e dei consumatori, regole di dipendenza, nessun codice di dominio | F-34, F-37 |
| `laravel/docs/ops/deploy-runbook.md` | Passi di rilascio ripetibili, variabili d'ambiente richieste, migrazioni con backup, rollback, controlli post-deploy (gate + checklist) | F-03, F-07 |
| `laravel/docs/ops/security-baseline.md` | Intestazioni, CSP (data di enforce e come leggere i report), sessione, throttle per rotta, cosa NON e' implementato (sezioni decorative eliminate) | F-06, F-12, F-21 |

---

## 6. Domande aperte per l'utente

1. Documenti svuotati (F-14): ripristinare dal commit `24e86ba1f~1` i circa 1.227 file di Xot e i 269 di UI, oppure eliminare quelli senza valore? Il ripristino va fatto in avanti (nuovo commit), non con un revert.
2. Segreti (F-02): ruotare password FTP, chiave Google Maps, IMAP, token Matomo, e le password DB che coincidono con quelle live? Si accetta di NON riscrivere la storia git (vieta il forward-only), sapendo che i valori restano recuperabili?
3. AI (F-08, D-AI-1): il modulo AI deve restare attivo in produzione? Se si', chi e' il titolare del trattamento, esiste una DPIA e un contratto con il fornitore LLM, e quali campi (titolo, descrizione, posizione) possono uscire? Se no, disattivarlo in `modules_statuses.json`?
4. CSP (F-06): quando passare da `report-only` a `enforce` e si accetta di eliminare `'unsafe-inline'`/`'unsafe-eval'` (richiede nonce e modifiche a Livewire/Alpine) o si tiene una CSP piu' debole ma applicata?
5. Coda e scheduler (F-05, DEC-27): qual e' il driver di coda in produzione (redis, database)? Chi gestisce il worker e il cron (fornitore o personale)? Quale scheduler del modulo Job va tenuto (Task/Frequency o Schedule)?
6. Backup (F-05): frequenza, destinazione e prova di ripristino richieste?
7. Topologia (F-09): l'app e' dietro un proxy o load balancer? Con quale IP, per impostare `trustProxies` e `trustHosts`?
8. Altri clienti (F-24): si possono togliere dal repository della PA le configurazioni di Quaeris, QualityMonitor, Futurely, Geekpiu, localhost e le chiavi legacy?
9. Dipendenze tra moduli (F-13): si approva un piano a ondate che sposta i contratti in Xot e le implementazioni nei moduli, accettando refactor su 14 moduli? In alternativa, si dichiara ufficialmente che Xot dipende da User/Tenant/Lang e si aggiorna la documentazione.
10. PHPStan (F-22): vuoi includere `app`, `routes`, `bootstrap` e `Themes` nell'ambito? La modifica di `phpstan.neon` spetta solo a te.
11. CI (F-04): quali controlli devono essere bloccanti subito (PHPStan, Pint, test, gate)? I test radice attuali non sono affidabili, quindi serve prima la bonifica (F-18).
12. Controller (F-20): le 13 classi in Media, Notify, User sono eccezioni accettate (per esempio OAuth o download) o vanno migrate a Folio/Actions?
13. Moduli attivi (F-25): quale `modules_statuses.json` fa fede in produzione e quali moduli devono risultare attivi? Event va attivato, rimosso o lasciato fuori?
14. Export (F-11): confermi la policy "apice davanti a `=`, `+`, `-`, `@`" per tutti gli export Excel?
15. Versionamento (F-28): si resta con monorepo piu' repo annidati con doppio tracciamento, o si sceglie un solo modello?

---

## Note di metodo

- File letti: circa 120 letti per intero o in parte con lo strumento di lettura, piu' decine di `grep`/`find`/`git` di sola lettura e un controllo di sintassi `php -l` su tutto l'ambito. Un conteggio esatto non e' disponibile; non lo dichiaro come preciso.
- Due correzioni rispetto alle mie note intermedie: i workflow sono 44 (non 33) e sono in `R/.github/workflows` (non in `laravel/.github`); la cifra di 8 file con `|| true` e' una misura di oggi.
- Non determinato (richiederebbe eseguire cio' che mi era vietato): esito reale di PHPStan e Pest, effetto a runtime di `bigint` contro UUID in AI, se il monitor di Job vede la coda di produzione, quale `modules_statuses.json` e' letto in produzione, topologia proxy.
