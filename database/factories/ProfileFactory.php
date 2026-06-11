<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\Profile;

/**
 * @extends Factory<Profile>
 */
class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [];
    }
}
