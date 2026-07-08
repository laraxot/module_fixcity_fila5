<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\TicketComment;

/**
 * @extends Factory<TicketComment>
 */
class TicketCommentFactory extends Factory
{
    protected $model = TicketComment::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
