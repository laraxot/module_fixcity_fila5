<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use Modules\Fixcity\Enums\TicketStatusEnum;
use ReflectionEnum;

use PHPUnit\Framework\Assert;
describe('TicketStatusEnum', function () {
    it('has all required status values', function () {
        $expectedStatuses = [
            'PENDING',
            'IN_REVIEW',
            'IN_PROGRESS',
            'ON_HOLD',
            'RESOLVED',
            'CLOSED',
            'REOPENED',
            'OPEN',
        ];

        $actualStatuses = array_column(TicketStatusEnum::cases(), 'name');

        Assert::assertCount(count($expectedStatuses), $actualStatuses);
        foreach ($expectedStatuses as $status) {
            Assert::assertContains($status, $actualStatuses);
        }
    });

    it('provides correct colors for each status', function () {
                        $statusColors = [
            [TicketStatusEnum::PENDING, 'yellow'],
            [TicketStatusEnum::IN_REVIEW, 'blue'],
            [TicketStatusEnum::IN_PROGRESS, 'orange'],
            [TicketStatusEnum::ON_HOLD, 'red'],
            [TicketStatusEnum::RESOLVED, 'green'],
            [TicketStatusEnum::CLOSED, 'gray'],
            [TicketStatusEnum::REOPENED, 'pink'],
            [TicketStatusEnum::OPEN, 'warning'],
        ];

        foreach ($statusColors as [$item, $expected]) {
            Assert::assertSame($expected, $item->getColor());
        }
    });

    it('provides correct icons for each status', function () {
                        $statusIcons = [
            [TicketStatusEnum::PENDING, 'ui-hourglass'],
            [TicketStatusEnum::IN_REVIEW, 'heroicon-o-clock'],
            [TicketStatusEnum::IN_PROGRESS, 'heroicon-o-arrow-path'],
            [TicketStatusEnum::ON_HOLD, 'heroicon-o-pause'],
            [TicketStatusEnum::RESOLVED, 'heroicon-o-check-circle'],
            [TicketStatusEnum::CLOSED, 'heroicon-o-x-circle'],
            [TicketStatusEnum::REOPENED, 'heroicon-o-arrow-uturn-left'],
            [TicketStatusEnum::OPEN, 'heroicon-o-exclamation-circle'],
        ];

        foreach ($statusIcons as [$item, $expected]) {
            Assert::assertSame($expected, $item->getIcon());
        }
    });

    it('provides correct labels for each status', function () {
                        $statusLabels = [
            [TicketStatusEnum::PENDING, 'Pending'],
            [TicketStatusEnum::IN_REVIEW, 'In Review'],
            [TicketStatusEnum::IN_PROGRESS, 'In Progress'],
            [TicketStatusEnum::ON_HOLD, 'On Hold'],
            [TicketStatusEnum::RESOLVED, 'Resolved'],
            [TicketStatusEnum::CLOSED, 'Closed'],
            [TicketStatusEnum::REOPENED, 'Reopened'],
            [TicketStatusEnum::OPEN, 'Open'],
        ];

        foreach ($statusLabels as [$item, $expected]) {
            Assert::assertSame($expected, $item->getLabel());
        }
    });

    it('provides translated labels for each status', function () {
        $statuses = TicketStatusEnum::cases();

        foreach ($statuses as $status) {
            Assert::assertNotEmpty($status->getLabel());
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new ReflectionEnum(TicketStatusEnum::class);
        $interfaces = $reflection->getInterfaceNames();

        Assert::assertContains('Filament\Support\Contracts\HasColor', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasIcon', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasLabel', $interfaces);
    });

    it('can be used in string context', function () {
        $status = TicketStatusEnum::PENDING;

        Assert::assertSame('pending', $status->value);
    });

    it('can be compared with string values', function () {
        $status = TicketStatusEnum::PENDING;

        Assert::assertTrue($status->value === 'pending');
        Assert::assertTrue($status === TicketStatusEnum::from('pending'));
    });

    it('provides consistent behavior across all methods', function () {
        $statuses = TicketStatusEnum::cases();

        foreach ($statuses as $status) {
            Assert::assertNotEmpty($status->getColor());
            Assert::assertNotEmpty($status->getIcon());
            Assert::assertNotEmpty($status->getLabel());
        }
    });
});
