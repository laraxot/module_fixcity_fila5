<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
<<<<<<< HEAD
use Modules\Fixcity\Models\TicketComment;

/**
 * @extends Factory<TicketComment>
 */
class TicketCommentFactory extends Factory
{
    /**
     * Modello legacy `ticket_comments` (ticket_id + user_id + content, soft delete).
     *
     * Non è `Modules\Comment\Models\Comment`: quello è polimorfico
     * (commentable_type/id, commentator_type/id, text) e non è un sostituto.
     * Vedi docs/wiki/memories/ticketcomment-legacy-not-substitutable.md
     *
     * @var class-string<TicketComment>
     */
    protected $model = TicketComment::class;
=======
use Modules\Comment\Models\Comment;

/**
 * @extends Factory<Comment>
 */
class TicketCommentFactory extends Factory
{
    protected $model = Comment::class;
>>>>>>> laraxot/dev

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
