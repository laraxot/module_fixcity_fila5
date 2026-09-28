---
title: "Mappa percorsi utente — Fixcity"
type: concept
module: Fixcity
confidence: high
created: 2026-09-26
updated: 2026-09-26
qmd: "user journey percorso anon cittadino operatore supervisore admin sistema cosa dovrebbe vedere cosa vede cosa puo fare gap"
tags: [journey, ux, actor, persona, gap-analysis, auth]
issues:
  - "https://github.com/laraxot/base_fixcity_fila5/issues"
discussions:
  - "https://github.com/laraxot/base_fixcity_fila5/discussions"
related:
  - actor-flow-map.md
  - ticket-workflow-state-machine.md
  - ../../bmad/actor-flows.md
  - ../../bmad/workflows/README.md
  - ../../bmad/workflows/actor-citizen.md
---

# Mappa percorsi utente — Fixcity

**Scopo:** per ogni tipo di utente, dal momento in cui apre il sito: cosa
**dovrebbe** vedere, cosa **vede** oggi, cosa **può fare**, cosa è **fatto**,
cosa **manca**. Fonte codice (settembre 2026), non story optimistiche.

Leggenda: ✅ fatto · ⚠️ parziale · ❌ mancante · 🐛 bug/demo.

**Non confondere** “esiste una Blade/Action” con “flusso verificato in browser/Pest”.

---

## 0. Attori

| Codice | Chi | Entry tipica |
|---|---|---|
| G | Guest (non loggato) | `/`, `/segnalazioni`, `/tickets/create` |
| Cu | Cittadino autenticato non verificato | `/auth/verify`, area personale |
| C | Cittadino autenticato verificato | area personale, wizard |
| O | Operatore PA (`operator`) | pannello Filament Fixcity |
| S | Supervisore (`supervisor`) | stesso pannello, KPI/SLA |
| A | Admin (`admin`) | pannello + config tenant |
| Y | Sistema | eventi, code, Notify |

Ruoli PA: `BasePolicy::PA_ROLES = operator|supervisor|admin`.

---

## 1. Guest — apre il sito

### Narrativa attesa

1. Apre `/` (o `/it`) → riconosce il Comune, CTA “Segnala”, link mappa/elenco,
   login/registrazione, privacy/accessibilità.
2. Esplora segnalazioni pubbliche (mappa/lista) senza dati privati.
3. Per creare una segnalazione deve autenticarsi; il redirect conserva la
   destinazione di ritorno. Per il tracking anonimo inserisce il codice ricevuto.
4. Non entra in backoffice né vede ticket privati altrui.

### Tabella percorsi

| Percorso | Dovrebbe vedere | Vede oggi | Può fare | Fatto | Manca |
|---|---|---|---|---|---|
| Home `/` | Landing FixCity che chiarisce il servizio, apre la mappa/lista pubblica e offre la CTA di segnalazione | CMS `home` (“Elenco segnalazioni”) con griglia ticket, mappa live e CTA; `/it/` risponde 200 | Esplorare le segnalazioni e avviare la CTA; creazione richiede login | ✅ codice + browser route 200 | Verificare redirect/ritorno CTA con account e accessibilità della landing |
| Header | Logo, Segnalazioni, Come funziona, Accedi | Header demo “Il mio Comune”, “Accedi area personale” spesso `#` | Click su voci presenti | ⚠️ markup | Route reali auth/segnalazioni ovunque |
| `/segnalazioni` | Mappa e lista live, filtri coerenti, empty state e paginazione | Il CMS usa `TicketLayoutViewModel`: elenco/query per ruolo e owner, mappa da `/api/tickets/geojson`, aggregati filtri dallo stesso GeoJSON live; mostra 20 risultati per pagina | Filtrare per tipo/stato, passare da mappa a elenco e navigare le pagine | ✅ Feature test + browser 320/768/1.440, click reale tab, zero overflow/JS/resource error | Test browser interattivo dei filtri su dati non vuoti e accessibilità |
| Popup dettaglio API `/api/ticket-details/{id}` | id, titolo, stato, code | `{id,title,description,images,status,slug}`; `code` capability solo all’owner autenticato | Leggere JSON | ✅ payload | Popup FO wired; token capability non pubblico |
| Submit (auth) | Conferma + codice | `CreateTicketAction` alloca code + flash; confirmation legge bag CMS | Vedere codice e link track | ⚠️ | Pest/browser (G-01 DB); `?code=` su redirect widget (lock) |
| Traccia per codice | Form → stato e timeline pubblica | Folio `/tickets/track`, lookup capability con limite 10/minuto; mostra solo eventi `status_change` pubblici. Validazione nativa e messaggio ARIA | Il guest usa il codice ricevuto; l'utente autenticato può seguire link `ticket_id` con `TicketPolicy::view` | ✅ Feature test per codice, timeline, lookup owner per ID e guest negato; suite modulo SQLite verde | Screenshot browser, rate-limit sotto carico e revisione redazione dati |
| Login `/auth/login` | Form login localizzato | `/it/auth/login` 200 con form; precedentemente catalogo IT vuoto causava 500, ora corretto | Tentare login | ✅ browser e Feature test guest form | Submit credenziali e errori accessibili |
| Register | Registrazione + verifica email | `/it/auth/register` 200 con form | Registrarsi | ✅ browser rendering | Flusso submit/verify end-to-end |
| Backoffice URL | Redirect login, zero leak | Middleware Filament/auth | — | ⚠️ da smoke | Test guest su `/admin` |

### Cosa il guest NON deve poter fare

Creare ticket privati altrui, vedere email/telefono altrui, accedere Filament,
modificare stati. La story 007 stabilisce l'autenticazione obbligatoria: alias
la pagina CMS `tickets.create` richiede auth; redirect guest, destinazione di
ritorno e mancata persistenza vanno ancora provati insieme nel flusso runtime.

---

## 2. Cittadino non verificato (email)

| Percorso | Dovrebbe | Vede / può | Fatto | Manca |
|---|---|---|---|---|
| Dopo register | Banner “verifica email”, link resend | `auth/verify` presente | ⚠️ | UX su create/area-personale se `verified` blocca |
| Wizard / pratiche | Blocco chiaro o solo lettura | Dipende middleware pagina (`dashboard` ha `auth+verified`) | ⚠️ | Matrice pagine che richiedono `verified` |
| Logout / profilo minimo | Uscire, aggiornare profilo base | Auth + `area-personale/profile-edit` | ⚠️ | Preferenze notifiche |

---

## 3. Cittadino autenticato verificato

### Narrativa attesa

Home → area personale → ultime pratiche → nuova segnalazione (privacy→dati→
posizione→riepilogo) → conferma con codice → tracking → commenti → rating a
chiusura. Vede **solo i propri** ticket (+ pubblici consentiti).

### Tabella percorsi

| Percorso | Dovrebbe vedere | Vede oggi | Può fare | Fatto | Manca |
|---|---|---|---|---|---|
| Area personale | Menu pratiche, seguite, notifiche, profilo, servizi | Pagine Folio `area-personale/*` | Aprire le sezioni | ⚠️ | Browser smoke e link globali di navigazione |
| Pratiche `/area-personale/pratiche` | Lista mie segnalazioni + stato + track | Lista owner da `BuildAuthenticatedUserTicketsQueryAction` + CTA create/seguite | Vedere proprie pratiche | ⚠️ | Pest ownership; link dettaglio completo |
| Seguite `/area-personale/seguite` | Lista follower personale + stato leggibile + tracking | Query limitata all'utente e a ticket pubblici o posseduti; link per ID autorizzato, senza capability code; stato vuoto e conteggio | Ritrovare segnalazioni seguite senza ricevere il segreto bearer | ✅ Query/notifica/privacy test Fixcity passano su SQLite | Screenshot responsive/accessibility e smoke con DB MySQL autorizzato |
| Conferma | Codice + link tracking | Flash session + CMS bag override (page > block) | Vedere codice se create via Action | ⚠️ | Browser smoke |
| Assegna (PA) | Select → `responsible_id` + audit + conferma | `AssignTicketAction` atomica + Activity `assignment` + notifica database al responsabile dopo commit | Assegnare e avvisare il responsabile senza esporre note al FO | ✅ suite Fixcity SQLite: 334 test / 1.377 asserzioni; test recipient/transazione | Verificare link resource e feedback nel browser/staging |
| Dettaglio mio ticket | Stato, mappa, allegati, timeline, commenti | CMS `tickets.view` monta `Ticket\\ViewWidget`; owner privato è ammesso dalla visibilità FO | Aprire dettaglio se owner | ⚠️ accesso previsto nel codice; test browser pending | Validare isolamento con due account; completare mappa/allegati/commenti e timeline |
| Timeline | Storico pubblico | `BuildTicketTimelineAction` filtra `status_change` pubblici; tracking mostra i risultati e l'empty state | Consultare aggiornamenti senza note interne | ⚠️ UI nel tree, Pest/browser pending | Prova positiva/negativa e accessibilità |
| Rating a chiusura | Stelle + conferma | Dettaglio CMS monta il prompt solo al proprietario su ticket risolto/chiuso; salva `RatingMorph` | Inviare una valutazione 1–5 una sola volta | ✅ Feature test: visibilità owner, diniego altro utente/pre-risoluzione, persistenza e conferma | Smoke browser/tastiera, race doppio invio e aggregato in dashboard PA |
| Follow/notifiche | Opt-in aggiornamenti | Dettaglio FO espone follow/unfollow; notifiche stato/assegnazione post-commit; link follower usa ID e Gate, mai capability code | Iscriversi, ritrovare, disiscriversi e aprire tracking dopo login | ✅ Feature test Action, dispatch e link senza codice passano in SQLite | Preferenze per canale, email/push e smoke multiutente MySQL |
| Isolamento | Deny ticket altrui | Policy e `isOwnedByAuthenticatedUser()` usano `owner_id`; i campi audit non concedono accesso | — | ✅ test policy/Feature nella suite SQLite; browser two-account pending | Smoke browser/staging con due account |

---

## 4. Operatore PA

### Narrativa attesa

Login BO → dashboard coda → lista filtrata → dettaglio → assegna
(`responsible_id`) → cambia stato (Enum) con motivazione → activity/notifica →
chiusura. Solo ticket autorizzati.

### Tabella percorsi

| Percorso | Dovrebbe | Vede / può | Fatto | Manca |
|---|---|---|---|---|
| Login pannello | Solo ruoli PA | Filament + `isPaOperator` | ⚠️ | Smoke ruolo errato |
| Dashboard | KPI, backlog, SLA | `XotBaseDashboard` + view modulo | ⚠️ base | Widget KPI/SLA live |
| Lista ticket | Filtri stato/priorità/tipo, assegnatario, range data, assegnati/non assegnati, search ed export | `TicketResource` list con filtri reali | ⚠️ filtri implementati; query/browser da verificare | Filtri avanzati ulteriori e Pest/browser su MariaDB test |
| Dettaglio | Dati, mappa, allegati, azioni | `ViewTicket` + assign + `ChangeStatus` | ✅ | Note interne dedicate |
| Assegna | Select operatore → `responsible_id` | `AssignTicketAction` + Activity assignment + notifica database post-commit | Assegnare | ✅ test suite SQLite | Smoke browser/staging |
| Cambia stato | Solo transition Enum + reason | Filament → `Actions\ChangeStatus` + `RecordTicketActivityAction` | ✅ status+activity | Smoke browser e-mail/canali esterni |
| Bulk / export | Multi-select | Parziale su resource | ⚠️ | Bulk transition completa |
| Commenti | Thread con cittadino | Relation manager se presente | ⚠️ | Moderazione abusi |

---

## 5. Supervisore

| Percorso | Dovrebbe | Vede / può | Fatto | Manca |
|---|---|---|---|---|
| Coda ente | Vista ampia + fuori SLA | Stesso BO; KPI actions esistono | ⚠️ | Distinzione UI operator vs supervisor |
| Riassegna | Override assign | Stessa Action assign | ✅ capability | Prove ruolo |
| Audit | Cronologia immutabile | Activity su status change; assign senza activity | ⚠️ | Audit append-only; activity su assign |
| Report / team | Performance operatori, reparti | Actions SLA/KPI | ⚠️ | UI report; departments |
| Escalation | Regole automatiche | — | ❌ | automation_rules |

---

## 6. Amministratore

| Percorso | Dovrebbe | Vede / può | Fatto | Manca |
|---|---|---|---|---|
| Dashboard globale | Salute tenant, code, export | Dashboard modulo | ⚠️ | Health queue/notify |
| Ruoli/utenti | CRUD operatori | User module Filament | ⚠️ | Matrice permessi documentata+test |
| Categorie / stati | Config senza deploy | Stati in `TicketStatusEnum` (SSoT codice) | ⚠️ Enum | UI admin categorie/SLA |
| Backup / release | Runbook + restore | Docs release BMAD | ⚠️ docs | Prove staging |
| Super-admin | `XotBasePolicy::before` | Bypass capability | ✅ codice | Audit obbligatorio su bypass |

---

## 7. Sistema

| Percorso | Dovrebbe | Oggi | Fatto | Manca |
|---|---|---|---|---|
| Create → evento | `TicketCreatedEvent` | Dispatch in `CreateTicketAction` | ✅ | Listener Notify verificato |
| Status → activity | Record + timeline | `ChangeStatus` scrive Activity; track renderizza solo `status_change` pubblici | ⚠️ | Test runtime ed E2E |
| Assign → activity/notify | Log + mail operatore | Solo save `responsible_id` | ❌ | Activity + Notify |
| Geo pubblico | GeoJSON filtrato | Actions GeoJSON | ✅ | FO non demo |
| Code / retry | Failure osservabile | QueueableAction disponibile | ⚠️ | Dead-letter UI admin |
| Rating aggregato | Metriche | Actions rating aggregate | ⚠️ | Widget |

---

## 8. Matrice “apre il sito → prima cosa”

| Attore | Prima schermata attesa | Prima schermata oggi | Prima azione utile possibile |
|---|---|---|---|
| Guest | Home + CTA Segnala | Home Sixteen; lista `/segnalazioni` live via CMS, mappa e filtri dal DB | Aprire login/registrazione o il wizard protetto |
| Cittadino | Area personale / mie pratiche | Lista caricata via query owner; dashboard Genesis resta placeholder | Vedere proprie pratiche, avviare create |
| Operatore | Dashboard BO ticket | Dashboard Filament Fixcity | Aprire lista ticket |
| Supervisore | Dashboard + SLA | Come operatore | Filtrare coda / riassegnare |
| Admin | Dashboard + config | Come PA + User admin | Gestire utenti/ticket |
| Sistema | — | Eventi su create/status | Propagare (parziale) |

---

## 9. Priorità gap (prodotto)

1. **Smoke browser visuale** a 320/768/1440 su wizard, lista/mappa, area cittadino e Filament PA; conservare screenshot/report nel tema.
2. **DB MySQL autorizzato/staging** per smoke end-to-end cittadino→PA→chiusura e prova isolamento con due account; l'intera suite Fixcity passa già sul test SQLite.
3. Collegare e provare commenti e rating nel dettaglio cittadino, incluse policy e transizioni `resolved/closed`.
4. Implementare preferenze notifiche e canali email/push; il canale in-app per stato/assegnazione è già testato.
5. Chiudere backup/restore, monitoraggio, rate-limit sotto carico e checklist release.

---

## 10. Collegamenti

- Flussi tecnici: [actor-flow-map.md](actor-flow-map.md)
- Workflow BMAD attori: [../../bmad/workflows/](../../bmad/workflows/README.md)
- State machine: [ticket-workflow-state-machine.md](ticket-workflow-state-machine.md)
- Sintesi attori: [../../bmad/actor-flows.md](../../bmad/actor-flows.md)
