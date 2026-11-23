<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Modules\Fixcity\Actions\RecordTicketActivityAction;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Actions\GetTicketSlaMetricsAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);
describe('Get Ticket Sla Metrics Action', function (): void {
    test('_it_returns_null_average_when_no_resolved_tickets', function (): void {
        /** @var TestCase $this */
        TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        Assert::assertSame(0, $sla['resolved_count']);
        Assert::assertNull($sla['avg_resolution_hours']);
    });

    test('_it_computes_average_resolution_hours', function (): void {
        Carbon::setTestNow('2026-05-29 12:00:00');

        $ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::COMPLAINT,
            'type_id' => null,
        ]);

        DB::connection($ticket->getConnectionName())->table($ticket->getTable())->where('id', $ticket->id)->update([
            'status' => TicketStatusEnum::RESOLVED->value,
            'created_at' => Carbon::parse('2026-05-28 12:00:00'),
            'updated_at' => Carbon::parse('2026-05-29 12:00:00'),
        ]);

        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        Assert::assertSame(1, $sla['resolved_count']);
        Assert::assertSame(24.0, $sla['avg_resolution_hours']);
        Assert::assertSame(1, $sla['resolved_last_30_days']);

        Carbon::setTestNow();
    });
    /** @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md */
    test('_it_uses_real_resolution_transition_time_instead_of_later_updates', function (): void {
        Carbon::setTestNow('2026-05-29 12:00:00');
        Notification::fake();

        $ticket = TicketFactory::new()->createOne([
            'type' => TicketTypeEnum::COMPLAINT,
            'type_id' => null,
        ]);

        DB::connection($ticket->getConnectionName())->table($ticket->getTable())->where('id', $ticket->id)->update([
            'status' => TicketStatusEnum::RESOLVED->value,
            'created_at' => Carbon::parse('2026-05-28 12:00:00'),
            'updated_at' => Carbon::parse('2026-05-29 12:00:00'),
        ]);

        $activity = app(RecordTicketActivityAction::class)->execute(
            $ticket,
            TicketActivityEventTypeEnum::StatusChange,
            TicketStatusEnum::IN_PROGRESS,
            TicketStatusEnum::RESOLVED,
            'Lavoro completato',
            TicketActivityVisibilityEnum::Public,
            null,
        );

        DB::connection($ticket->getConnectionName())
            ->table((new TicketActivity)->getTable())
            ->where('id', $activity->getKey())
            ->update(['created_at' => Carbon::parse('2026-05-28 18:00:00')]);

        $sla = app(GetTicketSlaMetricsAction::class)->execute();

        Assert::assertSame(1, $sla['resolved_count']);
        Assert::assertSame(6.0, $sla['avg_resolution_hours']);
        Assert::assertSame(1, $sla['resolved_last_30_days']);

        Carbon::setTestNow();
    });

});
