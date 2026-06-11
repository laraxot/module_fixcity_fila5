<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected $model = User::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
