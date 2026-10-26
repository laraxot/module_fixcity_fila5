---
id: dcp-287
title: "DCP-287: dettaglio per il proprietario, codice di tracciamento ai follower e ritorno dopo il login"
type: story
epic: go-live-front-office-segnalazioni
status: done
priority: P0
owner: module:Fixcity
module: Fixcity
created: 2026-10-26
updated: 2026-10-26
qmd: "dcp 287 dettaglio proprietario segnalazione non pubblica location FO-6-43 FO-2-36 FO-2-17 FO-2-18 follower codice di tracciamento capability FO-2-12 accedi per seguire ritorno login redirect BuildPublicTicketDetailAction BuildPersonalAreaTicketRowAction"
related: [dcp-104, dcp-110, dcp-124, dcp-148, dcp-149, dcp-150, dcp-284, dcp-286, dcp-288]
tags: [front-office, privacy, proprietario, follower, login, test, bmad]
---

# DCP-287: dettaglio per il proprietario, codice di tracciamento ai follower e ritorno dopo il login

## Fase BMAD

Dev (TDD): story prima, poi test rosso, poi codice, poi test verde e misura nel browser. Origine: audit front office del 2026-10-26,
gruppo FO-2 (sezioni 2 e 4) e FO-6-43. Ogni riscontro dell'audit e' stato riverificato: sotto, ogni fatto e' marcato MISURATO
(comando o browser eseguito qui, con l'output) o DEDOTTO (letto dal codice, non eseguito).

Ambito, in ordine: (1) il proprietario apre la propria segnalazione non pubblica (FO-6-43, FO-2-36 e la parte "visibilita'" di
FO-2-17); (2) il follower non vede il codice di tracciamento altrui (FO-2-18); (3) "Accedi per seguire" riporta alla segnalazione
(FO-2-12); (4) riscontri S e M di FO-2 che sono difetti: FO-2-32 (etichette di stato) e FO-2-16 (testi della cronologia e dei
prossimi passi); le funzioni nuove grandi (azioni del proprietario, FO-2-17 restante) hanno qui solo il disegno e il piano.

Fuori ambito (altri agenti): conferma e wizard (DCP-286), header/footer/ricerca (DCP-288, DCP-289), commenti (sessione f5),
invio delle notifiche (DCP-284, completata).

## Stato

done per tutto cio' che e' codice (P1, P2, P3, FO-2-32, FO-2-16 nei testi). Restano DA DECIDERE e il disegno delle azioni del
proprietario (sezioni sotto). Nessun commit, nessuna migrazione, nessuna dipendenza nuova.

## Fatti misurati prima di toccare

Istanza di prova `http://127.0.0.1:8091` (DB usa-e-getta, fixture), accessi con Playwright (`scratchpad/w3b/m1-owner.mjs`, `m2-follower.mjs`).

1. **Il proprietario di una segnalazione non pubblica SENZA posizione riceve 404 (MISURATO).** Giulia (proprietaria della 13, stato
   `pending`, `location` NULL nel DB di prova) apre `/it/tickets/13`: HTTP 404 "Pagina non trovata". Il link "Vedi il dettaglio" dell'area
   personale porta proprio li' (`detailLinks` di `m2-follower.json` contiene `/it/tickets/13`).
2. **Con la posizione il proprietario entra gia' (MISURATO).** Paolo (proprietario della 17, `pending`, con posizione) apre
   `/it/tickets/17`: 200, titolo "Voragine davanti alla scuola Dante". Quindi "non pubblica" NON e' la causa: lo e' la posizione.
3. **Gli altri restano fuori (MISURATO).** Paolo su `/it/tickets/14` (pending di un altro utente): 404; ospite su `/it/tickets/13` e
   `/it/tickets/17`: 404; Giulia su `/it/tickets/17` (pending di Paolo): 404. Le pubbliche (1 e 5) rispondono 200 a tutti.
4. **Causa (DEDOTTO dal codice, coerente con 1-3).** `BuildPublicTicketDetailAction::execute` cerca il ticket con
   `BuildPublicTicketsQueryAction`, che impone sempre `whereNotNull('location')` (serve a mappa ed elenco). Il ramo "o e' il mio" di
   quella query viene dopo il filtro sulla posizione, quindi non puo' salvare un ticket senza posizione. La regola "pubblico oppure
   del proprietario" esiste gia' due volte (`Ticket::isVisibleOnPublicFrontoffice` e `TicketPolicy::view`, terza clausola).
5. **Il follower vede il codice altrui (MISURATO).** Paolo segue la 5 (di Giulia): `/it/area-personale/seguite` contiene
   `DEMO-PUB-05` e il link `http://127.0.0.1:8091/it/tickets/track?code=DEMO-PUB-05`. Causa (DEDOTTO): `BuildPersonalAreaTicketRowAction`
   mette `code` e `track_query_url` in ogni riga e dice "ha gia' filtrato", ma la query dei seguiti consegna ticket di altri.
   Il dettaglio pubblico NON espone il codice (guardia: `BuildPublicTicketDetailActionTest`, insieme esatto di chiavi).
6. **Cosa permette il codice (MISURATO).** Da ospite, `GET /it/tickets/track?code=TCK-DGFEIXRKJJBU58MT` (ticket 13, `pending`, non
   pubblico) risponde 200 con titolo "Buca profonda in via Roma", stato "In attesa" e descrizione. Il codice e' quindi una capability
   per leggere il contenuto di una segnalazione anche quando NON e' pubblica, e chi lo conosce lo puo' passare ad altri. Per un
   follower la segnalazione era pubblica quando l'ha seguita, ma se lo stato torna non pubblico il codice continua a dare accesso al
   testo (DEDOTTO: i seguiti sono filtrati per stato visibile, il tracciamento per codice no).
7. **Il codice arriva ai follower anche per notifica (DEDOTTO, test esistente lo asserisce).** `NotifyTicketSubscribersOfStatusChangeAction`
   invia `TicketStatusChangedNotification($ticketCode, ...)` a proprietario E follower: `toArray` contiene `ticket_code` e
   `action_url` = `/tickets/track?code=<codice>`, e la mail usa lo stesso link. `TicketStatusNotificationTest` verifica proprio
   `code=` nell'URL per il follower. Anche DCP-284 lo segnala (DD-3).
8. **Il ritorno dopo il login per il "Segui" (MISURATO come difetto da audit, ricontrollato nel codice).** Il blocco usa
   `route('login', [], false)` senza parametro. Nel frattempo la sessione dei commenti ha aggiunto
   `Modules\User\Actions\Auth\StoreIntendedUrlAction` e `LoginWidget::mount` che legge `?redirect=` solo se e' un percorso interno
   (`AuthReturnsToRequiredPageTest` lo prova): il meccanismo c'e', il link del dettaglio non lo usa.
9. **Segui la propria segnalazione (MISURATO sul fixture).** Giulia segue la sua stessa 1 (`ticket_subscribers` id 1): il dettaglio non lo
   impedisce e mostra al proprietario "Segui questa segnalazione" (FO-2-17).

10. **Il feed delle notifiche mostrava il codice anche al follower (DEDOTTO dal codice, ora provato da test).**
    `BuildPersonalAreaNotificationsDataAction` leggeva solo `ticket_code` dalla notifica e scartava le voci senza codice: una
    notifica da follower senza codice sarebbe sparita dall'elenco. Va estesa insieme alla notifica (D-6).
11. **La cronologia non mostrava mai gli eventi riservati all'autore (DEDOTTO, poi provato da test).** `BuildPublicTicketDetailAction`
    non selezionava `owner_id`: `BuildTicketTimelineAction($ticket, $viewer)` non poteva riconoscere il proprietario e il motivo di un
    rifiuto (visibilita' AuthorOnly) non arrivava mai a lui. Selezionare `owner_id` per D-2 lo corregge come effetto voluto.
12. **Prima misura della pagina "Seguite" per il follower, poi smentita (MISURATO).** Una prima rimisura dopo la correzione mostrava
    ancora `DEMO-PUB-05` per Paolo; due rimisure successive, con lo stesso script e lo stesso utente, mostrano 0 codici e 0 link
    `track?code=` (output sotto, Esito). Causa della prima misura: non dimostrata; la ipotesi piu' probabile e' una pagina servita
    prima che la vista compilata fosse rigenerata dal ritocco del tema da parte di un'altra sessione. Non e' stato cambiato nulla per
    "farla passare": il codice non e' stato toccato fra la misura sbagliata e quelle giuste.
13. **Etichette di stato (MISURATO dalla prova rossa, non dal browser).** Il test di parita' era rosso in 4 lingue: l'area
    personale usava `personal_area.status.*` ("Aperta", "Risolta", de "Gelöst", es "En tramitación") mentre elenco, dettaglio e
    notifiche usano `ticket_status_enum` ("Aperto", "Risolto", de "Erledigt", es "En curso").

## Causa

Un solo difetto di visibilita' (posizione obbligatoria nella query condivisa), due difetti di privacy (la riga dell'area personale e la
notifica trattano il codice come dato del follower), un link che non usa il ritorno gia' disponibile, due elenchi di etichette di stato
per la stessa cosa (FO-2-32) e due testi che dicono il falso sulla cronologia di un caso concluso o riaperto (FO-2-16).

## Decisioni

- D-1 (accesso al dettaglio): ospite e altri utenti vedono solo le pubbliche con posizione (invariato); il proprietario vede la propria
  in qualunque stato e anche senza posizione; il personale PA continua ad avere il back office (nessun ingresso nuovo nel front
  office). Implementazione: la ricerca pubblica resta com'e'; se non trova nulla, ricerca per id e accetta solo se il visitatore e'
  il proprietario E la `TicketPolicy::view` lo consente. Nessuna modifica a `BuildPublicTicketsQueryAction` (mappa ed elenco restano
  identici) ne' alla Policy.
- D-2 (dati del proprietario): l'Action aggiunge `is_owner` (bool) e `tracking_code` (stringa solo per il proprietario, altrimenti
  `null`). Nessun altro campo nuovo: niente `owner_id`, email, autore. La guardia sull'insieme esatto di chiavi si aggiorna e resta
  la prova contro le fughe; un test di matrice verifica il codice per ruolo.
- D-3 (blocco): per il proprietario il blocco nasconde "Segui" (seguire il proprio non ha senso) e mostra una scheda "La tua
  segnalazione" con il codice e, se lo stato non e' pubblico, l'avviso che solo lui la vede. Solo classi gia' usate dal blocco
  (`cmp-card`, `title-medium`, `text-paragraph`). Le righe dei commenti non si toccano.
- D-4 (codice ai follower, area personale): la riga ha `is_owner`; `code`, `track_url`, `track_query_url` sono valorizzati solo
  per il proprietario. Il follower vede "Vedi il dettaglio". L'avviso "codice non disponibile" compare solo per il proprietario.
- D-5 (ritorno dopo il login): il link "Accedi per seguire" porta `?redirect=<percorso del dettaglio>`, validato da
  `StoreIntendedUrlAction`. Nessun codice nuovo di validazione.
- D-2 bis (precisazione): l'Action espone anche `is_public` (bool, stato in `TicketStatusEnum::canViewByAll()`), perche' il blocco
  deve dire al proprietario "per ora la vedi solo tu" solo quando e' vero. Nessun dato di altri.
- D-6 (notifiche di stato ai follower): `NotifyTicketSubscribersOfStatusChangeAction` manda al proprietario la notifica di sempre (con
  codice e link di tracciamento) e ai follower `TicketStatusChangedNotification::forFollower($ticket, $status)`: titolo, stato e link
  al dettaglio pubblico `/tickets/{id}` (nuova `BuildTicketDetailUrlAction`, gemella di `BuildTicketTrackUrlAction`), mai il codice, ne'
  in `database` ne' in mail. Se il proprietario segue anche la propria, riceve una sola notifica, con il codice. Il feed
  (`BuildPersonalAreaNotificationsDataAction`) ha `ticket_label`: codice per il proprietario, titolo per il follower.
- D-7 (cronologia): `owner_id` entra nel `select` dell'Action di dettaglio. Effetto voluto e provato: il proprietario vede gli eventi
  AuthorOnly (es. il motivo di un rifiuto) che prima non vedeva mai.
- D-8 (etichette di stato, FO-2-32): una sola fonte, la traduzione dell'enum (`fixcity::ticket_status_enum.values.<stato>.label`),
  letta con la lingua della riga. `personal_area.status.*` e' stato tolto dalle quattro lingue (nessun altro lettore: grep su
  Modules, Themes, app, resources). Cambia il testo visibile in italiano (Aperta -> Aperto, Risolta -> Risolto, Riaperta -> Riaperto,
  Chiusa -> Chiuso), in tedesco (Gelöst -> Erledigt), in inglese le maiuscole (In review -> In Review, In progress -> In Progress, On hold
  -> On Hold) e in spagnolo In tramitación -> En curso.
- D-9 (cronologia e prossimi passi, FO-2-16): con la cronologia vuota, i casi `resolved` e `closed` dicono "La segnalazione e' «X».
  Nella cronologia pubblica non ci sono altri aggiornamenti." (`status.empty_concluded`); `reopened` ha un blocco "Prossimi passi"
  proprio (`next_steps.reopened`) invece di quello di "Lavorazione". Testi nelle quattro lingue. NON fatto: l'evento iniziale
  "Segnalazione ricevuta" e il messaggio libero del Comune (richiedono `BuildTicketTimelineAction`, vedi Da decidere DD-8).

## Da decidere

Propongo, non decido. Ordinati per effetto.

- DD-1 (personale PA e dettaglio non pubblico): il personale PA continua ad avere il back office e NON entra nel dettaglio del front
  office di una segnalazione non pubblica. Proposta: lasciare cosi' (un solo posto per ruolo). Se serve il contrario, e' una riga in
  `findVisibleTicket` che passa dalla policy `view` (che gia' lo consente al personale).
- DD-2 (follower e stati): oggi il follower riceve solo i cambi di stato pubblici. DCP-284 DD-3 chiedeva due cose: la prima (il codice nelle
  notifiche dei follower) e' risolta da D-6; resta la seconda: i follower devono ricevere anche approvata/chiusa? Proposta: si' per
  `closed` (e' un cambio di stato che gia' ricevono) e no per approvata/rifiutata (riguardano la moderazione, non l'avanzamento).
- DD-3 (proprietario che segue la propria): il modulo di "Segui" non e' piu' mostrato al proprietario (seguire la propria non ha
  senso, FO-2-17). Le righe `ticket_subscribers` gia' esistenti per il proprietario (es. id 1 nel fixture) restano e non danno
  doppie notifiche (provato). Proposta: non cancellarle; se si vuole pulire il dato serve una scelta sui dati, non un cambio di codice.
- DD-4 (il codice come capability): `/tickets/track?code=` legge titolo, stato e descrizione anche di una segnalazione NON pubblica
  (fatto 6). Chi conosce il codice puo' passarlo ad altri. Proposta: tenerlo cosi' per il proprietario (e' il suo mezzo di
  accesso senza login) e accorciare la vita del codice solo se la PA lo chiede. Decide il titolare.
- DD-5 (ritorno dopo il login): il link usa `?redirect=` validato da `StoreIntendedUrlAction` (percorsi interni). Risponde alla domanda 5
  dell'audit con la seconda opzione. Se si vuole solo il ritorno di sessione, e' una riga nel blocco.
- DD-6 (terminologia): «pratiche» nel menu e nella voce "Le mie pratiche" contro «segnalazioni» nei titoli (FO-2-32, seconda
  meta'). Qui ho unificato solo le etichette di stato (difetto verificabile); la parola «pratiche» e' una scelta di linguaggio
  dell'ente. Proposta: «segnalazioni» ovunque.
- DD-7 (stati del proprietario, vedi Disegno): quali azioni in quali stati, e se "ritira" e' un nuovo stato `withdrawn`. `status` e'
  `varchar(191)` (misurato): un nuovo caso dell'enum NON richiede migrazione, ma richiede la matrice delle transizioni e le
  etichette nelle quattro lingue.
- DD-8 (cronologia): l'evento "Segnalazione ricevuta" e il messaggio libero del Comune richiedono modifiche a `BuildTicketTimelineAction`,
  che oggi ha un lock vecchio di giorni (vedi Esito, lock orfano). Proposta: sbloccare dopo verifica, poi farlo in una story propria.

## Criteri di accettazione verificabili

1. Matrice dettaglio (ospite, altro cittadino, proprietario, follower) x (pubblica, non pubblica): 200 solo per ospite/altro/follower
   sulla pubblica e per il proprietario su entrambe; tutto il resto 404.
2. Il proprietario apre una propria segnalazione non pubblica e senza posizione (caso della 13): 200, con titolo, stato e l'avviso che
   non e' ancora pubblica; "Vedi il dettaglio" dell'area personale non porta piu' a un 404 (HTTP e browser).
3. Nel markup del dettaglio per ospite, altro cittadino e follower non compare il codice di tracciamento; per il proprietario compare
   una sola volta.
4. Il follower non vede ne' il codice ne' un link `tickets/track` in `/area-personale/seguite`; il proprietario li vede in
   `/area-personale/pratiche`.
5. Il link "Accedi per seguire" porta il percorso del dettaglio in `?redirect=` e, dopo il login, si arriva al dettaglio.
6. Test esistenti vicini al codice verdi (`TicketPublicDetailPageTest`, `BuildPublicTicketDetailActionTest`,
   `CitizenMyTicketsDetailLinkTest`, `FollowedTicketsPageTest`, `PersonalAreaPagesTest`); PHPStan a zero sui file PHP toccati.
7. Chiavi lang nuove in it, en, de, es con gli stessi segnaposto.
8. (FO-2-32) Per ogni stato e per ogni lingua, l'etichetta dell'area personale e' uguale a quella di `TicketStatusEnum::getLabel()`.
9. (FO-2-16) Un caso `resolved` o `closed` con cronologia vuota non dice "Per ora non ci sono aggiornamenti"; un caso `reopened` ha
   il blocco "Prossimi passi" dedicato; un caso `open` conserva "Lavorazione".

## Test

Esecuzione solo con `PEST_OUT=<file> bash bashscripts/tools/pest-scratch-db.sh <percorso relativo a laravel/>`.

(Si compila a fine lavoro: vedi Esito.)

## Owned file scope

- `laravel/Modules/Fixcity/app/Actions/BuildPublicTicketDetailAction.php`
- `laravel/Modules/Fixcity/app/Actions/BuildPersonalAreaTicketRowAction.php`
- `laravel/Modules/Fixcity/app/Actions/BuildPersonalAreaFollowedDataAction.php`
- `laravel/Modules/Fixcity/app/Actions/BuildPersonalAreaPracticesDataAction.php`
- `laravel/Modules/Fixcity/lang/{it,en,de,es}/ticket_detail.php`
- `laravel/Themes/Sixteen/resources/views/components/blocks/ticket-detail/design-comuni.blade.php` (solo la sezione "Cosa puoi fare" e una scheda del proprietario; mai le righe dei commenti)
- `laravel/Themes/Sixteen/resources/views/components/blocks/personal-area-practices/section.blade.php`
- test: `laravel/Modules/Fixcity/tests/Feature/Pages/TicketOwnerDetailAccessTest.php` (nuovo), `TicketPublicDetailPageTest.php`,
  `Unit/Actions/BuildPublicTicketDetailActionTest.php`, `Feature/Pages/FollowedTicketsPageTest.php`

## Esito

(Si compila a fine lavoro.)
