<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/fixcity.php
return [
    'ticket' => [
        'title' => [
            'label' => 'Titolo',
            'placeholder' => 'Titolo',
            'help' => 'Inserisci un titolo descrittivo',
        ],
        'type' => [
            'label' => 'Tipo',
            'placeholder' => 'Tipo di disservizio',
        ],
        'content' => [
            'label' => 'Dettagli',
            'placeholder' => 'Dettagli',
            'helper_text' => 'Inserire al massimo 200 caratteri',
        ],
        'your-location' => 'La tua posizione',
        'insert-images' => 'Immagini',
        'steps' => [
            'auth' => [
                'label' => 'Autorizzazioni e condizioni',
                'description' => 'Leggi e accetta le condizioni per procedere',
            ],
            'data' => [
                'label' => 'Dati di segnalazione',
                'description' => 'Inserisci i dettagli della segnalazione',
            ],
            'summary' => [
                'label' => 'Riepilogo',
                'description' => 'Riepilogo della segnalazione',
            ],
        ],
        'fields' => [
            'accept_terms' => [
                'label' => "Ho letto e compreso l'informativa sulla privacy",
                'helper' => 'È necessario accettare per procedere',
            ],
            'privacy_notice' => [
                'content' => 'Il Comune deve pubblicare l\'informativa privacy del servizio prima che possano essere raccolti dati personali.',
            ],
            'issue' => [
                'label' => 'Disservizio*',
            ],
        ],
        'actions' => [
            'next' => [
                'label' => 'Avanti',
                'icon' => 'heroicon-m-arrow-right',
                'color' => 'primary',
            ],
            'save' => [
                'label' => 'Salva richiesta',
                'icon' => 'heroicon-m-check',
                'color' => 'success',
            ],
        ],
        'validation' => [
            'accept_terms' => "Devi accettare l'informativa sulla privacy per continuare.",
        ],
    ],
];
