<?php

declare(strict_types=1);

use Modules\Fixcity\Enums\TicketTypeEnum;

/**
 * Translations for {@see TicketTypeEnum}.
 *
 * Namespace: fixcity::ticket_type_enum (from TransTrait::getKeyTransClass).
 * Structure: values.{enum_value}.{key}
 */
return [
    'values' => [
        'road_maintenance' => [
            'label' => 'Road Maintenance',
            'description' => 'Reports related to potholes, cracks, damaged road signs',
            'color' => '#ff9800',
            'icon' => 'heroicon-o-wrench',
        ],
        'public_lighting' => [
            'label' => 'Public Lighting',
            'description' => 'Non-functional or damaged street lights',
            'color' => '#fbc02d',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'waste_collection' => [
            'label' => 'Waste Collection',
            'description' => 'Problems with waste collection, full bins',
            'color' => '#4caf50',
            'icon' => 'heroicon-o-trash',
        ],
        'parks_and_gardens' => [
            'label' => 'Parks and Gardens',
            'description' => 'Maintenance of parks, gardens, fallen trees',
            'color' => '#8bc34a',
            'icon' => 'heroicon-o-sparkles',
        ],
        'sewage_and_drainage' => [
            'label' => 'Sewage and Drainage',
            'description' => 'Problems with sewage or blocked drains',
            'color' => '#2196f3',
            'icon' => 'heroicon-o-archive-box',
        ],
        'public_buildings' => [
            'label' => 'Public Buildings',
            'description' => 'Repairs in public buildings such as schools, libraries',
            'color' => '#3f51b5',
            'icon' => 'heroicon-o-building-office',
        ],
        'environmental_reports' => [
            'label' => 'Environmental Reports',
            'description' => 'Pollution problems, illegal waste dumping',
            'color' => '#f44336',
            'icon' => 'heroicon-o-globe-alt',
        ],
        'public_transport' => [
            'label' => 'Public Transport',
            'description' => 'Problems related to public transport services',
            'color' => '#9c27b0',
            'icon' => 'fas-bus',
        ],
        'urban_furniture' => [
            'label' => 'Urban Furniture',
            'description' => 'Maintenance of benches, bins, urban fountains',
            'color' => '#00bcd4',
            'icon' => 'fas-couch',
        ],
        'public_safety' => [
            'label' => 'Public Safety',
            'description' => 'Problems related to citizen safety',
            'color' => '#ff5722',
            'icon' => 'heroicon-o-shield-check',
        ],
        'complaint' => [
            'label' => 'Complaint',
            'color' => 'danger',
            'icon' => 'heroicon-o-exclamation-triangle',
        ],
        'suggestion' => [
            'label' => 'Suggestion',
            'color' => 'success',
            'icon' => 'heroicon-o-light-bulb',
        ],
        'report' => [
            'label' => 'Report',
            'color' => 'warning',
            'icon' => 'heroicon-o-document-report',
        ],
        'request' => [
            'label' => 'Request',
            'color' => 'info',
            'icon' => 'heroicon-o-document',
        ],
        'other' => [
            'label' => 'Other',
            'color' => 'gray',
            'icon' => 'heroicon-o-question-mark-circle',
        ],
    ],
];
