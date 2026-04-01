<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Database\Factories\TicketSubscriberFactory;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

/**
 * @property int $id
 * @property int $user_id
 * @property int $ticket_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property Carbon|null $deleted_at
 * @property string|null $deleted_by
 * @property Ticket|null $ticket
 *
 * @method static TicketSubscriberFactory factory($count = null, $state = [])
 * @method static Builder|TicketSubscriber newModelQuery()
 * @method static Builder|TicketSubscriber newQuery()
 * @method static Builder|TicketSubscriber onlyTrashed()
 * @method static Builder|TicketSubscriber query()
 * @method static Builder|TicketSubscriber whereCreatedAt($value)
 * @method static Builder|TicketSubscriber whereCreatedBy($value)
 * @method static Builder|TicketSubscriber whereDeletedAt($value)
 * @method static Builder|TicketSubscriber whereDeletedBy($value)
 * @method static Builder|TicketSubscriber whereId($value)
 * @method static Builder|TicketSubscriber whereTicketId($value)
 * @method static Builder|TicketSubscriber whereUpdatedAt($value)
 * @method static Builder|TicketSubscriber whereUpdatedBy($value)
 * @method static Builder|TicketSubscriber whereUserId($value)
 * @method static Builder|TicketSubscriber withTrashed()
 * @method static Builder|TicketSubscriber withoutTrashed()
 *
 * @property User|null $user
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 * @property-read \Modules\Fixcity\Models\Profile|null $deleter
 *
 * @mixin \Eloquent
 */
class TicketSubscriber extends BaseModel
{
    protected $fillable = [
        'user_id', 'ticket_id',
    ];

    public function user(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'user_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'user_id', 'id');
    }
}
