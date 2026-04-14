<?php

declare(strict_types=1);

return [
    'steps' => [
        'privacy' => [
            'label' => 'Privacy e condizioni',
            'description' => 'Leggi l\'informativa e conferma il consenso prima di proseguire.',
        ],
        'data' => [
            'label' => 'Dati della segnalazione',
            'description' => 'Indica luogo, tipologia di disservizio e dettagli.',
        ],
        'summary' => [
            'label' => 'Riepilogo',
            'description' => 'Controlla i dati inseriti prima di inviare la segnalazione.',
        ],
        1 => [
            'label' => 'Autorizzazioni e condizioni',
        ],
        [
            'label' => 'Dati di segnalazione',
        ],
        [
            'label' => 'Riepilogo',
        ],
    ],
    'actions' => [
        'previous' => [
            'tooltip' => 'Indietro',
            'icon' => 'previous',
            'label' => 'Indietro',
        ],
        'next' => [
            'tooltip' => 'Avanti',
            'icon' => 'next',
            'label' => 'Avanti',
        ],
        'submit' => [
            'tooltip' => 'Invia segnalazione',
            'icon' => 'submit',
            'label' => 'Invia',
        ],
        'edit_contacts' => [
            'tooltip' => 'edit_contacts',
            'icon' => 'edit_contacts',
            'label' => 'edit_contacts',
        ],
    ],
    'fields' => [
        'address' => [
            'label' => 'Luogo*',
            'description' => '',
            'helper_text' => 'Indica il luogo del disservizio',
            'placeholder' => 'Cerca un luogo o usa la posizione GPS',
        ],
        'issueType' => [
            'label' => 'Tipo di disservizio*',
            'placeholder' => 'Seleziona il tipo di disservizio',
            'helper_text' => 'Seleziona la categoria del problema segnalato',
            'description' => '',
        ],
        'title' => [
            'label' => 'Titolo*',
            'description' => 'Titolo della segnalazione',
            'helper_text' => 'Max 255 caratteri',
            'placeholder' => 'Titolo breve del problema',
        ],
        'details' => [
            'label' => 'Dettagli**',
            'description' => 'Descrizione del problema',
            'helper_text' => 'Max 200 caratteri',
            'placeholder' => 'Descrivi il problema in dettaglio...',
        ],
        'email' => [
            'label' => 'Email',
            'description' => '',
            'helper_text' => 'Opzionale — riceverai aggiornamenti sullo stato della segnalazione',
            'placeholder' => 'la.tua@email.it',
        ],
        'images' => [
            'label' => 'Immagini',
            'description' => 'Puoi caricare fino a 10 immagini',
            'helper_text' => 'Seleziona una o più immagini da allegare alla segnalazione',
            'placeholder' => '',
        ],
        'userName' => [
            'label' => 'Nome completo',
            'description' => '',
            'helper_text' => 'Opzionale',
            'placeholder' => 'Nome e Cognome',
        ],
        'userFiscalCode' => [
            'label' => 'Codice Fiscale',
            'description' => '',
            'helper_text' => 'Opzionale',
            'placeholder' => 'RSSMRA80A01H501A',
        ],
        'userPhone' => [
            'label' => 'Telefono',
            'description' => '',
            'helper_text' => 'Opzionale',
            'placeholder' => '+39 02 1234567',
        ],
        'privacyAccepted' => [
            'label' => 'Ho letto e compreso l\'informativa sulla privacy',
            'placeholder' => '',
            'helper_text' => 'Leggi la privacy policy',
            'description' => 'Accettazione obbligatoria per procedere',
        ],
        'summary_notice' => [
            'label' => '',
            'description' => '',
            'helper_text' => '',
            'placeholder' => '',
        ],
        'review_images' => [
            'label' => 'review_images',
        ],
        'content' => [
            'label' => 'content',
            'description' => 'content',
            'helper_text' => 'content',
            'placeholder' => 'content',
        ],
        'type' => [
            'label' => 'type',
            'description' => 'type',
            'helper_text' => 'type',
            'placeholder' => 'type',
        ],
        'name' => [
            'label' => 'name',
            'description' => 'name',
            'helper_text' => 'name',
            'placeholder' => 'name',
        ],
        'review_email' => [
            'label' => 'review_email',
        ],
        'review_content' => [
            'label' => 'review_content',
        ],
        'review_address' => [
            'label' => 'review_address',
        ],
        'review_type' => [
            'label' => 'review_type',
        ],
        'review_name' => [
            'label' => 'review_name',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_name' => [
            'label' => 'author_name',
        ],
        'author_fiscal_code' => [
            'label' => 'author_fiscal_code',
            'description' => 'author_fiscal_code',
            'helper_text' => 'author_fiscal_code',
        ],
        'privacy_notice' => [
            'label' => 'privacy_notice',
        ],
        'type_id' => [
            'label' => 'Tipo di disservizio*',
            'description' => 'Seleziona il tipo di disservizio',
            'helper_text' => '',
            'placeholder' => '',
        ],
        'longitude' => [
            'description' => 'longitude',
            'helper_text' => 'longitude',
            'placeholder' => 'longitude',
        ],
        'location' => [
            'description' => 'location',
            'helper_text' => 'location',
            'placeholder' => 'location',
            'label' => 'location',
        ],
    ],
    'sections' => [
        'Immagini allegate' => [
            'heading' => 'Immagini allegate',
        ],
        'Attached Images' => [
            'heading' => 'Attached Images',
        ],
        'Riepilogo Segnalazione' => [
            'heading' => 'Riepilogo Segnalazione',
            'label' => 'Riepilogo Segnalazione',
        ],
        'images_review' => [
            'heading' => 'images_review',
            'label' => 'images_review',
        ],
        'review' => [
            'heading' => 'review',
            'label' => 'review',
        ],
        'place' => [
            'label' => 'place',
            'heading' => 'place',
        ],
        'inefficiency' => [
            'label' => 'inefficiency',
            'heading' => 'inefficiency',
        ],
        'author' => [
            'label' => 'author',
            'heading' => 'author',
        ],
        'author_info' => [
            'heading' => 'author_info',
        ],
        'Autore della segnalazione' => [
            'heading' => 'Autore della segnalazione',
            'label' => 'Autore della segnalazione',
        ],
        'Disservizio' => [
            'heading' => 'Disservizio',
            'label' => 'Disservizio',
        ],
        'Luogo' => [
            'heading' => 'Luogo',
            'label' => 'Luogo',
        ],
        'Contatti' => [
            'heading' => 'Contatti',
            'label' => 'Contatti',
        ],
        'fixcity::segnalazione' => [
            'sections' => [
                'summary' => [
                    'label' => [
                        'heading' => 'fixcity::segnalazione.sections.summary.label',
                        'label' => 'fixcity::segnalazione.sections.summary.label',
                    ],
                ],
                'place' => [
                    'label' => [
                        'label' => 'fixcity::segnalazione.sections.place.label',
                        'heading' => 'fixcity::segnalazione.sections.place.label',
                    ],
                ],
                'inefficiency' => [
                    'label' => [
                        'label' => 'fixcity::segnalazione.sections.inefficiency.label',
                        'heading' => 'fixcity::segnalazione.sections.inefficiency.label',
                    ],
                ],
            ],
        ],
        'Riepilogo' => [
            'heading' => 'Riepilogo',
            'label' => 'Riepilogo',
        ],
        'Disservizio!!' => [
            'heading' => 'Disservizio!!',
            'label' => 'Disservizio!!',
        ],
    ],
];
