---
title: "actor flows"
tags: [documentation]
qmd: "actor flows"
issues: []
discussions: []
# Actor Flows — Fixcity Fila5 (BMAD + Second Brain)

type: actor-flows
module: Fixcity
created: 2026-09-26
updated: 2026-09-27
status: draft
---

## Attori

### 1. Citttadino (Citizen / Cittadino)
- **Ruolo**: cittadino senza accesso PA; autenticato per inviare una segnalazione, guest per la sola consultazione pubblica
- **Azione principale**: crea una segnalazione (ticket) tramite wizard dopo l’accesso
- **Flusso**: `segnalazione-crea` (Folio) → `CreateTicketWizardWidget` → `CreateTicketAction` → salva `Ticket` → notifica PA
- **Permessi**: il [JSON canonico tickets.create](../config/database/content/pages/tickets.create.json) richiede `auth`. `TicketPolicy::create()` richiede un utente identificato o la permission dedicata. `viewAny()` della resource è PA/permission, non guest.
- **Consultazione pubblica**: passa da `BuildPublicTicketsQueryAction` e dalla proiezione esplicita `BuildPublicTicketDetailAction`, non da un’autorizzazione amministrativa generale.
- **Modifica/cancellazione**: `TicketPolicy::update()` contempla il proprietario; `delete()` richiede admin o permission specifica, con le eventuali regole `before()` ereditate.
- **Files chiave**: `Pages/CreateTicket.php` (Folio), `CreateTicketWizardWidget`, `TicketForm`, `CreateTicketAction`
- **Traduzioni**: `fixcity::ticket.*` (IT/EN)

### 2. Operatore / Ente PA (Operator)
- **Ruolo**: operatore PA che gestisce segnalazioni
- **Azione**: visualizza, aggiorna stato, assegna, commenta
- **Flusso**: `TicketResource` (Filament) → `EditTicket.php` → `ChangeStatus` → `RecordTicketActivityAction`
- **Permessi**: `update` (se proprietario o operatore), `assign`, `changeStatus` (se operatore/supervisore/admin)
- **Policy**: `TicketPolicy::update()` → ruolo PA oppure permission `ticket.update` oppure proprietario (confronto dell’identità autenticata)
- **Files**: `TicketResource/Pages/EditTicket.php`, `ChangeStatus.php`, `HasTicketRelations.php`

### 3. Supervisore / Admin (Supervisore / Admin)
- **Ruolo**: supervisione completa, gestione ruoli
- **Azione**: supervisione e gestione secondo le capability effettive; la cancellazione non è concessa automaticamente al supervisore dalla sola appartenenza a un ruolo PA
- **Permessi**: `viewAny` e `update` seguono le policy PA; `delete/deleteAny` seguono la regola privilegiata di `TicketPolicy`, oltre alle eventuali autorizzazioni ereditate
- **Policy**: `BasePolicy::PA_ROLES = ['operator', 'supervisor', 'admin']`
- **Files**: `TicketPolicy`, `UserBasePolicy`, `XotBasePolicy` (before super-admin)

### 4. Moderatore di quartiere
- **Flusso atteso**: login → coda del quartiere → approva/respinge con motivazione →
  deduplica/inoltro → timeline pubblica.
- **Stato verificato**: non esiste ancora una capability canonica distinta nei
  `PA_ROLES`; non viene dichiarato pronto solo perché è richiesto dalla demo.
- **Decisione necessaria**: definire perimetro, permessi, audit e ruolo persistente.

### 5. Mobile NativePHP
- Guest: dashboard/map/detail read-only dal GeoJSON condiviso.
- Cittadino autenticato: create/track/follow tramite Actions e policy condivise.
- PA: viste native solo dopo adapter e capability verificati; il backoffice web resta
  la superficie canonica fino alla prova APK.
- Evidence: `Modules/Fixcity/docs/bmad/fixcity-nativephp-mobile.md`.

### 4. Sistema / Automatismi
- **Eventi**: `TicketCreatedEvent`
- **Notifiche**: `Notify` module (Notifica)
- **Job/Queue**: `QueueableAction` per azioni pesanti
- **Geo**: `BuildTicketsGeoJsonAction`, `LoadPublicTicketsGeoJsonAction`
- **Rating**: `SubmitCitizenTicketRatingAction`, `GetCitizenRatingAggregateAction`

---

## Flusso Completo (M0 → M1)

```text
Cittadino → Wizard (segnalazione-crea) → CreateTicketWizardWidget
  → CreateTicketAction → Ticket (DB)
  → TicketCreatedEvent → Notify (PA)
  → Geo → BuildTicketsGeoJsonAction → Mappa pubblica
  → Rating → SubmitCitizenTicketRatingAction → Rating DB

PA (Operatore) → Filament TicketResource → Lista/Dettaglio
  → EditTicket → ChangeStatus / AssignTicketAction
  → RecordTicketActivityAction → Timeline
  → CommentsRelationManager → Comment (modulo Comment)

Supervisore/Admin → Dashboard (XotBaseDashboard) → Analytics
  → Export (TicketExporter) → Spreadsheet
  → Policy specifiche → before() per super-admin; capability PA nei singoli metodi
```

---

## Documentazione per attore (Second Brain)

- Citizen: `Modules/Fixcity/docs/bmad/stories/023-wizard-responsive-uiux.story.md`
- PA/Operator: `Modules/Fixcity/docs/bmad/stories/024-basepolicy-layer.story.md`
- System: `Modules/Fixcity/docs/second-brain.md` (sezione 5-7)

---

## Criteri di completamento per ogni attore

Ogni journey deve avere quattro prove: entry route, autorizzazione, persistenza/evento
e feedback accessibile. Un mock, un seeder o una Blade presente non valgono come prova
runtime. La checklist demo copre guest, citizen, operator, moderator (se abilitato),
supervisor, admin e failure path (401/403/404/422/429/500).

## Aggiornamenti richiesti nei moduli e temi

- `Modules/Fixcity/docs/` → aggiunto `actor-flows.md`
- `Modules/Comment/docs/` → documentare relazione con Ticket (CommentsRelationManager)
- `Modules/Notify/docs/` → documentare evento `TicketCreatedEvent`
- `Modules/Geo/docs/` → documentare GeoJson flows
- `Modules/User/docs/` → documentare `UserBasePolicy`
- `Themes/Sixteen/docs/` → documentare UI pari al Design Comuni

Tutti documentati con BMAD story numerata o con riferimento al Second Brain.
