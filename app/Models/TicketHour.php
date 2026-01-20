<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Support\Carbon;
use Modules\User\Models\User;
use Modules\Fixcity\Database\Factories\TicketHourFactory;
use Illuminate\Database\Eloquent\Builder;
use Carbon\CarbonInterval;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Xot\Datas\XotData;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property int|null $activity_id
 * @property float $value
 * @property string|null $comment
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property Carbon|null $deleted_at
 * @property string|null $deleted_by
 * @property Activity|null $activity
 * @property mixed $for_humans
 * @property Ticket|null $ticket
 * @property User|null $user
 *
 * @method static TicketHourFactory factory($count = null, $state = [])
 * @method static Builder|TicketHour newModelQuery()
 * @method static Builder|TicketHour newQuery()
 * @method static Builder|TicketHour onlyTrashed()
 * @method static Builder|TicketHour query()
 * @method static Builder|TicketHour whereActivityId($value)
 * @method static Builder|TicketHour whereComment($value)
 * @method static Builder|TicketHour whereCreatedAt($value)
 * @method static Builder|TicketHour whereCreatedBy($value)
 * @method static Builder|TicketHour whereDeletedAt($value)
 * @method static Builder|TicketHour whereDeletedBy($value)
 * @method static Builder|TicketHour whereId($value)
 * @method static Builder|TicketHour whereTicketId($value)
 * @method static Builder|TicketHour whereUpdatedAt($value)
 * @method static Builder|TicketHour whereUpdatedBy($value)
 * @method static Builder|TicketHour whereUserId($value)
 * @method static Builder|TicketHour whereValue($value)
 * @method static Builder|TicketHour withTrashed()
 * @method static Builder|TicketHour withoutTrashed()
 *
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 *
 * @mixin \Eloquent
 */
class TicketHour extends BaseModel
{
    protected $fillable = [
        'user_id', 'ticket_id', 'value', 'comment', 'activity_id',
    ];

    public function user(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'user_id', 'id');
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'activity_id', 'id');
    }

    public function forHumans(): Attribute
    {
        return new Attribute(
            get: function () {
                $seconds = $this->value * 3600;

                return CarbonInterval::seconds($seconds)->cascade()->forHumans();
            }
        );
    }
}
