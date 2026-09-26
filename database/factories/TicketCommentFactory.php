<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Comment\Models\Comment;

/**
 * @extends Factory<Comment>
 */
class TicketCommentFactory extends Factory
{
    protected $model = Comment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
