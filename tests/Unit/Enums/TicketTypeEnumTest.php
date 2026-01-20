<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use ReflectionEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Tests\TestCase;

describe('TicketTypeEnum', function () {
    it('has all required type values', function () {
        $expectedTypes = [
            'ROAD_MAINTENANCE',
            'PUBLIC_LIGHTING',
            'WASTE_COLLECTION',
            'PARKS_AND_GARDENS',
            'SEWAGE_AND_DRAINAGE',
            'PUBLIC_BUILDINGS',
            'ENVIRONMENTAL_REPORTS',
            'PUBLIC_TRANSPORT',
            'URBAN_FURNITURE',
            'PUBLIC_SAFETY',
            'COMPLAINT',
            'SUGGESTION',
            'REPORT',
            'REQUEST',
            'OTHER',
        ];

        $actualTypes = array_column(TicketTypeEnum::cases(), 'name');
        
        expect($actualTypes)->toHaveCount(count($expectedTypes));
        foreach ($expectedTypes as $type) {
            expect($actualTypes)->toContain($type);
        }
    });

    it('provides correct colors for each type', function () {
        $typeColors = [
            TicketTypeEnum::ROAD_MAINTENANCE => '#ff9800',
            TicketTypeEnum::PUBLIC_LIGHTING => '#fbc02d',
            TicketTypeEnum::WASTE_COLLECTION => '#4caf50',
            TicketTypeEnum::PARKS_AND_GARDENS => '#8bc34a',
            TicketTypeEnum::SEWAGE_AND_DRAINAGE => '#2196f3',
            TicketTypeEnum::PUBLIC_BUILDINGS => '#3f51b5',
            TicketTypeEnum::ENVIRONMENTAL_REPORTS => '#f44336',
            TicketTypeEnum::PUBLIC_TRANSPORT => '#9c27b0',
            TicketTypeEnum::URBAN_FURNITURE => '#00bcd4',
            TicketTypeEnum::PUBLIC_SAFETY => '#ff5722',
            TicketTypeEnum::COMPLAINT => 'danger',
            TicketTypeEnum::SUGGESTION => 'success',
            TicketTypeEnum::REPORT => 'warning',
            TicketTypeEnum::REQUEST => 'info',
            TicketTypeEnum::OTHER => 'gray',
        ];

        foreach ($typeColors as $type => $expectedColor) {
            /** @var TicketTypeEnum $type */
            expect($type->getColor())->toBe($expectedColor);
        }
    });

    it('provides correct icons for each type', function () {
        $typeIcons = [
            TicketTypeEnum::ROAD_MAINTENANCE => 'heroicon-o-wrench',
            TicketTypeEnum::PUBLIC_LIGHTING => 'heroicon-o-light-bulb',
            TicketTypeEnum::WASTE_COLLECTION => 'heroicon-o-trash',
            TicketTypeEnum::PARKS_AND_GARDENS => 'heroicon-o-sparkles',
            TicketTypeEnum::SEWAGE_AND_DRAINAGE => 'heroicon-o-archive-box',
            TicketTypeEnum::PUBLIC_BUILDINGS => 'heroicon-o-building-office',
            TicketTypeEnum::ENVIRONMENTAL_REPORTS => 'heroicon-o-globe-alt',
            TicketTypeEnum::PUBLIC_TRANSPORT => 'fas-bus',
            TicketTypeEnum::URBAN_FURNITURE => 'fas-couch',
            TicketTypeEnum::PUBLIC_SAFETY => 'heroicon-o-shield-check',
            TicketTypeEnum::COMPLAINT => 'heroicon-o-exclamation-triangle',
            TicketTypeEnum::SUGGESTION => 'heroicon-o-light-bulb',
            TicketTypeEnum::REPORT => 'heroicon-o-document-report',
            TicketTypeEnum::REQUEST => 'heroicon-o-document',
            TicketTypeEnum::OTHER => 'heroicon-o-question-mark-circle',
        ];

        foreach ($typeIcons as $type => $expectedIcon) {
            /** @var TicketTypeEnum $type */
            expect($type->getIcon())->toBe($expectedIcon);
        }
    });

    it('provides correct labels for each type', function () {
        $typeLabels = [
            TicketTypeEnum::ROAD_MAINTENANCE => 'Manutenzione Stradale',
            TicketTypeEnum::PUBLIC_LIGHTING => 'Illuminazione Pubblica',
            TicketTypeEnum::WASTE_COLLECTION => 'Raccolta Rifiuti',
            TicketTypeEnum::PARKS_AND_GARDENS => 'Aree Verdi e Parchi',
            TicketTypeEnum::SEWAGE_AND_DRAINAGE => 'Fognature e Drenaggi',
            TicketTypeEnum::PUBLIC_BUILDINGS => 'Edifici Pubblici',
            TicketTypeEnum::ENVIRONMENTAL_REPORTS => 'Segnalazioni Ambientali',
            TicketTypeEnum::PUBLIC_TRANSPORT => 'Trasporti Pubblici',
            TicketTypeEnum::URBAN_FURNITURE => 'Arredo Urbano',
            TicketTypeEnum::PUBLIC_SAFETY => 'Sicurezza Pubblica',
            TicketTypeEnum::COMPLAINT => 'Complaint',
            TicketTypeEnum::SUGGESTION => 'Suggestion',
            TicketTypeEnum::REPORT => 'Report',
            TicketTypeEnum::REQUEST => 'Request',
            TicketTypeEnum::OTHER => 'Other',
        ];

        foreach ($typeLabels as $type => $expectedLabel) {
            /** @var TicketTypeEnum $type */
            expect($type->getLabel())->toBe($expectedLabel);
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new ReflectionEnum(TicketTypeEnum::class);
        $interfaces = $reflection->getInterfaceNames();
        
        expect($interfaces)->toContain('Filament\Support\Contracts\HasColor');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasIcon');
        expect($interfaces)->toContain('Filament\Support\Contracts\HasLabel');
    });

    it('can be used in string context', function () {
        $type = TicketTypeEnum::ROAD_MAINTENANCE;
        $stringValue = (string) $type;
        
        expect($stringValue)->toBe('road_maintenance');
        expect($type->value)->toBe('road_maintenance');
    });

    it('provides consistent behavior across all methods', function () {
        $types = TicketTypeEnum::cases();
        
        foreach ($types as $type) {
            // All methods should return non-empty values
            expect($type->getColor())->not->toBeEmpty();
            expect($type->getIcon())->not->toBeEmpty();
            expect($type->getLabel())->not->toBeEmpty();
            
            // Colors should be valid CSS color names or Tailwind classes
            $validColors = ['red', 'green', 'blue', 'purple', 'gray', 'orange', 'yellow', 'indigo'];
            expect($validColors)->toContain($type->getColor());
            
            // Icons should contain valid icon identifiers
            expect($type->getIcon())->toContain('heroicon-o-');
        }
    });

    it('provides meaningful type categorization', function () {
        // Environmental and Safety should be red/orange (urgent)
        expect(TicketTypeEnum::ENVIRONMENTAL_REPORTS->getColor())->toBe('#f44336');
        expect(TicketTypeEnum::PUBLIC_SAFETY->getColor())->toBe('#ff5722');
        
        // Waste Collection and Parks should be green (positive)
        expect(TicketTypeEnum::WASTE_COLLECTION->getColor())->toBe('#4caf50');
        expect(TicketTypeEnum::PARKS_AND_GARDENS->getColor())->toBe('#8bc34a');
        
        // Road Maintenance and Public Lighting should be orange/yellow (attention)
        expect(TicketTypeEnum::ROAD_MAINTENANCE->getColor())->toBe('#ff9800');
        expect(TicketTypeEnum::PUBLIC_LIGHTING->getColor())->toBe('#fbc02d');
    });
});
