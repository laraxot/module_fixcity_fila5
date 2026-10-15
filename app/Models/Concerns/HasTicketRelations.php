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
use Modules\Xot\Contracts\UserContract;
use Modules\Xot\Datas\XotData;

/**
 * Relazioni Eloquent per il modello Ticket.
 *
 * @template TModel of Model
 *
 * Utilizza XotData per risolvere dinamicamente la classe utente,
 * garantendo flessibilità senza duplicazione di logiche.
 */
trait HasTicketRelations
{
    /**
     * Proprietario del ticket (owner_id).
     *
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function owner(): BelongsTo
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'owner_id', 'id');
    }

    /**
     * Responsabile del ticket (responsible_id).
     *
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function responsible(): BelongsTo
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * Attività del ticket (ticket_activities).
     *
     * @return HasMany<TicketActivity, $this>
     */
    public function activities(): HasMany
    {
        return $this->hasMany(TicketActivity::class, 'ticket_id', 'id');
    }

    /**
     * Utenti iscritti alle notifiche del ticket (pivot ticket_subscribers).
     *
     * @return BelongsToMany<Model&UserContract, $this>
     */
    public function ticketSubscribers(): BelongsToMany
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsToMany($userClass, 'ticket_subscribers', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    /**
     * Relazioni del ticket (ticket_relations).
     *
     * @return HasMany<TicketRelation, $this>
     */
    public function relations(): HasMany
    {
        return $this->hasMany(TicketRelation::class, 'ticket_id', 'id');
    }

    /**
     * Ore associate al ticket (ticket_hours).
     *
     * @return HasMany<TicketHour, $this>
     */
    public function hours(): HasMany
    {
        return $this->hasMany(TicketHour::class, 'ticket_id', 'id');
    }

    /**
     * Operatore assegnato al ticket (responsible_id).
     *
     * @return BelongsTo<Model&UserContract, $this>
     */
    public function assignee(): BelongsTo
    {
        $userClass = XotData::make()->getUserClass();

        return $this->belongsTo($userClass, 'responsible_id', 'id');
    }

    /**
     * Commenti legacy admin (tabella ticket_comments).
     *
     * @note Legacy compatibility relation. New ticket discussions use comments() from Modules\Comment.
     *
     * @return HasMany<TicketComment, $this>
     */
    public function ticketComments(): HasMany
    {
        return $this->hasMany(TicketComment::class, 'ticket_id', 'id');
    }
}
