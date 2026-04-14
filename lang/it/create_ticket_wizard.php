<?php

declare(strict_types=1);
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;

/**
 * Traduzioni per {@see CreateTicketWizardWidget}.
 * Chiave namespace: fixcity::create_ticket_wizard (da GetTransKeyAction).
 */
return [
    'fields' => [
        'place' => [
            'label' => 'Luogo',
        ],
        'inefficiency' => [
            'label' => 'Disservizio',
        ],
        'author' => [
            'label' => 'Autore della segnalazione',
            'description' => 'Informazione su di te',
        ],
        'type' => [
            'label' => 'Tipo di disservizio',
        ],
        'name' => [
            'label' => 'Titolo',
        ],
        'content' => [
            'label' => 'Dettagli',
            'helper_text' => 'Inserire al massimo 200 caratteri',
        ],
        'author_name' => [
            'label' => 'Nome completo',
        ],
        'author_fiscal_code' => [
            'label' => 'Codice Fiscale',
        ],
        'author_phone' => [
            'label' => 'Telefono',
        ],
        'privacyAccepted' => [
            'label' => 'Ho letto e compreso l\'informativa sulla privacy',
            'placeholder' => '',
            'helper_text' => '',
            'description' => 'Accettazione obbligatoria per procedere',
            'validation_attribute' => 'l\'informativa sulla privacy',
        ],
        'address' => [
            'label' => 'Luogo',
            'placeholder' => 'Cerca un luogo o usa la posizione GPS',
            'helper_text' => 'Indica il luogo del disservizio',
            'description' => '',
        ],
        'issueType' => [
            'label' => 'Tipo di disservizio',
            'placeholder' => 'Seleziona il tipo di disservizio',
            'helper_text' => 'Seleziona la categoria del problema segnalato',
            'description' => '',
        ],
        'title' => [
            'label' => 'Titolo',
            'placeholder' => 'Titolo breve del problema',
            'helper_text' => 'Max 255 caratteri',
            'description' => 'Titolo della segnalazione',
        ],
        'details' => [
            'label' => 'Descrizione',
            'placeholder' => 'Descrivi il problema in dettaglio...',
            'helper_text' => 'Max 200 caratteri',
            'description' => 'Descrizione del problema',
        ],
        'email' => [
            'label' => 'Email',
            'placeholder' => 'la.tua@email.it',
            'helper_text' => 'Opzionale — riceverai aggiornamenti sullo stato della segnalazione',
            'description' => '',
        ],
        'images' => [
            'label' => 'Immagini',
            'placeholder' => '',
            'helper_text' => 'Seleziona una o più immagini da allegare alla segnalazione',
            'description' => 'Puoi caricare fino a 10 immagini',
        ],
        'userName' => [
            'label' => 'Nome completo',
            'placeholder' => 'Nome e Cognome',
            'helper_text' => 'Opzionale',
            'description' => '',
        ],
        'userFiscalCode' => [
            'label' => 'Codice Fiscale',
            'placeholder' => 'RSSMRA80A01H501A',
            'helper_text' => 'Opzionale',
            'description' => '',
        ],
        'userPhone' => [
            'label' => 'Telefono',
            'placeholder' => '+39 02 1234567',
            'helper_text' => 'Opzionale',
            'description' => '',
        ],
        'review_title' => [
            'label' => 'Titolo',
        ],
        'review_issue_type' => [
            'label' => 'Tipo Segnalazione',
        ],
        'review_address' => [
            'label' => 'Indirizzo',
        ],
        'review_details' => [
            'label' => 'Descrizione',
        ],
        'review_email' => [
            'label' => 'Email',
        ],
        'review_user_phone' => [
            'label' => 'Telefono',
        ],
        'review_user_name' => [
            'label' => 'Nome Utente',
        ],
        'review_images' => [
            'label' => 'Anteprime',
        ],
        'summary_notice' => [
            'label' => '',
            'placeholder' => '',
            'helper_text' => '',
            'description' => '',
        ],
    ],
    'actions' => [
        'next' => [
            'label' => 'Avanti',
            'tooltip' => 'Vai al passaggio successivo',
            'icon' => '',
        ],
        'previous' => [
            'label' => 'Precedente',
            'tooltip' => 'Torna al passaggio precedente',
            'icon' => '',
        ],
        'submit' => [
            'label' => 'Invia',
            'tooltip' => 'Invia la segnalazione al Comune',
            'icon' => '',
        ],
    ],
    'steps' => [
        '1' => [
            'label' => 'Autorizzazioni e condizioni',
        ],
        '2' => [
            'label' => 'Dati di segnalazione',
        ],
        '3' => [
            'label' => 'Riepilogo',
        ],
    ],
    'summary' => [
        'empty' => 'Non specificato',
        'section' => [
            'label' => 'Riepilogo segnalazione',
            'description' => 'Verifica i dati prima dell\'invio',
        ],
        'images' => [
            'section_label' => 'Immagini allegate',
            'count_description' => ':count immagini caricate',
            'empty_description' => 'Nessuna immagine caricata',
            'limit_message' => 'E altre :count immagini',
        ],
    ],
    'notifications' => [
        'submit_failed' => [
            'title' => 'Invio non riuscito',
            'body' => 'Non è stato possibile salvare la segnalazione. Controlla i dati inseriti e riprova.',
        ],
    ],
];
