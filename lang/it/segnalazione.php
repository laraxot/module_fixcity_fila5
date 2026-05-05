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
        'elenco' => [
            'label' => 'Segnalazioni',
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

    'wizard_a11y' => [
        'skip_to_main' => [
            'label' => 'Vai al contenuto principale',
            'placeholder' => '',
            'help' => 'Salta intestazione e vai direttamente al modulo di segnalazione.',
        ],
        'main_region' => [
            'label' => 'Modulo segnalazione',
            'placeholder' => '',
            'help' => 'Area con i passi del wizard e il modulo di invio.',
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
        'title' => [
            'label' => 'Segnalazioni',
        ],
        'subtitle' => [
            'text' => 'Consulta le segnalazioni aperte nel territorio e filtra i risultati per categoria.',
        ],
    ],

    /*
     * Segnalazioni elenco page keys
     */
    'filters' => [
        'legend' => [
            'label' => 'Filtra per categoria',
        ],
    ],

    'results' => [
        'count' => [
            'text' => ':count segnalazioni trovate',
        ],
        'empty' => 'Nessuna segnalazione trovata.',
    ],

    'filter' => [
        'button' => [
            'label' => 'Filtra',
        ],
        'remove' => [
            'label' => 'Rimuovi filtri',
        ],
    ],

    'tabs' => [
        'map' => [
            'label' => 'Mappa',
        ],
        'list' => [
            'label' => 'Elenco',
        ],
    ],

    'map' => [
        'image' => [
            'alt' => 'Mappa delle segnalazioni',
        ],
        'cta' => [
            'title' => [
                'label' => 'Hai notato un disservizio?',
            ],
            'text' => [
                'label' => 'Invia una nuova segnalazione e aiuta il Comune a intervenire in modo più rapido.',
            ],
            'button' => [
                'label' => 'Segnala un disservizio',
            ],
        ],
    ],

    'card' => [
        'type' => [
            'label' => 'Tipo di segnalazione',
            'short' => 'Segnalazione',
        ],
        'expand' => [
            'button' => [
                'label' => 'Mostra dettagli',
            ],
        ],
        'address' => [
            'label' => 'Indirizzo',
        ],
        'detail' => [
            'label' => 'Dettagli',
        ],
        'edit' => [
            'link' => [
                'label' => 'Modifica',
            ],
        ],
    ],

    'load-more' => [
        'button' => [
            'label' => 'Carica altre segnalazioni',
        ],
    ],

    'contacts' => [
        'title' => [
            'label' => 'Hai bisogno di aiuto?',
        ],
        'faq' => [
            'link' => [
                'label' => 'Leggi le domande frequenti',
            ],
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
        /** Wizard test: MapPicker mirror; non salvato su Ticket (dehydrated false). */
        'map_reference' => [
            'label' => 'Mappa (MapPicker — confronto)',
            'description' => 'Stesso componente Lit di LocationPicker; coordinate non salvate sulla segnalazione.',
        ],
        /** Coordinate ufficiali per il ticket. */
        'location' => [
            'label' => 'Mappa (LocationPicker)',
            'description' => 'Indica il punto sulla mappa: coordinate salvate sulla segnalazione.',
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
        'priority' => [
            'label' => 'Priorità*',
            'description' => 'Indica la priorità della segnalazione',
        ],
        'content' => [
            'label' => 'Contenuto*',
            'description' => 'Descrivi il disservizio in dettaglio',
            'char_limit' => 'Inserire al massimo 500 caratteri',
            'max_chars' => [
                'label' => 'Inserire al massimo 500 caratteri',
            ],
        ],
        'summary' => [
            'warning' => 'Attenzione',
            'declaration' => 'Le informazioni che hai fornito hanno valore di dichiarazione. Verifica che siano corrette.',
            'segnalazione_section' => 'Segnalazione Disservizio',
            'dati_generali_section' => 'Dati Generali',
            'author' => 'Autore della segnalazione',
            'cf' => 'Codice Fiscale',
            'contacts' => 'Contatti',
            'phone' => 'Telefono',
            'email' => 'Email',
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
        'sidebar_hint' => [
            'label' => 'Compila il modulo nella colonna principale per procedere.',
            'placeholder' => '',
            'help' => 'Usa i pulsanti «Avanti» e «Indietro» sotto il modulo; non duplicare il wizard nella barra laterale.',
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
     * Wizard step labels
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
    * GDPR Notice keys - used by wizard privacy step
    */
    'gdpr_notice' => [
        'text' => 'Il Comune di :municipality gestisce i dati personali forniti e liberamente comunicati sulla base dell\'articolo 13 del Regolamento (UE) 2016/679 General Data Protection Regulation (GDPR) e degli articoli 13 e successive modifiche e integrazione del decreto legislativo (di seguito d.lgs) 267/2000 (Testo unico enti locali).',
        'privacy_link' => 'informativa sulla privacy.',
    ],

    /*
     * Geolocation keys - used by address-field component
     */
    'privacy_geolocation' => [
        'title' => [
            'label' => 'Segnalazione disservizio',
        ],
        'description' => [
            'text' => 'Leggi l\'informativa sulla privacy e acconsenti al trattamento dei dati personali.',
        ],
        'details' => [
            'text' => 'Per i dettagli sul trattamento dei dati personali consulta l\'',
            'link' => [
                'label' => 'informativa sulla privacy.',
            ],
        ],
        'accept' => [
            'label' => 'Ho letto e compreso l\'informativa sulla privacy',
        ],
        'intro' => [
            'text' => 'Il Comune di Firenze gestisce i dati personali forniti e liberamente comunicati sulla base dell\'articolo 13 del Regolamento (UE) 2016/679 General Data Protection Regulation (GDPR) e degli articoli 13 e successive modifiche e integrazione del decreto legislativo (di seguito d.lgs) 267/2000 (Testo unico enti locali).',
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
        'label' => 'Autorizzazioni e condizioni',
        'data' => [
            'label' => 'Dati di segnalazione',
        ],
        'summary' => [
            'label' => 'Riepilogo',
        ],
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
        'summary_declaration' => [
            'text' => 'Le informazioni che hai fornito hanno valore di dichiarazione. Verifica che siano corrette.',
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
