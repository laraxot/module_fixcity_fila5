<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Datas\XotData;

class TicketActivitySeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::connection((new TicketActivity)->getConnectionName())->hasTable((new TicketActivity)->getTable())) {
            $this->command?->warn('TicketActivitySeeder: table not migrated, skipped.');

            return;
        }

        $userClass = XotData::make()->getUserClass();
        $userId = $userClass::query()->where('email', DemoUsersSeeder::OPERATOR_EMAIL)->value('id');
        if ($userId === null) {
            return;
        }

        /** @var array<string, array{0: TicketStatusEnum, 1: TicketStatusEnum}> $transitions */
        $transitions = [
            'DEMO-001' => [TicketStatusEnum::OPEN, TicketStatusEnum::IN_PROGRESS],
            'DEMO-002' => [TicketStatusEnum::PENDING, TicketStatusEnum::OPEN],
            'DEMO-003' => [TicketStatusEnum::PENDING, TicketStatusEnum::OPEN],
            'DEMO-004' => [TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED],
            'DEMO-005' => [TicketStatusEnum::OPEN, TicketStatusEnum::IN_PROGRESS],
            'DEMO-006' => [TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::ON_HOLD],
        ];

        $index = 0;
        foreach ($transitions as $code => $transition) {
            $from = $transition[0];
            $to = $transition[1];
            $ticket = Ticket::query()->where('code', $code)->first();
            if ($ticket === null) {
                continue;
            }

            $reason = 'Demo: aggiornamento stato '.$to->getLabel();
            $activity = TicketActivity::query()->updateOrCreate(
                [
                    'ticket_id' => $ticket->getKey(),
                    'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                    'reason' => $reason,
                ],
                [
                    'user_id' => $userId,
                    'payload' => ['source' => 'demo', 'from' => $from->value, 'to' => $to->value],
                    'visibility' => TicketActivityVisibilityEnum::Public->value,
                ],
            );

            $timestamp = now()->subDays(7 - $index);
            $activity->forceFill(['created_at' => $timestamp, 'updated_at' => $timestamp])->save();
            ++$index;
        }
    }
}
