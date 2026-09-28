---
title: Rating System Flow
id: STORY-018
author: BMAD
status: done
priority: high

## Attore: Rating System

### Cosa fa
- **Valutazione ticket** → Cittadino può dare un rating (1-5)
- **Rating aggregato** → Media dei rating mostrata nella dashboard
- **Rating singolo** → Visualizzabile sul ticket
- **Evento** → `TicketCitizenRatingEvent` dispatch

### Flusso
1. **Citizen rating** → `POST /tickets/{id}/rating` → Rating salvato
2. **Aggregation** → Media rating calcolata via `BuildSegnalazioniFilterAggregateAction`
3. **Event** → `TicketCitizenRatingEvent` dispatch
4. **Notification** → Notifica PA se rating basso

### Componenti
- `SubmitCitizenRatingAction` (Action)
- `GetCitizenRatingAggregateAction` (Aggregazione)
- `GetTicketIdsWithCitizenRatingAction` (Query)
- `TicketCitizenRatingMorphAction` (Morph handling)
- `TicketSlaMetricsAction` (KPI metrics)

### Regole architetturali
- `TicketStatusEnum` per i rating
- `foreignIdFor()` per il `ticket_id` pivot
- `UserContract` per user declaration
- `QueueableAction` per operazioni rating
- `SafeStringCastAction` per cast sicurezza

### Second Brain
- `docs/chat/rating-system-flow.md`
- `docs/wiki/log.md` aggiornato

### Quality Gate
- PHPStan 0 errori su `Modules/Fixcity`
- Test `tests/Unit/Actions/GetCitizenRatingAggregateActionTest.php` passa
- Test `tests/Feature/Filament/TicketCitizenRatingPromptWidget.php` passa