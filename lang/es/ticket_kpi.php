<?php

declare(strict_types=1);

return [
    'export' => [
        'header_action' => [
            'label' => 'Exportar lista',
        ],
    ],
    'sla' => [
        'avg_hours' => [
            'description' => [
                'label' => 'Horas medias entre creación y actualización (resueltos/cerrados)',
            ],
            'label' => 'Tiempo medio de resolución',
        ],
        'resolved_30d' => [
            'description' => [
                'label' => 'Avisos cerrados en el último mes',
            ],
            'label' => 'Resueltos (30 días)',
        ],
        'resolved_total' => [
            'description' => [
                'label' => 'Total de avisos concluidos',
            ],
            'label' => 'Avisos resueltos',
        ],
    ],
    'stats' => [
        'backlog' => [
            'description' => [
                'label' => 'Abiertos, en curso o en espera',
            ],
            'label' => 'Pendientes activos',
        ],
        'in_progress' => [
            'description' => [
                'label' => 'Avisos asignados a los operadores',
            ],
            'label' => 'En curso',
        ],
        'resolved' => [
            'description' => [
                'label' => 'Avisos concluidos',
            ],
            'label' => 'Resueltos / cerrados',
        ],
        'total' => [
            'description' => [
                'label' => 'Todos los avisos del sistema',
            ],
            'label' => 'Avisos totales',
        ],
    ],
    'export_map_json' => [
        'title' => 'JSON del mapa generado',
        'body' => 'Archivo escrito en :path',
    ],
];
