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
            'label' => 'Privacy',
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
        ],
        'author_fiscal_code' => [
            'label' => 'author_fiscal_code',
        ],
        'author_name' => [
            'label' => 'author_name',
        ],
        'location' => [
            'label' => 'location',
            'placeholder' => 'location',
            'helper_text' => 'location',
            'description' => 'location',
        ],
        'content' => [
            'label' => 'content',
            'placeholder' => 'content',
            'helper_text' => 'content',
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
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => 'priority',
<<<<<<< HEAD
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
=======
            'placeholder' => 'priority',
            'label' => 'priority',
        ],
        'type' => [
            'description' => 'type',
            'helper_text' => 'type',
            'placeholder' => 'type',
            'label' => 'type',
        ],
        'slug' => [
            'description' => 'slug',
            'helper_text' => 'slug',
            'placeholder' => 'slug',
            'label' => 'slug',
        ],
        'accept_terms' => [
            'description' => 'accept_terms',
            'helper_text' => 'accept_terms',
            'placeholder' => 'accept_terms',
            'label' => 'accept_terms',
        ],
        'privacy_notice' => [
            'description' => 'privacy_notice',
            'helper_text' => 'privacy_notice',
            'placeholder' => 'privacy_notice',
            'label' => 'privacy_notice',
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
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
<<<<<<< HEAD
        'fixcity::segnalazione' => [
            'summary' => [
                'title' => [
                    'label' => 'fixcity::segnalazione.summary.title',
                    'heading' => 'fixcity::segnalazione.summary.title',
                ],
            ],
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'privacy',
        ],
        'dati-di-segnalazione' => [
            'label' => 'dati-di-segnalazione',
        ],
        'riepilogo' => [
            'label' => 'riepilogo',
=======
        'empty' => [
            'heading' => 'empty',
            'label' => 'empty',
        ],
    ],
    'steps' => [
        'step-2' => [
            'label' => 'step-2',
        ],
        'step-1' => [
            'label' => 'step-1',
>>>>>>> 123bd69b8 (refactor: standardize wizard method from getWizardSteps() to Filament's native getSteps() across Fixcity module)
        ],
    ],
];
