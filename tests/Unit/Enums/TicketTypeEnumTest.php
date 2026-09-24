<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Enums;

use Modules\Fixcity\Enums\TicketTypeEnum;

use PHPUnit\Framework\Assert;
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
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
            'URBAN_FURNITURE',
            'PUBLIC_SAFETY',
            'COMPLAINT',
            'SUGGESTION',
            'REPORT',
            'REQUEST',
            'OTHER',
        ];

        $actualTypes = array_column(TicketTypeEnum::cases(), 'name');

        Assert::assertCount(count($expectedTypes), $actualTypes);
        foreach ($expectedTypes as $type) {
            Assert::assertContains($type, $actualTypes);
        }
    });

    it('provides correct colors for each type', function () {
                        $typeColors = [
            [TicketTypeEnum::ROAD_MAINTENANCE, '#ff9800'],
            [TicketTypeEnum::PUBLIC_LIGHTING, '#fbc02d'],
            [TicketTypeEnum::WASTE_COLLECTION, '#4caf50'],
            [TicketTypeEnum::PARKS_AND_GARDENS, '#8bc34a'],
            [TicketTypeEnum::SEWAGE_AND_DRAINAGE, '#2196f3'],
            [TicketTypeEnum::PUBLIC_BUILDINGS, '#3f51b5'],
            [TicketTypeEnum::ENVIRONMENTAL_REPORTS, '#f44336'],
            [TicketTypeEnum::PUBLIC_TRANSPORT, '#9c27b0'],
            [TicketTypeEnum::URBAN_FURNITURE, '#00bcd4'],
            [TicketTypeEnum::PUBLIC_SAFETY, '#ff5722'],
            [TicketTypeEnum::COMPLAINT, 'danger'],
            [TicketTypeEnum::SUGGESTION, 'success'],
            [TicketTypeEnum::REPORT, 'warning'],
            [TicketTypeEnum::REQUEST, 'info'],
            [TicketTypeEnum::OTHER, 'gray'],
        ];

        foreach ($typeColors as [$item, $expected]) {
            Assert::assertSame($expected, $item->getColor());
        }
    });

    it('provides correct icons for each type', function () {
                        $typeIcons = [
            [TicketTypeEnum::ROAD_MAINTENANCE, 'heroicon-o-wrench'],
            [TicketTypeEnum::PUBLIC_LIGHTING, 'heroicon-o-light-bulb'],
            [TicketTypeEnum::WASTE_COLLECTION, 'heroicon-o-trash'],
            [TicketTypeEnum::PARKS_AND_GARDENS, 'heroicon-o-sparkles'],
            [TicketTypeEnum::SEWAGE_AND_DRAINAGE, 'heroicon-o-archive-box'],
            [TicketTypeEnum::PUBLIC_BUILDINGS, 'heroicon-o-building-office'],
            [TicketTypeEnum::ENVIRONMENTAL_REPORTS, 'heroicon-o-globe-alt'],
            [TicketTypeEnum::PUBLIC_TRANSPORT, 'fas-bus'],
            [TicketTypeEnum::URBAN_FURNITURE, 'fas-couch'],
            [TicketTypeEnum::PUBLIC_SAFETY, 'heroicon-o-shield-check'],
            [TicketTypeEnum::COMPLAINT, 'heroicon-o-exclamation-triangle'],
            [TicketTypeEnum::SUGGESTION, 'heroicon-o-light-bulb'],
            [TicketTypeEnum::REPORT, 'heroicon-o-document-chart-bar'],
            [TicketTypeEnum::REQUEST, 'heroicon-o-document'],
            [TicketTypeEnum::OTHER, 'heroicon-o-question-mark-circle'],
        ];

        foreach ($typeIcons as [$item, $expected]) {
            Assert::assertSame($expected, $item->getIcon());
        }
    });

    it('provides correct labels for each type', function () {
                        $typeLabels = [
            [TicketTypeEnum::ROAD_MAINTENANCE, 'Manutenzione Stradale'],
            [TicketTypeEnum::PUBLIC_LIGHTING, 'Illuminazione Pubblica'],
            [TicketTypeEnum::WASTE_COLLECTION, 'Raccolta Rifiuti'],
            [TicketTypeEnum::PARKS_AND_GARDENS, 'Aree Verdi e Parchi'],
            [TicketTypeEnum::SEWAGE_AND_DRAINAGE, 'Fognature e Drenaggi'],
            [TicketTypeEnum::PUBLIC_BUILDINGS, 'Edifici Pubblici'],
            [TicketTypeEnum::ENVIRONMENTAL_REPORTS, 'Segnalazioni Ambientali'],
            [TicketTypeEnum::PUBLIC_TRANSPORT, 'Trasporti Pubblici'],
            [TicketTypeEnum::URBAN_FURNITURE, 'Arredo Urbano'],
            [TicketTypeEnum::PUBLIC_SAFETY, 'Sicurezza Pubblica'],
            [TicketTypeEnum::COMPLAINT, 'Complaint'],
            [TicketTypeEnum::SUGGESTION, 'Suggestion'],
            [TicketTypeEnum::REPORT, 'Report'],
            [TicketTypeEnum::REQUEST, 'Request'],
            [TicketTypeEnum::OTHER, 'Other'],
        ];

        foreach ($typeLabels as [$item, $expected]) {
            Assert::assertSame($expected, $item->getLabel());
        }
    });

    it('implements required Filament interfaces', function () {
        $reflection = new \ReflectionEnum(TicketTypeEnum::class);
        $interfaces = $reflection->getInterfaceNames();

        Assert::assertContains('Filament\Support\Contracts\HasColor', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasIcon', $interfaces);
        Assert::assertContains('Filament\Support\Contracts\HasLabel', $interfaces);
    });

    it('can be used in string context', function () {
        $type = TicketTypeEnum::ROAD_MAINTENANCE;
        Assert::assertSame('road_maintenance', $type->value);
        Assert::assertSame('road_maintenance', $type->value);
    });

    it('provides consistent behavior across all methods', function () {
        $types = TicketTypeEnum::cases();

        foreach ($types as $type) {
            // All methods should return non-empty values
            Assert::assertNotEmpty($type->getColor());
            Assert::assertNotEmpty($type->getIcon());
            Assert::assertNotEmpty($type->getLabel());
        }
    });

    it('provides meaningful type categorization', function () {
        // Environmental and Safety should be red/orange (urgent)
        Assert::assertSame('#f44336', TicketTypeEnum::ENVIRONMENTAL_REPORTS->getColor());
        Assert::assertSame('#ff5722', TicketTypeEnum::PUBLIC_SAFETY->getColor());
        // Waste Collection and Parks should be green (positive)
        Assert::assertSame('#4caf50', TicketTypeEnum::WASTE_COLLECTION->getColor());
        Assert::assertSame('#8bc34a', TicketTypeEnum::PARKS_AND_GARDENS->getColor());
        // Road Maintenance and Public Lighting should be orange/yellow (attention)
        Assert::assertSame('#ff9800', TicketTypeEnum::ROAD_MAINTENANCE->getColor());
        Assert::assertSame('#fbc02d', TicketTypeEnum::PUBLIC_LIGHTING->getColor());
    });
});
