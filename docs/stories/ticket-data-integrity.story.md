---
title: "Fixcity — Integrità dei dati delle segnalazioni"
type: story
bmad: true
status: review
created: 2026-11-10
owner: Modules/Fixcity
tags: [fixcity, bmad, database, production, audit]
related:
  - ../audit/DATABASE_AUDIT.md
  - ../audit/PRODUCT_UX_AUDIT.md
  - production-hardening-p0.story.md
---

# Integrità dei dati delle segnalazioni

Correzioni DB-03, DB-04, DB-05, DB-06, DB-07, DB-10 di `DATABASE_AUDIT.md` e PRD-04 di `PRODUCT_UX_AUDIT.md`. Chiude anche il punto
"`CreateTicket` del back office non scarta `status`/`priority`" aperto in `production-hardening-p0.story.md`.

## Problema

- **DB-10**: il ticket creato dal back office non aveva `code` né `TicketCreatedEvent`: nessuna ricevuta e nessun aggiornamento al segnalante.
  Anche `CreateTicketWidget::create()` creava il ticket senza codice.
- **CreateTicket** accettava `status` e `priority` dal form (campi Hidden riscrivibili dal browser).
- **DB-05**: alla creazione partivano due ricevute (lifecycle Received dal listener e `TicketStatusChangedNotification` da `SendTicketReceiptAction`).
- **DB-06**: ticket e foto fuori da una transazione; ricevuta inviata prima del salvataggio delle foto. Un errore sulle foto lasciava un
  ticket senza foto e, al nuovo "Invia", un secondo ticket.
- **DB-07**: approva, chiudi, scarta e cambia stato leggevano e validavano lo stato fuori dalla transazione e senza lock.
- **DB-03/DB-04**: nessun indice su `status`, `type`, `owner_id`, `responsible_id`, `created_at`, `code`, `ticket_activities.ticket_id`.
- **PRD-04**: il wizard accettava il consenso su un'informativa con segnaposto `[[ente.*]]` (titolare, DPO, PEC non compilati).

## Modifiche

| Voce | File | Cosa fa ora |
|---|---|---|
| DB-10, DB-06 | `app/Actions/CreateTicketAction.php` | Unico punto di creazione. Alloca il codice se manca; insert e callback `$afterCreate` (allegati) in una transazione su `fixcity` e `media`; nuovo tentativo (max 3) se il codice collide con l'indice unique; evento e flash della conferma dopo il commit; `$flashConfirmation = false` per il back office. |
| DB-06 | `app/Events/TicketCreatedEvent.php` | `ShouldDispatchAfterCommit`: se il chiamante ha una transazione aperta, l'evento parte solo al commit. |
| DB-06 | `app/Filament/Widgets/CreateTicketWizardWidget.php` | `saveRelationships()` delle foto passa come callback a `CreateTicketAction`, dentro la transazione. |
| DB-10 | `app/Filament/Resources/TicketResource/Pages/CreateTicket.php` | `handleRecordCreation()` delega a `CreateTicketAction` (codice, evento). `status`/`priority` del form scartati con `EditTicket::withoutWorkflowFields()`; il server imposta PENDING e LOW come nel wizard. |
| DB-10 | `app/Filament/Widgets/CreateTicketWidget.php` | `create()` passa da `CreateTicketAction` (prima `Ticket::create` senza codice). |
| DB-04 | `app/Actions/AllocateTicketCodeAction.php` | Controllo di unicità anche sui ticket cancellati (`withTrashed`). |
| DB-05 | `app/Actions/SendTicketReceiptAction.php` | Non invia più nulla: resta solo `mailedTo()` per la pagina di conferma, calcolato sui canali della notifica Received che parte davvero. |
| DB-07 | `app/Actions/LockTicketForStatusChangeAction.php` (nuovo) | Transazione + `lockForUpdate()` sulla riga; il controllo della transizione avviene sullo stato riletto; l'istanza del chiamante riceve gli attributi salvati. |
| DB-07 | `ChangeStatus`, `ApproveTicketAction`, `CloseTicketAction`, `RejectTicketAction` | Lettura e validazione dello stato dentro il lock. `AssignTicketAction` aveva già il lock. |
| DB-03, DB-04 | `database/migrations/2026_11_10_000001_add_query_indexes_to_tickets_table.php` (nuovo) | Indici idempotenti: `tickets(status, created_at)`, `(type, status)`, `(owner_id, created_at)`, `(responsible_id)`, `(created_at)`; `ticket_activities(ticket_id, created_at)`; `ticket_comments(ticket_id)`, `ticket_relations(ticket_id)`. `tickets.code` unique se non ci sono duplicati, altrimenti indice semplice + warning nel log. `down()` vuoto. |
| PRD-04 | `app/Actions/GetMissingEnteFieldsAction.php` (nuovo) | Elenca i campi `[[ente.*]]` rimasti nell'informativa interpolata. |
| PRD-04 | `CreateTicketWizardWidget::save()` | Se mancano dati dell'ente usati dall'informativa: errore `data.submit`, nessun ticket, `Log::warning` con i campi mancanti. |
| PRD-04 | `lang/{it,en,de,es}/ticket_submit.php` | Chiave `ente_not_configured`. |

## Test

| File | Copre |
|---|---|
| `tests/Feature/Actions/CreateTicketAtomicityTest.php` (nuovo) | Rollback di ticket e niente notifiche se gli allegati falliscono; callback sul ticket salvato; evento solo dopo il commit; back office senza flash. |
| `tests/Feature/Actions/TicketReceiptNotificationTest.php` (aggiornato) | Una sola notifica in totale al proprietario (Received), nessuna `TicketStatusChangedNotification`; conferma email invariata. |
| `tests/Feature/Filament/TicketResourceTest.php` (aggiornato) | Back office: `status`/`priority` del form ignorati (PENDING, LOW), `code` `TCK-…`, `TicketCreatedEvent` dispatchato. |
| `tests/Feature/Actions/TicketStatusChangeLockTest.php` (nuovo) | Copia vecchia del ticket: la seconda transizione fallisce con `DomainException`; doppia chiusura registra una sola attività; scarto dopo presa in lavorazione rifiutato. |
| `tests/Feature/Database/TicketQueryIndexesTest.php` (nuovo) | Presenza degli indici; due ticket con lo stesso codice falliscono se l'indice è unique. |
| `tests/Feature/Filament/CreateTicketWizardEnteGuardTest.php` (nuovo) | Informativa con `[[ente.*]]`: invio rifiutato, nessun ticket, warning; elenco dei campi mancanti. |

## Verifica

- `php -l` su tutti i file PHP toccati: nessun errore (ambiente senza `vendor/`, PHP 8.3).
- Da eseguire dove c'è `vendor/`:
  - `cd laravel && vendor/bin/pest Modules/Fixcity/tests/Feature/Actions Modules/Fixcity/tests/Feature/Filament Modules/Fixcity/tests/Feature/Database Modules/Fixcity/tests/Unit/Actions`
  - `cd laravel && ./vendor/bin/phpstan analyse Modules/Fixcity`
  - `php artisan migrate` su una copia del database di produzione; nel log cercare "tickets.code has duplicate values".

## Aperto

- **Back office e allegati**: Filament salva gli allegati dopo `handleRecordCreation()`, fuori dalla transazione di `CreateTicketAction`.
  Per includerli serve sovrascrivere `create()` della pagina; non fatto per non duplicare il ciclo di vita di Filament.
- **Segnalante da sportello** (DB-10): `owner_id` resta l'operatore, quindi la ricevuta va all'operatore. Serve un campo "segnalante".
- **File su disco**: con il rollback le righe `media` spariscono, i file eventualmente già copiati restano orfani.
- **Indice `code` semplice**: se la migrazione lo crea (duplicati esistenti), sistemare i duplicati e promuoverlo a unique con una nuova migrazione.
- **PRD-04**: il controllo copre solo i dati dell'ente usati dall'informativa, non le sezioni art. 13 (`AssessPrivacyPolicyAction`):
  applicarlo al wizard romperebbe i test che usano un'informativa minima. Il widget legacy `CreateTicketWidget` non ha il controllo.
  Restano da fare: compilare `ente.php` di produzione, dichiarazione di accessibilità su AgID.
- Il test "evento dopo il commit" presuppone che `Event::fake()` rispetti `ShouldDispatchAfterCommit` (Laravel 11+): verificarlo alla prima esecuzione.

## Revisione indipendente del 2026-11-10

- **Ordine dei commit (M1).** Con due connessioni Laravel aggancia i callback "dopo il commit" all'ultima transazione aperta
  (`fixcity`). Prima partivano con `media` ancora aperta: un worker poteva elaborare le miniature prima che la riga `media`
  esistesse. Ora `CreateTicketAction` apre e chiude a mano: `media` per prima, `fixcity` per ultima.
- **Back office (M2).** Gli allegati del form si salvano dentro la transazione (`$afterCreate` con `saveRelationships()`), prima
  della ricevuta. La seconda chiamata di Filament trova gli uuid gia' salvati e non duplica.
- **File orfani (M4).** Dopo un rollback i file gia' scritti restano su disco. Con InnoDB gli id non vengono riusati dopo un
  rollback, quindi nessun media nuovo finisce nella cartella di uno annullato: resta solo spazio da recuperare.
- **Widget legacy (L4).** `CreateTicketWidget` (non montato): controllo dell'identita', dei dati dell'ente in `create()` e
  priorita' forzata a LOW. `submit()` non controlla i dati dell'ente: i test esistenti usano un'informativa simulata.
- **Ancora aperto:** per i ticket da sportello `owner_id` resta l'operatore, quindi ricevuta e avviso PA partono a suo nome.
