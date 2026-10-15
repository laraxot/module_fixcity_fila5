<?php

declare(strict_types=1);

return [
    'fields' => [
        'title' => ['label' => 'Wie verständlich sind die Informationen auf dieser Seite?'],
        'subtitle' => ['label' => 'Ihr Feedback hilft uns, den Dienst zu verbessern.'],
        'star' => [
            'legend' => ['label' => 'Bewerten Sie diese Seite von 1 bis 5 Sternen'],
            'labels' => [
                1 => ['label' => '1 von 5 Sternen vergeben'],
                2 => ['label' => '2 von 5 Sternen vergeben'],
                3 => ['label' => '3 von 5 Sternen vergeben'],
                4 => ['label' => '4 von 5 Sternen vergeben'],
                5 => ['label' => '5 von 5 Sternen vergeben'],
            ],
        ],
        'positive_question' => ['label' => 'Was gefällt Ihnen an dieser Seite am besten?'],
        'positive_options' => ['options' => [
            1 => ['label' => 'Die Informationen sind verständlich'],
            2 => ['label' => 'Die Informationen sind vollständig'],
            3 => ['label' => 'Ich finde leicht, was ich suche'],
            4 => ['label' => 'Das Design gefällt mir'],
            5 => ['label' => 'Sonstiges'],
        ]],
        'negative_question' => ['label' => 'Was stimmt auf dieser Seite nicht?'],
        'negative_options' => ['options' => [
            1 => ['label' => 'Die Informationen sind unklar'],
            2 => ['label' => 'Die Informationen sind unvollständig'],
            3 => ['label' => 'Ich finde schwer, was ich suche'],
            4 => ['label' => 'Das Design gefällt mir nicht'],
            5 => ['label' => 'Sonstiges'],
        ]],
        'text_question' => ['label' => 'Möchten Sie weitere Details hinzufügen?'],
        'text_field' => ['label' => ['label' => 'Details'], 'help_text' => ['text' => 'Maximal 200 Zeichen']],
    ],
    'actions' => ['back' => ['label' => 'Zurück'], 'next' => ['label' => 'Weiter'], 'submit' => ['label' => 'Senden']],
    'messages' => ['thank_you' => ['text' => 'Vielen Dank für Ihr Feedback!']],
];
