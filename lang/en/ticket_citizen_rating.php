<?php

declare(strict_types=1);

return [
    'prompt' => [
        'title' => ['label' => 'How would you rate the resolution of this report?'],
        'description' => ['label' => 'Your 1 to 5 star rating helps us improve our service.'],
        'stars_legend' => ['label' => 'Rate the resolution from 1 to 5 stars'],
        'thank_you' => ['label' => 'Thank you for your rating.'],
    ],
    'actions' => ['submit' => ['label' => 'Submit rating']],
    'admin' => [
        'stats' => [
            'average' => [
                'label' => 'Average rating',
                'description' => ['label' => 'Average stars across rated reports'],
            ],
            'count' => [
                'label' => 'Ratings received',
                'description' => ['label' => 'Reports with citizen feedback'],
            ],
        ],
    ],
    'validation' => [
        'rating_between' => ['label' => 'The rating must be between 1 and 5 stars.'],
        'rating_required' => ['label' => 'Select a star rating before submitting.'],
        'auth_required' => ['label' => 'Sign in to rate this report.'],
        'already_rated' => ['label' => 'You have already rated this report.'],
        'status_not_resolved' => ['label' => 'You can rate only resolved or closed reports.'],
        'not_owner' => ['label' => 'Only the citizen who submitted the report can rate it.'],
    ],
];
