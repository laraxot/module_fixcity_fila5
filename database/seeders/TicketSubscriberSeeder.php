<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketSubscriber;
use Modules\Xot\Datas\XotData;

class TicketSubscriberSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection((new TicketSubscriber)->getConnectionName())->hasTable((new TicketSubscriber)->getTable())) {
            $this->command?->warn('TicketSubscriberSeeder: table not migrated, skipped.');

            return;
        }
        if (in_array(Schema::connection((new TicketSubscriber)->getConnectionName())->getColumnType((new TicketSubscriber)->getTable(), 'user_id'), ['bigint', 'integer', 'int'], true)) {
            $this->command?->warn('TicketSubscriberSeeder: legacy integer user_id is incompatible with UUID users, skipped.');

            return;
        }

        $userClass = XotData::make()->getUserClass();
        $userIds = $userClass::query()->orderBy('id')->limit(2)->pluck('id');
        if ($userIds->isEmpty()) {
            return;
        }

        foreach (Ticket::query()->orderBy('id')->limit(3)->get() as $ticket) {
            foreach ($userIds as $userId) {
                TicketSubscriber::query()->firstOrCreate([
                    'ticket_id' => $ticket->getKey(),
                    'user_id' => $userId,
                ]);
            }
        }
    }
}
