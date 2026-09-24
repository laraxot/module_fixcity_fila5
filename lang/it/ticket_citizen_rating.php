<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_citizen_rating.php
return [
    'prompt' => [
        'title' => [
            'label' => 'Come valuti la risoluzione di questa segnalazione?',
        ],
        'description' => [
            'label' => 'La tua valutazione (da 1 a 5 stelle) ci aiuta a migliorare il servizio.',
        ],
        'stars_legend' => [
            'label' => 'Valuta da 1 a 5 stelle la risoluzione',
        ],
        'thank_you' => [
            'label' => 'Grazie per la tua valutazione.',
        ],
    ],
    'actions' => [
        'submit' => [
            'label' => 'Invia valutazione',
        ],
    ],
    'admin' => [
        'stats' => [
            'average' => [
                'label' => 'Media valutazioni',
                'description' => [
                    'label' => 'Media stelle su segnalazioni valutate',
                ],
            ],
            'count' => [
                'label' => 'Valutazioni ricevute',
                'description' => [
                    'label' => 'Segnalazioni con feedback cittadino',
                ],
            ],
        ],
    ],
    'validation' => [
        'rating_between' => [
            'label' => 'La valutazione deve essere tra 1 e 5 stelle.',
        ],
        'rating_required' => [
            'label' => 'Seleziona un numero di stelle prima di inviare.',
        ],
        'auth_required' => [
            'label' => 'Accedi per valutare la segnalazione.',
        ],
        'already_rated' => [
            'label' => 'Hai già valutato questa segnalazione.',
        ],
        'status_not_resolved' => [
            'label' => 'Puoi valutare solo segnalazioni risolte o chiuse.',
        ],
        'not_owner' => [
            'label' => 'Solo il cittadino che ha aperto la segnalazione può valutarla.',
        ],
    ],
];
