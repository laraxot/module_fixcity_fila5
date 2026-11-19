<?php

declare(strict_types=1);

return [
    'export' => [
        'header_action' => [
            'label' => 'Liste exportieren',
        ],
    ],
    'sla' => [
        'avg_hours' => [
            'description' => [
                'label' => 'Durchschnittliche Stunden zwischen Erstellung und Aktualisierung (gelöst/geschlossen)',
            ],
            'label' => 'Durchschnittliche Lösungszeit',
        ],
        'resolved_30d' => [
            'description' => [
                'label' => 'Im letzten Monat geschlossene Meldungen',
            ],
            'label' => 'Gelöst (30 Tage)',
        ],
        'resolved_total' => [
            'description' => [
                'label' => 'Abgeschlossene Meldungen insgesamt',
            ],
            'label' => 'Gelöste Meldungen',
        ],
    ],
    'stats' => [
        'backlog' => [
            'description' => [
                'label' => 'Offen, in Bearbeitung oder wartend',
            ],
            'label' => 'Aktiver Rückstand',
        ],
        'in_progress' => [
            'description' => [
                'label' => 'Den Mitarbeitern zugewiesene Meldungen',
            ],
            'label' => 'In Bearbeitung',
        ],
        'resolved' => [
            'description' => [
                'label' => 'Abgeschlossene Meldungen',
            ],
            'label' => 'Gelöst / geschlossen',
        ],
        'total' => [
            'description' => [
                'label' => 'Alle Meldungen im System',
            ],
            'label' => 'Meldungen insgesamt',
        ],
    ],
    'export_map_json' => [
        'title' => 'Karten-JSON erstellt',
        'body' => 'Datei geschrieben nach :path',
    ],
];
