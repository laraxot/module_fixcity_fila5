---
id: dcp-284
title: "DCP-284: notifiche del ciclo di vita della segnalazione (ricevuta, approvata, scartata, presa in carico, chiusa)"
type: story
epic: go-live-trasparenza-stato
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 284 notifiche ciclo di vita segnalazione A-01 trasparenza stato TicketLifecycleNotification NotifyTicketOwnerOfLifecycleEventAction NotifyTicketReceivedAction TicketCreatedEvent TicketActivityObserver EventServiceProvider approvata scartata presa in carico chiusa PA_ROLES lang it en de es"
related: [dcp-111, dcp-148, dcp-200, dcp-208, dcp-281]
tags: [notifiche, trasparenza, ciclo-di-vita, lang, test, bmad]
---

# DCP-284: notifiche del ciclo di vita della segnalazione

## Fase BMAD

Dev (TDD): story prima, poi test rosso, poi codice, poi test verde. Origine: gap A-01 del report `gap-A-fixcity-mobile` (A-02 e A-25
solo come confini: vedi "Fuori ambito"). Promessa di prodotto (`PRODUCT.md`, Positioning): dopo l'accesso il cittadino vede sempre
lo stato reale della sua segnalazione. Ogni affermazione del report e' stata riverificata nel codice: sotto, ogni fatto e' marcato
MISURATO (letto o eseguito qui) o DEDOTTO (conseguenza di fatti misurati, non eseguita).

## Stato

done (nel perimetro dichiarato; restano le decisioni DD-1..DD-6). Nessun commit, nessuna migrazione, nessuna dipendenza nuova. Esito con gli output reali in fondo (sezione Esito).

## Fatti misurati prima di toccare

1. **Cosa notifica oggi (MISURATO).** Un solo punto di uscita verso il cittadino: `Observers/TicketActivityObserver::created`
   (riga 15) chiama `Actions/NotifyTicketSubscribersOfStatusChangeAction`. Quest'ultima esce subito se l'attivita' non e'
   `status_change` con visibilita' `public` (righe 25-26) o se lo stato di arrivo non e' in `TicketStatusEnum::canViewByAll()`
   (riga 36). Destinatari: proprietario e iscritti (follower), deduplicati per id nella singola esecuzione (`$sentTo`).
   Notifica: `Notifications/TicketStatusChangedNotification` (canali `database` + `mail`; mail solo con email verificata e
   preferenza `email_ticket_updates` non disattivata; `ShouldQueueAfterCommit`, 3 tentativi, backoff 60 e 300 s).
   Test esistenti: `TicketStatusNotificationTest`, `NotificationRetryPolicyTest`, `PersonalAreaNotificationsActionsTest`.
2. **Chi registra le attivita' (MISURATO).** Tutte le Action del ciclo di vita passano da `RecordTicketActivityAction`, che crea una
   riga `ticket_activities`: `ApproveTicketAction` (evento `approved`, Public), `RejectTicketAction` (`rejected`, AuthorOnly, resta
   `pending`), `CloseTicketAction` (`closed`, Public), `AssignTicketAction` (`assignment`, Internal, payload `responsible_id`),
   `ChangeStatus` (`status_change`; Public solo se lo stato di arrivo e' visibile), `TicketMediaObserver` (`media_added`). Gli eventi
   `approved`, `rejected`, `closed`, `assignment`, `media_added` NON sono `status_change`: oggi non notificano nessuno (causa di A-01).
3. **Creazione (MISURATO).** `CreateTicketAction.php:41` emette `TicketCreatedEvent`. L'unico listener, `Listeners/TicketCreatedListener`,
   non e' registrato da nessuna parte (`Providers/EventServiceProvider` ha `$listen` vuoto; grep di `TicketCreatedListener` in `app/`
   trova solo la sua definizione) e comunque chiama `ChangeStatus` verso `pending` (che per un ticket gia' `pending` esce senza fare
   nulla): non notifica nessuno. Nessuna notifica alla creazione ne' al cittadino ne' alla PA. Il widget legacy `CreateTicketWidget`
   emette lo stesso evento (riga 242).
4. **Dove si registra un listener senza toccare `FixcityServiceProvider` (MISURATO).** `XotBaseServiceProvider::register()`
   (riga 55) registra sempre `Modules\Fixcity\Providers\EventServiceProvider` (sottoclasse di quello Illuminate, quindi senza
   scoperta automatica). Il listener va li'. Il file `FixcityServiceProvider` non viene toccato. TRAPPOLA (MISURATO): in `$listen` il
   listener NON parte. `bootstrap/cache/events.php` esiste in questo checkout e riporta `Modules\Fixcity\Providers\EventServiceProvider => []`:
   con la cache degli eventi Laravel legge `$listen` da quel file, non dalla classe. Con `$listen` i test della creazione davano
   "sent 0 times" (prima esecuzione verde parziale: 10 falliti su 21, due cause, questa e un helper di test). Per questo la
   registrazione e' esplicita, `Event::listen(...)` in `boot()` (vedi D-2).
5. **Cosa vede il proprietario nella timeline (MISURATO).** `BuildTicketTimelineAction`: Public + AuthorOnly per il proprietario;
   Internal solo per gli operatori. Quindi `approved`, `closed` (Public) e `rejected` (AuthorOnly, con motivo) sono visibili al
   proprietario; `assignment` (Internal) NO. Notificare la presa in carico al cittadino espone quindi un fatto che oggi la
   timeline nasconde: vedi "Da decidere" DD-2.
6. **Il feed "notifiche" del cittadino (MISURATO).** `/area-personale/notifiche` legge `BuildAuthenticatedUserTicketNotificationsQueryAction`,
   che filtra `type = TicketStatusChangedNotification::class` (riga 30); `BuildPersonalAreaNotificationsDataAction::item` scarta le
   righe senza `ticket_code` o senza `status` valido e la vista del tema compone la frase da codice e etichetta di stato
   (`item_text`: "La segnalazione :code ha un nuovo stato: :status."). Conseguenza (DEDOTTO): una notifica `database` di un tipo nuovo
   verrebbe scritta ma non comparirebbe mai nel feed; e un tipo nuovo aggiunto al feed deve fornire `ticket_code` e `status`.
7. **Destinatari PA (MISURATO).** Ruoli PA: `BasePolicy::PA_ROLES = ['operator','supervisor','admin']`, unica fonte, gia' usata da
   `isPaOperator` e da `ListAssignableTicketOperatorsAction` (DCP-281). `TicketPolicy::view` e `viewAny` danno a questi ruoli la
   visione di TUTTE le segnalazioni. La tabella `tickets` non ha colonne di comune/tenant (`information_schema` di `fixcity_data`:
   `project_id`, `epic_id`, `sprint_id` legacy, nessuna `tenant_id`), e nel database di sviluppo `tenants` e `tenant_user` sono vuote.
   Nel database di sviluppo esiste il ruolo `operator`; `supervisor` e `admin` non sono righe (A-06). Conseguenza (DEDOTTO): "gli
   operatori del comune" non e' un insieme definito nel modello dati; l'insieme "chi ha un ruolo PA" coincide con chi puo' gia'
   aprire ogni segnalazione nel back office, quindi notificarlo non rivela nulla che non veda gia'.
8. **Lingua del destinatario (MISURATO).** `users.lang` esiste ed e' valorizzato alla registrazione (`Gdpr/.../ValidateUserDataAction.php:39`,
   `'lang' => app()->getLocale()`): 83 utenti su 85 nel database di sviluppo hanno `it`. Nessun `HasLocalePreference` ne' `locale()`
   sulle notifiche Fixcity esistenti (A-22): il testo accodato esce nella lingua del worker, non del destinatario.
9. **Notify non e' riusabile qui (MISURATO).** Le notifiche Fixcity esistenti non usano `Modules/Notify`. `Notify/app/Models/NotificationTemplate.php:327`
   rende i modelli con `Blade::render($template, $data)` su stringhe del database, vietato per i contenuti nuovi; i template non hanno
   seed per Fixcity. Le due notifiche di ticket di Notify (`Notify/app/Notifications/TicketStatusChangedNotification`) hanno testi
   inglesi fissi e non sono usate da Fixcity (A-32). Riuso quindi: notifiche Laravel + chiavi lang del modulo + canali `database`/`mail`
   come le due esistenti.
10. **Test e posta (MISURATO).** `phpunit.xml`: `MAIL_MAILER=array`, `QUEUE_CONNECTION=sync`. I test non inviano posta reale.
    Baseline prima di toccare (`pest-scratch-db.sh`, Feature/Actions + Unit/Notifications + CreateTicketConfirmationFlowTest +
    BuildTicketTimelineActionTest): `Tests: 1 skipped, 69 passed (316 assertions)`; lo skip e' di DCP-281 (attore di `ChangeStatus`).
11. **Cosa NON ho potuto misurare.** Nessun invio reale verso caselle esterne (la consegna esterna e' non provata, D-15 del report D).
    Nessun worker di coda in esecuzione (A-07): in produzione le notifiche `ShouldQueueAfterCommit` partono solo con un worker.

## Causa

- A-01: le notifiche sono state collegate a un solo tipo di attivita' (`status_change` pubblico). Gli altri eventi del ciclo di vita
  scrivono una riga di audit ma nessun listener li legge. La creazione emette un evento senza listener registrato (lo stub
  `TicketCreatedListener` non e' mai stato agganciato). STORY-003 ("done") cita classi inesistenti: era una promessa, non un fatto.
- Effetto sul prodotto: il cittadino scopre l'approvazione, lo scarto o la chiusura solo riaprendo la pagina; la PA non sa che e'
  arrivata una segnalazione.

## Decisioni

- D-1 (aggancio per eventi di attivita'): estendo l'unico punto esistente, `TicketActivityObserver::created`, con una seconda
  chiamata a una nuova Action `NotifyTicketOwnerOfLifecycleEventAction`. Cosi' `ChangeStatus.php` e `AssignTicketAction.php` (di un
  altro agente, DCP-281) non si toccano. Un evento di attivita' = una sola chiamata = al piu' un messaggio per destinatario.
- D-2 (aggancio per la creazione): nuovo listener `NotifyTicketReceivedListener` su `TicketCreatedEvent`, registrato in
  `Providers/EventServiceProvider::boot()` con `Event::listen(TicketCreatedEvent::class, NotifyTicketReceivedListener::class)`, NON
  in `$listen` (cache degli eventi: fatto 4), cosi' resiste a `event:cache` e a `optimize`. `TicketCreatedListener` resta com'e' e
  non registrato (attiverebbe un `ChangeStatus` mai provato). Un'Action `NotifyTicketReceivedAction` orchestra proprietario e PA.
- D-3 (nessun doppione con l'esistente): `NotifyTicketOwnerOfLifecycleEventAction` gestisce SOLO `approved`, `rejected`,
  `assignment`, `closed`. `status_change` resta di `NotifyTicketSubscribersOfStatusChangeAction`. Gli insiemi sono disgiunti per
  costruzione: uno stesso cambio di stato non puo' produrre due messaggi. `media_added` non notifica (D-7).
- D-4 (destinatari del cittadino): solo il proprietario. I follower non ricevono gli eventi nuovi: oggi ricevono il codice di
  tracciamento nella notifica di stato (il test esistente lo asserisce), cioe' la capability che il dettaglio pubblico non espone.
  Estendere ai follower richiede prima quella decisione: DD-3.
- D-5 (canali): `database` sempre; `mail` se email verificata, non vuota e preferenza attiva: stessa regola delle due notifiche
  esistenti, in un trait condiviso dalle due classi nuove (3 metodi). Nessun SMS, push, WhatsApp, Telegram.
- D-6 (PA): alla creazione, notifica `database` + `mail` agli utenti con un ruolo di `BasePolicy::PA_ROLES` (insieme ottenuto da
  `ListAssignableTicketOperatorsAction`, fonte unica), escluso il proprietario. Il messaggio contiene solo numero della segnalazione e
  link al back office: nessun codice di tracciamento, nessuna email, nessun `owner_id`, nessun testo del cittadino. Lo scoping per
  comune non e' modellato: DD-1.
- D-7 (media): nessuna notifica per `media_added`. Nel wizard le foto le carica il proprietario stesso (N foto = N messaggi di rumore
  a chi le ha appena caricate) e nel codice non c'e' un percorso con cui la PA aggiunga allegati. DA DECIDERE solo se nascera' quel
  percorso.
- D-8 (presa in carico): si notifica solo la PRIMA assegnazione con un responsabile (le assegnazioni precedenti si leggono nello
  storico delle attivita'); riassegnazioni e rimozioni non notificano il cittadino. Il messaggio non nomina l'operatore e non usa il
  testo di `reason` (che contiene l'id del responsabile).
- D-9 (motivo dello scarto): incluso solo per `rejected`, perche' e' AuthorOnly (progettato per il proprietario) ed e' gia' nella sua
  timeline. Testo libero dell'operatore: normalizzato (spazi), troncato a 300 caratteri e, nella mail, con i caratteri speciali di
  Markdown preceduti da backslash (il renderer delle mail ha `html_input => escape`, ma i link Markdown resterebbero attivi). Il
  motivo e' ridotto a una riga, quindi senza titoli, elenchi o blocchi di codice. Limite noto (documentato, non risolto): un URL
  scritto per esteso nel motivo resta un collegamento automatico leggibile nella mail (autolink GFM); e' testo dell'operatore
  verso il proprietario della segnalazione, non un link nascosto dietro un testo scelto.
- D-10 (lingua): la notifica porta la lingua del destinatario (`users.lang` se supportata, altrimenti la lingua corrente) con
  `Notification::locale()`: vale anche quando la accoda l'operatore. Piccola Action `ResolveNotificationLocaleAction`, riusabile per
  le notifiche esistenti (non le modifico: A-22 resta aperto per loro).
- D-11 (feed del cittadino): il feed include anche il tipo nuovo; per le righe con `event` mostra l'etichetta breve dell'evento al posto
  dell'etichetta di stato ("ha un nuovo stato: Presa in carico."). Nessuna modifica al tema.
- D-12 (URL di tracciamento): estratta `BuildTicketTrackUrlAction` (3 punti identici: notifica di stato, feed, notifica nuova).
- D-13 (testi): chiavi in `lang/{it,en,de,es}/ticket_notification.php` (file gia' esistente in it/en, nuovo in de/es). Nessun
  `Blade::render`, nessun testo da database: `__()` con segnaposto, valori escapati dal renderer (`{{ }}`) piu' l'escape Markdown di D-9.

## Da decidere

- DD-1 (PA, comune): chi riceve la notifica di nuova segnalazione "del comune"? Il modello dati non ha il comune sul ticket e la
  tabella dei tenant e' vuota. Implementato il minimo sicuro (chiunque abbia un ruolo PA, che gia' vede ogni segnalazione).
  Alternative: una casella condivisa dell'ente (serve un indirizzo in configurazione), oppure un campo comune sul ticket. Collegata
  a DEC-59 proposta nel report e ad A-06 (nessun seed di operatori/supervisori in produzione: senza utenti con ruolo PA il messaggio
  non ha destinatari).
- DD-2 (presa in carico): la timeline nasconde l'assegnazione al proprietario (Internal). La notifica la rivela in forma generica.
  Confermare che e' voluto, oppure rendere Public un evento "presa in carico" distinto dall'assegnazione interna.
- DD-3 (follower): i follower dovrebbero ricevere approvata/chiusa? Prima va deciso il codice di tracciamento nelle notifiche di stato
  che oggi i follower ricevono.
- DD-4 (riassegnazione): avvisare il cittadino a ogni cambio di responsabile? Oggi no (evita rumore).
- DD-5 (lingua delle notifiche esistenti): `TicketStatusChangedNotification` e `TicketAssignedNotification` restano senza lingua del
  destinatario (A-22); la nuova Action e' pronta per applicarla.
- DD-6 (consegna reale): nessuna prova di recapito esterno (postfix solo locale, nessun SMTP/SPF/DKIM) e nessun worker di coda
  garantito (D-15 e A-07): fuori dal perimetro di questa story.

## Matrice evento x destinatario x canale x template

| Evento (origine) | Destinatario | Canale | Template (chiavi `fixcity::ticket_notification.*`, classe) | Esito |
|---|---|---|---|---|
| Segnalazione ricevuta (`TicketCreatedEvent`) | proprietario | database, mail | `lifecycle.received.*`, `TicketLifecycleNotification` | vedi Esito |
| Segnalazione ricevuta (`TicketCreatedEvent`) | utenti con ruolo PA (escluso proprietario) | database, mail | `staff.received.*`, `TicketReceivedStaffNotification` | vedi Esito |
| Approvata (`approved`, Public) | proprietario | database, mail | `lifecycle.approved.*` | vedi Esito |
| Scartata (`rejected`, AuthorOnly) con motivo | proprietario | database, mail | `lifecycle.rejected.*` | vedi Esito |
| Presa in carico (`assignment`, prima assegnazione) | proprietario | database, mail | `lifecycle.assigned.*` | vedi Esito |
| Cambio di stato verso stato visibile (`status_change`, Public) | proprietario e follower | database, mail | gia' esistente: `fixcity::ticket.notification.*`, `TicketStatusChangedNotification` | gia' coperto, riverificato |
| Chiusa (`closed`, Public) | proprietario | database, mail | `lifecycle.closed.*` | vedi Esito |
| Allegato aggiunto (`media_added`) | nessuno | nessuno | non necessario (D-7) | vedi Esito |
| Assegnazione all'operatore | operatore assegnato | database, mail | gia' esistente: `TicketAssignedNotification` | invariato |

Mai: eventi su segnalazioni non pubbliche a destinatari diversi dal proprietario; codice di tracciamento, email o `owner_id` nei
messaggi alla PA; nome dell'operatore o id del responsabile nei messaggi al cittadino.

## Criteri di accettazione

1. `ApproveTicketAction` su una segnalazione `pending` invia al proprietario esattamente una `TicketLifecycleNotification` di tipo
   `approved` e nessuna `TicketStatusChangedNotification`.
2. `RejectTicketAction` invia al proprietario una notifica `rejected` il cui testo contiene il motivo; un follower non riceve nulla.
3. La prima `AssignTicketAction` con un responsabile invia al proprietario una notifica `assigned` che non contiene id ne' nome del
   responsabile; una seconda assegnazione e la rimozione dell'assegnazione non inviano altro al proprietario; l'operatore assegnato
   continua a ricevere `TicketAssignedNotification`.
4. `CloseTicketAction` invia al proprietario una sola notifica `closed`.
5. Un `status_change` pubblico continua a produrre una sola `TicketStatusChangedNotification` per destinatario e nessuna
   `TicketLifecycleNotification`.
6. Un `media_added` non invia nulla.
7. `CreateTicketAction` invia al proprietario una notifica `received` e a ciascun utente con ruolo PA (diverso dal proprietario) una
   `TicketReceivedStaffNotification`, una volta sola ciascuno; un cittadino senza ruolo PA non riceve il messaggio alla PA.
8. Il messaggio alla PA ha come dati solo titolo, testo, id della segnalazione e URL del back office (insieme esatto di chiavi).
9. Un motivo con HTML e link Markdown non produce `<script`, `<img` ne' link esterni nell'HTML della mail.
10. La notifica porta la lingua del destinatario (`users.lang`); senza lingua valida usa la lingua corrente.
11. Le chiavi nuove esistono in it, en, de, es con gli stessi segnaposto; nessun valore vuoto.
12. Le due classi nuove sono `ShouldQueueAfterCommit`, 3 tentativi, backoff 60 e 300 s; canale `mail` solo con email verificata e
    preferenza attiva.
13. Il feed del cittadino elenca le notifiche nuove con l'etichetta dell'evento, `unread_count` coincide con le righe elencate,
    "segna come letta" funziona, e le notifiche di un altro utente o della PA non compaiono.
14. Ticket senza codice di tracciamento o senza proprietario: nessuna eccezione, nessun invio al proprietario (log di avviso come
    l'esistente).
15. I test esistenti elencati nel fatto 10 restano verdi.
16. PHPStan (config unica, senza `--level`) a zero sui file PHP creati o modificati; Pint solo sui miei file.

## Test

Esecuzione solo con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso relativo a laravel/>`.

- `Modules/Fixcity/tests/Feature/Actions/TicketLifecycleNotificationTest.php` (criteri 1-6, 9, 10, 12, 14)
- `Modules/Fixcity/tests/Feature/Actions/TicketReceivedNotificationTest.php` (criteri 7, 8, 14)
- `Modules/Fixcity/tests/Feature/Actions/PersonalAreaLifecycleNotificationsTest.php` (criterio 13)
- `Modules/Fixcity/tests/Unit/Notifications/TicketLifecycleNotificationLangTest.php` (criterio 11)
- regressione: i test esistenti del fatto 10

## Owned file scope

Elenco esatto dei file che tocco (C creato, M modificato). Se ne tocco altri, li aggiungo qui.

- C `laravel/Modules/Fixcity/docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md`
- C `laravel/Modules/Fixcity/app/Enums/TicketLifecycleEventEnum.php`
- C `laravel/Modules/Fixcity/app/Notifications/TicketLifecycleNotification.php`
- C `laravel/Modules/Fixcity/app/Notifications/TicketReceivedStaffNotification.php`
- C `laravel/Modules/Fixcity/app/Notifications/Concerns/DeliversByDatabaseAndVerifiedMail.php`
- C `laravel/Modules/Fixcity/app/Actions/NotifyTicketOwnerOfLifecycleEventAction.php`
- C `laravel/Modules/Fixcity/app/Actions/NotifyTicketReceivedAction.php`
- C `laravel/Modules/Fixcity/app/Actions/SendTicketNotificationAction.php` (invio con lingua del destinatario; errore di un invio registrato nel log con soli id e classi, non interrompe gli altri destinatari)
- C `laravel/Modules/Fixcity/app/Actions/ResolveNotificationLocaleAction.php`
- C `laravel/Modules/Fixcity/app/Actions/BuildTicketTrackUrlAction.php`
- C `laravel/Modules/Fixcity/app/Listeners/NotifyTicketReceivedListener.php`
- M `laravel/Modules/Fixcity/app/Observers/TicketActivityObserver.php`
- M `laravel/Modules/Fixcity/app/Providers/EventServiceProvider.php`
- M `laravel/Modules/Fixcity/app/Actions/BuildAuthenticatedUserTicketNotificationsQueryAction.php`
- M `laravel/Modules/Fixcity/app/Actions/BuildPersonalAreaNotificationsDataAction.php`
- M `laravel/Modules/Fixcity/app/Notifications/TicketStatusChangedNotification.php` (solo l'URL, via `BuildTicketTrackUrlAction`)
- M `laravel/Modules/Fixcity/lang/it/ticket_notification.php`, `lang/en/ticket_notification.php`
- C `laravel/Modules/Fixcity/lang/de/ticket_notification.php`, `lang/es/ticket_notification.php`
- C i quattro file di test elencati sopra
- M `laravel/Modules/Fixcity/docs/bmad/decision-log.md` (una voce in coda)

Non toccati per regola: `Actions/ChangeStatus.php`, `Actions/AssignTicketAction.php`, `Providers/FixcityServiceProvider.php`, le pagine
legacy `tickets/[slug].blade.php` e `[id].blade.php`, `laravel/routes/web.php`, `bootstrap/*`, `Modules/Comment/**`, `Modules/Seo/**`, il tema.

## Fuori ambito

- A-02 (attore `user_id = null` in `ChangeStatus` e `AssignTicketAction`): DCP-281. Le notifiche non dipendono dall'attore.
- A-25 (migrazioni duplicate): nessuna migrazione in questa story.
- Consegna reale della posta e worker di coda: DD-6.

## Esito

Stato: completata nel perimetro dichiarato. Output reali (tutti MISURATI, eseguiti il 2026-10-26).

### Prove

- Baseline prima di toccare il codice (Feature/Actions + Unit/Notifications + CreateTicketConfirmationFlowTest + BuildTicketTimelineActionTest):
  `Tests: 1 skipped, 69 passed (316 assertions)`.
- Rosso dei quattro file nuovi prima del codice: `Tests: 18 failed, 3 passed (24 assertions)` (i 3 verdi sono guardie di regressione:
  `media_added` non notifica, ticket senza codice o proprietario non invia, `status_change` resta alla sua notifica).
- Verde dei quattro file nuovi: `Tests: 21 passed (265 assertions)`, 18.80 s.
- Regressione larga (Feature/Actions, Unit/Notifications, Unit/Actions, `PersonalAreaSettingsPagesTest`, `TicketPublicDetailPageTest`,
  `FixcityDemoSeederTest`): `Tests: 1 skipped, 226 passed (1417 assertions)`, 257.72 s. Lo skip e' quello di DCP-281 (attore di `ChangeStatus`).
- Ultima esecuzione, dopo Pint e dopo le correzioni di tipi ai test (nuovi + notifiche vicine: `TicketStatusNotificationTest`,
  `TicketAssignmentNotificationTest`, `PersonalAreaNotificationsActionsTest`, `NotificationRetryPolicyTest`):
  `Tests: 39 passed (331 assertions)`, 37.39 s.
- PHPStan (config unica, senza `--level`, da `laravel/`) su 15 file PHP dell'app piu' 4 file di test: `[OK] No errors`. `php -l` sui 4 file lang: ok.
- Pint solo sui miei file (elenco esplicito, mai cartelle ne' `--dirty`): ha toccato solo tre file nuovi (import e spazi); il diff dei cinque
  file esistenti modificati e' identico prima e dopo.
- Nessun emoji ne' trattino lungo nei file scritti. Nome della story validato con `md-filenames.py`.

Due errori trovati dall'esecuzione e corretti (non dalla lettura): il listener su `TicketCreatedEvent` non partiva in `$listen` per la
cache degli eventi (fatto 4), e un helper di test usava `App::withLocale`, che non esiste.

### Matrice: stato per evento

| Evento | Stato | Prova |
|---|---|---|
| Ricevuta al proprietario | FATTO e VERIFICATO | `TicketReceivedNotificationTest` (conferma, non duplicata) |
| Ricevuta alla PA | FATTO e VERIFICATO nel minimo sicuro; scoping per comune DA DECIDERE (DD-1) | stesso file: un avviso per utente PA, escluso il proprietario, insieme esatto di chiavi |
| Approvata | FATTO e VERIFICATO | `TicketLifecycleNotificationTest` (una sola, nessuna `TicketStatusChangedNotification`) |
| Scartata con motivo | FATTO e VERIFICATO | stesso file (motivo nel testo, follower esclusi, mail Markdown sicura) |
| Presa in carico | FATTO e VERIFICATO (solo la prima assegnazione); rivelare l'assegnazione al cittadino DA DECIDERE (DD-2) | stesso file (nessun id ne' nome del responsabile; l'assegnato continua a ricevere la sua) |
| Cambio di stato verso stato visibile | NON NECESSARIO: gia' coperto da `NotifyTicketSubscribersOfStatusChangeAction`; riverificato, nessun doppione | `TicketStatusNotificationTest` + guardia nel file nuovo |
| Chiusa | FATTO e VERIFICATO | `TicketLifecycleNotificationTest` |
| Allegato aggiunto | NON NECESSARIO (D-7) | guardia: nessun invio |
| Feed `/area-personale/notifiche` | FATTO e VERIFICATO | `PersonalAreaLifecycleNotificationsTest` (etichetta evento, `unread_count` coerente, "segna come letta" solo del proprietario, la notifica PA non compare) |
| Lingua del destinatario | FATTO e VERIFICATO per le notifiche nuove (`de` e fallback `it` per lingua sconosciuta); A-22 resta aperto per le due esistenti (DD-5) | test di lingua |

### Non verificato

- Nessuna posta reale verso caselle esterne: i test usano `MAIL_MAILER=array` e `Notification::fake()`. Recapito esterno non provato (DD-6).
- Nessun worker di coda eseguito: `ShouldQueueAfterCommit` parte in produzione solo con un worker (A-07). Provati la classe, i 3 tentativi e il backoff, non un processo reale.
- Con la cache degli eventi attiva ho provato la registrazione esplicita nei test (che girano con la cache presente nel checkout); non ho eseguito `event:cache` ne' `optimize`.
- Nessuna prova nel browser del feed: verificata la logica e i dati (la vista del tema non e' stata toccata).
- Nessun utente con ruolo `supervisor` o `admin` nel database di sviluppo (A-06): l'avviso alla PA e' stato provato con ruoli creati nei test.
- Coda dei follower, riassegnazioni e comune: decisioni aperte DD-1..DD-4.

### Osservazioni fuori perimetro (non corrette)

- A-22: `TicketStatusChangedNotification` e `TicketAssignedNotification` non portano la lingua del destinatario; i follower ricevono il codice di
  tracciamento nella notifica di stato (DD-3); la logica dei canali e' ripetuta in tre punti (due vecchie notifiche piu' il trait nuovo).
- `FixcityServiceProvider.php`, `AssignTicketAction.php`, `ChangeStatus.php` risultano modificati nel working tree da altre sessioni: non li ho toccati.
