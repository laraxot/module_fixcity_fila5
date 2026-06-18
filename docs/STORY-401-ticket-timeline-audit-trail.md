---
title: "STORY-401: Ticket Timeline & Audit Trail"
type: story
tags: [fixcity, ticket, timeline, audit-trail, workflow, sla]
status: draft
priority: critical
assignee: TBD
created: 2026-06-17
updated: 2026-06-17
---

# STORY-401: Ticket Timeline & Audit Trail

## User Story

**Come** cittadino o operatore comunale  
**Voglio** vedere la cronologia completa di una segnalazione (data creazione, presa in carico, inizio lavori, completamento, verifica)  
**Per** avere trasparenza sullo stato di avanzamento e garantire accountability

## Background & Motivazione

Analisi competitiva (FixMyStreet, SeeClickFix, Decoro Urbano, Degradozero) mostra che tutte le piattaforme mature tracciano un **workflow temporale** dettagliato. Fixcity attualmente ha solo uno stato singolo (`status` enum) senza tracciamento delle date intermedie.

**Competitor benchmark:**
- **FixMyStreet**: Questionario post-risoluzione a 2 settimane
- **SeeClickFix**: Stati `open` → `acknowledged` → `closed` con timestamps
- **Decoro Urbano**: Workflow `in attesa` → `in carico` → `risolta`
- **Ril.fe.de.ur**: Iter completo: apertura → ricezione → verifica → inoltro → risoluzione

## Acceptance Criteria

### AC1: Modello Dati Timeline
```gherkin
Dato un ticket esistente
Quando viene creato
Allora deve avere i campi temporali:
  - reported_at (datetime, default now)
  - acknowledged_at (datetime, nullable)
  - assigned_at (datetime, nullable)  
  - started_at (datetime, nullable)
  - completed_at (datetime, nullable)
  - verified_at (datetime, nullable)
  - closed_at (datetime, nullable)
```

### AC2: Stati Intermedi Workflow
```gherkin
Dato un ticket in stato "submitted"
Quando un operatore prende in carico
Allora stato diventa "acknowledged" 
E acknowledged_at = now()

Dato un ticket in stato "acknowledged"
Quando viene assegnato a tecnico
Allora stato diventa "assigned"
E assigned_at = now()

Dato un ticket in stato "assigned"
Quando iniziano i lavori
Allora stato diventa "in_progress"
E started_at = now()

Dato un ticket in stato "in_progress"
Quando lavori completati
Allora stato diventa "completed"
E completed_at = now()

Dato un ticket in stato "completed"
Quando verificato da supervisore
Allora stato diventa "verified"
E verified_at = now()

Dato un ticket in stato "verified" (o completed)
Quando chiuso definitivamente
Allora stato diventa "closed"
E closed_at = now()
```

### AC3: History Table (Audit Trail)
```gherkin
Dato un ticket qualsiasi
Quando cambia stato
Allora viene creato record in ticket_status_history con:
  - ticket_id (FK)
  - from_status (enum)
  - to_status (enum)
  - changed_by (user_id, nullable per system)
  - changed_at (datetime)
  - notes (text, nullable)
  - metadata (json, per dati extra)
```

### AC4: API Timeline
```gherkin
Dato un ticket con ID valido
Quando chiamo GET /api/tickets/{id}/timeline
Allora ricevo JSON con:
  - ticket base info
  - current_status
  - dates object con tutti i timestamps
  - history array ordinato cronologicamente
  - duration calcolati (es: time_to_acknowledge, time_to_resolve)
```

### AC5: UI Timeline Component
```gherkin
Dato una pagina dettaglio ticket
Quando visualizzo il ticket
Allora vedo componente timeline verticale con:
  - Step completati (icona verde)
  - Step corrente (icona animata/blu)
  - Step futuri (icona grigia)
  - Date e orari per ogni step
  - Nome operatore che ha fatto il cambio (se applicabile)
```

### AC6: SLA Monitoring
```gherkin
Dato un ticket con acknowledged_at
Quando calcolo SLA
Allora posso confrontare acknowledged_at - reported_at contro SLA target
E se > SLA target, mark come "SLA breached"
```

## Technical Notes

### Database Schema

```php
// Migration per tickets table (update)
Schema::table('tickets', function (Blueprint $table) {
    $table->datetime('reported_at')->nullable()->after('created_at');
    $table->datetime('acknowledged_at')->nullable();
    $table->datetime('assigned_at')->nullable();
    $table->datetime('started_at')->nullable();
    $table->datetime('completed_at')->nullable();
    $table->datetime('verified_at')->nullable();
    $table->datetime('closed_at')->nullable();
    $table->foreignId('acknowledged_by')->nullable()->constrained('users');
    $table->foreignId('assigned_by')->nullable()->constrained('users');
    $table->foreignId('assigned_to')->nullable()->constrained('users'); // tecnico
    $table->foreignId('completed_by')->nullable()->constrained('users');
    $table->foreignId('verified_by')->nullable()->constrained('users');
    $table->foreignId('closed_by')->nullable()->constrained('users');
});

// Nuova migration ticket_status_history
Schema::create('ticket_status_history', function (Blueprint $table) {
    $table->id();
    $table->foreignId('ticket_id')->constrained()->cascadeOnDelete();
    $table->string('from_status', 50);
    $table->string('to_status', 50);
    $table->foreignId('changed_by')->nullable()->constrained('users');
    $table->datetime('changed_at');
    $table->text('notes')->nullable();
    $table->json('metadata')->nullable();
    $table->timestamps();
    $table->index(['ticket_id', 'changed_at']);
});
```

### TicketStatusEnum Update
```php
enum TicketStatusEnum: string
{
    case SUBMITTED = 'submitted';       // Segnalazione ricevuta
    case ACKNOWLEDGED = 'acknowledged'; // Presa in carico
    case ASSIGNED = 'assigned';         // Assegnata a tecnico
    case IN_PROGRESS = 'in_progress';     // Lavori iniziati
    case COMPLETED = 'completed';         // Lavori completati
    case VERIFIED = 'verified';           // Verificata da supervisore
    case CLOSED = 'closed';               // Chiusa definitivamente
    case REJECTED = 'rejected';           // Rifiutata (non valida)
    
    // Metodo per validare transizioni
    public function canTransitionTo(self $newStatus): bool
    {
        return match($this) {
            self::SUBMITTED => [self::ACKNOWLEDGED, self::REJECTED],
            self::ACKNOWLEDGED => [self::ASSIGNED, self::REJECTED],
            self::ASSIGNED => [self::IN_PROGRESS],
            self::IN_PROGRESS => [self::COMPLETED],
            self::COMPLETED => [self::VERIFIED, self::CLOSED],
            self::VERIFIED => [self::CLOSED],
            default => [],
        } === true ? in_array($newStatus, match($this) {
            self::SUBMITTED => [self::ACKNOWLEDGED, self::REJECTED],
            self::ACKNOWLEDGED => [self::ASSIGNED, self::REJECTED],
            self::ASSIGNED => [self::IN_PROGRESS],
            self::IN_PROGRESS => [self::COMPLETED],
            self::COMPLETED => [self::VERIFIED, self::CLOSED],
            self::VERIFIED => [self::CLOSED],
            default => [],
        }) : false;
    }
}
```

### Action Classes

```php
// app/Actions/Ticket/TransitionTicketAction.php
class TransitionTicketAction
{
    use QueueableAction;
    
    public function execute(
        Ticket $ticket,
        TicketStatusEnum $newStatus,
        ?User $actor = null,
        ?string $notes = null,
        ?array $metadata = null
    ): Ticket {
        $oldStatus = $ticket->status;
        
        // Validazione transizione
        if (!$oldStatus->canTransitionTo($newStatus)) {
            throw new InvalidStatusTransitionException(
                "Cannot transition from {$oldStatus->value} to {$newStatus->value}"
            );
        }
        
        return DB::transaction(function () use ($ticket, $newStatus, $oldStatus, $actor, $notes, $metadata) {
            // Update ticket
            $ticket->status = $newStatus;
            $ticket->setStatusTimestamp($newStatus); // magic method per settare campo data corretto
            $ticket->setStatusActor($newStatus, $actor); // magic method per settare actor
            $ticket->save();
            
            // Create history record
            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'from_status' => $oldStatus->value,
                'to_status' => $newStatus->value,
                'changed_by' => $actor?->id,
                'changed_at' => now(),
                'notes' => $notes,
                'metadata' => $metadata,
            ]);
            
            // Dispatch event
            event(new TicketStatusChanged($ticket, $oldStatus, $newStatus, $actor));
            
            return $ticket->fresh();
        });
    }
}
```

### API Endpoint

```php
// routes/api.php
Route::get('/tickets/{ticket}/timeline', [TicketTimelineController::class, 'show'])
    ->name('api.tickets.timeline');

// Controller
class TicketTimelineController
{
    public function show(Ticket $ticket): JsonResponse
    {
        $timeline = [
            'ticket' => new TicketResource($ticket),
            'current_status' => $ticket->status->value,
            'dates' => [
                'reported_at' => $ticket->reported_at?->toIso8601String(),
                'acknowledged_at' => $ticket->acknowledged_at?->toIso8601String(),
                'assigned_at' => $ticket->assigned_at?->toIso8601String(),
                'started_at' => $ticket->started_at?->toIso8601String(),
                'completed_at' => $ticket->completed_at?->toIso8601String(),
                'verified_at' => $ticket->verified_at?->toIso8601String(),
                'closed_at' => $ticket->closed_at?->toIso8601String(),
            ],
            'durations' => [
                'time_to_acknowledge' => $ticket->acknowledged_at 
                    ? $ticket->reported_at->diffInHours($ticket->acknowledged_at) 
                    : null,
                'time_to_resolve' => $ticket->completed_at 
                    ? $ticket->reported_at->diffInHours($ticket->completed_at) 
                    : null,
                'total_duration' => $ticket->closed_at 
                    ? $ticket->reported_at->diffInHours($ticket->closed_at) 
                    : null,
            ],
            'history' => TicketStatusHistoryResource::collection(
                $ticket->statusHistory()->with('changedBy')->latest('changed_at')->get()
            ),
        ];
        
        return response()->json($timeline);
    }
}
```

## Definition of Done

- [ ] Migration database creata e testata
- [ ] Enum TicketStatusEnum aggiornato con validazione transizioni
- [ ] Action TransitionTicketAction implementata con test
- [ ] Model TicketStatusHistory creato con relazioni
- [ ] API endpoint `/api/tickets/{id}/timeline` funzionante
- [ ] Componente UI Timeline visualizzato in pagina ticket
- [ ] Test unitari per tutte le transizioni di stato
- [ ] Test feature per API timeline
- [ ] Documentazione API aggiornata
- [ ] PHPStan level max passa

## Related Issues

- Epic: EPIC-004 Workflow Management
- Depends on: STORY-XXX (Ticket Base Model)
- Blocks: STORY-402 (Rich Questionnaire), STORY-403 (Notification System)

## Discussion Links

- GitHub Issue: `https://github.com/laraxot/fixcity/issues/401`
- GitHub Discussion: `https://github.com/laraxot/fixcity/discussions/401`

## Notes

**Pattern da seguire:** Implementazione simile a moduli esistenti (XotBaseModel, Actions pattern). Usare `spatie/laravel-activitylog` opzionale per audit trail avanzato se necessario in futuro.

**Performance:** Aggiungere index su `ticket_id + changed_at` per query timeline veloci. Considerare pagination per history con > 50 record.

**SLA:** Configurare target SLA in config/fixcity.php (es: acknowledge_within_hours = 24, resolve_within_hours = 168).
