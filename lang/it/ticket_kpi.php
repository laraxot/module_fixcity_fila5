<?php

declare(strict_types=1);

return [
    'stats' => [
        'total' => [
            'label' => 'Segnalazioni totali',
            'description' => [
                'label' => 'Tutte le segnalazioni nel sistema',
            ],
        ],
        'backlog' => [
            'label' => 'Backlog attivo',
            'description' => [
                'label' => 'Aperte, in lavorazione o in attesa',
            ],
        ],
        'in_progress' => [
            'label' => 'In lavorazione',
            'description' => [
                'label' => 'Segnalazioni assegnate agli operatori',
            ],
        ],
        'resolved' => [
            'label' => 'Risolte / chiuse',
            'description' => [
                'label' => 'Segnalazioni concluse',
            ],
        ],
    ],
    'sla' => [
        'avg_hours' => [
            'label' => 'Tempo medio risoluzione',
            'description' => [
                'label' => 'Ore medie tra creazione e aggiornamento (risolte/chiuse)',
            ],
        ],
        'resolved_total' => [
            'label' => 'Ticket risolti',
            'description' => [
                'label' => 'Totale segnalazioni concluse',
            ],
        ],
        'resolved_30d' => [
            'label' => 'Risolte (30 giorni)',
            'description' => [
                'label' => 'Segnalazioni chiuse nell’ultimo mese',
            ],
        ],
    ],
    'export' => [
        'header_action' => [
            'label' => 'Esporta elenco',
        ],
    ],
];
