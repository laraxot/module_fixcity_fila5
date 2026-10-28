<?php

declare(strict_types=1);

return [
    'summaries' => [
        'images_none' => 'Nessuna immagine allegata',
        'images_choice' => '{1} Una immagine allegata|[2,*] :count immagini allegate',
    ],
    'fields' => [
        'location' => [
            'label' => 'Luogo',
            'invalid' => 'Scegli il punto sulla mappa o cerca un indirizzo: serve per sapere dove si trova il disservizio.',
            'placeholder' => 'Cerca un luogo',
            'helper_text' => 'Indica il luogo del disservizio',
            'address' => [
                'label' => 'Indirizzo del disservizio',
                'placeholder' => 'Cerca un indirizzo',
                'helper_text' => 'Cerca l’indirizzo o seleziona il punto sulla mappa',
                'description' => 'Indirizzo del disservizio',
            ],
            'description' => 'location',
        ],
        'type' => [
            'label' => 'Tipo di disservizio',
            'placeholder' => 'Seleziona il tipo di disservizio',
            'helper_text' => 'Seleziona la categoria del disservizio',
            'description' => 'Categoria del disservizio',
        ],
        'content' => [
            'label' => 'Dettagli',
            'placeholder' => 'Descrivi il problema',
            'helper_text' => 'Inserire al massimo 200 caratteri',
            'description' => 'Descrivi il disservizio in modo chiaro',
        ],
        'images' => [
            'camera_button' => 'Scatta una foto',
            'upload_button' => 'Scegli file',
            'remove' => 'Elimina :name',
            'uploading' => 'Caricamento in corso',
            'preparing' => 'Preparazione foto',
            'error_prepare' => 'Non riesco a leggere questa foto: prova a scattarla di nuovo o scegli un file JPG o PNG',
            'error_type' => 'Formato non consentito: usa JPG, PNG o PDF',
            'error_size' => 'Il file supera :max MB',
            'error_count' => 'Puoi allegare al massimo :max file',
            'error_upload' => 'Caricamento non riuscito, riprova',
            'error_camera' => 'Formato foto non supportato. Usa JPG, PNG o PDF.',
            'label' => 'Immagini',
            'helper_text' => 'Formati ammessi: :formats. Fino a :max_files file, massimo :max_size MB ciascuno.',
            'placeholder' => 'Aggiungi immagini (facoltativo)',
            'description' => 'Immagini a supporto della segnalazione',
        ],
        'author' => [
            'show_more' => 'Mostra tutto',
        ],
        'author_fiscal_code' => [
            'label' => 'Codice fiscale',
            'description' => 'Codice fiscale dell’autore',
            'helper_text' => 'Dato associato al tuo profilo',
            'placeholder' => 'Codice fiscale',
        ],
        'title' => [
            'label' => 'Titolo',
        ],
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
            'description' => 'Luogo indicato o coordinate',
        ],
        'review_email' => [
            'label' => 'Email di contatto',
            'description' => 'Anteprima dell\'indirizzo email indicato',
        ],
        'review_content' => [
            'label' => 'Dettaglio segnalazione',
            'description' => 'Descrizione del disservizio',
        ],
        'review_name' => [
            'label' => 'Titolo / nome disservizio',
            'description' => 'Titolo sintetico indicato dall’utente',
        ],
        'review_type' => [
            'label' => 'Tipo segnalazione',
            'description' => 'Anteprima della tipologia scelta',
        ],
        'review_priority' => [
            'label' => 'Priorità',
            'description' => 'Anteprima della priorità selezionata',
        ],
        'review_author_name' => [
            'label' => 'Nome e cognome',
            'description' => 'Riepilogo autore della segnalazione',
        ],
        'review_author_fiscal_code' => [
            'label' => 'Codice fiscale',
            'description' => 'Riepilogo codice fiscale autore',
        ],
        'review_contact_phone' => [
            'label' => 'Telefono',
            'description' => 'Riepilogo recapito telefonico',
        ],
        'review_contact_email' => [
            'label' => 'Email di contatto',
            'description' => 'Riepilogo indirizzo email',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_name' => [
            'label' => 'author_name',
            'description' => 'author_name',
            'helper_text' => 'author_name',
            'placeholder' => 'author_name',
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
        'type_legacy' => [
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
        'gdpr_text' => [
            'label' => 'gdpr_text',
        ],
        'status' => [
            'label' => 'status',
            'placeholder' => 'status',
            'helper_text' => 'status',
            'description' => 'status',
        ],
    ],
    'required_notice' => 'I campi contraddistinti dal simbolo asterisco sono obbligatori',
    'navscroll' => [
        'title' => 'INFORMAZIONI RICHIESTE',
    ],
    'nav' => [
        'aria_label' => 'Step',
        'back' => 'Indietro',
        'next' => 'Avanti',
    ],
    'hints' => [
        'max_length' => 'Inserire al massimo :max caratteri',
    ],
    'validation' => [
        'required' => 'Il campo :attribute è obbligatorio.',
        'min' => 'Il campo :attribute deve contenere almeno :min caratteri.',
        'max' => 'Il campo :attribute non può superare :max caratteri.',
        'privacy_accepted' => 'Per inviare la segnalazione devi accettare l\'informativa sulla privacy.',
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
        'empty' => [
            'heading' => '',
            'label' => '',
        ],
        'Contatti' => [
            'heading' => 'Contatti',
        ],
        'location' => [
            'heading' => 'Luogo',
            'description' => 'Indica il luogo del disservizio',
        ],
        'issue' => [
            'heading' => 'Disservizio',
            'description' => 'Descrivi il problema segnalato',
        ],
        'author' => [
            'heading' => 'Autore della segnalazione',
            'description' => 'Informazioni su di te',
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
    ],
];
