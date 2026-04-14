<?php

declare(strict_types=1);

/**
 * Segnalazione translations - Italian (COMPLETE)
 *
 * Translation namespace: fixcity::segnalazione.*
 * Format: namespace::context.collection.element.type (5 levels)
 *
 * Covers both test pages (segnalazione-02-dati.blade.php)
 * and the Filament ticket-create-wizard widget.
 */
return [
    'breadcrumb' => [
        'home' => [
            'label' => 'Home',
        ],
        'services' => [
            'label' => 'Servizi',
        ],
    ],

    'inefficiency_types' => [
        'property_damage' => [
            'label' => 'Danneggiamento proprietà pubblica',
        ],
    ],

    /*
     * Page-level keys
     */
    'page' => [
        'title' => [
            'label' => 'Segnalazione disservizio',
        ],
    ],

    /*
     * Heading keys
     */
    'heading' => [
        'report' => [
            'label' => 'Segnalazione disservizio',
        ],
        'report_author' => [
            'label' => 'Autore della segnalazione',
            'description' => 'Informazione su di te',
        ],
        'contacts' => [
            'label' => 'Contatti',
        ],
    ],

    /*
     * Fields keys - used by both test pages and widget
     */
    'fields' => [
        'title' => [
            'label' => 'Titolo*',
            'description' => 'Titolo della segnalazione',
        ],
        'type' => [
            'label' => 'Tipo di disservizio*',
            'description' => 'Seleziona il tipo di disservizio',
        ],
        /** Stesso dominio di `type`: il modello Ticket persiste su colonna/cast `type_id`. */
        'type_id' => [
            'label' => 'Tipo di disservizio*',
            'description' => 'Seleziona il tipo di disservizio',
        ],
        'details' => [
            'label' => 'Dettagli**',
            'char_limit' => 'Inserire al massimo 200 caratteri',
            'max_chars' => [
                'label' => 'Inserire al massimo 200 caratteri',
            ],
        ],
        'address' => [
            'label' => 'Luogo*',
            'placeholder' => 'Cerca un luogo*',
        ],
        'use_my_location' => [
            'label' => 'Usa la tua posizione',
        ],
        'images' => [
            'label' => 'Immagini',
            'upload_label' => 'Carica file',
            'upload_button' => 'Carica file',
            'upload_aria' => [
                'label' => 'Carica file per il disservizio',
            ],
            'description' => 'Seleziona una o più immagini da allegare alla segnalazione',
            'help_text' => 'Seleziona una o più immagini da allegare alla segnalazione',
            'delete_aria' => [
                'label' => 'elimina immagine caricata',
            ],
        ],
        'name' => [
            'label' => 'Nome completo',
            'placeholder' => 'Nome e cognome',
        ],
        'fiscal_code' => [
            'label' => 'Codice Fiscale',
            'placeholder' => 'Codice fiscale',
        ],
        'phone' => [
            'label' => 'Telefono',
            'placeholder' => '+39 xxx xxxxxxx',
        ],
        'email' => [
            'label' => 'Email',
        ],
        'required_note' => [
            'label' => 'I campi contraddistinti dal simbolo asterisco sono obbligatori',
        ],
        'required' => [
            'note' => [
                'label' => 'I campi contraddistinti dal simbolo asterisco sono obbligatori',
            ],
        ],
        'sidebar_title' => [
            'label' => 'INFORMAZIONI RICHIESTE',
        ],
        'step' => [
            'privacy' => [
                'label' => 'Autorizzazioni e condizioni',
            ],
            'data' => [
                'label' => 'Dati di segnalazione',
            ],
            'summary' => [
                'label' => 'Riepilogo',
            ],
            'back' => [
                'label' => 'Indietro',
            ],
            'next' => [
                'label' => 'Avanti',
            ],
            'save' => [
                'label' => 'Salva',
            ],
            'save_request' => [
                'label' => 'Salva Richiesta',
            ],
            'confirm' => [
                'label' => 'Conferma e invia',
            ],
            'confirmed' => [
                'label' => 'Confermato',
            ],
            'active' => [
                'label' => 'Attivo',
            ],
            'saved_success' => [
                'message' => 'Richiesta salvata con successo',
            ],
        ],
        'place' => [
            'section' => [
                'label' => 'Luogo',
            ],
            'help' => [
                'label' => 'Indica il luogo del disservizio',
            ],
            'search' => [
                'label' => 'Cerca un luogo*',
            ],
        ],
        'inefficiency' => [
            'section' => [
                'label' => 'Disservizio',
            ],
            'type' => [
                'label' => 'Tipo di disservizio**',
            ],
            'options' => [
                'property_damage' => [
                    'label' => 'Danneggiamento proprietà pubblica',
                ],
            ],
        ],
        'author' => [
            'section' => [
                'label' => 'Autore della segnalazione',
            ],
            'about' => [
                'label' => 'Informazione su di te',
            ],
            'cf' => [
                'label' => 'Codice Fiscale',
            ],
            'show_all' => [
                'label' => 'Mostra tutto',
            ],
            'contacts' => [
                'label' => 'Contatti',
            ],
            'edit' => [
                'label' => 'Modifica',
            ],
        ],
    ],

    /*
     * Sections keys — parity con Design Comuni
     */
    'sections' => [
        'place' => [
            'label' => 'Luogo',
            'description' => 'Indica il luogo del disservizio',
        ],
        'inefficiency' => [
            'label' => 'Disservizio',
            'description' => '',
        ],
        'author' => [
            'label' => 'Autore della segnalazione',
            'description' => 'Informazione su di te',
        ],
        'summary' => [
            'label' => 'Riepilogo',
            'description' => '',
        ],
        'contacts' => [
            'label' => 'Contatti',
            'edit_action' => 'Modifica',
        ],
    ],

    /*
     * Actions keys - used by widget
     */
    'actions' => [
        'back' => [
            'label' => 'Indietro',
        ],
        'save' => [
            'label' => 'Salva Richiesta',
        ],
        'save_short' => [
            'label' => 'Salva',
        ],
        'next' => [
            'label' => 'Avanti',
        ],
        'show_all' => [
            'label' => 'Mostra tutto',
        ],
        'remove_file' => [
            'aria' => [
                'label' => 'Rimuovi file',
            ],
        ],
        'remove_image' => [
            'aria' => [
                'label' => 'Rimuovi immagine',
            ],
        ],
        'submit' => [
            'label' => 'Conferma e invia',
        ],
    ],

    /*
     * Steps keys - used by widget
     */
    'steps' => [
        'active' => [
            'label' => 'Attivo',
        ],
        'confirmed' => [
            'label' => 'Confermato',
        ],
        'privacy' => [
            'label' => 'Autorizzazioni e condizioni',
        ],
        'data' => [
            'label' => 'Dati di segnalazione',
        ],
        'summary' => [
            'label' => 'Riepilogo',
        ],
    ],

    /*
     * Privacy keys - used by widget
     */
    'privacy' => [
        'intro' => [
            'text' => 'Il Comune di Firenze gestisce i dati personali forniti e liberamente comunicati sulla base dell\'articolo 13 del Regolamento (UE) 2016/679 General data protection regulation (Gdpr) e degli articoli 13 e successive modifiche e integrazione del decreto legislativo (di seguito d.lgs) 267/2000 (Testo unico enti locali).',
        ],
        'detail_prefix' => [
            'text' => 'Per i dettagli sul trattamento dei dati personali consulta l\'',
        ],
        'link' => [
            'label' => 'informativa sulla privacy.',
        ],
        'checkbox' => [
            'label' => 'Ho letto e compreso l\'informativa sulla privacy',
        ],
        'error' => [
            'not_accepted' => 'Devi accettare l\'informativa sulla privacy per continuare.',
        ],
    ],

    /*
     * Geolocation keys - used by address-field component
     */
    'geolocation' => [
        'not_supported' => 'La geolocalizzazione non è supportata dal tuo browser.',
        'address_not_found' => 'Indirizzo non trovato. Prova a inserirlo manualmente.',
        'error' => 'Errore durante il recupero della posizione. Riprova.',
        'permission_denied' => 'Permesso di geolocalizzazione negato. Consenti l\'accesso alla posizione nelle impostazioni del browser.',
    ],

    /*
     * Contact keys - used by widget
     */
    'contact' => [
        'heading' => [
            'label' => 'Contatta il comune',
        ],
        'phone' => [
            'label' => 'Chiama il numero verde :phone',
        ],
        'faq' => [
            'label' => 'Leggi le domande frequenti',
        ],
        'assistance' => [
            'label' => 'Richiedi assistenza',
        ],
        'appointment' => [
            'label' => 'Prenota appuntamento',
        ],
    ],

    /*
     * Warning keys - used by widget
     */
    'warning' => [
        'title' => [
            'label' => 'Attenzione',
        ],
        'message' => [
            'label' => 'Compila tutti i campi obbligatori',
        ],
        'message_extra' => [
            'label' => 'I campi con asterisco sono obbligatori',
        ],
    ],

    /*
     * Create options keys - used by widget
     */
    'create_options' => [
        'public_damage' => [
            'label' => 'Danneggiamento proprietà pubblica',
        ],
        'maintenance' => [
            'label' => 'Manutenzione stradale',
        ],
        'urban_decorum' => [
            'label' => 'Arredo urbano',
        ],
    ],
];
