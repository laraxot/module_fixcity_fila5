<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Database\Factories\TicketFactory;
use PHPUnit\Framework\Assert;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Fixcity\Actions\GetTicketSlaMetricsAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;

class GetTicketSlaMetricsActionTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_null_average_when_no_resolved_tickets(): void
    {
        $this->seedTicket(TicketStatusEnum::OPEN);

        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        Assert::assertSame(0, $sla['resolved_count']);
        Assert::assertNull($sla['avg_resolution_hours']);
    }

    public function test_it_computes_average_resolution_hours(): void
    {
        Carbon::setTestNow('2026-05-29 12:00:00');

        $ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::COMPLAINT,
            'type_id' => null,
        ]);

        DB::table($ticket->getTable())->where('id', $ticket->id)->update([
            'status' => TicketStatusEnum::RESOLVED->value,
            'created_at' => Carbon::parse('2026-05-28 12:00:00'),
            'updated_at' => Carbon::parse('2026-05-29 12:00:00'),
        ]);

        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        Assert::assertSame(1, $sla['resolved_count']);
        Assert::assertSame(24.0, $sla['avg_resolution_hours']);
        Assert::assertSame(1, $sla['resolved_last_30_days']);

        Carbon::setTestNow();
    }

    private function seedTicket(TicketStatusEnum $status): void
    {
        $ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::COMPLAINT,
            'type_id' => null,
        ]);

        DB::table($ticket->getTable())
            ->where('id', $ticket->id)
            ->update(['status' => $status->value]);
    }
}
