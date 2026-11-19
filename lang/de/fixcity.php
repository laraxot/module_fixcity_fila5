<?php

declare(strict_types=1);

// Chiavi del wizard tickets/create (DCP-55 difetto 2); glossario: Themes/Sixteen/docs/i18n/01-glossario.md.
return [
    'ticket' => [
        'actions' => [
            'next' => [
                'label' => 'Weiter',
            ],
            'save' => [
                'label' => 'Meldung senden',
            ],
        ],
        'fields' => [
            'accept_terms' => [
                'helper' => 'Sie müssen zustimmen, um fortzufahren',
                'label' => 'Ich habe die Datenschutzerklärung gelesen und verstanden',
            ],
            'issue' => [
                'label' => 'Störung*',
            ],
        ],
        'steps' => [
            'auth' => [
                'description' => 'Lesen und akzeptieren Sie die Bedingungen, um fortzufahren',
                'label' => 'Einwilligungen und Bedingungen',
            ],
            'data' => [
                'description' => 'Geben Sie die Einzelheiten Ihrer Meldung ein',
                'label' => 'Angaben zur Meldung',
            ],
            'summary' => [
                'label' => 'Zusammenfassung',
            ],
        ],
        'validation' => [
            'accept_terms' => 'Sie müssen die Datenschutzerklärung akzeptieren, um fortzufahren.',
        ],
    ],
];
