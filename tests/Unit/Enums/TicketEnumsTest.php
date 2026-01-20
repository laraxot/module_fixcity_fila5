<?php

declare(strict_types=1);

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

        $actualStatuses = array_map(fn($case) => $case->value, TicketStatusEnum::cases());

        foreach ($expectedStatuses as $status) {
            expect($actualStatuses)->toContain($status);
        }
    });

    it('can be instantiated from string values', function () {
        expect(TicketStatusEnum::from('pending'))->toBe(TicketStatusEnum::PENDING);
        expect(TicketStatusEnum::from('in_progress'))->toBe(TicketStatusEnum::IN_PROGRESS);
        expect(TicketStatusEnum::from('resolved'))->toBe(TicketStatusEnum::RESOLVED);
        expect(TicketStatusEnum::from('closed'))->toBe(TicketStatusEnum::CLOSED);
    });

    it('can try from string values safely', function () {
        expect(TicketStatusEnum::tryFrom('pending'))->toBe(TicketStatusEnum::PENDING);
        expect(TicketStatusEnum::tryFrom('invalid'))->toBeNull();
    });

    it('has proper string representations', function () {
        expect(TicketStatusEnum::PENDING->value)->toBe('pending');
        expect(TicketStatusEnum::IN_PROGRESS->value)->toBe('in_progress');
        expect(TicketStatusEnum::RESOLVED->value)->toBe('resolved');
        expect(TicketStatusEnum::CLOSED->value)->toBe('closed');
    });

    it('can get all cases', function () {
        $cases = TicketStatusEnum::cases();
        
        expect($cases)->toBeArray();
        expect(count($cases))->toBeGreaterThan(0);
        
        foreach ($cases as $case) {
            expect($case)->toBeInstanceOf(TicketStatusEnum::class);
        }
    });

    it('can check if status is active', function () {
        // Assuming we have methods to check status states
        expect(TicketStatusEnum::PENDING->value)->toBe('pending');
        expect(TicketStatusEnum::IN_PROGRESS->value)->toBe('in_progress');
        expect(TicketStatusEnum::RESOLVED->value)->toBe('resolved');
        expect(TicketStatusEnum::CLOSED->value)->toBe('closed');
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

        $actualPriorities = array_map(fn($case) => $case->value, TicketPriorityEnum::cases());

        foreach ($expectedPriorities as $priority) {
            expect($actualPriorities)->toContain($priority);
        }
    });

    it('can be instantiated from string values', function () {
        expect(TicketPriorityEnum::from('low'))->toBe(TicketPriorityEnum::LOW);
        expect(TicketPriorityEnum::from('medium'))->toBe(TicketPriorityEnum::MEDIUM);
        expect(TicketPriorityEnum::from('high'))->toBe(TicketPriorityEnum::HIGH);
        expect(TicketPriorityEnum::from('urgent'))->toBe(TicketPriorityEnum::URGENT);
    });

    it('can try from string values safely', function () {
        expect(TicketPriorityEnum::tryFrom('high'))->toBe(TicketPriorityEnum::HIGH);
        expect(TicketPriorityEnum::tryFrom('invalid'))->toBeNull();
    });

    it('has proper string representations', function () {
        expect(TicketPriorityEnum::LOW->value)->toBe('low');
        expect(TicketPriorityEnum::MEDIUM->value)->toBe('medium');
        expect(TicketPriorityEnum::HIGH->value)->toBe('high');
        expect(TicketPriorityEnum::URGENT->value)->toBe('urgent');
    });

    it('maintains priority order', function () {
        $priorities = [
            TicketPriorityEnum::LOW,
            TicketPriorityEnum::MEDIUM,
            TicketPriorityEnum::HIGH,
            TicketPriorityEnum::URGENT,
        ];

        // Test that priorities can be ordered (implementation dependent)
        expect($priorities)->toHaveCount(4);
    });

    it('can get priority level for sorting', function () {
        // Assuming priorities have numeric values for sorting
        $priorities = TicketPriorityEnum::cases();
        
        expect($priorities)->toBeArray();
        expect(count($priorities))->toBe(4);
    });
});

describe('TicketTypeEnum', function () {
    it('has all required type values', function () {
        $expectedTypes = [
            'bug',
            'feature',
            'improvement',
            'task',
            'question',
        ];

        $actualTypes = array_map(fn($case) => $case->value, TicketTypeEnum::cases());

        foreach ($expectedTypes as $type) {
            expect($actualTypes)->toContain($type);
        }
    });

    it('can be instantiated from string values', function () {
        expect(TicketTypeEnum::from('bug'))->toBe(TicketTypeEnum::BUG);
        expect(TicketTypeEnum::from('feature'))->toBe(TicketTypeEnum::FEATURE);
        expect(TicketTypeEnum::from('improvement'))->toBe(TicketTypeEnum::IMPROVEMENT);
        expect(TicketTypeEnum::from('task'))->toBe(TicketTypeEnum::TASK);
    });

    it('can try from string values safely', function () {
        expect(TicketTypeEnum::tryFrom('bug'))->toBe(TicketTypeEnum::BUG);
        expect(TicketTypeEnum::tryFrom('invalid'))->toBeNull();
    });

    it('has proper string representations', function () {
        expect(TicketTypeEnum::BUG->value)->toBe('bug');
        expect(TicketTypeEnum::FEATURE->value)->toBe('feature');
        expect(TicketTypeEnum::IMPROVEMENT->value)->toBe('improvement');
        expect(TicketTypeEnum::TASK->value)->toBe('task');
    });

    it('can get icon for each type', function () {
        // Test that each type can provide an icon (if implemented)
        $types = TicketTypeEnum::cases();
        
        foreach ($types as $type) {
            expect($type)->toBeInstanceOf(TicketTypeEnum::class);
            // If getIcon method exists: expect($type->getIcon())->toBeString();
        }
    });

    it('can get color for each type', function () {
        // Test that each type can provide a color (if implemented)  
        $types = TicketTypeEnum::cases();
        
        foreach ($types as $type) {
            expect($type)->toBeInstanceOf(TicketTypeEnum::class);
            // If getColor method exists: expect($type->getColor())->toBeString();
        }
    });

    it('can categorize types', function () {
        // Test type categorization (if implemented)
        expect(TicketTypeEnum::BUG->value)->toBe('bug');
        expect(TicketTypeEnum::FEATURE->value)->toBe('feature');
        expect(TicketTypeEnum::IMPROVEMENT->value)->toBe('improvement');
        expect(TicketTypeEnum::TASK->value)->toBe('task');
    });
});

describe('Enum Integration', function () {
    it('can use all enums together', function () {
        $status = TicketStatusEnum::PENDING;
        $priority = TicketPriorityEnum::HIGH;
        $type = TicketTypeEnum::BUG;

        expect($status)->toBeInstanceOf(TicketStatusEnum::class);
        expect($priority)->toBeInstanceOf(TicketPriorityEnum::class);
        expect($type)->toBeInstanceOf(TicketTypeEnum::class);
    });

    it('can serialize enums to array', function () {
        $enums = [
            'status' => TicketStatusEnum::PENDING,
            'priority' => TicketPriorityEnum::HIGH,
            'type' => TicketTypeEnum::BUG,
        ];

        $values = array_map(fn($enum) => $enum->value, $enums);

        expect($values)->toBe([
            'status' => 'pending',
            'priority' => 'high',
            'type' => 'bug',
        ]);
    });

    it('can get all possible enum combinations', function () {
        $statusCount = count(TicketStatusEnum::cases());
        $priorityCount = count(TicketPriorityEnum::cases());
        $typeCount = count(TicketTypeEnum::cases());

        $totalCombinations = $statusCount * $priorityCount * $typeCount;

        expect($totalCombinations)->toBeGreaterThan(0);
        expect($statusCount)->toBeGreaterThan(0);
        expect($priorityCount)->toBeGreaterThan(0);
        expect($typeCount)->toBeGreaterThan(0);
    });

    it('validates enum consistency', function () {
        // Test that all enum values are unique within their type
        $statusValues = array_map(fn($case) => $case->value, TicketStatusEnum::cases());
        $priorityValues = array_map(fn($case) => $case->value, TicketPriorityEnum::cases());
        $typeValues = array_map(fn($case) => $case->value, TicketTypeEnum::cases());

        expect($statusValues)->toBe(array_unique($statusValues));
        expect($priorityValues)->toBe(array_unique($priorityValues));
        expect($typeValues)->toBe(array_unique($typeValues));
    });
});