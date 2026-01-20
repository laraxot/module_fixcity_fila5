<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Fixcity\Models\Activity;
use Webmozart\Assert\Assert;

class ActivitySeeder extends Seeder
{
    private array $data = [
        [
            'name' => 'Programming',
            'description' => 'Programming related activities',
        ],
        [
            'name' => 'Testing',
            'description' => 'Testing related activities',
        ],
        [
            'name' => 'Learning',
            'description' => 'Activities related to learning and training',
        ],
        [
            'name' => 'Research',
            'description' => 'Activities related to research',
        ],
        [
            'name' => 'Other',
            'description' => 'Other activities',
        ],
    ];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->data as $item) {

            Assert::isArray($item);
            Assert::allString(array_keys($item));
            Assert::isMap($item);
            Activity::firstOrCreate($item);
        }
    }
}
