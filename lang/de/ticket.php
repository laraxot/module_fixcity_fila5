<?php

declare(strict_types=1);

return [
    'heading' => [
        'title' => [
            'label' => 'Meldungen',
        ],
        'subtitle' => [
            'text' => 'Sehen Sie sich die offenen Meldungen in Ihrer Gemeinde an und filtern Sie die Ergebnisse nach Kategorie.',
        ],
    ],
    'results' => [
        'count' => [
            'text' => ':count Meldungen gefunden',
        ],
        'empty' => 'Keine Meldungen gefunden.',
    ],
    'filter' => [
        'button' => [
            'label' => 'Filtern',
        ],
        'remove' => [
            'label' => 'Filter zurücksetzen',
        ],
    ],
    'map' => [
        'cta' => [
            'title' => [
                'label' => 'Störung bemerkt?',
            ],
            'text' => [
                'label' => 'Senden Sie eine neue Meldung und helfen Sie der Gemeinde, schneller zu reagieren.',
            ],
            'button' => [
                'label' => 'Störung melden',
            ],
        ],
        'image' => [
            'alt' => 'Karte der Meldungen',
        ],
    ],
    'contacts' => [
        'block_title' => [
            'label' => 'Gemeinde kontaktieren',
        ],
        'faq' => [
            'link' => [
                'label' => 'Häufige Fragen lesen',
            ],
        ],
        'assistenza' => [
            'link' => [
                'label' => 'Hilfe anfordern',
            ],
        ],
        'phone' => [
            'link' => [
                'label' => 'Gebührenfreie Nummer 05 0505 anrufen',
            ],
        ],
        'appointment' => [
            'link' => [
                'label' => 'Termin vereinbaren',
            ],
        ],
    ],
    'pagination' => ['aria' => 'Seitennavigation der Meldungen', 'page' => 'Seite :page', 'previous' => 'Vorherige Seite', 'next' => 'Nächste Seite'],
    'page' => ['title' => ['label' => 'Störung melden']],
    'track' => ['title' => 'Meldung verfolgen', 'account_subtitle' => 'Sie sehen eine Meldung, die mit Ihrem Konto verknüpft ist.', 'subtitle' => 'Geben Sie den Bestätigungscode ein, um den Status zu prüfen.', 'submit' => 'Suchen', 'help' => 'Geben Sie den vollständigen Code ein: TCK- gefolgt von 16 Zeichen.', 'not_found' => 'Für diesen Code wurde keine Meldung gefunden.', 'timeline_title' => 'Aktualisierungen', 'timeline_empty' => 'Es gibt noch keine öffentlichen Aktualisierungen.'],
    'fields' => ['code' => ['label' => 'Meldungscode'], 'status' => ['label' => 'Status']],
    'actions' => ['create' => ['label' => 'Neue Meldung senden']],
    'pratiche' => ['description' => 'Ihre eingereichten Meldungen.', 'count' => '{0} Keine Meldungen|{1} :count Meldung|[2,*] :count Meldungen', 'empty' => 'Sie haben noch keine Meldungen eingereicht.'],
];
