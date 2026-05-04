<?php

declare(strict_types=1);

return [
    'fields' => [
        'id' => [
            'label' => 'ID',
            'placeholder' => 'Inserisci ID',
        ],
        'title' => [
            'label' => 'Titolo',
            'placeholder' => 'Inserisci il titolo',
            'help' => 'Inserisci un titolo descrittivo',
        ],
        'category' => [
            'name' => [
                'label' => 'Categoria',
                'placeholder' => 'Seleziona categoria',
            ],
            'label' => 'Categoria',
            'placeholder' => 'Filtra per categoria',
        ],
        'status' => [
            'label' => 'Stato',
            'placeholder' => 'Seleziona stato',
            'options' => [
                'open' => 'Aperto',
                'in_progress' => 'In Lavorazione',
                'resolved' => 'Risolto',
                'closed' => 'Chiuso',
            ],
        ],
        'priority' => [
            'label' => 'Priorità',
            'placeholder' => 'Seleziona la priorità',
            'help' => 'Indica l\'urgenza del ticket',
            'options' => [
                'low' => 'Bassa',
                'medium' => 'Media',
                'high' => 'Alta',
                'urgent' => 'Urgente',
            ],
            'description' => 'Livello di priorità della segnalazione',
            'helper_text' => 'Scegli in base all\'urgenza del problema riscontrato',
        ],
        'content' => [
            'label' => 'Contenuto',
            'placeholder' => 'Descrivi il problema...',
            'help' => 'Fornisci una descrizione dettagliata',
            'description' => 'Descrizione dettagliata del problema segnalato',
            'helper_text' => 'Fornisci quanti più dettagli possibili per facilitare la gestione',
        ],
        'created_at' => [
            'label' => 'Data Creazione',
        ],
        'updated_at' => [
            'label' => 'Ultima Modifica',
        ],
        'applyFilters' => [
            'label' => 'Applica filtri',
        ],
        'toggleColumns' => [
            'label' => 'Mostra/nascondi colonne',
        ],
        'value' => [
            'label' => 'Valore',
        ],
        'reorderRecords' => [
            'label' => 'Riordina elementi',
        ],
        'count' => [
            'label' => 'Conteggio',
        ],
        'create' => [
            'label' => 'Crea',
        ],
        'edit' => [
            'label' => 'Modifica',
        ],
        'delete' => [
            'label' => 'Elimina',
        ],
        'openFilters' => [
            'label' => 'Apri filtri',
        ],
        'resetFilters' => [
            'label' => 'Reimposta filtri',
        ],
        'name' => [
            'label' => 'Nome',
            'placeholder' => 'Inserisci il nome',
            'help' => 'Inserisci un nome identificativo',
            'description' => 'Nome identificativo della segnalazione',
            'helper_text' => 'Usa un nome breve e descrittivo',
        ],
        'slug' => [
            'label' => 'Slug',
            'placeholder' => 'Inserisci lo slug',
            'help' => 'URL-friendly versione del nome',
            'description' => 'Identificatore URL della segnalazione',
            'helper_text' => 'Generato automaticamente dal nome',
        ],
        'type' => [
            'label' => 'Tipo',
            'placeholder' => 'Seleziona tipo',
            'options' => [
                'road_maintenance' => 'Manutenzione Stradale',
                'public_lighting' => 'Illuminazione Pubblica',
                'waste_collection' => 'Raccolta Rifiuti',
                'parks_and_gardens' => 'Aree Verdi e Parchi',
                'sewage_and_drainage' => 'Fognature e Drenaggi',
                'public_buildings' => 'Edifici Pubblici',
                'environmental_reports' => 'Segnalazioni Ambientali',
                'public_transport' => 'Trasporti Pubblici',
                'urban_furniture' => 'Arredo Urbano',
                'public_safety' => 'Sicurezza Pubblica',
                'complaint' => 'Reclamo',
                'suggestion' => 'Suggerimento',
                'report' => 'Segnalazione',
                'request' => 'Richiesta',
                'other' => 'Altro',
            ],
            'description' => 'Categoria tipologica della segnalazione',
            'helper_text' => 'Seleziona la categoria che meglio descrive il problema',
        ],
        'images' => [
            'label' => 'Immagini',
            'placeholder' => 'Carica immagini',
            'help' => 'Allega immagini al ticket',
            'description' => 'Fotografie o immagini relative al problema segnalato',
            'helper_text' => 'Formati accettati: jpeg, png, jpg, gif, webp (max 10 file)',
        ],
        'search' => [
            'label' => 'Cerca',
            'placeholder' => 'Cerca nei ticket...',
        ],
        'location' => [
            'description' => 'Posizione geografica del problema segnalato',
            'helper_text' => 'Clicca sulla mappa o usa la posizione corrente',
            'placeholder' => 'Seleziona la posizione sulla mappa',
            'label' => 'Posizione',
        ],
        'longitude' => [
            'description' => 'Coordinata longitudine della segnalazione',
            'helper_text' => 'Valore numerico della longitudine (es. 12.4964)',
            'placeholder' => 'Es. 12.4964',
            'label' => 'Longitudine',
        ],
        'latitude' => [
            'description' => 'Coordinata latitudine della segnalazione',
            'helper_text' => 'Valore numerico della latitudine (es. 41.9028)',
            'placeholder' => 'Es. 41.9028',
            'label' => 'Latitudine',
        ],
    ],
    'actions' => [
        'create' => [
            'label' => 'Nuova Segnalazione',
            'tooltip' => 'Crea una nuova segnalazione',
            'icon' => 'heroicon-o-plus',
        ],
        'edit' => 'Modifica Ticket',
        'delete' => 'Elimina Ticket',
        'view' => 'Visualizza Ticket',
        'generateTickets' => [
            'label' => 'Genera Segnalazioni',
        ],
        'reorderRecords' => [
            'tooltip' => 'Riordina elementi',
            'icon' => 'heroicon-o-arrows-up-down',
            'label' => 'reorderRecords',
        ],
        'logout' => [
            'tooltip' => 'Esci dall\'account',
            'icon' => 'heroicon-o-arrow-right-on-rectangle',
            'label' => 'Esci',
        ],
        'cancel' => [
            'tooltip' => 'Annulla operazione',
            'icon' => 'heroicon-o-x-mark',
            'label' => 'Annulla',
        ],
        'profile' => [
            'tooltip' => 'Vai al profilo',
            'icon' => 'heroicon-o-user-circle',
            'label' => 'Profilo',
        ],
        'previous' => [
            'tooltip' => 'Passo precedente',
            'icon' => 'heroicon-o-chevron-left',
            'label' => 'Indietro',
        ],
        'next' => [
            'tooltip' => 'Passo successivo',
            'icon' => 'heroicon-o-chevron-right',
            'label' => 'Avanti',
        ],
        'createAnother' => [
            'tooltip' => 'Salva e crea un\'altra segnalazione',
            'icon' => 'heroicon-o-plus-circle',
            'label' => 'Salva e crea un\'altra',
        ],
        'resetColumnManager' => [
            'tooltip' => 'resetColumnManager',
            'icon' => 'resetColumnManager',
            'label' => 'resetColumnManager',
        ],
        'applyTableColumnManager' => [
            'tooltip' => 'applyTableColumnManager',
            'icon' => 'applyTableColumnManager',
            'label' => 'applyTableColumnManager',
        ],
    ],
    'messages' => [
        'created' => [
            'text' => 'Ticket creato con successo',
        ],
        'updated' => [
            'text' => 'Ticket aggiornato con successo',
        ],
        'deleted' => [
            'text' => 'Ticket eliminato con successo',
        ],
        'no_tickets' => [
            'text' => 'Nessun ticket trovato.',
        ],
        'images_uploaded' => [
            'text' => '{0} Nessuna immagine caricata|{1} :count immagine caricata|[2,*] :count immagini caricate',
        ],
    ],
    'sections' => [
        'empty' => [
            'heading' => 'Nessuna segnalazione',
            'label' => 'Non ci sono segnalazioni da visualizzare',
        ],
        'summary' => [
            'label' => 'Riepilogo Segnalazione',
            'description' => 'Verifica i dati prima dell\'invio',
        ],
        'images' => [
            'label' => 'Immagini Allegate',
        ],
    ],
    'ticket-form' => [
        'steps' => [
            'privacy' => [
                'label' => 'Privacy',
            ],
            'data' => [
                'label' => 'Dati della segnalazione',
            ],
            'summary' => [
                'label' => 'Riepilogo',
            ],
        ],
    ],
    'notifications' => [
        'submit_failed' => [
            'title' => 'Errore',
            'body' => 'Si è verificato un errore durante l\'invio. Riprova.',
        ],
    ],
    'navigation' => [
        'label' => 'Segnalazioni',
        'sort' => 89,
        'icon' => 'heroicon-o-exclamation-triangle',
        'group' => 'Gestione Segnalazioni',
    ],
    'model' => [
        'label' => 'Segnalazione',
    ],
    'rules' => [
        'image' => [
            'max_files' => 10,
            'allowed_types' => 'jpeg, png, jpg, gif, webp',
        ],
    ],
    'label' => 'Segnalazione',
];
