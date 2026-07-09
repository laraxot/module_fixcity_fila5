<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use Modules\Fixcity\Enums\TicketPriorityEnum;
use ReflectionEnum;

use PHPUnit\Framework\Assert;
describe('TicketPriorityEnum', function () {
    it('has all required priority values', function () {
        $expectedPriorities = [
            'LOW',
            'MEDIUM',
            'HIGH',
            'URGENT',
            'CRITICAL',
        ];

// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
        $actualPriorities = array_column(TicketPriorityEnum::cases(), 'name');

        Assert::assertCount(count($expectedPriorities), $actualPriorities);
        foreach ($expectedPriorities as $priority) {
            Assert::assertContains($priority, $actualPriorities);
        }
    });

    it('provides correct colors for each priority', function () {
                        $priorityColors = [
            [TicketPriorityEnum::LOW, 'gray'],
            [TicketPriorityEnum::MEDIUM, 'blue'],
            [TicketPriorityEnum::HIGH, 'orange'],
            [TicketPriorityEnum::URGENT, 'red'],
            [TicketPriorityEnum::CRITICAL, 'danger'],
        ];

        foreach ($priorityColors as [$item, $expected]) {
            Assert::assertSame($expected, $item->getColor());
        }
    });

    it('provides correct icons for each priority', function () {
                        $priorityIcons = [
            [TicketPriorityEnum::LOW, 'heroicon-o-arrow-down'],
            [TicketPriorityEnum::MEDIUM, 'heroicon-o-minus'],
            [TicketPriorityEnum::HIGH, 'heroicon-o-arrow-up'],
            [TicketPriorityEnum::URGENT, 'heroicon-o-exclamation-triangle'],
            [TicketPriorityEnum::CRITICAL, 'heroicon-o-exclamation-circle'],
        ];

        foreach ($priorityIcons as [$item, $expected]) {
            Assert::assertSame($expected, $item->getIcon());
        }
    });

    it('provides correct labels for each priority', function () {
                        $priorityLabels = [
            [TicketPriorityEnum::LOW, 'Low'],
            [TicketPriorityEnum::MEDIUM, 'Medium'],
            [TicketPriorityEnum::HIGH, 'High'],
            [TicketPriorityEnum::URGENT, 'Urgent'],
            [TicketPriorityEnum::CRITICAL, 'Critical'],
        ];

        foreach ($priorityLabels as [$item, $expected]) {
            Assert::assertSame($expected, $item->getLabel());
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new ReflectionEnum(TicketPriorityEnum::class);
        $interfaces = $reflection->getInterfaceNames();

        Assert::assertContains('Filament\Support\Contracts\HasColor', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasIcon', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasLabel', $interfaces);
    });

    it('can be used in string context', function () {
        $priority = TicketPriorityEnum::MEDIUM;

        Assert::assertSame('medium', $priority->value);
    });

    it('provides consistent behavior across all methods', function () {
        $priorities = TicketPriorityEnum::cases();

        foreach ($priorities as $priority) {
            // All methods should return non-empty values
            Assert::assertNotEmpty($priority->getColor());
            Assert::assertNotEmpty($priority->getIcon());
            Assert::assertNotEmpty($priority->getLabel());
            // Colors should be valid CSS color names or Tailwind classes
            $validColors = ['gray', 'blue', 'orange', 'red', 'danger'];
            Assert::assertContains($priority->getColor(), $validColors);
            // Icons should contain valid icon identifiers
            Assert::assertStringContainsString('heroicon-o-', (string) $priority->getIcon());
        }
    });
});
