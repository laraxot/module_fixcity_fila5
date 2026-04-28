<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Database\Factories\TicketActivityFactory;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $old_status_id
 * @property int $new_status_id
 * @property int $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property Carbon|null $deleted_at
 * @property string|null $deleted_by
 * @property Ticket|null $ticket
 * @property User|null $user
 *
 * @method static TicketActivityFactory factory($count = null, $state = [])
 * @method static Builder|TicketActivity newModelQuery()
 * @method static Builder|TicketActivity newQuery()
 * @method static Builder|TicketActivity onlyTrashed()
 * @method static Builder|TicketActivity query()
 * @method static Builder|TicketActivity whereCreatedAt($value)
 * @method static Builder|TicketActivity whereCreatedBy($value)
 * @method static Builder|TicketActivity whereDeletedAt($value)
 * @method static Builder|TicketActivity whereDeletedBy($value)
 * @method static Builder|TicketActivity whereId($value)
 * @method static Builder|TicketActivity whereNewStatusId($value)
 * @method static Builder|TicketActivity whereOldStatusId($value)
 * @method static Builder|TicketActivity whereTicketId($value)
 * @method static Builder|TicketActivity whereUpdatedAt($value)
 * @method static Builder|TicketActivity whereUpdatedBy($value)
 * @method static Builder|TicketActivity whereUserId($value)
 * @method static Builder|TicketActivity withTrashed()
 * @method static Builder|TicketActivity withoutTrashed()
 *
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 * @property-read \Modules\Fixcity\Models\Profile|null $deleter
 *
 * @mixin \Eloquent
 */
class TicketActivity extends BaseModel
{
    /** @var list<string> */
    protected $fillable = [
        'ticket_id',
        'old_status_id',
        'new_status_id',
        'user_id',
    ];

    /**
     * Get the ticket that owns the activity.
     *
     * @return BelongsTo<Ticket, $this>
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    /**
     * Get the user that owns the activity.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        /** @var class-string<User> $user_class */
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'user_id', 'id');
    }
}
