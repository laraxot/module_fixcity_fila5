<?php

namespace Modules\Fixcity\Database\Factories;

use Modules\Fixcity\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [];
    }
}
