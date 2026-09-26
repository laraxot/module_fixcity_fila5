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
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Datas\XotData;

trait HasTicketRelations
{
    /**
     * @return BelongsTo<Model, Ticket>
     */
    public function owner(): BelongsTo
    {
        /** @var class-string<Model> $userClass */
        $userClass = XotData::make()->getUserClass();

        /** @var BelongsTo<Model, Ticket> */
        return $this->belongsTo($userClass, 'owner_id', 'id');
    }

    /**
     * @return BelongsTo<Model, Ticket>
     */
    public function responsible(): BelongsTo
    {
        /** @var class-string<Model> $userClass */
        $userClass = XotData::make()->getUserClass();

        /** @var BelongsTo<Model, Ticket> */
        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * @return HasMany<TicketActivity, Ticket>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    /**
     * Users subscribed to ticket workflow notifications (pivot ticket_subscribers).
     *
     * @return BelongsToMany<Model, Ticket>
     */
    public function ticketSubscribers(): BelongsToMany
    {
        /** @var class-string<Model> $userClass */
        $userClass = XotData::make()->getUserClass();

        /** @var BelongsToMany<Model, Ticket> */
        return $this->belongsToMany($userClass, 'ticket_subscribers', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * @return HasMany<TicketRelation, Ticket>
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    /**
     * @return HasMany<TicketHour, Ticket>
     */
    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    /**
     * @return BelongsTo<Model, Ticket>
     */
    public function assignee(): BelongsTo
    {
        /** @var class-string<Model> $userClass */
        $userClass = XotData::make()->getUserClass();

        /** @var BelongsTo<Model, Ticket> */
        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * Commenti legacy admin (tabella ticket_comments).
     *
     * @note Legacy compatibility relation. New ticket discussions use comments() from Modules\Comment.
     *
     * @return HasMany<TicketComment, Ticket>
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'id');
    }
}
