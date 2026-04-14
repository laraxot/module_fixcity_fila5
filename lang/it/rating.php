<?php

declare(strict_types=1);

return [
    'fields' => [
        'title' => [
            'label' => 'Quanto sono chiare le informazioni su questa pagina?',
        ],
        'subtitle' => [
            'label' => 'Il tuo feedback ci aiuta a migliorare il servizio.',
        ],
        'star' => [
            'legend' => [
                'label' => 'Valuta da 1 a 5 stelle la pagina',
            ],
            'labels' => [
                1 => [
                    'label' => 'Valuta 1 stella su 5',
                ],
                2 => [
                    'label' => 'Valuta 2 stelle su 5',
                ],
                3 => [
                    'label' => 'Valuta 3 stelle su 5',
                ],
                4 => [
                    'label' => 'Valuta 4 stelle su 5',
                ],
                5 => [
                    'label' => 'Valuta 5 stelle su 5',
                ],
            ],
        ],
        'positive_question' => [
            'label' => 'Cosa ritieni di meglio di questa pagina?',
        ],
        'positive_options' => [
            'options' => [
                1 => [
                    'label' => 'Le informazioni sono chiare',
                ],
                2 => [
                    'label' => 'Le informazioni sono complete',
                ],
                3 => [
                    'label' => 'È facile trovare quello che cerco',
                ],
                4 => [
                    'label' => 'Il design è gradevole',
                ],
                5 => [
                    'label' => 'Altro',
                ],
            ],
        ],
        'negative_question' => [
            'label' => 'Cosa non va in questa pagina?',
        ],
        'negative_options' => [
            'options' => [
                1 => [
                    'label' => 'Le informazioni non sono chiare',
                ],
                2 => [
                    'label' => 'Le informazioni sono incomplete',
                ],
                3 => [
                    'label' => 'È difficile trovare quello che cerco',
                ],
                4 => [
                    'label' => 'Il design non è gradevole',
                ],
                5 => [
                    'label' => 'Altro',
                ],
            ],
        ],
        'text_question' => [
            'label' => 'Vuoi aggiungere altri dettagli?',
        ],
        'text_field' => [
            'label' => [
                'label' => 'Dettaglio',
            ],
            'help_text' => [
                'text' => 'Inserire massimo 200 caratteri',
            ],
        ],
    ],
    'actions' => [
        'back' => [
            'label' => 'Indietro',
        ],
        'next' => [
            'label' => 'Avanti',
        ],
        'submit' => [
            'label' => 'Invia',
        ],
    ],
    'messages' => [
        'thank_you' => [
            'text' => 'Grazie per il tuo feedback!',
        ],
    ],
];
