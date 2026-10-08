---
title: PA Operator Ticket Management Flow
id: STORY-009
author: BMAD
status: done
priority: must
type: story
module: Fixcity
tags:
- bmad
- fixcity
created: legacy
updated: 2026-09-26
qmd: story 009 pa operator flow FixCity BMAD story
issues:
- https://github.com/laraxot/base_fixcity_fila5/issues/383
discussions:
- https://github.com/laraxot/base_fixcity_fila5/discussions/392
---

## Attore: Operatore PA (PA Operator)

### Cosa vede dopo login (PA view)
- **Dashboard PA** → Statistiche KPI (totali, aperti, in lavorazione, chiusi)
- **Lista ticket** → Tutti i ticket (non solo i propri)
- **Filtri** → Stato, tipo, data, responsabile
- **Timeline** → Attività del ticket selezionato
- **Azioni rapide** → Assegna, Cambia stato, Chiudi

### Cosa può fare
- ✅ Vedere TUTTI i ticket (citizen + PA)
- ✅ Assegnare ticket a un operatore (self-assign o assegna ad altri)
- ✅ Cambiare lo stato del ticket (PENDING → IN_PROGRESS → RESOLVED → CLOSED)
- ✅ Aggiungere commenti ai ticket
- ✅ Visualizzare la timeline delle attività
- ✅ Esportare ticket (CSV/JSON)
- ✅ Gestire i filtri (categorie, tipi)
- ✅ Visualizzare mappe dei ticket (GeoJSON)
- ❌ Non può eliminare ticket (solo soft-delete?)
- ❌ Non può cambiare l'owner del ticket
- ❌ Non può modificare la configurazione globale

### Flusso di navigazione
1. **Dashboard PA** → `GET /admin/fixcity` → `Dashboard` Page
2. **Lista ticket** → `GET /admin/fixcity/tickets` → `ListTickets` Page
3. **Filtra** → `POST /admin/fixcity/tickets/filter` → `BuildTicketFilterAggregateAction`
4. **Assegna** → `POST /admin/fixcity/tickets/{id}/assign` → `AssignTicketAction`
5. **Cambia stato** → `POST /admin/fixcity/tickets/{id}/change-status` → `ChangeStatus` Action
6. **View Ticket** → `GET /admin/fixcity/tickets/{id}` → `ViewTicket` Page
7. **Timeline** → Dentro ViewTicket → `TicketActivity` RelationManager
8. **Export** → `GET /admin/fixcity/tickets/export` → `TicketExporter`

### Componenti Filament coinvolti
- `Dashboard` (con `GetTicketKpiAggregateAction`)
- `ListTickets` (con `BuildTicketFilterAggregateAction`)
- `ViewTicket` (con `TicketActivity` RelationManager)
- `EditTicket` (con ChangeStatus Action)
- `TicketsTable` (Tabella principale)
- `TicketExporter` (Esportazione)

### Regole architetturali
- `XotBaseListRecords` per ListTickets
- `XotBaseViewRecord` per ViewTicket
- `XotBaseEditRecord` per EditTicket
- `XotBaseDashboard` per Dashboard
- Actions in `app/Actions/` (ChangeStatus, AssignTicket, etc.)
- Relations: `owner()`, `assignee()`, `activities()`, `ticketSubscribers()`, `relations()`

### Second Brain
- `docs/chat/pa-operator-flow.md`
- `docs/wiki/log.md` aggiornato

### Quality Gate
- PHPStan 0 errori
- Test feature passano
- UI/UX verificata con Design Comuni
