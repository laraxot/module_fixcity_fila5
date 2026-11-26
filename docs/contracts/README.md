---
title: "Contratti"
type: index
tags: [contracts, actions, interfaces]
created: 2026-11-18
updated: 2026-11-26
module: Fixcity
---

# Contratti

Gli asset pubblici del modulo seguono il percorso dichiarato in [ADR del logo FixCity](../adr/0001-fixcity-public-logo-path.md); le Action e le route non devono introdurre un secondo percorso per lo stesso asset.

Cosa promette la superficie pubblica di questo pacchetto: le Action che scrivono dati, chiamano servizi esterni o hanno effetti collaterali, e le interfacce (`app/Contracts`). Regola: `Modules/Xot/docs/rules/action-behavioral-contract.md`; governance: `Modules/Xot/docs/adr/0012-behavioral-contract-governance.md`.

## Standard di un contratto

Un file per Action (o per superficie), nome in kebab-case della classe senza date ne' suffissi numerici (`create-ticket-action.md`). Frontmatter: `type: contract`, `action: "<FQCN con doppie barre>"`, `module`, `status`, `updated`. Sei sezioni `##`, in quest'ordine e senza altre: Input, Output, Invarianti, Effetti collaterali, Errori, Non fa.

- **Invarianti** e' una tabella `| ID | Regola | Group Pest |` con ID stabili `<AMBITO>-INV-<N>`. La colonna Group contiene l'ID se esiste un test con `->group('<ID>')`, altrimenti `— (non dimostrato...)`. Un ID non si riusa e non si rinumera: se una regola cambia significato si aggiunge un ID nuovo.
- Il comportamento di oggi si documenta com'e', con la dicitura "Limite noto" quando e' un difetto o una scelta aperta.
- Il docblock della classe contiene `@see docs/contracts/<file>.md` e `@contract docs/contracts/<file>.md`. Chi cambia il comportamento aggiorna il contratto nello stesso commit.
- Controllo del nome: `python3 -I bashscripts/tools/docs/md-filenames.py name <percorso>`. Controllo di struttura da `laravel/`: `vendor/bin/pest Modules/Xot/tests/Unit/Docs/ActionContractsAreCompleteTest.php` e `vendor/bin/pest Modules/Xot/tests/Unit/Docs/PackagesHaveContractsAndAdrTest.php`.

## Contratti

- [assign-ticket-action](assign-ticket-action.md): AssignTicketAction: assegna o toglie il responsabile (solo ruoli PA), con voce di cronologia e notifica dopo il commit.
- [build-public-ticket-detail-action](build-public-ticket-detail-action.md): BuildPublicTicketDetailAction: il solo costruttore dei campi del dettaglio pubblico `/tickets/{id}` (18 chiavi, codice di tracciamento solo al proprietario).
- [build-ticket-public-image-urls-action](build-ticket-public-image-urls-action.md): BuildTicketPublicImageUrlsAction: URL relativi delle foto, solo immagini, solo ticket pubblici o del proprietario.
- [can-view-ticket-media-action](can-view-ticket-media-action.md): CanViewTicketMediaAction: chi puo' aprire un allegato (foto per Policy::view, documenti solo al proprietario).
- [change-status](change-status.md): ChangeStatus: cambio di stato sotto lock, valida la matrice di transizione e scrive la voce di cronologia.
- [change-ticket-priority-action](change-ticket-priority-action.md): ChangeTicketPriorityAction: cambia la priorita' dopo `Gate::authorize('changePriority')` e scrive l'audit.
- [create-ticket-action](create-ticket-action.md): CreateTicketAction: unico punto di creazione di una segnalazione (codice, allegati, cronologia, evento dopo il commit).
- [delete-ticket-action](delete-ticket-action.md): DeleteTicketAction: soft delete con `deleted_by`, solo admin o `ticket.delete`.
- [geocode-ticket-address-action](geocode-ticket-address-action.md): GeocodeTicketAddressAction: reverse geocoding verso Nominatim con cache; oggi senza chiamanti di produzione.
- [get-public-ticket-by-code-action](get-public-ticket-by-code-action.md): GetPublicTicketByCodeAction: ricerca per codice di tracciamento con freno di 10 mancati al minuto per indirizzo (429).
- [mark-personal-area-notifications-as-read-action](mark-personal-area-notifications-as-read-action.md): MarkPersonalAreaNotificationsAsReadAction: segna come lette le notifiche di stato del cittadino, solo le sue.
- [notify-ticket-assignee-of-assignment-action](notify-ticket-assignee-of-assignment-action.md): NotifyTicketAssigneeOfAssignmentAction: avvisa l'operatore appena assegnato (database e, se verificato, mail).
- [notify-ticket-subscribers-of-status-change-action](notify-ticket-subscribers-of-status-change-action.md): NotifyTicketSubscribersOfStatusChangeAction: avvisa proprietario e follower dei cambi di stato pubblici, senza esporre il codice ai follower.
- [policy-registration](policy-registration.md): Registrazione automatica di `TicketPolicy` (nessun `Gate::policy()` a mano).
- [purge-deleted-accounts-action](purge-deleted-accounts-action.md): PurgeDeletedAccountsAction: cancellazione degli account eliminati oltre la retention, se senza segnalazioni aperte.
- [record-ticket-communication-action](record-ticket-communication-action.md): RecordTicketCommunicationAction: comunicazioni dell'operatore con un ufficio esterno (mail, sollecito, risposta ricevuta) dietro `communicate`.
- [redirect-ticket-track-code-middleware](redirect-ticket-track-code-middleware.md): RedirectTicketTrackCodeMiddleware: porta `/tickets/track/{code}` al modulo di ricerca con il codice in query.
- [send-accessibility-feedback-action](send-accessibility-feedback-action.md): SendAccessibilityFeedbackAction: mail di segnalazione di accessibilita' verso la casella dell'ente, con tetto di 5 all'ora.
- [send-ticket-to-ente-action](send-ticket-to-ente-action.md): SendTicketToEnteAction: mail minima all'ente con il solo numero del ticket, una volta per ticket.
- [set-ticket-subscription-action](set-ticket-subscription-action.md): SetTicketSubscriptionAction: segui/smetti di seguire un ticket visibile, idempotente.
- [sign-ticket-media-url-action](sign-ticket-media-url-action.md): SignTicketMediaUrlAction: firma HMAC a scadenza degli URL dei media per ticket non pubblici.
- [submit-citizen-ticket-rating-action](submit-citizen-ticket-rating-action.md): SubmitCitizenTicketRatingAction: voto 1-5 del proprietario su ticket risolto, una sola volta.
- [submit-ticket-once-action](submit-ticket-once-action.md): SubmitTicketOnceAction: invio idempotente della segnalazione per (proprietario, token) con lock.
- [ticket-policy](ticket-policy.md): TicketPolicy: chi puo' vedere, modificare, cancellare, assegnare, cambiare stato e priorita' di un ticket.
- [transition-ticket-status-action](transition-ticket-status-action.md): TransitionTicketStatusAction: primitiva di sola transizione; oggi nessun chiamante di produzione.

Altri file della cartella: `_template.md` (modello da copiare), `INDEX.md` e `index.md` (inventari generati, non scritti a mano).

## Stato di copertura

Misurato il 2026-11-26 leggendo le tabelle delle Invarianti e cercando `->group('<ID>')` nei test del pacchetto (nessun test e' stato eseguito: la prova e' la lettura del corpo del test, non l'esito).

| Contratto | Invarianti | Dimostrate | Non dimostrate |
|---|---|---|---|
| [assign-ticket-action](assign-ticket-action.md) | 6 | 2 | 4 |
| [build-public-ticket-detail-action](build-public-ticket-detail-action.md) | 8 | 8 | 0 |
| [build-ticket-public-image-urls-action](build-ticket-public-image-urls-action.md) | 5 | 3 | 2 |
| [can-view-ticket-media-action](can-view-ticket-media-action.md) | 4 | 2 | 2 |
| [change-status](change-status.md) | 8 | 6 | 2 |
| [change-ticket-priority-action](change-ticket-priority-action.md) | 4 | 1 | 3 |
| [create-ticket-action](create-ticket-action.md) | 8 | 6 | 2 |
| [delete-ticket-action](delete-ticket-action.md) | 4 | 2 | 2 |
| [geocode-ticket-address-action](geocode-ticket-address-action.md) | 7 | 5 | 2 |
| [get-public-ticket-by-code-action](get-public-ticket-by-code-action.md) | 6 | 5 | 1 |
| [mark-personal-area-notifications-as-read-action](mark-personal-area-notifications-as-read-action.md) | 5 | 3 | 2 |
| [notify-ticket-assignee-of-assignment-action](notify-ticket-assignee-of-assignment-action.md) | 6 | 4 | 2 |
| [notify-ticket-subscribers-of-status-change-action](notify-ticket-subscribers-of-status-change-action.md) | 8 | 6 | 2 |
| [policy-registration](policy-registration.md) | 2 | 1 | 1 |
| [purge-deleted-accounts-action](purge-deleted-accounts-action.md) | 7 | 6 | 1 |
| [record-ticket-communication-action](record-ticket-communication-action.md) | 6 | 0 | 6 |
| [redirect-ticket-track-code-middleware](redirect-ticket-track-code-middleware.md) | 5 | 3 | 2 |
| [send-accessibility-feedback-action](send-accessibility-feedback-action.md) | 7 | 5 | 2 |
| [send-ticket-to-ente-action](send-ticket-to-ente-action.md) | 8 | 6 | 2 |
| [set-ticket-subscription-action](set-ticket-subscription-action.md) | 4 | 2 | 2 |
| [sign-ticket-media-url-action](sign-ticket-media-url-action.md) | 4 | 2 | 2 |
| [submit-citizen-ticket-rating-action](submit-citizen-ticket-rating-action.md) | 7 | 4 | 3 |
| [submit-ticket-once-action](submit-ticket-once-action.md) | 5 | 4 | 1 |
| [ticket-policy](ticket-policy.md) | 11 | 7 | 4 |
| [transition-ticket-status-action](transition-ticket-status-action.md) | 2 | 0 | 2 |
| **Totale** | **147** | **93** | **54** |

Invarianti dimostrate: **93 su 147** (63%). Una invariante dimostrata ha un test che asserisce la regola; i test non sono stati rilanciati in questa passata (Pest scrive sul database di sviluppo), quindi "dimostrata" significa "esiste un test che la asserisce", non "il test passa oggi".

## Backlog contract

Action del pacchetto che scrivono o hanno effetti collaterali e non hanno ancora un contratto. Ordine di rischio (autorizzazione e stato prima, poi dati personali, poi il resto):

| Action | Motivo |
|---|---|
| ApproveTicketAction | Moderazione: `Gate::authorize('changeStatus')` (riga 23), motivo obbligatorio, `setStatus` sotto `LockTicketForStatusChangeAction` (riga 43); non passa da `ChangeStatus`. |
| RejectTicketAction | Riporta il ticket a `pending` con motivo obbligatorio (righe 25, 28 e 47), stesso schema di Approve. |
| CloseTicketAction | Chiusura formale dopo la risoluzione (righe 23 e 46). |
| ConfirmTicketResolutionAction | Decisione del cittadino proprietario (conferma o contesta), unica con `__invoke` di route; autorizza per `owner_id` e non per Policy; due messaggi di validazione in italiano nel codice (righe 45 e 50). |
| ResolveTicketAction | Segna `resolved` con motivo obbligatorio. |
| RecordTicketActivityAction | Unica scrittura su `ticket_activities` (riga 31): da qui nasce ogni notifica del ciclo di vita tramite l'osservatore. |
| LockTicketForStatusChangeAction | Transazione con `lockForUpdate` da cui passano tutte le transizioni di stato. |
| FollowTicketAction | POST `/{lang}/tickets/{ticket}/follow`: il contratto di `SetTicketSubscriptionAction` copre la scrittura, non la route (404 per ticket non visibile). |
| NotifyTicketOwnerOfLifecycleEventAction | Avviso al proprietario per approvazione, scarto, presa in carico, chiusura. |
| NotifyTicketReceivedAction | Conferma al proprietario e avviso a tutti i ruoli PA; lo scoping per comune e' una decisione aperta (DD-1, story DCP-284). |
| SetFixcityEmailNotificationPreferenceAction | Scrive la preferenza di posta sul profilo in transazione (riga 17): dato personale e canale di notifica. |
| ResolveEnteTicketEmailAction | Decide la casella di ricezione dell'ente senza ripiegare sui contatti pubblici: e' la sorgente di `TICKET-ENTE-MAIL-INV-1`. |
| AllocateTicketCodeAction | Genera il codice di tracciamento (capability token); l'unicita' e' garantita dall'indice su `tickets.code`. |
| ListAssignableTicketOperatorsAction | Sola lettura: elenca gli utenti con ruolo PA, stessa lista di `BasePolicy::PA_ROLES`. |
| BuildTicketPublicDetailsPayloadAction | Secondo costruttore di payload pubblico (tracciamento e popup della mappa), non controlla la visibilita' (vedi ADR 0003, Stato attuale). |
| EnsureTicketCitizenRatingDefinitionAction | `firstOrCreate` della definizione di rating (riga 22). |
| GenerateTicketAction, GenerateTicketsAction, GenerateClusterTestJsonAction | Generatori di dati di prova (factory e `file_put_contents`): non sono flussi di produzione. |
| BuildAuthenticatedUserTicketNotificationsQueryAction, GetTicketKpiAggregateAction, ResolveNotificationLocaleAction | Segnalate dallo script di audit per la presenza di `DB::` o `Notification`, ma sono letture o pure risoluzioni: rischio basso. |

Candidati a una revisione di merito, non a un contratto: `TransitionTicketStatusAction` e `GeocodeTicketAddressAction` hanno un contratto ma nessun chiamante di produzione (vedi "Limite noto" nei rispettivi file).

## Backlog test

Invarianti senza group (54). Per tutte vale: nessun test del pacchetto porta questo ID; il perche' sta nella colonna Group del contratto.

| Contratto | Invarianti non dimostrate |
|---|---|
| assign-ticket-action | TICKET-ASSIGN-INV-1, TICKET-ASSIGN-INV-3, TICKET-ASSIGN-INV-4, TICKET-ASSIGN-INV-6 |
| build-ticket-public-image-urls-action | TICKET-MEDIA-URLS-INV-4, TICKET-MEDIA-URLS-INV-5 |
| can-view-ticket-media-action | TICKET-MEDIA-ACCESS-INV-1, TICKET-MEDIA-ACCESS-INV-4 |
| change-status | TICKET-STATUS-CHANGE-INV-4, TICKET-STATUS-CHANGE-INV-8 |
| change-ticket-priority-action | TICKET-PRIORITY-INV-1, TICKET-PRIORITY-INV-2, TICKET-PRIORITY-INV-3 |
| create-ticket-action | FIXCITY-CREATE-INV-2, FIXCITY-CREATE-INV-8 |
| delete-ticket-action | TICKET-DELETE-INV-1, TICKET-DELETE-INV-4 |
| geocode-ticket-address-action | TICKET-GEOCODE-INV-6, TICKET-GEOCODE-INV-7 |
| get-public-ticket-by-code-action | TICKET-TRACK-LOOKUP-INV-6 |
| mark-personal-area-notifications-as-read-action | PERSONAL-NOTIF-READ-INV-3, PERSONAL-NOTIF-READ-INV-5 |
| notify-ticket-assignee-of-assignment-action | TICKET-NOTIFY-ASSIGN-INV-3, TICKET-NOTIFY-ASSIGN-INV-5 |
| notify-ticket-subscribers-of-status-change-action | TICKET-NOTIFY-STATUS-INV-2, TICKET-NOTIFY-STATUS-INV-7 |
| policy-registration | TICKET-POLICY-REG-INV-1 |
| purge-deleted-accounts-action | PRIVACY-PURGE-INV-7 |
| record-ticket-communication-action | TICKET-COMM-INV-1, TICKET-COMM-INV-2, TICKET-COMM-INV-3, TICKET-COMM-INV-4, TICKET-COMM-INV-5, TICKET-COMM-INV-6 |
| redirect-ticket-track-code-middleware | TRACK-REDIRECT-INV-3, TRACK-REDIRECT-INV-5 |
| send-accessibility-feedback-action | A11Y-FEEDBACK-INV-7, A11Y-FEEDBACK-INV-6 |
| send-ticket-to-ente-action | TICKET-ENTE-MAIL-INV-7, TICKET-ENTE-MAIL-INV-8 |
| set-ticket-subscription-action | TICKET-FOLLOW-INV-3, TICKET-FOLLOW-INV-4 |
| sign-ticket-media-url-action | TICKET-MEDIA-SIGN-INV-3, TICKET-MEDIA-SIGN-INV-4 |
| submit-citizen-ticket-rating-action | TICKET-RATING-INV-2, TICKET-RATING-INV-4, TICKET-RATING-INV-6 |
| submit-ticket-once-action | TICKET-SUBMIT-ONCE-INV-3 |
| ticket-policy | TICKET-POLICY-INV-5, TICKET-POLICY-INV-7, TICKET-POLICY-INV-10, TICKET-POLICY-INV-11 |
| transition-ticket-status-action | TICKET-TRANSITION-INV-1, TICKET-TRANSITION-INV-2 |

Test da scrivere per primi, in ordine di rischio:

1. `TICKET-ASSIGN-INV-1`, `TICKET-PRIORITY-INV-1`, `TICKET-DELETE-INV-1`, `TICKET-COMM-INV-1`: chiamare l'Action direttamente con un utente privo dell'abilita' e aspettarsi `AuthorizationException`. Oggi la Policy e' provata a livello di classe (`TicketPolicyTest`) e il pulsante nascosto in Filament, non il rifiuto dell'Action.
2. `TICKET-COMM-INV-1..6`: nessun test del pacchetto esercita `RecordTicketCommunicationAction`.
3. `TICKET-ASSIGN-INV-6`: `tests/Feature/Actions/TicketActivityActorUuidTest.php` ("records the UUID of the acting operator when a ticket is assigned") assegna a un utente senza ruolo PA, che `TICKET-ASSIGN-INV-2` oggi respinge: il test va riallineato (assegnare a un `operator`).
4. `TICKET-TRACK-LOOKUP-INV-6`: `tests/Unit/TicketTrackPageJsonGuardTest.php` (riga 32) pretende `["throttle:10,1"]` come unica voce di `middleware`, ma `config/local/fixcity/database/content/pages/tickets/track.json` (riga 21) ne ha due (`throttle:10,1` e `RedirectTicketTrackCodeMiddleware`) come la copia del modulo (riga 16): o il test o i file vanno corretti.
5. `TICKET-MEDIA-SIGN-INV-3` e `-4`: firma scaduta, `expires` non numerico, firma su un altro percorso.
6. `TICKET-NOTIFY-STATUS-INV-7`, `TICKET-NOTIFY-ASSIGN-INV-5`: eccezione nell'accodamento catturata e registrata senza dati personali.
7. `TICKET-RATING-INV-2`, `-4`, `-6`: utente non autenticato, stato non risolto e concorrenza sul voto, provati solo indirettamente dal widget.
8. `PRIVACY-PURGE-INV-7`: spariscono anche notifiche e collegamenti Google dell'account cancellato.
