<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Fixcity\Models\Report;
use Modules\Fixcity\Enums\ReportStatusEnum;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'location' => [
                'lat' => $this->faker->latitude(),
                'lng' => $this->faker->longitude(),
            ],
            'address' => $this->faker->address(),
            'category' => $this->faker->randomElement([
                'strade',
                'illuminazione',
                'rifiuti',
                'verde_pubblico',
                'arredo_urbano'
            ]),
            'reporter_email' => $this->faker->email(),
            'status' => $this->faker->randomElement(ReportStatusEnum::cases()),
            'metadata' => [
                'priority' => $this->faker->randomElement(['alta', 'media', 'bassa']),
                'zone' => $this->faker->city(),
                'created_at' => $this->faker->dateTimeThisYear()->format('Y-m-d H:i:s')
            ]
        ];
    }
} 