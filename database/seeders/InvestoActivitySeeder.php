<?php

declare(strict_types=1);

namespace Modules\Fixcity\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\User\Models\User;
use Modules\Xot\Datas\XotData;

/**
 * Investor demo activities — sample activities for the demo.
 *
 * Creates realistic activity logs for demo tickets (status changes, assignments).
 *
 * Usage:
 *   php artisan db:seed --class="Modules\Fixcity\Database\Seeders\InvestoActivitySeeder"
 */
class InvestoActivitySeeder extends Seeder
{
    public function run(): void
    {
        $userClass = XotData::make()->getUserClass();
        $operator = $userClass::query()->where('email', DemoUsersSeeder::OPERATOR_EMAIL)->first();

        if (! $operator instanceof User) {
            if ($this->command !== null) {
                $this->command->warn('InvestoActivitySeeder: operator missing — skipped.');
            }

            return;
        }

        $tickets = Ticket::query()
            ->where('code', 'like', 'INV-%')
            ->limit(30)
            ->get();

        foreach ($tickets as $ticket) {
            $this->createActivitiesForTicket($ticket, $operator);
        }

        if ($this->command !== null) {
            $this->command->info('InvestoActivitySeeder: attività demo create per '.$tickets->count().' ticket.');
        }
    }

    private function createActivitiesForTicket(Ticket $ticket, User $operator): void
    {
        $createdAt = Carbon::parse($ticket->created_at ?? 'now');
        $status = $ticket->status;

        // Ticket assignment activity
        TicketActivity::query()->create([
            'ticket_id' => $ticket->id,
            'user_id' => $operator->id,
            'event_type' => TicketActivityEventTypeEnum::Assignment->value,
            'payload' => [
                'source' => 'demo',
                'message' => 'Ticket assegnato all\'operatore',
            ],
            'visibility' => TicketActivityVisibilityEnum::Public->value,
            'created_at' => $createdAt,
            'updated_at' => $createdAt,
        ]);

        // Status change activity based on current status
        if (in_array($status, [TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true)) {
            TicketActivity::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $operator->id,
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'payload' => [
                    'source' => 'demo',
                    'from' => TicketStatusEnum::OPEN->value,
                    'to' => TicketStatusEnum::IN_PROGRESS->value,
                ],
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $createdAt->copy()->addHours(2),
                'updated_at' => $createdAt->copy()->addHours(2),
            ]);
        }

        if (in_array($status, [TicketStatusEnum::RESOLVED, TicketStatusEnum::CLOSED], true)) {
            TicketActivity::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $operator->id,
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'payload' => [
                    'source' => 'demo',
                    'from' => TicketStatusEnum::IN_PROGRESS->value,
                    'to' => TicketStatusEnum::RESOLVED->value,
                ],
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $createdAt->copy()->addDays(3),
                'updated_at' => $createdAt->copy()->addDays(3),
            ]);
        }

        if ($status === TicketStatusEnum::CLOSED) {
            TicketActivity::query()->create([
                'ticket_id' => $ticket->id,
                'user_id' => $operator->id,
                'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
                'payload' => [
                    'source' => 'demo',
                    'from' => TicketStatusEnum::RESOLVED->value,
                    'to' => TicketStatusEnum::CLOSED->value,
                ],
                'visibility' => TicketActivityVisibilityEnum::Public->value,
                'created_at' => $createdAt->copy()->addDays(5),
                'updated_at' => $createdAt->copy()->addDays(5),
            ]);
        }
    }
}
