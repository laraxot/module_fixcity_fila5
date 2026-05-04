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
            'label' => 'Accetto la privacy',
            'placeholder' => 'Accetto la privacy policy',
            'helper_text' => 'Devi accettare per procedere con la segnalazione',
            'description' => 'Accettazione dell\'informativa sulla privacy',
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
        ],
        'review_email' => [
            'label' => 'Email di contatto',
            'description' => 'Anteprima dell\'indirizzo email indicato',
        ],
        'review_content' => [
            'label' => 'Dettaglio segnalazione',
            'description' => 'Anteprima del contenuto della segnalazione',
        ],
        'review_name' => [
            'label' => 'Nome segnalazione',
            'description' => 'Anteprima del nome identificativo',
        ],
        'review_type' => [
            'label' => 'Tipo segnalazione',
            'description' => 'Anteprima della tipologia scelta',
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
    ],
];
