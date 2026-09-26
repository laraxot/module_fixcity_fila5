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
     * @return BelongsTo<Model&UserContract, $this, string, string, string>
     */
    public function owner(): BelongsTo
    {
        /** @var class-string<Model&UserContract> $userClass */
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo<Model&UserContract, $this, string, string, string>
     */
    public function responsible(): BelongsTo
    {
        /** @var class-string<Model&UserContract> $userClass */
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * @return HasMany<TicketActivity, $this, string, string>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    /**
     * Users subscribed to ticket workflow notifications (pivot ticket_subscribers).
     *
     * @return BelongsToMany<User, $this, ticket_subscribers, string, string>
     */
    public function ticketSubscribers(): BelongsToMany
    {
        /** @var class-string<User> $userClass */
        $userClass = XotData::make()->getUserClass();

        return $this->belongsToMany($userClass, 'ticket_subscribers', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<TicketRelation, $this, string, string>
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    /**
     * @return HasMany<TicketHour, $this, string, string>
     */
    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    /**
     * @return BelongsTo<User, $this, string, string, string>
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
     * @note Legacy compatibility relation. New ticket discussions use comments() from Modules\Comment.
     *
     * @return HasMany<TicketComment, $this, string, string>
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'id');
    }
}
