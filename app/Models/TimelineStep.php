<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Database\Factories\TimelineStepFactory;
use Modules\User\Models\User;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

/**
 * @property int $id
 * @property int $ticket_id
 * @property string $type
 * @property string $description
 * @property string|null $responsible
 * @property Carbon|null $performed_at
 * @property string|null $action_note
 * @property bool $requires_followup
 * @property Carbon|null $followup_at
 * @property TimelineAttachment|null $attachment
 * @property bool $private_note
 * @property json|null $metadata
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property Ticket|null $ticket
 *
 * @method static TimelineStepFactory factory($count = null, $state = [])
 * @method static Builder|TimelineStep newModelQuery()
 * @method static Builder|TimelineStep newQuery()
 * @method static Builder|TimelineStep onlyTrashed()
 * @method static Builder|TimelineStep query()
 * @method static Builder|TimelineStep whereType($value)
 * @method static Builder|TimelineStep whereTicketId($value)
 * @method static Builder|TimelineStep wherePerformedAt($value)
 * @method static Builder|TimelineStep whereResponsible($value)
 * @method static Builder|TimelineStep whereRequiresFollowup($value)
 * @method static Builder|TimelineStep whereFollowupAt($value)
 * @method static Builder|TimelineStep whereAttachmentId($value)
 * @method static Builder|TimelineStep wherePrivateNote($value)
 * @method static Builder|TimelineStep whereMetadata($value)
 * @method static Builder|TimelineStep whereCreatedAt($value)
 * @method static Builder|TimelineStep whereUpdatedAt($value)
 * @method static Builder|TimelineStep whereUpdatedBy($value)
 * @method static Builder|TimelineStep whereCreatedBy($value)
 * @method static Builder|TimelineStep withTrashed()
 * @method static Builder|TimelineStep withoutTrashed()
 *
 * @mixin \Eloquent
 */
class TimelineStep extends BaseModel
{
    protected $fillable = [
        'ticket_id', 'type', 'description', 'responsible', 'performed_at',
        'action_note', 'requires_followup', 'followup_at',
        'attachment_id', 'private_note', 'metadata',
    ];

    protected $casts = [
        'requires_followup' => 'boolean',
        'private_note' => 'boolean',
        'metadata' => 'json',
        'performed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        parent::booted();

        static::creating(function (TimelineStep $step): void {
            // Esegui automati in fase di creazione
        });

        static::updated(function (TimelineStep $step): void {
            // Aggiornamenti automatici in fase di modifica
        });
    }

    /** @return BelongsTo<Ticket, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo|TimelineAttachment|null */
    public function attachment(): BelongsTo
    {
        return $this->belongsTo(TimelineAttachment::class, 'attachment_id', 'id');
    }

    /**
     * Determina se il ticket può essere chiuso da questo stato.
     */
    public function canClose(): bool
    {
        return in_array($this->type, [
            'response_received',
            'response_negative',
            'response_delegated',
            'response_soon',
        ]);
    }

    /**
     * Determina il tipo di chiusura appropriato in base al passo corrente.
     */
    public function getClosureType(): ?string
    {
        $type = $this->type;
        $esito = ($this->metadata['esito'] ?? 'unknown');

        $map = [
                'risolto' => 'resolved',
                'delegato' => 'delegated',
                'non risolto' => 'not_resolved',
            ];
            return $map[$esito] ?? 'not_resolved';

        if ($type === 'response_negative') {
            return 'rejected';
        }
        if ($type === 'response_delegated') {
            return 'delegated';
        }
        if ($type === 'response_soon') {
            return null;
        }

        return null;
    }

    /**
     * Restituisce la label visibile per il tipo di chiusura.
     */
    public function getClosureLabel(): ?string
    {
        $map = [
            'resolved' => 'Risolto',
            'rejected' => 'Scartato',
            'delegato' => 'Delegato',
            'not_resolved' => 'Non risolto',
        ];

        return $map[$this->getClosureType()] ?? null;
    }

    /**
     * Determina se questo passo richiede un sollecito automatico.
     */
    public function needsReminder(): bool
    {
        return $this->requires_followup && $this->followup_at && $this->followup_at->isPast() && $this->ticket->isWaitingForReminder();
    }
}