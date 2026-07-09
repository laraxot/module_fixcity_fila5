<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Models\TicketComment;
use Modules\Fixcity\Models\TicketHour;
use Modules\Fixcity\Models\TicketRelation;
use Modules\User\Models\User;
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

trait HasTicketRelations
{
    /**
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function owner(): BelongsTo
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function responsible(): BelongsTo
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * @return HasMany<TicketActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    /**
     * Users subscribed to ticket workflow notifications (pivot ticket_subscribers).
     *
     * @return BelongsToMany<User, $this>
     */
    public function ticketSubscribers(): BelongsToMany
    {
        /** @var class-string<User> $userClass */
        $userClass = XotData::make()->getUserClass();

        return $this->belongsToMany($userClass, 'ticket_subscribers', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<TicketRelation, $this>
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    /**
     * @return HasMany<TicketHour, $this>
     */
    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function assignee(): BelongsTo
    {
        /** @var class-string<User> $userModel */
        $userModel = config('auth.providers.users.model');

        return $this->belongsTo($userModel, 'assignee_id');
    }

    /**
     * Commenti legacy admin (tabella ticket_comments).
     *
     * @deprecated Use comments() from Modules\Comment for new ticket discussions.
     *
     * @return HasMany<TicketComment, $this>
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class);
    }
}
