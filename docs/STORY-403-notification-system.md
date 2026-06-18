---
title: "STORY-403: Notification System (Email, Push, SMS)"
type: story
tags: [fixcity, notifications, email, push, sms, real-time, decoro-urbano]
status: draft
priority: high
assignee: TBD
created: 2026-06-17
updated: 2026-06-17
---

# STORY-403: Notification System (Email, Push, SMS)

## User Story

**Come** cittadino che ha fatto una segnalazione  
**Voglio** ricevere notifiche in tempo reale sui cambi di stato (presa in carico, lavori iniziati, completati)  
**Per** essere informato senza dover controllare manualmente l'app

## Background & Motivazione

**Competitor Analysis:**
- **Decoro Urbano**: Notifiche push per aggiornamenti stato e news comunali
- **SeeClickFix**: Email + in-app notifications, commenti thread
- **FixMyStreet**: Email di conferma + survey post-risoluzione
- **Degradozero**: Email automatica di conferma al segnalante

**Gap Fixcity**: Nessun sistema di notifiche attualmente implementato.

## Acceptance Criteria

### AC1: Canali di Notifica Supportati
```gherkin
Dato un utente con preferenze configurate
Quando avviene un evento notificabile
Allora il sistema può inviare via:
  - Email (Laravel Mail con template HTML/text)
  - Push Notification (Firebase Cloud Messaging / OneSignal)
  - SMS (Twilio / AWS SNS)
  - In-App (real-time toast/polling)
  - WhatsApp (opzionale, Twilio API)
```

### AC2: Eventi Notificabili
```gherkin
Dato un ticket nel sistema
Quando avviene uno dei seguenti eventi:
  - Ticket creato (conferma al cittadino)
  - Status cambiato (acknowledged, assigned, in_progress, completed, verified, closed)
  - Commento aggiunto (da operatore o cittadino)
  - Ticket riaperto
  - Ticket in prossimità SLA breach
  - Survey disponibile (post-completamento)
Allora viene triggerata notifica ai destinatari appropriati
```

### AC3: Destinatari Multipli
```gherkin
Dato un evento su un ticket
Quando viene processato
Allora notifica può essere inviata a:
  - Cittadino segnalante (reporter)
  - Operatore assegnato (assigned_to)
  - Manager reparto
  - Amministrazione comunale (per ticket critici)
  - Altri cittadini che "seguono" il ticket
```

### AC4: Preferenze Utente
```gherkin
Dato un utente loggato
Quando accede a profilo /settings/notifications
Allora può configurare:
  - Canali attivi per tipologia evento (email on/off, push on/off)
  - Frequenza digest (immediate, hourly digest, daily digest)
  - Tipologie ticket per cui ricevere notifiche (tutte o solo certe)
  - Orario silenzioso (es: notifiche push solo 08:00-22:00)
```

### AC5: Template Email Personalizzabili
```gherkin
Dato un amministratore
Quando accede a /admin/notification-templates
Allora può:
  - Modificare template email per ogni tipo evento
  - Usare variabili dinamiche ({{ticket.id}}, {{ticket.status}}, {{user.name}})
  - Anteprima in tempo reale
  - Test send a indirizzo email
  - Versioning template (rollback se necessario)
```

### AC6: Notifiche In-App Real-time
```gherkin
Dato un utente sulla web app
Quando arriva una notifica
Allora vede:
  - Toast/alert in alto a destra
  - Badge contatore notifiche non lette
  - Dropdown con lista notifiche recenti
  - Link diretto al ticket correlato
  - Mark as read / dismiss
```

### AC7: Rate Limiting & Throttling
```gherkin
Dato un ticket con molti aggiornamenti rapidi
Quando vengono generati > 5 eventi in 1 ora per lo stesso utente
Allora sistema raggruppa in digest
E non invia email individuali per evitare spam
```

### AC8: Delivery Tracking
```gherkin
Dato una notifica inviata
Quando viene processata
Allora sistema traccia:
  - sent_at (timestamp)
  - delivered_at (conferma delivery dal provider)
  - opened_at (pixel tracking per email)
  - clicked_at (link tracking)
  - failed_at + error_message (se fallita)
```

## Technical Notes

### Database Schema

```php
// Migration: notifications
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type', 100); // class name del notification type
    $table->morphs('notifiable'); // user_id / user_type (polimorfico per supportare anche guest)
    $table->json('data'); // payload notification
    $table->datetime('read_at')->nullable();
    $table->datetime('sent_at')->nullable();
    $table->datetime('delivered_at')->nullable();
    $table->datetime('failed_at')->nullable();
    $table->text('failure_reason')->nullable();
    $table->string('channel', 50); // email, push, sms, whatsapp
    $table->timestamps();
    
    $table->index(['notifiable_type', 'notifiable_id', 'read_at']);
    $table->index(['type', 'sent_at']);
});

// Migration: notification_templates
Schema::create('notification_templates', function (Blueprint $table) {
    $table->id();
    $table->string('key', 100)->unique(); // ticket_created, status_changed, etc.
    $table->string('event_type', 100); // TicketCreated, TicketStatusChanged, etc.
    $table->string('channel', 50); // email, push, sms
    $table->string('subject');
    $table->text('body'); // template con placeholders {{var}}
    $table->json('variables_schema'); // definizione variabili disponibili
    $table->boolean('is_active')->default(true);
    $table->timestamps();
});

// Migration: user_notification_preferences
Schema::create('user_notification_preferences', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->string('event_type', 100);
    $table->string('channel', 50);
    $table->boolean('is_enabled')->default(true);
    $table->string('frequency', 20)->default('immediate'); // immediate, hourly, daily
    $table->json('schedule')->nullable(); // {days: [1,2,3], start_time: "08:00", end_time: "22:00"}
    $table->timestamps();
    
    $table->unique(['user_id', 'event_type', 'channel']);
});
```

### Laravel Notification Classes

```php
// app/Notifications/TicketStatusChangedNotification.php
class TicketStatusChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;
    
    public function __construct(
        public Ticket $ticket,
        public TicketStatusEnum $oldStatus,
        public TicketStatusEnum $newStatus,
        public ?User $actor = null,
    ) {}
    
    public function via(object $notifiable): array
    {
        $preferences = $notifiable->notificationPreferences()
            ->where('event_type', 'ticket_status_changed')
            ->get();
            
        $channels = [];
        
        foreach ($preferences as $pref) {
            if ($pref->is_enabled && $this->shouldSendNow($pref)) {
                $channels[] = $pref->channel;
            }
        }
        
        return $channels;
    }
    
    public function toMail(object $notifiable): MailMessage
    {
        $template = NotificationTemplate::where('event_type', 'ticket_status_changed')
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();
            
        $variables = $this->buildTemplateVariables();
        
        return (new MailMessage)
            ->subject($this->interpolate($template?->subject ?? 'Stato segnalazione aggiornato', $variables))
            ->view('emails.notification', [
                'body' => $this->interpolate($template?->body ?? $this->defaultBody(), $variables),
                'ticket' => $this->ticket,
                'actionUrl' => route('tickets.show', $this->ticket),
                'actionText' => 'Visualizza segnalazione',
            ]);
    }
    
    public function toFcm(object $notifiable): FcmMessage
    {
        return (new FcmMessage)
            ->title('Aggiornamento segnalazione #' . $this->ticket->id)
            ->body("Stato cambiato: {$this->oldStatus->label()} → {$this->newStatus->label()}")
            ->data([
                'ticket_id' => $this->ticket->id,
                'action' => 'view_ticket',
            ])
            ->badge(1);
    }
    
    public function toTwilio(object $notifiable): TwilioSmsMessage
    {
        return (new TwilioSmsMessage)
            ->content("Fixcity: Segnalazione {$this->ticket->id} aggiornata. Nuovo stato: {$this->newStatus->label()}. " . 
                     route('tickets.show', $this->ticket));
    }
    
    public function toDatabase(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'old_status' => $this->oldStatus->value,
            'new_status' => $this->newStatus->value,
            'actor_id' => $this->actor?->id,
            'actor_name' => $this->actor?->name,
            'message' => "Stato cambiato da {$this->oldStatus->label()} a {$this->newStatus->label()}",
            'action_url' => route('tickets.show', $this->ticket),
        ];
    }
    
    private function buildTemplateVariables(): array
    {
        return [
            'ticket.id' => $this->ticket->id,
            'ticket.title' => $this->ticket->title,
            'ticket.status' => $this->newStatus->label(),
            'ticket.old_status' => $this->oldStatus->label(),
            'ticket.address' => $this->ticket->address,
            'user.name' => $this->ticket->reporter?->name ?? 'Cittadino',
            'actor.name' => $this->actor?->name ?? 'Sistema',
            'site.name' => config('app.name'),
            'site.url' => config('app.url'),
        ];
    }
    
    private function interpolate(string $template, array $variables): string
    {
        return str_replace(
            array_map(fn ($k) => "{{{$k}}}", array_keys($variables)),
            array_values($variables),
            $template
        );
    }
    
    private function shouldSendNow(UserNotificationPreference $pref): bool
    {
        if ($pref->frequency === 'immediate') {
            return $this->isInUserActiveHours($pref);
        }
        
        // Per digest, accumulare in coda e inviare con scheduler
        return false;
    }
    
    private function isInUserActiveHours(UserNotificationPreference $pref): bool
    {
        if (empty($pref->schedule)) {
            return true;
        }
        
        $now = now();
        $start = Carbon::parse($pref->schedule['start_time']);
        $end = Carbon::parse($pref->schedule['end_time']);
        
        return $now->between($start, $end);
    }
}
```

### Event Listeners

```php
// app/Listeners/SendTicketStatusNotification.php
class SendTicketStatusNotification
{
    public function handle(TicketStatusChanged $event): void
    {
        $ticket = $event->ticket;
        
        // Notifica al cittadino segnalante
        if ($ticket->reporter) {
            $ticket->reporter->notify(new TicketStatusChangedNotification(
                $ticket,
                $event->oldStatus,
                $event->newStatus,
                $event->actor,
            ));
        }
        
        // Notifica all'operatore assegnato (se diverso dall'attore del cambio)
        if ($ticket->assignedTo && $ticket->assignedTo->id !== $event->actor?->id) {
            $ticket->assignedTo->notify(new TicketStatusChangedNotification(
                $ticket,
                $event->oldStatus,
                $event->newStatus,
                $event->actor,
            ));
        }
        
        // Notifica broadcast per in-app real-time
        broadcast(new TicketUpdated($ticket))->toOthers();
    }
}
```

### Scheduled Digests

```php
// app/Console/Commands/SendNotificationDigests.php
class SendNotificationDigests extends Command
{
    protected $signature = 'notifications:send-digests {frequency}';
    
    public function handle(): void
    {
        $frequency = $this->argument('frequency'); // hourly or daily
        
        // Raggruppa notifiche pendenti per utente
        $pendingNotifications = Notification::whereNull('sent_at')
            ->whereHas('notifiable', function ($q) use ($frequency) {
                $q->whereHas('notificationPreferences', function ($q2) use ($frequency) {
                    $q2->where('frequency', $frequency)
                        ->where('is_enabled', true);
                });
            })
            ->get()
            ->groupBy('notifiable_id');
            
        foreach ($pendingNotifications as $userId => $notifications) {
            $user = User::find($userId);
            if (!$user) continue;
            
            // Invia email digest
            $user->notify(new DigestNotification($notifications));
            
            // Marca come inviate
            $notifications->each->update(['sent_at' => now()]);
        }
    }
}

// routes/console.php
Schedule::command('notifications:send-digests hourly')->hourly();
Schedule::command('notifications:send-digests daily')->dailyAt('08:00');
```

### Livewire In-App Notifications

```php
// app/Livewire/NotificationDropdown.php
class NotificationDropdown extends Component
{
    use WithPagination;
    
    public int $unreadCount = 0;
    public bool $isOpen = false;
    
    public function mount(): void
    {
        $this->updateUnreadCount();
    }
    
    #[On('echo:private-notifications.{userId}')] 
    public function handleRealtimeNotification($event): void
    {
        $this->updateUnreadCount();
        $this->dispatch('new-notification', message: $event['message']);
    }
    
    public function markAsRead(string $notificationId): void
    {
        auth()->user()->notifications()
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
            
        $this->updateUnreadCount();
    }
    
    public function markAllAsRead(): void
    {
        auth()->user()->unreadNotifications->markAsRead();
        $this->updateUnreadCount();
    }
    
    public function getNotificationsProperty(): Collection
    {
        return auth()->user()->notifications()
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
    }
    
    private function updateUnreadCount(): void
    {
        $this->unreadCount = auth()->user()->unreadNotifications()->count();
    }
    
    public function render(): View
    {
        return view('livewire.notification-dropdown');
    }
}
```

## Definition of Done

- [ ] Database schema con notifications, templates, preferences
- [ ] Laravel Notification classes per tutti gli eventi ticket
- [ ] Email templates configurabili da admin
- [ ] Push notifications via Firebase/OneSignal
- [ ] SMS integration (Twilio)
- [ ] In-app notification dropdown con real-time updates
- [ ] User preferences panel
- [ ] Rate limiting e digest system
- [ ] Delivery tracking (sent/delivered/opened/failed)
- [ ] Test unitari per tutti i canali
- [ ] Test feature per workflow end-to-end
- [ ] Documentazione per configurazione provider
- [ ] PHPStan level max passa

## Related Issues

- Epic: EPIC-005 User Engagement
- Depends on: STORY-401 (Timeline - eventi da notificare)
- Blocks: STORY-404 (Survey System - notifica survey disponibile)

## Discussion Links

- GitHub Issue: `https://github.com/laraxot/fixcity/issues/403`
- GitHub Discussion: `https://github.com/laraxot/fixcity/discussions/403`
