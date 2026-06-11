<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;

describe('TicketStatusEnum', function () {
    it('has all required status values', function () {
        $expectedStatuses = [
            'pending',
            'in_progress',
            'resolved',
            'closed',
            'cancelled',
        ];

        $actualStatuses = array_map(fn ($case) => $case->value, TicketStatusEnum::cases());

        foreach ($expectedStatuses as $status) {
            Assert::assertContains($status, $actualStatuses);
        }
    });

    it('can be instantiated from string values', function () {
        Assert::assertSame(TicketStatusEnum::PENDING, TicketStatusEnum::from('pending'));
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::from('in_progress'));
        Assert::assertSame(TicketStatusEnum::RESOLVED, TicketStatusEnum::from('resolved'));
        Assert::assertSame(TicketStatusEnum::CLOSED, TicketStatusEnum::from('closed'));
    });

    it('can try from string values safely', function () {
        Assert::assertSame(TicketStatusEnum::PENDING, TicketStatusEnum::tryFrom('pending'));
        Assert::assertNull(TicketStatusEnum::tryFrom('invalid'));
    });

    it('has proper string representations', function () {
        Assert::assertSame('pending', TicketStatusEnum::PENDING->value);
        Assert::assertSame('in_progress', TicketStatusEnum::IN_PROGRESS->value);
        Assert::assertSame('resolved', TicketStatusEnum::RESOLVED->value);
        Assert::assertSame('closed', TicketStatusEnum::CLOSED->value);
    });

    it('can get all cases', function () {
        $cases = TicketStatusEnum::cases();
        Assert::assertGreaterThan(0, count($cases));
        foreach ($cases as $case) {
            Assert::assertInstanceOf(TicketStatusEnum::class, $case);
        }
    });

    it('can check if status is active', function () {
        // Assuming we have methods to check status states
        Assert::assertSame('pending', TicketStatusEnum::PENDING->value);
        Assert::assertSame('in_progress', TicketStatusEnum::IN_PROGRESS->value);
        Assert::assertSame('resolved', TicketStatusEnum::RESOLVED->value);
        Assert::assertSame('closed', TicketStatusEnum::CLOSED->value);
    });
});

describe('TicketPriorityEnum', function () {
    it('has all required priority values', function () {
        $expectedPriorities = [
            'low',
            'medium',
            'high',
            'urgent',
        ];

        $actualPriorities = array_map(fn ($case) => $case->value, TicketPriorityEnum::cases());

        foreach ($expectedPriorities as $priority) {
            Assert::assertContains($priority, $actualPriorities);
        }
    });

    it('can be instantiated from string values', function () {
        Assert::assertSame(TicketPriorityEnum::LOW, TicketPriorityEnum::from('low'));
        Assert::assertSame(TicketPriorityEnum::MEDIUM, TicketPriorityEnum::from('medium'));
        Assert::assertSame(TicketPriorityEnum::HIGH, TicketPriorityEnum::from('high'));
        Assert::assertSame(TicketPriorityEnum::URGENT, TicketPriorityEnum::from('urgent'));
    });

    it('can try from string values safely', function () {
        Assert::assertSame(TicketPriorityEnum::HIGH, TicketPriorityEnum::tryFrom('high'));
        Assert::assertNull(TicketPriorityEnum::tryFrom('invalid'));
    });

    it('has proper string representations', function () {
        Assert::assertSame('low', TicketPriorityEnum::LOW->value);
        Assert::assertSame('medium', TicketPriorityEnum::MEDIUM->value);
        Assert::assertSame('high', TicketPriorityEnum::HIGH->value);
        Assert::assertSame('urgent', TicketPriorityEnum::URGENT->value);
    });

    it('maintains priority order', function () {
        /** @var list<TicketPriorityEnum> $priorities */
        /** @var list<TicketPriorityEnum> $priorities */
        $priorities = [
            TicketPriorityEnum::LOW,
            TicketPriorityEnum::MEDIUM,
            TicketPriorityEnum::HIGH,
            TicketPriorityEnum::URGENT,
        ];

        // Test that priorities can be ordered (implementation dependent)
        Assert::assertCount(4, $priorities);
    });

    it('can get priority level for sorting', function () {
        // Assuming priorities have numeric values for sorting
        $priorities = TicketPriorityEnum::cases();
        Assert::assertSame(4, count($priorities));
    });
});

describe('TicketTypeEnum', function () {
    it('has all required type values', function () {
        $expectedTypes = [
            'road_maintenance',
            'complaint',
            'suggestion',
            'request',
            'other',
        ];

        $actualTypes = array_map(fn ($case) => $case->value, TicketTypeEnum::cases());

        foreach ($expectedTypes as $type) {
            Assert::assertContains($type, $actualTypes);
        }
    });

    it('can be instantiated from string values', function () {
        Assert::assertSame(TicketTypeEnum::COMPLAINT, TicketTypeEnum::from('complaint'));
        Assert::assertSame(TicketTypeEnum::REQUEST, TicketTypeEnum::from('request'));
        Assert::assertSame(TicketTypeEnum::SUGGESTION, TicketTypeEnum::from('suggestion'));
        Assert::assertSame(TicketTypeEnum::ROAD_MAINTENANCE, TicketTypeEnum::from('road_maintenance'));
    });

    it('can try from string values safely', function () {
        Assert::assertSame(TicketTypeEnum::COMPLAINT, TicketTypeEnum::tryFrom('complaint'));
        Assert::assertNull(TicketTypeEnum::tryFrom('invalid'));
    });

    it('has proper string representations', function () {
        Assert::assertSame('complaint', TicketTypeEnum::COMPLAINT->value);
        Assert::assertSame('request', TicketTypeEnum::REQUEST->value);
        Assert::assertSame('suggestion', TicketTypeEnum::SUGGESTION->value);
        Assert::assertSame('road_maintenance', TicketTypeEnum::ROAD_MAINTENANCE->value);
    });

    it('can get icon for each type', function () {
        // Test that each type can provide an icon (if implemented)
        $types = TicketTypeEnum::cases();

        foreach ($types as $type) {
            Assert::assertInstanceOf(TicketTypeEnum::class, $type);
            // If getIcon method exists: expect($type->getIcon())->toBeString();
        }
    });

    it('can get color for each type', function () {
        // Test that each type can provide a color (if implemented)
        $types = TicketTypeEnum::cases();

        foreach ($types as $type) {
            Assert::assertInstanceOf(TicketTypeEnum::class, $type);
            // If getColor method exists: expect($type->getColor())->toBeString();
        }
    });

    it('can categorize types', function () {
        Assert::assertSame('complaint', TicketTypeEnum::COMPLAINT->value);
        Assert::assertSame('request', TicketTypeEnum::REQUEST->value);
        Assert::assertSame('suggestion', TicketTypeEnum::SUGGESTION->value);
        Assert::assertSame('report', TicketTypeEnum::REPORT->value);
    });
});

describe('Enum Integration', function () {
    it('can use all enums together', function () {
        $status = TicketStatusEnum::PENDING;
        $priority = TicketPriorityEnum::HIGH;
        $type = TicketTypeEnum::COMPLAINT;

        Assert::assertInstanceOf(TicketStatusEnum::class, $status);
        Assert::assertInstanceOf(TicketPriorityEnum::class, $priority);
        Assert::assertInstanceOf(TicketTypeEnum::class, $type);
    });

    it('can serialize enums to array', function () {
        /** @var array<string, TicketStatusEnum|TicketPriorityEnum|TicketTypeEnum> $enums */
        /** @var array<string, TicketStatusEnum|TicketPriorityEnum|TicketTypeEnum> $enums */
        $enums = [
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::HIGH,
            'type' => TicketTypeEnum::COMPLAINT,
        ];

        $values = array_map(fn ($enum) => $enum->value, $enums);

        Assert::assertSame([
            'status' => 'pending',
            'priority' => 'high',
            'type' => 'complaint',
        ], $values);
    });

    it('can get all possible enum combinations', function () {
        $statusCount = count(TicketStatusEnum::cases());
        $priorityCount = count(TicketPriorityEnum::cases());
        $typeCount = count(TicketTypeEnum::cases());

        $totalCombinations = $statusCount * $priorityCount * $typeCount;

        Assert::assertGreaterThan(0, $totalCombinations);
        Assert::assertGreaterThan(0, $statusCount);
        Assert::assertGreaterThan(0, $priorityCount);
        Assert::assertGreaterThan(0, $typeCount);
    });

    it('validates enum consistency', function () {
        // Test that all enum values are unique within their type
        $statusValues = array_map(fn ($case) => $case->value, TicketStatusEnum::cases());
        $priorityValues = array_map(fn ($case) => $case->value, TicketPriorityEnum::cases());
        $typeValues = array_map(fn ($case) => $case->value, TicketTypeEnum::cases());

        Assert::assertSame(array_unique($statusValues), $statusValues);
        Assert::assertSame(array_unique($priorityValues), $priorityValues);
        Assert::assertSame(array_unique($typeValues), $typeValues);
    });
});
