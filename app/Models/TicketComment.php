<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Database\Factories\TicketCommentFactory;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

/**
 * @property int $id
 * @property int $ticket_id
 * @property int $user_id
 * @property string $content
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $updated_by
 * @property string|null $created_by
 * @property string|null $deleted_by
 * @property Ticket|null $ticket
 * @property User|null $user
 *
 * @method static TicketCommentFactory factory($count = null, $state = [])
 * @method static Builder|TicketComment newModelQuery()
 * @method static Builder|TicketComment newQuery()
 * @method static Builder|TicketComment onlyTrashed()
 * @method static Builder|TicketComment query()
 * @method static Builder|TicketComment whereContent($value)
 * @method static Builder|TicketComment whereCreatedAt($value)
 * @method static Builder|TicketComment whereCreatedBy($value)
 * @method static Builder|TicketComment whereDeletedAt($value)
 * @method static Builder|TicketComment whereDeletedBy($value)
 * @method static Builder|TicketComment whereId($value)
 * @method static Builder|TicketComment whereTicketId($value)
 * @method static Builder|TicketComment whereUpdatedAt($value)
 * @method static Builder|TicketComment whereUpdatedBy($value)
 * @method static Builder|TicketComment whereUserId($value)
 * @method static Builder|TicketComment withTrashed()
 * @method static Builder|TicketComment withoutTrashed()
 *
 * @property-read Profile|null $creator
 * @property-read Profile|null $updater
 * @property-read Profile|null $deleter
 *
 * @deprecated Legacy ticket_comments model. Use Modules\Comment\Models\Comment for new ticket discussions.
 *
 * @mixin \Eloquent
 */
class TicketComment extends BaseModel
{
    protected $fillable = [
        'user_id', 'ticket_id', 'content',
    ];

    public static function boot(): void
    {
        parent::boot();
        /*
        static::created(function (TicketComment $item) {
            Assert::notNull($item->ticket);
            foreach ($item->ticket->watchers as $user) {
                $user->notify(new TicketCommented($item));
            }
        });
        */
    }

    /** @return BelongsTo<Model&\Modules\Xot\Contracts\UserContract, $this> */
    public function user(): BelongsTo
    {
        $user_class = XotData::make()->getUserClass();

        return $this->belongsTo($user_class, 'user_id', 'id');
    }

    /** @return BelongsTo<Ticket, $this> */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id', 'id');
    }
}
