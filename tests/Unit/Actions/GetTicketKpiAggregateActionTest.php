<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use PHPUnit\Framework\Assert;
use Modules\User\Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Fixcity\Actions\GetTicketKpiAggregateAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Fixcity\Tests\TestCase;

uses(\Modules\Fixcity\Tests\TestCase::class);

describe('Get Ticket Kpi Aggregate Action', function (): void {
    test('_it_aggregates_ticket_counts_by_status', function (): void {
$owner = UserFactory::new()->createOne();
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

        Assert::assertSame(3, $kpi['total']);
        Assert::assertSame(2, $kpi['backlog']);
        Assert::assertSame(1, $kpi['in_progress']);
        Assert::assertSame(1, $kpi['resolved']);
    });
});
