---
# Actor Flows — Fixcity Fila5 (BMAD + Second Brain)

type: actor-flows
module: Fixcity
created: 2026-09-26
updated: 2026-09-26
---

## Attori

### 1. Citttadino (Citizen / Cittadino)
- **Ruolo**: chiamante pubblico, senza accesso PA
- **Azione principale**: crea `segala` (ticket) tramite wizard pubblico
- **Flusso**: `segnalazione-crea` (Folio) → `CreateTicketWizardWidget` → `CreateTicketAction` → salva `Ticket` → notifica PA
- **Permessi**: `viewAny` pubblico, `create` pubblico, nessun `update/delete`
- **Files chiave**: `Pages/CreateTicket.php` (Folio), `CreateTicketWizardWidget`, `TicketForm`, `CreateTicketAction`
- **Traduzioni**: `fixcity::ticket.*` (IT/EN)

### 2. Operatore / Ente PA (Operator)
- **Ruolo**: operatore PA che gestisce segnalazioni
- **Azione**: visualizza, aggiorna stato, assegna, commenta
- **Flusso**: `TicketResource` (Filament) → `EditTicket.php` → `ChangeStatus` → `RecordTicketActivityAction`
- **Permessi**: `update` (se proprietario o operatore), `assign`, `changeStatus` (se operatore/supervisore/admin)
- **Policy**: `TicketPolicy::update()` → `$user->hasRole('operator') || $ticket->isOwnedByAuthenticatedUser()`
- **Files**: `TicketResource/Pages/EditTicket.php`, `ChangeStatus.php`, `HasTicketRelations.php`

### 3. Supervisore / Admin (Supervisore / Admin)
- **Ruolo**: supervisione completa, gestione ruoli
- **Azione**: visualizza tutto, modifica, elimina, gestisce utenti
- **Permessi**: `viewAny` (tutti), `update/delete` (tutti se admin/supervisor)
- **Policy**: `BasePolicy::PA_ROLES = ['operator', 'supervisor', 'admin']`
- **Files**: `TicketPolicy`, `UserBasePolicy`, `XotBasePolicy` (before super-admin)

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
  → Policy override → BasePolicy (tutti i ruoli PA)
```

---

## Documentazione per attore (Second Brain)

- Citizen: `Modules/Fixcity/docs/bmad/stories/023-wizard-responsive-uiux.story.md`
- PA/Operator: `Modules/Fixcity/docs/bmad/stories/024-basepolicy-layer.story.md`
- System: `Modules/Fixcity/docs/second-brain.md` (sezione 5-7)

---

## Aggiornamenti richiesti nei moduli e temi

- `Modules/Fixcity/docs/` → aggiunto `actor-flows.md`
- `Modules/Comment/docs/` → documentare relazione con Ticket (CommentsRelationManager)
- `Modules/Notify/docs/` → documentare evento `TicketCreatedEvent`
- `Modules/Geo/docs/` → documentare GeoJson flows
- `Modules/User/docs/` → documentare `UserBasePolicy`
- `Themes/Sixteen/docs/` → documentare UI pari al Design Comuni

Tutti documentati con BMAD story numerata o con riferimento al Second Brain.
