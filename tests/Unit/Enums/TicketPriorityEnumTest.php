<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use ReflectionEnum;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Tests\TestCase;

describe('TicketPriorityEnum', function () {
    it('has all required priority values', function () {
        $expectedPriorities = [
            'LOW',
            'MEDIUM',
            'HIGH',
            'URGENT',
            'CRITICAL',
        ];

        $actualPriorities = array_column(TicketPriorityEnum::cases(), 'name');
        
        expect($actualPriorities)->toHaveCount(count($expectedPriorities));
        foreach ($expectedPriorities as $priority) {
            expect($actualPriorities)->toContain($priority);
        }
    });

    it('provides correct colors for each priority', function () {
        $priorityColors = [
            TicketPriorityEnum::LOW => 'gray',
            TicketPriorityEnum::MEDIUM => 'blue',
            TicketPriorityEnum::HIGH => 'orange',
            TicketPriorityEnum::URGENT => 'red',
            TicketPriorityEnum::CRITICAL => 'danger',
        ];

        foreach ($priorityColors as $priority => $expectedColor) {
            /** @var TicketPriorityEnum $priority */
            expect($priority->getColor())->toBe($expectedColor);
        }
    });

    it('provides correct icons for each priority', function () {
        $priorityIcons = [
            TicketPriorityEnum::LOW => 'heroicon-o-arrow-down',
            TicketPriorityEnum::MEDIUM => 'heroicon-o-minus',
            TicketPriorityEnum::HIGH => 'heroicon-o-arrow-up',
            TicketPriorityEnum::URGENT => 'heroicon-o-exclamation-triangle',
            TicketPriorityEnum::CRITICAL => 'heroicon-o-exclamation-circle',
        ];

        foreach ($priorityIcons as $priority => $expectedIcon) {
            /** @var TicketPriorityEnum $priority */
            expect($priority->getIcon())->toBe($expectedIcon);
        }
    });

    it('provides correct labels for each priority', function () {
        $priorityLabels = [
            TicketPriorityEnum::LOW => 'Low',
            TicketPriorityEnum::MEDIUM => 'Medium',
            TicketPriorityEnum::HIGH => 'High',
            TicketPriorityEnum::URGENT => 'Urgent',
            TicketPriorityEnum::CRITICAL => 'Critical',
        ];

        foreach ($priorityLabels as $priority => $expectedLabel) {
            /** @var TicketPriorityEnum $priority */
            expect($priority->getLabel())->toBe($expectedLabel);
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new ReflectionEnum(TicketPriorityEnum::class);
        $interfaces = $reflection->getInterfaceNames();
        
        expect($interfaces)->toContain('Filament\Support\Contracts\HasColor');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasIcon');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasLabel');
    });

    it('can be used in string context', function () {
        $priority = TicketPriorityEnum::MEDIUM;
        $stringValue = (string) $priority;
        
        expect($stringValue)->toBe('medium');
        expect($priority->value)->toBe('medium');
    });

    it('provides consistent behavior across all methods', function () {
        $priorities = TicketPriorityEnum::cases();
        
        foreach ($priorities as $priority) {
            // All methods should return non-empty values
            expect($priority->getColor())->not->toBeEmpty();
            expect($priority->getIcon())->not->toBeEmpty();
            expect($priority->getLabel())->not->toBeEmpty();
            
            // Colors should be valid CSS color names or Tailwind classes
            $validColors = ['gray', 'blue', 'orange', 'red', 'danger'];
            expect($validColors)->toContain($priority->getColor());
            
            // Icons should contain valid icon identifiers
            expect($priority->getIcon())->toContain('heroicon-o-');
        }
    });
});
