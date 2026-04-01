<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use ReflectionEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Tests\TestCase;

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
        
        expect($actualStatuses)->toHaveCount(count($expectedStatuses));
        foreach ($expectedStatuses as $status) {
            expect($actualStatuses)->toContain($status);
        }
    });

    it('provides correct colors for each status', function () {
        $statusColors = [
            TicketStatusEnum::PENDING => 'yellow',
            TicketStatusEnum::IN_REVIEW => 'blue',
            TicketStatusEnum::IN_PROGRESS => 'orange',
            TicketStatusEnum::ON_HOLD => 'red',
            TicketStatusEnum::RESOLVED => 'green',
            TicketStatusEnum::CLOSED => 'gray',
            TicketStatusEnum::REOPENED => 'pink',
            TicketStatusEnum::OPEN => 'warning',
        ];

        foreach ($statusColors as $status => $expectedColor) {
            /** @var TicketStatusEnum $status */
            expect($status->getColor())->toBe($expectedColor);
        }
    });

    it('provides correct icons for each status', function () {
        $statusIcons = [
            TicketStatusEnum::PENDING => 'ui-hourglass',
            TicketStatusEnum::IN_REVIEW => 'heroicon-o-clock',
            TicketStatusEnum::IN_PROGRESS => 'heroicon-o-arrow-path',
            TicketStatusEnum::ON_HOLD => 'heroicon-o-pause',
            TicketStatusEnum::RESOLVED => 'heroicon-o-check-circle',
            TicketStatusEnum::CLOSED => 'heroicon-o-x-circle',
            TicketStatusEnum::REOPENED => 'heroicon-o-arrow-uturn-left',
            TicketStatusEnum::OPEN => 'heroicon-o-exclamation-circle',
        ];

        foreach ($statusIcons as $status => $expectedIcon) {
            /** @var TicketStatusEnum $status */
            expect($status->getIcon())->toBe($expectedIcon);
        }
    });

    it('provides correct labels for each status', function () {
        $statusLabels = [
            TicketStatusEnum::PENDING => 'Pending',
            TicketStatusEnum::IN_REVIEW => 'In Review',
            TicketStatusEnum::IN_PROGRESS => 'In Progress',
            TicketStatusEnum::ON_HOLD => 'On Hold',
            TicketStatusEnum::RESOLVED => 'Resolved',
            TicketStatusEnum::CLOSED => 'Closed',
            TicketStatusEnum::REOPENED => 'Reopened',
            TicketStatusEnum::OPEN => 'Open',
        ];

        foreach ($statusLabels as $status => $expectedLabel) {
            /** @var TicketStatusEnum $status */
            expect($status->getLabel())->toBe($expectedLabel);
        }
    });

    it('provides correct color classes for each status', function () {
        $statusColorClasses = [
            TicketStatusEnum::PENDING => 'badge-warning',
            TicketStatusEnum::IN_REVIEW => 'badge-info',
            TicketStatusEnum::IN_PROGRESS => 'badge-info',
            TicketStatusEnum::ON_HOLD => 'badge-danger',
            TicketStatusEnum::RESOLVED => 'badge-success',
            TicketStatusEnum::CLOSED => 'badge-secondary',
            TicketStatusEnum::REOPENED => 'badge-secondary',
            TicketStatusEnum::OPEN => 'badge-warning',
        ];

        foreach ($statusColorClasses as $status => $expectedClass) {
            /** @var TicketStatusEnum $status */
            expect($status->getColorClass())->toBe($expectedClass);
        }
    });

    it('provides translated labels for each status', function () {
        $statuses = TicketStatusEnum::cases();
        
        foreach ($statuses as $status) {
            $translatedLabel = $status->label();
            expect($translatedLabel)->not->toBeEmpty();
            expect($translatedLabel)->not->toBe($status->getLabel()); // Should be different from English label
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new ReflectionEnum(TicketStatusEnum::class);
        $interfaces = $reflection->getInterfaceNames();
        
        expect($interfaces)->toContain('Filament\Support\Contracts\HasColor');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasIcon');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasLabel');
    });

    it('can be used in string context', function () {
        $status = TicketStatusEnum::PENDING;
        $stringValue = (string) $status;
        
        expect($stringValue)->toBe('pending');
        expect($status->value)->toBe('pending');
    });

    it('can be compared with string values', function () {
        $status = TicketStatusEnum::PENDING;
        
        expect($status->value === 'pending')->toBeTrue();
        expect($status === TicketStatusEnum::from('pending'))->toBeTrue();
    });

    it('provides consistent behavior across all methods', function () {
        $statuses = TicketStatusEnum::cases();
        
        foreach ($statuses as $status) {
            // All methods should return non-empty values
            expect($status->getColor())->not->toBeEmpty();
            expect($status->getIcon())->not->toBeEmpty();
            expect($status->getLabel())->not->toBeEmpty();
            expect($status->getColorClass())->not->toBeEmpty();
            expect($status->label())->not->toBeEmpty();
            
            // Colors should be valid CSS color names or Tailwind classes
            $validColors = ['yellow', 'blue', 'orange', 'red', 'green', 'gray', 'pink', 'warning'];
            expect($validColors)->toContain($status->getColor());
            
            // Icons should contain valid icon identifiers
            expect($status->getIcon())->toContain('heroicon-o-')->or->toContain('ui-');
        }
    });
});
