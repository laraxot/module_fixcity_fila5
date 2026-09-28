---
title: Authenticated Citizen Flow
id: STORY-014
author: BMAD
status: done
priority: must

## Attore: Cittadino autenticato

### Cosa vede dopo login
- **Dashboard personale** → "I miei ticket" con elenco
- **Statistiche personali** → Numero ticket, stato, risposta media
- **Notifiche** → Badge con notifiche non lette
- **Button** → "Crea nuova segnalazione"

### Cosa può fare
- ✅ Vedere i propri ticket (lista e dettaglio)
- ✅ Creare nuovi ticket tramite wizard
- ✅ Vedere lo stato del proprio ticket
- ✅ Aggiungere commenti ai propri ticket
- ✅ Valutare il ticket con rating
- ✅ Modificare il proprio profilo
- ❌ Non può vedere ticket di altri cittadini
- ❌ Non può assegnare ticket a operatori
- ❌ Non può cambiare lo stato del ticket
- ❌ Non può accedere al backoffice PA

### Flusso di navigazione
1. **Dashboard** → `GET /admin` → `filament.pages.dashboard` (citizen view)
2. **Create Ticket** → `GET /admin/fixcity/tickets/create` → Wizard widget
3. **My Tickets** → `GET /admin/fixcity/tickets?filter=mine` → Lista filtrata
4. **View Ticket** → `GET /admin/fixcity/tickets/{id}` → TicketResource View
5. **Add Comment** → Dentro ViewTicket → Comment RelationManager
6. **Rate Ticket** → `POST /tickets/{id}/rate` → SubmitCitizenRatingAction

### Componenti Filament coinvolti
- `CreateTicketWizardWidget` (widget)
- `TicketResource` (Resource)
- `ViewTicket` Page
- `TicketCitizenRatingPromptWidget` (rating prompt)
- `CitizenRatingOverviewWidget` (rating stats)

### Regole architetturali
- `XotBaseCreateRecord` per CreateTicket Page
- `XotBaseViewRecord` per ViewTicket Page
- `XotBaseWizardWidget` per CreateTicketWizardWidget
- Relations: `owner()`, `assignee()`, `activities()`, `ticketSubscribers()`

### Second Brain
- `docs/chat/citizen-authenticated-flow.md`
- `docs/wiki/log.md` aggiornato

### Quality Gate
- PHPStan 0 errori
- Test `tests/Feature/Filament/CreateTicketWizardWidgetTest.php` passa
- Test `tests/Feature/Filament/TicketResourceTest.php` passa