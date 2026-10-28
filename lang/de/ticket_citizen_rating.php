<?php

declare(strict_types=1);

return [
    'prompt' => [
        'title' => ['label' => 'Wie bewerten Sie die Bearbeitung dieser Meldung?'],
        'description' => ['label' => 'Ihre Bewertung mit 1 bis 5 Sternen hilft uns, unseren Service zu verbessern.'],
        'stars_legend' => ['label' => 'Bewerten Sie die Bearbeitung mit 1 bis 5 Sternen'],
        'thank_you' => ['label' => 'Vielen Dank für Ihre Bewertung.'],
    ],
    'actions' => ['submit' => ['label' => 'Bewertung senden']],
    'admin' => [
        'stats' => [
            'average' => [
                'label' => 'Durchschnittliche Bewertung',
                'description' => ['label' => 'Durchschnittliche Sternezahl der bewerteten Meldungen'],
            ],
            'count' => [
                'label' => 'Erhaltene Bewertungen',
                'description' => ['label' => 'Meldungen mit Rückmeldung von Bürgerinnen und Bürgern'],
            ],
        ],
    ],
    'validation' => [
        'rating_between' => ['label' => 'Die Bewertung muss zwischen 1 und 5 Sternen liegen.'],
        'rating_required' => ['label' => 'Bitte wählen Sie vor dem Senden eine Sternebewertung aus.'],
        'auth_required' => ['label' => 'Melden Sie sich an, um diese Meldung zu bewerten.'],
        'already_rated' => ['label' => 'Sie haben diese Meldung bereits bewertet.'],
        'status_not_resolved' => ['label' => 'Sie können nur erledigte oder geschlossene Meldungen bewerten.'],
        'not_owner' => ['label' => 'Nur die Person, die diese Meldung eingereicht hat, kann sie bewerten.'],
    ],
];
