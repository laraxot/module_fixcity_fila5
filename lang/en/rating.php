<?php

declare(strict_types=1);

return [
    'fields' => [
        'title' => [
            'label' => 'How clear is the information on this page?',
        ],
        'subtitle' => [
            'label' => 'Your feedback helps us improve the service.',
        ],
        'star' => [
            'legend' => [
                'label' => 'Rate this page from 1 to 5 stars',
            ],
            'labels' => [
                1 => [
                    'label' => 'Rate 1 star out of 5',
                ],
                2 => [
                    'label' => 'Rate 2 stars out of 5',
                ],
                3 => [
                    'label' => 'Rate 3 stars out of 5',
                ],
                4 => [
                    'label' => 'Rate 4 stars out of 5',
                ],
                5 => [
                    'label' => 'Rate 5 stars out of 5',
                ],
            ],
        ],
        'positive_question' => [
            'label' => 'What do you like best about this page?',
        ],
        'positive_options' => [
            'options' => [
                1 => [
                    'label' => 'The information is clear',
                ],
                2 => [
                    'label' => 'The information is complete',
                ],
                3 => [
                    'label' => "It's easy to find what I'm looking for",
                ],
                4 => [
                    'label' => 'The design is appealing',
                ],
                5 => [
                    'label' => 'Other',
                ],
            ],
        ],
        'negative_question' => [
            'label' => "What's wrong with this page?",
        ],
        'negative_options' => [
            'options' => [
                1 => [
                    'label' => 'The information is unclear',
                ],
                2 => [
                    'label' => 'The information is incomplete',
                ],
                3 => [
                    'label' => "It's hard to find what I'm looking for",
                ],
                4 => [
                    'label' => 'The design is not appealing',
                ],
                5 => [
                    'label' => 'Other',
                ],
            ],
        ],
        'text_question' => [
            'label' => 'Would you like to add more details?',
        ],
        'text_field' => [
            'label' => [
                'label' => 'Details',
            ],
            'help_text' => [
                'text' => 'Maximum 200 characters',
            ],
        ],
    ],
    'actions' => [
        'back' => [
            'label' => 'Back',
        ],
        'next' => [
            'label' => 'Next',
        ],
        'submit' => [
            'label' => 'Submit',
        ],
    ],
    'messages' => [
        'thank_you' => [
            'text' => 'Thank you for your feedback!',
        ],
    ],
];
