---
title: "Analisi di progetto D: User, Gdpr, Notify, Activity"
type: analysis
status: draft
updated: 2026-10-26
tags: [bmad, completamento, audit, lacune]
qmd: "analisi di progetto d: user, gdpr, notify, activity audit completamento fixcity"
related: ["../completion-gap-register.md"]
---

> Rapporto grezzo dell'analisi del 2026-10-26, conservato per intero. I percorsi `D/`, `shots/` e `.../scratchpad/` citati come prove puntano a una cartella temporanea della sessione e possono non esistere piu': le prove vanno rifatte prima di correggere. Il registro unico e lo stato di ogni lacuna sono in [completion-gap-register.md](../completion-gap-register.md).

# Analisi lacune D: User, Gdpr, Notify, Activity

Analista in sola lettura. Nessun file del repository e' stato scritto, modificato o cancellato. Non ho eseguito Pest, PHPStan, artisan, composer, npm, migrate ne' git che scrive. Non ho interrogato il database. Ho usato solo Read e comandi shell di lettura (grep, find, wc, sed -n).

Convenzione di prova usata in tutto il documento:

- VERIFICATO: ho letto io il file o ho eseguito io il comando indicato (file:riga o comando riportati).
- RIPORTATO: l'informazione viene da un documento, da una story o dal brief e non l'ho ricontrollata nel codice.
- non determinato: serve eseguire il codice o interrogare il database, cosa vietata o non fatta.

I percorsi sono relativi a `/mnt/nas07/var/www/_bases/base_fixcity_fila5/laravel/Modules` salvo dove indicato.

Limite generale: il mapping delle connessioni di modulo (`user`, `gdpr`, `activity`, `notify`) verso i database fisici non e' risolvibile leggendo il codice (config/local/fixcity/database.php elenca solo forge, laravel, fixcity_data e varianti user_*). Quindi l'atomicita' delle operazioni che scrivono su piu' connessioni (per esempio la cancellazione account) e' non determinata.

---

## 1. Perimetro e ownership per modulo

| Modulo | File PHP in app/ | File di test | Migrazioni | Story | File .md | Cosa dovrebbe possedere | Cosa possiede davvero (VERIFICATO) |
| --- | --- | --- | --- | --- | --- | --- | --- |
| User | 683 | 196 | 84 | 155 | 2106 | identita', ruoli e permessi, login, reset, verifica email, OAuth | Login FO (LoginWidget), policy utenti, Socialite, ruoli base. La registrazione FO vera NON e' qui: e' in Gdpr |
| Gdpr | 83 | 43 | 6 file (4 create_consents, 2 create_treatments) | 36 | 424 | consensi, registro richieste interessato, cancellazione/anonimizzazione, cookie, DPO, retention | Cookie bar (funziona), widget di registrazione FO con consensi (Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php), azione di cancellazione (rotta), salvataggio consensi |
| Notify | 238 | 160 | 30 | circa 50 | 3670 | canali (email, SMS, push), template, preferenze, log di invio | Template email Spatie (SpatieEmail), tantissimi provider SMS/push/WhatsApp in gran parte non collegati a Fixcity. Fixcity NON chiama alcuna azione Notify |
| Activity | 89 | 138 | 16 | circa 48 | 802 | audit trail, log, retention | Wrapper di spatie/laravel-activitylog, risorsa Filament di lettura/scrittura log. L'audit del ciclo di vita del ticket vive in Fixcity (`ticket_activities`), non qui |

Fatti di ownership che contano per le decisioni:

1. La registrazione front office e' in Gdpr (`Themes/Sixteen/resources/views/components/pages/auth-register.blade.php:133` incorpora il widget Gdpr). Il `RegisterWidget` di User e' un fallback non usato dal FO (VERIFICATO, lettura).
2. Gdpr dipende da User (regola `unique:user.users,email`, listener su `Modules\User\Events\UserRegistered`). User importa Activity (`User/app/Filament/Widgets/Auth/RegisterWidget.php:11`) mentre `Activity/module.json:15` richiede User: ciclo (VERIFICATO).
3. Il modello utente di Fixcity (`Modules\Fixcity\Models\User`) estende `Modules\User\Models\User`; non ha policy propria registrata (vedi D-06).
4. Le preferenze di notifica del cittadino vivono nel profilo (`preferences.fixcity.email_ticket_updates`, VERIFICATO via GetFixcityEmailNotificationPreferenceAction), non in Notify.

---

## 2. Stato reale con evidenze

### 2.1 Gdpr

Funziona (VERIFICATO per lettura, non eseguito):

- Cookie bar: vista, provider e middleware `InjectCookieBanner` esistono; story dcp-153 "done" confermata come presenza di codice. Inventario in `Actions/Cookie/GetCookieInventoryAction.php`.
- Widget di registrazione FO: limite 10 tentativi per 600 secondi per IP, regole (nome, cognome, email unica, `Password::defaults()`, `privacy_accepted` e `terms_accepted` obbligatori, marketing facoltativo) in `RegisterWidget.php:61-72`. Salva i consensi con `SaveGdprConsentsAction` (ip, user agent, versione documento, locale).
- Fixcity ha `tests/Feature/Auth/RegistrationConsentTest.php` con 6 casi (password debole, prova con versione, rate limit): esiste, non l'ho eseguito.

Non funziona o e' incompleto: vedi D-01, D-02, D-09, D-10, D-12, D-13, D-21, D-22.

Non esiste (grep vuoti, VERIFICATO): registro delle richieste dell'interessato (`data_subject_requests` assente in tutti i php e json), esportazione/portabilita', qualunque consumatore di `gdpr.dpo`.

### 2.2 User

Funziona (VERIFICATO per lettura):

- Login FO con throttle 5 tentativi per 60 secondi per email+IP (`Widgets/Auth/LoginWidget.php:61,72`), solo nel widget.
- Template email di reset e verifica passano da `Notify\Emails\SpatieEmail` (`Providers/UserServiceProvider.php:52-135`).
- Rotte Folio `login`, `register`, `password.request`, `password.reset`, `password.confirm`, `verification.notice` esistono come pagine in `User/resources/views/pages/auth/*`.

Rotto o a rischio: D-03 .. D-08, D-11, D-14, D-27, D-31, D-32.

### 2.3 Notify

- Email: unico canale realmente utilizzato dal prodotto (notifiche Fixcity `database` + `mail`, `TicketStatusChangedNotification` con `ShouldQueueAfterCommit`, 3 tentativi, backoff 60 e 300 secondi).
- Dev: `MAIL_MAILER=sendmail`, `MAIL_FROM_ADDRESS=noreply@fixcity.local`, `QUEUE_CONNECTION=sync` (VERIFICATO in .env, valori non segreti).
- Non esistono chiavi Twilio, SendGrid, Firebase o simili nel .env (VERIFICATO, solo nomi).
- Push APNs e Web Push sono simulati e restituiscono successo (D-24).

### 2.4 Activity

- `Activity/app/Models/BaseModel.php:23` ha `protected $connection = 'activity'` (story 9.1 confermata).
- Il listener di login e' uno stub vuoto (D-19). Retention non schedulata (D-13).

### 2.5 Campione di story "done" verificate contro il codice (12)

| Story (id o slug, nome abbreviato) | Modulo | Esito | Prova |
| --- | --- | --- | --- |
| dcp-153 cookiebar Gdpr | Gdpr | CONFERMATA | vista, provider e middleware presenti |
| account-deletion-flash-translation | Gdpr | CONFERMATA | chiave lang presente |
| dcp-185 TreatmentForm 7 campi | Gdpr | CONFERMATA | 7 campi nel form |
| 4.27 jobs-to-queueable-actions | Notify | CONFERMATA | nessuna cartella `app/Jobs` |
| services-to-actions | Notify | CONFERMATA | nessuna cartella `app/Services` |
| 12.5 oauth callback route alignment | User | CONFERMATA | `routes/socialite.php` coerente col controller |
| debug-calls-removal | User | CONFERMATA | restano solo `dddx` commentati |
| uppercase-root-dirs | User | CONFERMATA | nessuna cartella `Database`/`Application` |
| 9.1 connection restored | Activity | CONFERMATA | `BaseModel.php:23` |
| 9.3 remove Livewire SuperAdmin | User | CONTRADDETTA | `User/app/Http/Livewire/Profile/SuperAdmin.php` esiste ancora |
| dcp-204 (ready-for-dev) "stato misurato" | Gdpr | CONFERMATA | colonne `subject_type` e `revoked_user_agent` assenti, `// use SoftDeletes;` in `BaseUser.php:150` |
| Notify absolute-completion / ABSOLUTE_COMPLETION_100 | Notify | CONTRADDETTA | push simulati, stub, skip nei test (vedi D-24) |

Osservazione: quasi tutte le story "done" sono igiene (PHPStan, rinomine, icone, docs). Nessuna story "done" prova un flusso GDPR o di sicurezza end to end.

### 2.6 Story aperte con stato reale (VERIFICATO sul frontmatter, contenuto non sempre riletto)

| Story | Stato dichiarato | Stato reale |
| --- | --- | --- |
| Gdpr dcp-204 | ready-for-dev | vera: i difetti elencati esistono (D-01, D-02) |
| Gdpr dcp-140, 135, 136, 136a, 164 | ready-for-dev | contenuto non riverificato da me |
| Gdpr dcp-141 | needs-decision | retention dei log (DEC-12) |
| Gdpr dcp-142 | needs-data | registro trattamenti: `Treatment` a 0 righe (RIPORTATO dalla story, tinker) |
| Gdpr dcp-165-eta | needs-decision | nessun controllo eta' nel codice (D-12) |
| Gdpr dcp-168 | needs-decision | contenuto non riverificato |
| Gdpr dcp-178 | in-progress | prova consenso parzialmente fatta (Password::defaults, test Fixcity) |
| User 11.1 policy bypass | backlog, critical | vera (`UserPolicy.php:24,32,40,49`) |
| User 11.2 login rate limiting | backlog, critical | parzialmente superata: il widget ha throttle; l'API no (D-08) |
| User 11.3 cascade delete GDPR | backlog, high | vera (D-01) |
| User 11.4 encrypt socialite token | backlog, high | vera (`SocialiteUser.php:70,78`, nessun cast cifrato) |
| User 11.5 mass assignment CreateUserData | backlog, medium | non riverificata |
| User dcp-144 SPID/CIE | needs-decision, P1 | non nel registro DEC (D-16) |
| User dcp-138 limite tentativi | ready-for-dev, P1 | in parte coperta dal widget |
| User dcp-53 area personale | in-progress, P0 | non riverificata |
| User 14.1 matrice permessi | backlog | vera (D-14) |

---

## 3. Lacune

Totale 39: 16 BLOCCA go-live, 18 QUALITA', 5 RIFINITURA. Ordinate per gravita' e, dentro la gravita', per impatto.

Legenda "Chi": sviluppo = il team di codice; titolare = l'ente come titolare del trattamento (con DPO); ente = decisioni organizzative o contratti; fornitore = terzi (SMTP, OAuth, SMS). Dimensione: S fino a mezza giornata, M fino a 2-3 giorni, L oltre.

### 3.1 BLOCCA go-live

| ID | Lacuna | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Slug story proposto |
| --- | --- | --- | --- | --- | --- | --- | --- |
| D-01 | La cancellazione dell'account non funziona e non anonimizza. GET riporta alla home senza mostrare la vista. Il POST richiede la parola "cancella" mentre le lingue en/de/es hanno parole diverse. Mette solo `deleted_at` e `deleted_by`: nome, email, profilo, ticket, dispositivi, token Passport e Socialite, media restano. Marca come cancellati anche i log di audit. Nessun link in UI, nessun test. Esiste un secondo percorso concorrente con cancellazione fisica | VERIFICATO. `Gdpr/app/Actions/Account/DeleteAccountAction.php:38` (Gate::authorize), `:47` (`in:cancella`), `:72` (solo deleted_at), `:77-80` (update deleted_at sui log, contro il docblock `:22`); `Gdpr/lang/en/account.php:12` (confirm_word); `User/app/Models/BaseUser.php:150` (`// use SoftDeletes;`); `User/app/Actions/User/DeleteUserAction.php` (delete fisico, usato da `Widgets/Profile/DeleteAccountWidget.php`). Comando: `grep -rn "DeleteAccountAction\|account.delete" */tests` vuoto. Esito di Gate::authorize per l'utente Fixcity: non determinato (vedi D-06). Atomicita' tra connessioni: non determinata | titolare decide DEC-10 (anonimizzare o cancellare, destino di `tickets.owner_id` NOT NULL); sviluppo esegue | DEC-10, D-02, D-09, DEC-35 (tipi id), story 11.3 | L | Gdpr (azione), User (modello) | `gdpr-cancellazione-account-anonimizzazione` |
| D-02 | La revoca dei consensi usa colonne che non esistono. L'azione filtra su `subject_type` e scrive `revoked_user_agent`, ma le migrazioni dei consensi e il fillable del modello non le hanno. Chi salva i consensi non scrive `subject_type`. Il flusso di cancellazione chiama la revoca quindi fallirebbe a runtime (previsto da lettura, non eseguito) | VERIFICATO. `Gdpr/app/Actions/Consent/RevokeGdprConsentsAction.php:31` e `:36`. Comando: `grep -rn "subject_type\|revoked_user_agent" Gdpr/database/migrations` senza match nelle 4 `create_consents_table`. `Gdpr/app/Actions/SaveGdprConsentsAction.php` scrive `user_id`, `user_type`, `subject_id` e non `subject_type`. Runtime: non determinato | sviluppo | D-01, D-21 | S | Gdpr | `gdpr-revoca-consensi-colonne-mancanti` |
| D-03 | La verifica dell'email non esiste nel flusso reale. Il widget Gdpr imposta `email_verified_at = now()` senza prova e fa login subito, senza inviare la mail. `AUTH_MUST_VERIFY_EMAIL=true` e' letto solo dal widget di fallback di User. Conseguenza: le notifiche Fixcity passano il controllo `hasVerifiedEmail()` che e' sempre vero, quindi mail di stato e assegnazione arrivano a indirizzi mai provati (chiunque puo' registrare l'email di un'altra persona). Gli utenti Google hanno `email_verified_at` nullo e non ricevono mai mail | VERIFICATO. `Gdpr/app/Actions/Validation/ValidateUserDataAction.php:40`; `Gdpr/app/Actions/Registration/HandleSuccessfulRegistrationAction.php` (Auth::login); `User/app/Filament/Widgets/Auth/RegisterWidget.php:104` (unico uso di `must_verify_email`); `Fixcity/app/Notifications/TicketStatusChangedNotification.php:79` e `TicketAssignedNotification.php:35` (`hasVerifiedEmail()`) | titolare decide DEC-11 e DEC-13; sviluppo esegue | DEC-11, DEC-13, D-04, D-05, D-15 | L | User + Gdpr | `user-verifica-email-registrazione` |
| D-04 | La rotta `verification.verify` non e' definita in nessun file caricato: compare solo in `User/routes/auth.php:25` e `User/routes/web_tall.php:54`, che nessun provider carica. Il `VerifyEmail::toMailUsing` non potrebbe generare l'URL (eccezione attesa, non eseguita). La pagina `verify.blade.php` ha due bachi: `redirect('/')` senza `return` (riga 19) e `event(new Verified)` lanciato dopo un semplice reinvio (riga 24). `EmailVerificationController` esiste ma non e' instradato. `web_tall.php:58` limita la lingua a it,en | VERIFICATO. `grep -rn "verification.verify"` nei moduli e in `laravel/routes`: solo i due file sopra. `grep` sui provider: nessun caricamento di `auth.php`/`web_tall.php`. `User/resources/views/pages/auth/verify.blade.php:19,24` | sviluppo | D-03 | S | User | `user-rotta-verification-verify` |
| D-05 | La mail di reset password non ha un template seminato. `SpatieEmail` fa `firstOrCreate` con un corpo di default sbagliato ("La tua registrazione e' in attesa di approvazione") e una sincronizzazione solo per lo slug `verify-email`, solo per la lingua corrente. Il seeder semina solo `registration_moderated/completed/rejected`. Il link di reset e' costruito senza l'email. Se nel database il template `reset-password` non e' stato modificato a mano, il cittadino riceve un testo senza link | VERIFICATO. `Notify/app/Emails/SpatieEmail.php:52` (firstOrCreate), `syncVerifyEmailTemplate()`; `grep -rin "reset" Notify/database/seeders` vuoto; `User/app/Providers/UserServiceProvider.php:52-135`. Stato dei template nel database: non determinato | sviluppo | D-03, D-15, D-34 | S | Notify | `notify-template-reset-password-seed` |
| D-06 | `UserPolicy` risponde `true` a view, create, update e delete (solo viewAny e' falso). La story 11.1 (critical, backlog) e' ancora vera. Per l'utente Fixcity non esiste policy registrata (nessun Gate::policy, nessun UsePolicy, nessuna policy Fixcity) e Laravel non cerca la policy della classe padre: quale policy venga applicata e' non determinato | VERIFICATO. `User/app/Models/Policies/UserPolicy.php:24,32,40,49`; `User/app/Models/Policies/BaseUserPolicy.php` (versione corretta, ruoli super-admin, admin, hr-manager); story `User/docs/stories/11.1-fix-userpolicy-authorization-bypass.story.md`. Nessun test su questa policy (grep vuoto) | sviluppo | D-14, D-01 (Gate::authorize 'delete') | S | User | `user-policy-bypass-autorizzazione` |
| D-07 | Google OAuth non e' pronto per la produzione. Il callback collega un account locale esistente solo per email, senza controllare che il provider la dichiari verificata e senza prova che il locale ne sia titolare (con D-03 e' una presa di controllo preventiva). I nuovi utenti Google non registrano alcun consenso privacy/termini ne' email verificata. Il token Socialite e' in chiaro (solo `$hidden`). Il controllo `IsProviderConfiguredAction` e' commentato. Nessuna credenziale nel .env. Il pulsante si vede sempre, e il login mostra anche Microsoft e GitHub. Nel config esiste una chiave Google Maps scritta nel file (non riportata qui) | VERIFICATO. `User/app/Http/Controllers/Socialite/ProcessCallbackController.php:47,82`; `User/app/Actions/Socialite/CreateUserAction.php`; `User/app/Models/SocialiteUser.php:70,78`; `User/resources/views/filament/widgets/auth/login.blade.php:82,92,102`; `Themes/Sixteen` `google-button.blade.php` (condizione sempre vera); `.env`: nessuna `GOOGLE_*` (solo nomi controllati); `laravel/config/local/fixcity/google.php` (chiave maps in chiaro) | fornitore/ente (credenziali OAuth); titolare (consenso, base giuridica); sviluppo | D-03, DEC-11, DEC-28 (credenziali), RIPORTATO: DCP-269, DCP-271 dalla memoria di progetto | M | User | `user-google-oauth-collegamento-sicuro` |
| D-08 | Superfici di accesso non protette o rischiose. (a) `POST /api/user/login` non ha throttle: il limite G-07 e' solo nel widget; non esiste alcun `RateLimiter::for` ne' `throttleApi` nel codice. (b) `RegisterController` assegna in massa `$request->all()` ma non e' instradato: rischio latente. (c) `RegistrationWidget::getFormModel()` scrive `remember_token` su un utente esistente scelto dall'email della richiesta, senza autenticazione, e lo carica nel form: possibile presa in carico di un record | VERIFICATO. `User/routes/api.php:32` (route `api.login`); `User/app/Http/Controllers/Api/RegisterController.php:40,55`; `User/app/Filament/Widgets/RegistrationWidget.php:77-79`; `grep -rn "RateLimiter::for\|throttleApi" laravel/app laravel/bootstrap Modules/*/app` senza risultati utili. Il widget `RegistrationWidget` e' usato solo da test del modulo Cms (RIPORTATO) | sviluppo (per i file morti: ritiro con suffisso `.old`) | D-06 | M | User | `user-api-login-throttle-e-codice-morto-rischioso` |
| D-09 | Mancano registro delle richieste dell'interessato ed esportazione dei dati (accesso, portabilita'). La config contiene la voce `data_subject_rights` ma nessun codice la legge. La pagina impostazioni FO ha solo testo informativo e rinvia ai contatti del footer e al Garante | VERIFICATO. `grep -rn "data_subject_requests"` su php e json: vuoto; `grep -rniE "export\|portabil" Gdpr/app`: vuoto; `Gdpr/config/config.php` (chiavi retention/data_subject_rights senza consumatori); `Themes/Sixteen/resources/views/.../personal-area-settings/settings.blade.php` (sezione informativa). DEC-09 aperta nel registro (`Fixcity/docs/stories/dcp-200-registro-unico-delle-decisioni-aperte.story.md:43`) | titolare e DPO decidono; sviluppo esegue | DEC-09, D-01, D-10 | L | Gdpr | `gdpr-registro-richieste-interessato-ed-export` |
| D-10 | Mancano DPO e documenti di responsabilita'. Nome, email e telefono del DPO sono stringhe vuote e nessun codice li legge; nessun riferimento al DPO in `config/local/fixcity`. Non risultano registro dei trattamenti popolato (tabella `Treatment` a 0 righe, RIPORTATO), DPIA, nomine a responsabile del trattamento, procedura per violazioni dei dati | VERIFICATO. `Gdpr/config/config.php:225` (`'dpo'`); `grep -rniE "dpo" laravel/config/local/fixcity` vuoto. RIPORTATO: story dcp-142 (needs-data), controllo LG-02 del gate `bashscripts/tools/release/go-live-gate.py` | ente e titolare forniscono i dati; sviluppo mostra il contatto | DEC-06, DEC-07 | S codice, L documenti | Gdpr | `gdpr-dpo-registro-trattamenti-dpia` |
| D-11 | La regola password e' incoerente in tre punti. Il codice ha minimo 8 (default `$min = 8`) e `config/local/fixcity/password.php` restituisce `[]`. Il registro DEC-15 dice che il server accetta 9. L'interfaccia dice 12 (`User/lang/it/auth.php:576,596,1184`, `user_form.php:28`) e gli schemi Filament usano `min:12`. `User/config/password.php` ha `'min' => 6`, non usato. Il testo di aiuto e' una stringa italiana fissa. Valore effettivo a runtime: non determinato | VERIFICATO. `User/app/Datas/PasswordData.php:30` (`$min = 8`), `:98` (messaggio italiano fisso); `User/app/Providers/UserServiceProvider.php:147` (Password::defaults); `User/app/Filament/Widgets/Auth/Schemas/UserForm.php:132`, `User/app/Filament/Resources/UserResource/Schemas/UserForm.php:137` | titolare decide DEC-15; sviluppo esegue | DEC-15 | S | User | `user-regola-password-unica` |
| D-12 | Nessun controllo dell'eta' minima in registrazione: il form non chiede data di nascita ne' dichiarazione di eta' | VERIFICATO. `Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php:61-72` (regole). Registro: DEC-12, story dcp-165-eta (needs-decision) | titolare e DPO decidono (14 o 16 anni); sviluppo | DEC-12 | S | Gdpr | `gdpr-eta-minima-registrazione` |
| D-13 | La conservazione dei dati e' dichiarata ma non applicata. Il comando `notify:cleanup-logs` esce con errore perche' richiede `notify.cleanup.enabled`, chiave inesistente. Nulla e' schedulato: `laravel/routes/console.php` ha solo `inspire` e non esistono `Schedule`/`withSchedule` nei moduli. `activitylog.php` dice 365 giorni ma `activitylog:clean` non gira; `ActivityMaintenanceAction` (default 90 giorni) e' raggiungibile solo da un adapter e dai test. Tre durate diverse (30, 90, 365) senza una decisione. Log con ip e user agent restano senza scadenza (consents, tentativi di registrazione) | VERIFICATO. `Notify/app/Console/Commands/CleanupNotificationLogsCommand.php:33` e `:39`; `laravel/config/local/fixcity/activitylog.php:16`; `grep -rn "withSchedule\|Schedule::" laravel/app laravel/bootstrap laravel/routes Modules/*/app` senza job reali. Le schedule a database del modulo Job: non determinato | titolare e DPO decidono durate (DEC-12, dcp-141); sviluppo e sistemista eseguono | DEC-12, DEC-27 (scheduler e worker) | M | Gdpr (matrice), Activity e Notify (comandi) | `gdpr-retention-log-e-scheduler` |
| D-14 | Ruoli PA non pronti per la produzione. Le policy Fixcity si basano su `operator`, `supervisor`, `admin`; il `RoleSeeder` di User semina solo super-admin, admin, moderator, editor, user, guest; `operator` esiste solo nel seeder demo; `supervisor` non e' creato da nessuna parte. Non c'e' una matrice ruoli-permessi per la PA (il documento `User/docs/roles-permissions.md` e' una nota generica sul guard) | VERIFICATO. `Fixcity/app/Policies/BasePolicy.php:38` (PA_ROLES); `User/database/seeders/RoleSeeder.php`; `DemoOperatorPanelAccessSeeder` (Fixcity); `grep -rn "supervisor" */database/seeders` senza creazione. Story 14.1 (backlog) | ente definisce i ruoli reali; sviluppo | D-06, D-27, D-32 | M | User | `user-ruoli-pa-seed-e-matrice-permessi` |
| D-15 | La consegna reale della posta non e' dimostrata. Il dev usa `sendmail` locale con mittente `noreply@fixcity.local`; nessun SMTP, SPF, DKIM; la coda in dev e' `sync` e il worker di produzione non e' deciso. Reset password e verifica email dipendono da questo. Il PRD dichiara "Twilio/SendGrid Active" e requisiti di notifica "Complete": nessuna configurazione li prova | VERIFICATO. `.env` (MAIL_MAILER, MAIL_FROM_ADDRESS, QUEUE_CONNECTION, valori non segreti); `Fixcity/docs/roadmap/prd.md:548-552,810-821`. Registro: DEC-26 (SMTP) e DEC-27 (worker). Gestione dei rimbalzi: non verificata | ente e fornitore (SMTP, dominio); sviluppo | DEC-16 (dominio), DEC-26, DEC-27, D-03, D-05 | M | Notify | `notify-consegna-email-reale` |
| D-16 | SPID e CIE: la story dcp-144 e' in needs-decision con priorita' P1 e cita l'obbligo di accesso con identita' digitale per i servizi online della PA (CAD art. 64, RIPORTATO dalla story, non ricontrollato). Non compare in nessuna voce DEC-01..DEC-51. Diventa bloccante solo se l'ente conferma l'obbligo | VERIFICATO lo stato della story e l'assenza nel registro (`grep -n "SPID\|CIE\|DCP-144"` sul registro non trova una decisione). RIPORTATO il riferimento normativo | ente decide; sviluppo/fornitore integra | nuova decisione (vedi sezione 6) | L | User | `user-spid-cie-decisione-e-integrazione` |

### 3.2 QUALITA'

| ID | Lacuna | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Slug story proposto |
| --- | --- | --- | --- | --- | --- | --- | --- |
| D-17 | C-3 ancora aperto: `NotificationTemplate::compileString()` esegue `Blade::render` su template modificabili da admin, cioe' puo' eseguire PHP. Il percorso `MailTemplate` (Mustache) e' sicuro. Il rischio e' limitato perche' non c'e' alcun chiamante fuori da Notify (pagine di test admin) | VERIFICATO. `Notify/app/Models/NotificationTemplate.php:327` (Blade::render), `:137-139` (compile), `Notify/app/Actions/SendNotificationAction.php:52`, `NotificationManager.php:59`; grep sui chiamanti esterni vuoto | sviluppo | D-24 | S | Notify | `notify-compilestring-solo-allowlist` |
| D-18 | Audit trail modificabile: la risorsa Filament dei consensi ha pagine create e edit, quella dei log attivita' ha `CreateActivity` e `EditActivity`; `ActivityPolicy` consente update e delete per permesso; esiste `RestoreActivityAction`; la cancellazione account marca i log come cancellati. Un registro di audit e una prova di consenso dovrebbero essere solo in aggiunta | VERIFICATO. `Gdpr/app/Filament/Resources/ConsentResource.php:43-44`; `Activity/app/Filament/Resources/ActivityResource/Pages/CreateActivity.php`, `EditActivity.php`; `Gdpr/app/Actions/Account/DeleteAccountAction.php:77-80` | titolare decide immutabilita'; sviluppo | D-01, D-21 | S | Activity e Gdpr | `activity-audit-trail-solo-aggiunta` |
| D-19 | `LoginListener::handle()` e' vuoto (`// ...`) ma registrato sull'evento Login; 7 test sono saltati con la bandiera `$loginLoggingNotImplemented`, 9 in `ListLogActivitiesActionTest` per "non implementato". Nessun log di accesso in Activity. Se un'altra tabella (`authentication_log`) registri i login: non determinato | VERIFICATO. `Activity/app/Listeners/LoginListener.php:12-15`; `Activity/tests/Unit/Listeners/LoginListenerTest.php:46-52`; `Activity/app/Providers/EventServiceProvider.php` | sviluppo | D-13 | S | Activity | `activity-login-listener-implementazione` |
| D-20 | La proprieta' dell'audit del ciclo di vita del ticket e' divisa: Fixcity scrive `ticket_activities` (`RecordTicketActivityAction`, tipi status_change, assignment, approved, rejected, mail_*, followup, media_added) mentre il modulo Activity e' usato da Fixcity solo per `priority_changed` e dalla registrazione. `DeleteTicketAction` non scrive alcuna traccia; il modello Ticket non usa `LogsActivity`; non c'e' log degli accessi di un operatore a dati personali del cittadino | VERIFICATO. `Fixcity/app/Actions/RecordTicketActivityAction.php`; `Fixcity/app/Actions/ChangeTicketPriorityAction.php` (LogActivityAction); `grep -rn "LogsActivity" Fixcity/app/Models` vuoto per Ticket. Modifica, ripristino e cancellazione non tracciati | titolare/ente decide dove vive l'audit; sviluppo | D-18 | M | Activity + Fixcity | `activity-ownership-audit-ciclo-segnalazione` |
| D-21 | La prova di consenso ha ridondanze e codice morto. `SaveGdprConsentsAction` crea al volo le righe `Treatment` con versione documento '1.0' (confonde le voci di consenso con il registro dell'art. 30); `documentUrl` non e' mai valorizzato. `gdpr_events` (`Models/Event.php`) non e' scritta da nessuno. `SaveRegistrationConsentsAction` e il listener `SaveGdprConsents` (mappa a 7 trattamenti) ascoltano `Modules\User\Events\UserRegistered`, che non viene mai emesso | VERIFICATO. `Gdpr/app/Actions/SaveGdprConsentsAction.php` (TREATMENT_DEFAULTS); `grep -rn "gdpr_events\|new Event\|Event::create" Gdpr/app` nessuna scrittura; `grep -rn "UserRegistered" laravel/Modules` solo classe, listener, factory | sviluppo; titolare per le versioni dei documenti | D-02, D-18, D-10 | M | Gdpr | `gdpr-prova-consenso-unica-e-storico` |
| D-22 | La config di Gdpr e' ereditata da un progetto assicurativo: documenti privacy "Generali/Oris Broker", consensi `con1..con7`, categorie "gestione polizze/sinistri", `retention` e `data_subject_rights` senza alcun lettore. `config/consent.php` (pacchetto foothing) sembra non usato | VERIFICATO. `Gdpr/config/config.php`; `grep -rn "insurance_consents\|processing_register\|privacy_documents" Gdpr/app Fixcity/app` senza consumatori | sviluppo | D-09, D-13 | S | Gdpr | `gdpr-config-ripulita-da-progetto-assicurativo` |
| D-23 | L'inventario cookie elenca sessione, `XSRF-TOKEN` e cookie di consenso ma non il cookie "ricordami" (`remember_web_*`, casella presente nel login) ne' il cookie lingua (non determinato). La lista `gdpr.config.cookie.extra` e' vuota | VERIFICATO. `Gdpr/app/Actions/Cookie/GetCookieInventoryAction.php`; `User/app/Filament/Widgets/Auth/LoginWidget.php:61,72` | sviluppo; titolare approva elenco | D-10 | S | Gdpr | `gdpr-inventario-cookie-completo` |
| D-24 | I canali oltre l'email non sono reali. Push APNs e Web Push restituiscono "sent (simulated)" con esito positivo; la selezione dei destinatari push e' uno stub che torna `[]`; ci sono TODO in WhatsApp, Telegram e notifica generica; i circa 12 provider SMS non hanno chiavi ne' chiamanti in Fixcity. `SendPushToDeviceAction` registra il token del dispositivo grezzo nei log di errore. Documenti "completamento assoluto 100%" e il PRD dichiarano il contrario | VERIFICATO. `Notify/app/Actions/Push/SendPushToPlatformAction.php:105,130`; `Notify/app/Datas/PushCriteriaData.php:13`; `Notify/app/Notifications/WhatsAppNotification.php:66`, `GenericNotification.php:113`; `Notify/app/Channels/TelegramChannel.php:26,34`; `Notify/docs/absolute-completion.md` e gemelli; `Fixcity/docs/roadmap/prd.md:548-552,810-821` | titolare/ente decide il perimetro; sviluppo | D-15, D-25 | S se si toglie, L se si implementa | Notify | `notify-canali-sms-push-perimetro-reale` |
| D-25 | Confine di dominio: Fixcity non usa nessuna azione Notify (`grep` su `Modules.Notify` in `Fixcity/app` vuoto) e ha le proprie notifiche; Notify contiene copie `TicketStatusChangedNotification` e `TicketAssignedNotification` usate solo da un test di copertura. Le preferenze utente coprono solo `email_ticket_updates` | VERIFICATO. `Notify/app/Notifications/TicketStatusChangedNotification.php`, `TicketAssignedNotification.php`, `Notify/tests/Unit/Notifications/NotificationsCoverageTest.php`; `Fixcity/app/Actions/NotifyTicketAssigneeOfAssignmentAction.php`, `NotifyTicketSubscribersOfStatusChangeAction.php` | sviluppo | D-24 | M | Notify e Fixcity | `notify-confine-dominio-notifiche-ticket` |
| D-26 | Conflitto fra due migrazioni della tabella `notifications`: quella del 2022 usa `morphs` (bigint), quella del 2026-06-10 si dichiara "UNICA migrazione" con `uuidMorphs`. La tabella legacy con bigint nel database dati e' la DEC-33 (RIPORTATO). In piu' risulta un errore di test per `notifiable_id` bigint (memoria di progetto, RIPORTATO). Dato sacro: non resettare ne' forzare | VERIFICATO. `Notify/database/migrations/2022_10_12_133532_create_notifications_table.php`, `2026_06_10_134000_create_notifications_table.php`. Stato reale delle tabelle: non determinato | titolare decide (DEC-33); sviluppo | DEC-33, DEC-35 | M | Notify | `notify-tabella-notifications-uuid` |
| D-27 | Nessuna autenticazione a due fattori per gli operatori della PA. Le colonne e i contratti (`TwoFactorAuthentication*`) esistono, nessuna classe implementa il provider; la migrazione `2014_10_12_200000_add_two_factor_columns_to_users_table.fortify` ha un nome che non la fa girare | VERIFICATO. `grep -rn "TwoFactorAuthenticationProvider" User/app` solo contratto; nome del file migrazione. Obbligatorieta' per la PA: decisione non presa | ente decide; sviluppo | D-14 | L | User | `user-2fa-operatori-pa` |
| D-28 | Dipendenze fra moduli non unidirezionali: User importa Activity, mentre Activity richiede User (ciclo); User importa anche Tenant (4 punti), Media (4), UI (1), Notify (1: `SpatieEmail`); `AssignFreeCreditsListener` importa `Modules\Predict\Models\Profile` ma il modulo Predict non esiste (protetto da `class_exists`) | VERIFICATO. `User/app/Filament/Widgets/Auth/RegisterWidget.php:11`; `Activity/module.json:15`; `User/app/Listeners/AssignFreeCreditsListener.php:9,14`; conteggi con `grep -rln "use Modules\\\\<Mod>" User/app` | sviluppo | D-35 | M | User | `user-dipendenze-moduli-unidirezionali` |
| D-29 | Residui di altri progetti nel modulo User: ruolo `restaurant::admin` in `canAccessPanel` e pannello `accesso` che risponde sempre `true`; ruolo `hr-manager` nelle policy (`BaseUserPolicy.php:18,26,35,43`); story 3.1-3.4 (RBAC Evaluator, Staff, Senior Management), 6.2 e 6.3 (inviti) e 3.9 (residenza dati) appartengono a un dominio di valutazione HR. Da marcare superseded, non da cancellare | VERIFICATO. `User/app/Models/BaseUser.php` (canAccessPanel); `User/app/Models/Policies/BaseUserPolicy.php:18`; elenco story in `User/docs/stories` | sviluppo; titolare conferma la residenza dati 3.9 | D-06, D-36 | S | User | `user-residui-altri-progetti-superseded` |
| D-30 | Test: tanti ma poco mirati ai flussi critici. User 196 file, 1095 chiamate a test, 55 skip/todo; nomi tipo `UserCoverage100RemainingTest`, `ModuleCoverageBoostTest`, `UserGapAttackCoverageTest` (riempimento di copertura); 36 file User e 30 Notify senza alcun token di asserzione. Mancano test per: `DeleteAccountAction`, `RevokeGdprConsentsAction`, throttle del login API, bypass di `UserPolicy`, login di un utente cancellato, collegamento OAuth per email, contenuto della mail di reset. Non ho eseguito Pest (scrive sul DB di sviluppo) | VERIFICATO. `grep -rln "DeleteAccountAction\|RevokeGdprConsentsAction\|account.delete" */tests` vuoto; conteggi con `grep -rc "it(\|test(" */tests`; skip: `Notify/tests/.../ThemeManagementBusinessLogicTest.php:23`, `TemplateManagementBusinessLogicTest.php:22`, `ContactTest.php:346,352`. Esito dei test: non determinato | sviluppo | D-01..D-08 | M | User, Gdpr, Notify, Activity | `user-gdpr-test-flussi-critici` |
| D-31 | Enumerazione degli account e dati personali nei log. Il reset password restituisce uno stato diverso se l'email non esiste (`passwords.user`); la registrazione segnala l'email duplicata; il widget di registrazione scrive ip e user agent nel log a livello debug | VERIFICATO. `User/app/Filament/Widgets/Auth/ForgotPasswordWidget.php`; `Gdpr/app/Actions/Validation/ValidateUserDataAction.php`; `Gdpr/app/Filament/Widgets/Auth/RegisterWidget.php` | sviluppo; titolare per i log | D-13 | S | User + Gdpr | `user-enumerazione-account-e-log-pii` |
| D-32 | `hasPermissionToOrCreate` esegue `Permission::findOrCreate` a ogni controllo: una lettura scrive righe nella tabella dei permessi (usata da `TicketPolicy`). `GdprBasePolicy::before` concede tutto se `XotData::make()->super_admin` coincide con l'email dell'utente: l'impostazione non e' presente per Fixcity (nullo), e se fosse impostata si fiderebbe di un'email non verificata (D-03) | VERIFICATO. `User/app/Models/Traits/HasSpatiePermission.php:14-23`; `Gdpr/app/Policies/GdprBasePolicy.php` (metodo `before`); `Fixcity/app/Policies/TicketPolicy.php` | sviluppo | D-03, D-14 | S | User + Gdpr | `user-permessi-scrittura-su-lettura` |
| D-33 | La regola password usa `uncompromised(0)`, cioe' una chiamata a un servizio terzo (controllo password compromesse). Non risulta dichiarata nell'informativa e non e' nel registro DEC | VERIFICATO nel codice (`User/app/Datas/PasswordData.php`, uso di `uncompromised`); assenza nell'informativa: non determinata (testi legali dipendono dal CMS) | titolare decide se tenerla e dichiararla | D-11, D-10 | S | User | `user-controllo-password-compromesse-informativa` |
| D-34 | Quattro lingue nel sito (it, en, de, es) ma le traduzioni dei messaggi di accesso sono parziali: `verification_email` esiste solo in it/en, `reset_password` solo in de/en/it; la sincronizzazione del template di verifica copia solo la lingua corrente | VERIFICATO. `ls User/lang/*/verification_email.php User/lang/*/reset_password.php`; `Notify/app/Emails/SpatieEmail.php` (`syncVerifyEmailTemplate`) | sviluppo | D-05 | S | User + Notify | `user-notify-email-in-quattro-lingue` |

### 3.3 RIFINITURA

| ID | Lacuna | Evidenza | Chi decide / esegue | Dipendenze | Dim. | Modulo owner | Slug story proposto |
| --- | --- | --- | --- | --- | --- | --- | --- |
| D-35 | Il volume di documentazione nasconde l'essenziale. File .md: User 2106, Notify 3670 (1471 nella radice di docs), Activity 802, Gdpr 424. Copie byte-identiche: User 65 gruppi (89 copie in piu'), Notify 204 gruppi (219), Activity 68 (76), Gdpr 26 (34). Nomi che collidono dopo normalizzazione maiuscole/underscore: User 189, Notify 411, Activity 41, Gdpr 19. Notify ha almeno 138 file con nomi tipo completion/ultimate/absolute; Gdpr contiene documenti di temi non suoi (algolia-docsearch, custom-404-page). Servono solo poche pagine canoniche per modulo (vedi sezione 5) | VERIFICATO con `find ... -name '*.md' \| wc -l`, `md5sum` per gruppi e normalizzazione dei nomi con `tr`. Le cifre di DEC-51 (cartelle `docs/archive` e `docs/consolidated` con Notify 202, User 140) non corrispondono all'albero: quelle cartelle non esistono a quei percorsi (`find -type d -name archive`) | sviluppo; titolare su DEC-42, DEC-43, DEC-51 | DEC-42, DEC-43 (i file `.old` non entrano in git), DEC-51 | L | Notify, User, Activity, Gdpr | `moduli-docs-consolidamento-con-mappa-di-migrazione` |
| D-36 | Stato delle story non allineato al codice: 11.2 ancora backlog ma in parte superata; 9.3 "done" con il file ancora presente; Gdpr `completion-backlog.md` dichiara nel perimetro "data export" che non esiste; `bmad-remaining-work.md` e `completion-backlog.md` dei quattro moduli sono modelli generici; molte story "done" sono igiene. Regola: non cancellare ne' rinumerare, ma collegare o marcare superseded | VERIFICATO. `Gdpr/docs/completion-backlog.md` (2336 B), `Gdpr/docs/bmad-remaining-work.md` (797 B); `User/app/Http/Livewire/Profile/SuperAdmin.php` | sviluppo | D-29, D-35 | S | tutti e quattro | `user-gdpr-notify-activity-allineamento-stato-story` |
| D-37 | File residui nei moduli: User ha `.fila2`, `.boh`, `.to_widget`, `.no`, `.wip`, `.test`, `Http/Livewire/Profile/SuperAdmin.php` e 8 file `tests/coverage-*.xml` tracciati da git; la radice di Notify ha `compare_structure.py`, `local.html`, `local_updated.html`, `reference.html`, `screenshot-group-b.js`, `docs.lock`, `skills-lock.json` (i `.lock` non vanno toccati). Per la regola del progetto si ritira con il suffisso `.old`, non si cancella | VERIFICATO con `find User -name '*.fila2' -o -name '*.boh' ...` e `ls Notify` | sviluppo | D-35, DEC-43 | S | User, Notify | `user-notify-pulizia-file-residui` |
| D-38 | Migrazioni duplicate per la stessa tabella: User 7x `create_users_table`, 7x `create_teams_table`, 6x `create_profiles_table`, 5x `create_roles_table`; Notify 9x `create_mail_templates_table`, 3x `create_notification_logs_table`, 3x `create_mail_template_versions_table`; Activity 5x `create_activity_table`; Gdpr 4x `create_consents_table`, 2x `create_treatments_table`. Dati sacri: non rimuovere ne' rieseguire, ma documentare quale migrazione e' la fonte di verita' | VERIFICATO con `ls */database/migrations \| sed ... \| sort \| uniq -c` | sviluppo (solo documentazione) | DEC-33 | M | tutti e quattro | `moduli-migrazioni-duplicate-fonte-unica` |
| D-39 | Finiture: `GdprData.php` ha un docblock "Class MetatagData" con proprieta' duplicate (copia e incolla); stringhe italiane fisse in `DeleteUserAction` e `PasswordData`; helper `Auth()` maiuscolo in `RevokeGdprConsentsAction.php:37` | VERIFICATO nei file citati | sviluppo | nessuna | S | Gdpr, User | `gdpr-user-finiture-codice-e-i18n` |

---

## 4. Contraddizioni tra documenti e codice

| N. | Documento dice | Il codice dice | Prova |
| --- | --- | --- | --- |
| 1 | `DeleteAccountAction` (docblock `:22`): non cancella fisicamente, conserva l'audit trail | `:77-80` marca come cancellati i log di audit dell'utente | `Gdpr/app/Actions/Account/DeleteAccountAction.php:22,77-80` |
| 2 | DEC-15: il server accetta password da 9 caratteri | `PasswordData.php:30` ha `$min = 8`; la UI dice 12 | `User/app/Datas/PasswordData.php:30`; `User/lang/it/auth.php:576` |
| 3 | PRD Fixcity: requisiti di notifica (email, SMS, in-app, preferenze) "Complete", Twilio e SendGrid "Active" | `sendmail` locale, nessuna chiave SMS/SendGrid, nessun chiamante SMS | `Fixcity/docs/roadmap/prd.md:548-552,810-821`; `.env` |
| 4 | Notify: "COMPLETAMENTO ASSOLUTO 100%" | Push simulati, selezione destinatari stub, TODO, 20 test saltati | `Notify/docs/absolute-completion.md`; D-24 |
| 5 | Migrazione 2026-06-10 delle notifiche si dichiara "UNICA migrazione" | Resta la migrazione del 2022 con `morphs` | D-26 |
| 6 | User 9.3 "remove Livewire SuperAdmin" e' done | Il file esiste ancora | `User/app/Http/Livewire/Profile/SuperAdmin.php` |
| 7 | Gdpr `completion-backlog.md` include "data export" | Nessun codice di esportazione | `grep -rniE "export" Gdpr/app` vuoto |
| 8 | DEC-51: 466 file in `docs/archive` e `docs/consolidated` (Notify 202, User 140) | Le cartelle non esistono a quei percorsi | `find` per nome cartella |
| 9 | DEC-50: merge in corso in User e Notify (MERGE_HEAD) | Nessun `MERGE_HEAD` ne' modifiche in sospeso nei repo User, Gdpr, Notify, Activity | `ls .git/MERGE_HEAD` assente, `git status` pulito. Il quadro attuale puo' essere cambiato dopo la stesura del registro |
| 10 | `.env`: `AUTH_MUST_VERIFY_EMAIL=true` e il modulo promette una conferma (DEC-11) | Il flusso reale marca la mail verificata subito | `ValidateUserDataAction.php:40` |
| 11 | Story 11.2: manca il rate limiting sul login (backlog, critical) | Il widget ha il limite 5/60 s; l'API no | `LoginWidget.php:61,72`; `User/routes/api.php:32` |
| 12 | DEC-17: la pagina `terms` non esiste | Esistono `terms.index.json`, `privacy.index.json`, `note-legali.index.json` | `laravel/config/local/fixcity/database/content/pages/` (parzialmente superata) |
| 13 | Tre durate diverse di conservazione dei log | Notify 30 giorni, Activity 90 (azione) e 365 (config) | D-13 |
| 14 | `UserPolicy` e `BaseUserPolicy` sembrano equivalenti | La prima risponde sempre `true`, la seconda controlla i ruoli | `UserPolicy.php:24..49` contro `BaseUserPolicy.php:18..43` |
| 15 | La registrazione di User sembra la sorgente di verita' | Il FO usa il widget di Gdpr; quello di User e' un fallback | `auth-register.blade.php:133` |

---

## 5. Documentazione mancante o da riscrivere

Regola dei nomi: niente date, timestamp o suffissi numerici; la data va nel frontmatter. Prima di creare un `.md` usare `python3 -I bashscripts/tools/docs/md-filenames.py name <percorso>`. Le pagine vanno nei moduli proprietari (`laravel/Modules/<Modulo>/docs/`). Non ho verificato se le sottocartelle `docs/wiki/concepts` esistano in tutti i moduli: da controllare prima di creare.

| Percorso proposto | Contenuto | Sostituisce o chiude |
| --- | --- | --- |
| `Gdpr/docs/wiki/concepts/account-deletion-scope.md` | per ogni tabella e connessione: cosa si cancella, cosa si anonimizza, cosa si conserva e perche' (esito di DEC-10) | D-01, story 11.3, dcp-204 |
| `Gdpr/docs/wiki/concepts/data-subject-requests-register.md` | modello dei dati, chi gestisce, tempi, verifica identita' (dopo DEC-09) | D-09 |
| `Gdpr/docs/wiki/concepts/consent-proof-data-model.md` | quale tabella e' la prova, versioni dei documenti, immutabilita' | D-18, D-21 |
| `Gdpr/docs/compliance/retention-matrix.md` | durata per tipo di log e dato personale, chi esegue la pulizia e con che comando schedulato | D-13 |
| `Gdpr/docs/wiki/how-to/dpo-and-accountability-documents.md` | DPO, registro dei trattamenti, DPIA, nomine, violazioni: elenco con stato e responsabile | D-10 |
| `User/docs/wiki/concepts/auth-flows-registration-verification-reset.md` | flusso reale (Gdpr RegisterWidget), rotte caricate, template email, cosa e' fallback | D-03..D-05, contraddizione 15 |
| `User/docs/wiki/concepts/roles-and-permissions-pa-matrix.md` | matrice operator, supervisor, admin per risorsa e azione (da sostituire a `roles-permissions.md` generico) | D-14, story 14.1 |
| `User/docs/wiki/concepts/google-oauth-account-linking.md` | quando si collega un account, consensi richiesti, credenziali, token cifrato | D-07 |
| `User/docs/wiki/rules/password-policy-single-source.md` | una sola regola (valore da DEC-15) e dove e' definita | D-11 |
| `Notify/docs/wiki/concepts/channels-real-vs-simulated.md` | tabella per canale: reale, simulato, stub, non usato da Fixcity | D-24, D-25 |
| `Notify/docs/wiki/concepts/email-templates-and-slugs.md` | slug, lingue, chi li semina, come si verificano | D-05, D-34 |
| `Notify/docs/wiki/rules/mail-template-mustache-only.md` | divieto di Blade/PHP nei template modificabili | D-17 |
| `Activity/docs/wiki/concepts/audit-trail-ownership-and-retention.md` | cosa va in Activity e cosa in `ticket_activities`, append-only, durata | D-18, D-20, D-13 |
| README di indice per modulo (una schermata) | elenco delle pagine canoniche, stato reale, rinvio alla mappa di migrazione | D-35 |

Da riscrivere o ritirare con suffisso `.old` dopo mappa di migrazione e collegamenti (non cancellare): i documenti di completamento di Notify (`absolute-completion.md`, `ABSOLUTE_COMPLETION_100.md`, `PROJECT_COMPLETION_REPORT.md`, `ULTIMATE_COMPLETION_REPORT.md`, `SUPER_MUCCA_COMPLETION.md` e i gemelli con underscore o trattino), i due `bmad-remaining-work.md` e `completion-backlog.md` generici di ogni modulo, i documenti di tema dentro `Gdpr/docs`. Nota DEC-43: i file `.old` non entrano in git (`.gitignore` riga 13), quindi il ritiro non e' tracciato senza una decisione.

---

## 6. Domande aperte per l'utente

Le prime sei corrispondono a voci gia' nel registro (`dcp-200`); le altre sono proposte di nuove decisioni. Il numero va assegnato dal registro al momento dell'inserimento, per evitare collisioni di numerazione.

1. DEC-10: alla cancellazione si anonimizza l'utente tenendo le segnalazioni di interesse pubblico, oppure si cancella tutto? Il campo `tickets.owner_id` e' NOT NULL e senza chiave esterna. Quali tabelle e connessioni rientrano?
2. DEC-11 e DEC-13: la verifica dell'email diventa obbligatoria prima di ricevere mail e prima della pubblicazione? Che si fa con gli utenti gia' registrati con `email_verified_at` impostata d'ufficio?
3. DEC-15: il numero giusto e' 12? Il registro dice che il server oggi ne accetta 9, io leggo 8 nel codice e la UI dice 12. Serve confermare quale e' il valore reale in produzione (non determinato da me).
4. DEC-12: eta' minima (14 o 16 anni) e durata di conservazione dei log, per tipo.
5. DEC-06 e DEC-09: chi e' il DPO e chi gestisce le richieste degli interessati? Senza questi dati D-09 e D-10 non si chiudono.
6. DEC-26 e DEC-27: quale SMTP e dominio mittente, e chi garantisce worker e scheduler in produzione?
7. Nuova: SPID e CIE sono obbligatori per questo ente al go-live, oppure si accettano solo email e password (con eventuale Google)? Riferimento: story dcp-144.
8. Nuova: Google OAuth va attivato al go-live? Se si, chi fornisce le credenziali e come si gestiscono consenso e verifica email per chi arriva da Google? Se no, si toglie il pulsante (e Microsoft e GitHub dal login).
9. Nuova: dove vive l'audit del ciclo di vita del ticket, nel modulo Activity o in `ticket_activities` di Fixcity? E si vuole un log degli accessi degli operatori ai dati personali?
10. Nuova: per la prima versione si tengono solo email e notifica in app, togliendo dal perimetro SMS, push, WhatsApp e Telegram? Cosa dire nel PRD, che oggi li dichiara completi?
11. Nuova: i log di audit e le prove di consenso devono essere non modificabili anche per i super amministratori (niente create/edit/delete da Filament)?
12. Nuova: 2FA obbligatoria per gli operatori della PA?
13. Nuova: quali sono i ruoli reali dell'ente (operator, supervisor, admin) e chi puo' fare cosa? Serve per la matrice e per i seeder di produzione.
14. Nuova: si vuole mantenere il controllo delle password compromesse (servizio terzo) e dichiararlo nell'informativa, oppure toglierlo?
15. Nuova: la residenza dei dati in UE (story 3.9) e' un requisito dell'ente? Dove e' ospitato il database e il servizio di posta?
16. Tecnica: quale policy deve governare gli utenti di Fixcity (la classe estende quella di User ma non ne eredita la risoluzione)? Mi serve una prova a runtime che io non posso fare in sola lettura.

---

Cosa NON ho potuto verificare (tutto "non determinato"): esito dei test (Pest non eseguito), contenuto reale del database (template `reset-password`, righe di `Treatment`, colonna `deleted_at` di `activity_log`, tipi delle tabelle `notifications`), mapping delle connessioni di modulo sui database fisici, comportamento effettivo di `Gate::authorize('delete', $user)` per l'utente Fixcity, valore effettivo della lunghezza minima password a runtime, schedulazioni a database del modulo Job, presenza di un worker di coda in produzione, accessibilita' reale dei form di autenticazione (non misurata in browser).
