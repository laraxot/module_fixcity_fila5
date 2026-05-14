<?php

declare(strict_types=1);

return [
    'fields' => [
        'name' => [
            'label' => 'Nome',
            'placeholder' => 'Inserisci il nome',
            'helper_text' => 'Inserisci un nome identificativo',
            'description' => 'Nome identificativo della segnalazione',
        ],
        'privacy' => [
            'description' => 'Accettazione informativa sulla privacy',
            'label' => 'Autorizzazioni e condizioni',
            'helper_text' => 'Devi accettare la privacy policy per procedere',
        ],
        'privacyAccepted' => [
            'label' => 'Ho letto e compreso l\'informativa sulla privacy',
            'placeholder' => '',
            'helper_text' => 'Obbligatorio per inviare la segnalazione',
            'description' => 'Allineato a Design Comuni segnalazione-01-privacy',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'Inserisci la tua email',
            'helper_text' => 'La tua email per essere contattato',
            'description' => 'Indirizzo email dell\'autore della segnalazione',
        ],
        'review_images' => [
            'label' => 'Immagini allegate',
            'description' => 'Anteprima delle immagini allegate alla segnalazione',
        ],
        'review_location' => [
            'label' => 'Posizione',
            'description' => 'Anteprima della posizione geografica indicata',
            'helper_text' => 'review_location',
            'placeholder' => 'review_location',
        ],
        'review_email' => [
            'label' => 'Email di contatto',
            'description' => 'Anteprima dell\'indirizzo email indicato',
        ],
        'review_content' => [
            'label' => 'Dettaglio segnalazione',
            'description' => 'Anteprima del contenuto della segnalazione',
            'helper_text' => 'review_content',
            'placeholder' => 'review_content',
        ],
        'review_name' => [
            'label' => 'Nome segnalazione',
            'description' => 'Anteprima del nome identificativo',
            'helper_text' => 'review_name',
            'placeholder' => 'review_name',
        ],
        'review_type' => [
            'label' => 'Tipo segnalazione',
            'description' => 'Anteprima della tipologia scelta',
            'helper_text' => 'review_type',
            'placeholder' => 'review_type',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_fiscal_code' => [
            'label' => 'author_fiscal_code',
            'description' => 'author_fiscal_code',
            'helper_text' => 'author_fiscal_code',
            'placeholder' => 'author_fiscal_code',
        ],
        'author_name' => [
            'label' => 'author_name',
            'description' => 'author_name',
            'helper_text' => 'author_name',
            'placeholder' => 'author_name',
        ],
        'location' => [
            'label' => 'location',
            'placeholder' => 'location',
            'helper_text' => 'location',
            'description' => 'location',
            'address' => [
                'label' => 'location.address',
                'description' => 'location.address',
                'helper_text' => 'location.address',
                'placeholder' => 'location.address',
            ],
        ],
        'content' => [
            'label' => 'content',
            'placeholder' => 'content',
            'helper_text' => '',
            'description' => 'content',
        ],
        'images' => [
            'label' => 'images',
            'placeholder' => 'images',
            'helper_text' => 'images',
            'description' => 'images',
        ],
        'author_email' => [
            'label' => 'author_email',
            'description' => 'author_email',
            'helper_text' => 'author_email',
            'placeholder' => 'author_email',
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => '',
            'label' => 'priority',
            'placeholder' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
            'placeholder' => 'slug',
            'helper_text' => '',
            'description' => 'slug',
        ],
        'type' => [
            'label' => 'type',
            'placeholder' => 'type',
            'helper_text' => '',
            'description' => 'type',
        ],
        'type_id' => [
            'description' => 'type_id',
            'helper_text' => '',
            'label' => 'type_id',
            'placeholder' => 'type_id',
        ],
        'review_priority' => [
            'label' => 'review_priority',
        ],
        'gdpr_text' => [
            'label' => 'gdpr_text',
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => 'priority',
            'label' => 'priority',
            'placeholder' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
            'placeholder' => 'slug',
            'helper_text' => 'slug',
            'description' => 'slug',
        ],
        'type' => [
            'label' => 'type',
            'placeholder' => 'type',
            'helper_text' => 'type',
            'description' => 'type',
        ],
        'type_id' => [
            'description' => 'type_id',
            'helper_text' => 'type_id',
            'label' => 'type_id',
            'placeholder' => 'type_id',
        ],
        'review_priority' => [
            'label' => 'review_priority',
        ],
    ],
    'sections' => [
        'Riepilogo' => [
            'heading' => 'Riepilogo',
            'label' => 'Riepilogo',
        ],
        'Autore della segnalazione' => [
            'heading' => 'Autore della segnalazione',
            'label' => 'Autore della segnalazione',
        ],
        'Luogo' => [
            'label' => 'Luogo',
            'heading' => 'Luogo',
        ],
        'Disservizio' => [
            'label' => 'Disservizio',
            'heading' => 'Disservizio',
        ],
        'fixcity::segnalazione' => [
            'summary' => [
                'title' => [
                    'label' => 'fixcity::segnalazione.summary.title',
                    'heading' => 'fixcity::segnalazione.summary.title',
                ],
            ],
        ],
        'empty' => [
            'heading' => 'empty',
            'label' => 'empty',
        ],
        'Contatti' => [
            'heading' => 'Contatti',
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'Autorizzazioni e condizioni',
            'tooltip' => 'Accettazione informativa sulla privacy',
            'description' => 'Leggi e accetta l\'informativa sulla privacy',
            'color' => 'success',
            'icon' => 'heroicon-o-shield-check',
        ],
        'dati-di-segnalazione' => [
            'label' => 'Dati di segnalazione',
            'tooltip' => 'Compila i dati della segnalazione',
            'description' => 'Inserisci i dettagli del disservizio',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'riepilogo' => [
            'label' => 'Riepilogo',
            'tooltip' => 'Riepilogo finale della segnalazione',
            'description' => 'Verifica i dati prima dell\'invio',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'summary' => [
            'label' => 'Riepilogo',
            'tooltip' => 'Riepilogo finale della segnalazione',
            'description' => 'Verifica i dati prima dell\'invio',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'data' => [
            'label' => 'Dati di segnalazione',
            'tooltip' => 'Compila i dati della segnalazione',
            'description' => 'Inserisci i dettagli del disservizio',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'Riepilogo' => [
            'label' => 'Riepilogo',
        ],
        'Dati di segnalazione' => [
            'label' => 'Dati di segnalazione',
        ],
        'Autorizzazioni e condizioni' => [
            'label' => 'Autorizzazioni e condizioni',
        ],
        'fixcity::ticket_form' => [
            'steps' => [
                'summary' => [
                    'label' => [
                        'label' => 'fixcity::ticket_form.steps.summary.label',
                    ],
                ],
                'privacy' => [
                    'label' => [
                        'label' => 'fixcity::ticket_form.steps.privacy.label',
                    ],
                ],
                'data' => [
                    'label' => [
                        'label' => 'fixcity::ticket_form.steps.data.label',
                    ],
                ],
            ],
        ],
    ],
];
