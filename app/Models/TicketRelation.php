<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Support\Carbon;
use Modules\Fixcity\Database\Factories\TicketRelationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $ticket_id
 * @property int|null $relation_id
 * @property string $type
 * @property int $sort
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property Carbon|null $deleted_at
 * @property string|null $deleted_by
 * @property Ticket|null $relation
 * @property Ticket|null $ticket
 * @method static TicketRelationFactory factory($count = null, $state = [])
 * @method static Builder|TicketRelation newModelQuery()
 * @method static Builder|TicketRelation newQuery()
 * @method static Builder|TicketRelation onlyTrashed()
 * @method static Builder|TicketRelation query()
 * @method static Builder|TicketRelation whereCreatedAt($value)
 * @method static Builder|TicketRelation whereCreatedBy($value)
 * @method static Builder|TicketRelation whereDeletedAt($value)
 * @method static Builder|TicketRelation whereDeletedBy($value)
 * @method static Builder|TicketRelation whereId($value)
 * @method static Builder|TicketRelation whereRelationId($value)
 * @method static Builder|TicketRelation whereSort($value)
 * @method static Builder|TicketRelation whereTicketId($value)
 * @method static Builder|TicketRelation whereType($value)
 * @method static Builder|TicketRelation whereUpdatedAt($value)
 * @method static Builder|TicketRelation whereUpdatedBy($value)
 * @method static Builder|TicketRelation withTrashed()
 * @method static Builder|TicketRelation withoutTrashed()
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 * @property-read \Modules\Fixcity\Models\Profile|null $deleter
 * @mixin \Eloquent
 */
class TicketRelation extends BaseModel
{
    protected $fillable = [
        'ticket_id', 'type', 'relation_id', 'sort',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function relation(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'relation_id', 'id');
    }
}
