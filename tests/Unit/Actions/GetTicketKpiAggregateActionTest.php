<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\GetTicketKpiAggregateAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Tests\TestCase;

class GetTicketKpiAggregateActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_aggregates_ticket_counts_by_status(): void
    {
        $owner = User::factory()->create();
        $table = (new Ticket)->getTable();
        $now = now()->toDateTimeString();

        foreach ([TicketStatusEnum::OPEN, TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::RESOLVED] as $status) {
            DB::table($table)->insert([
                'name' => 'Segnalazione test',
                'slug' => Str::uuid()->toString(),
                'content' => 'contenuto',
                'owner_id' => $owner->id,
                'responsible_id' => null,
                'status_id' => null,
                'order' => 0,
                'status' => $status->value,
                'type' => 'complaint',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $kpi = app(GetTicketKpiAggregateAction::class)->execute();

        $this->assertSame(3, $kpi['total']);
        $this->assertSame(2, $kpi['backlog']);
        $this->assertSame(1, $kpi['in_progress']);
        $this->assertSame(1, $kpi['resolved']);
    }
}
