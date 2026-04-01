<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use DB;
use Illuminate\Database\Seeder;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\User;
use Illuminate\Support\Str;

class TicketDatabaseSeeder extends Seeder
{

    private array $realTickets = [
        [
            'name' => 'Buca in via Solferino',
            'content' => 'Presenza di una buca pericolosa sul manto stradale che necessita intervento urgente',
            'status' => TicketStatusEnum::PENDING->value,
            'priority' => TicketPriorityEnum::HIGH->value,
        ],
        [
            'name' => 'Panchina danneggiata al parco',
            'content' => 'La panchina risulta instabile e potenzialmente pericolosa per gli utenti del parco',
            'status' => TicketStatusEnum::RESOLVED->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
        ],
        [
            'name' => 'Lampione non funzionante',
            'content' => 'Il lampione dell\'illuminazione pubblica è spento da diversi giorni',
            'status' => TicketStatusEnum::PENDING->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
        ],
        [
            'name' => 'Cestino rifiuti stracolmo',
            'content' => 'Il cestino dei rifiuti non viene svuotato da giorni e sta creando problemi di igiene',
            'status' => TicketStatusEnum::PENDING->value,
            'priority' => TicketPriorityEnum::MEDIUM->value,
        ]
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->realTickets as $ticket) {
            /** @var array<string, mixed> $ticket */
            DB::table('tickets')->insertOrIgnore([
                'name' => $ticket['name'],
                'content' => $ticket['content'],
                'status' => $ticket['status'],
                'priority' => $ticket['priority'],
                'owner_id' => User::first()->id ?? 1,
                'slug' => Str::slug((string) $ticket['name']),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
