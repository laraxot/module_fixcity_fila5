<?php

return array (
  'fields' => 
  array (
    'id' => 
    array (
      'label' => 'ID',
      'placeholder' => 'Inserisci ID',
    ),
    'title' => 
    array (
      'label' => 'Titolo',
      'placeholder' => 'Inserisci il titolo',
      'help' => 'Inserisci un titolo descrittivo',
    ),
    'category' => 
    array (
      'name' => 
      array (
        'label' => 'Categoria',
        'placeholder' => 'Seleziona categoria',
      ),
      'label' => 'Categoria',
      'placeholder' => 'Filtra per categoria',
    ),
    'status' => 
    array (
      'label' => 'Stato',
      'placeholder' => 'Seleziona stato',
      'options' => 
      array (
        'open' => 'Aperto',
        'in_progress' => 'In Lavorazione',
        'resolved' => 'Risolto',
        'closed' => 'Chiuso',
      ),
    ),
    'priority' => 
    array (
      'label' => 'Priorità',
      'placeholder' => 'Seleziona la priorità',
      'help' => 'Indica l\'urgenza del ticket',
      'options' => 
      array (
        'low' => 'Bassa',
        'medium' => 'Media',
        'high' => 'Alta',
        'urgent' => 'Urgente',
      ),
      'description' => 'priority',
      'helper_text' => 'priority',
    ),
    'content' => 
    array (
      'label' => 'Contenuto',
      'placeholder' => 'Descrivi il problema...',
      'help' => 'Fornisci una descrizione dettagliata',
      'description' => 'content',
      'helper_text' => 'content',
    ),
    'created_at' => 
    array (
      'label' => 'Data Creazione',
    ),
    'updated_at' => 
    array (
      'label' => 'Ultima Modifica',
    ),
    'applyFilters' => 
    array (
      'label' => 'applyFilters',
    ),
    'toggleColumns' => 
    array (
      'label' => 'toggleColumns',
    ),
    'value' => 
    array (
      'label' => 'value',
    ),
    'reorderRecords' => 
    array (
      'label' => 'reorderRecords',
    ),
    'count' => 
    array (
      'label' => 'count',
    ),
    'create' => 
    array (
      'label' => 'create',
    ),
    'edit' => 
    array (
      'label' => 'edit',
    ),
    'delete' => 
    array (
      'label' => 'delete',
    ),
    'openFilters' => 
    array (
      'label' => 'openFilters',
    ),
    'resetFilters' => 
    array (
      'label' => 'resetFilters',
    ),
    'name' => 
    array (
      'label' => 'Nome',
      'placeholder' => 'Inserisci il nome',
      'help' => 'Inserisci un nome identificativo',
      'description' => 'name',
      'helper_text' => 'name',
    ),
    'slug' => 
    array (
      'label' => 'Slug',
      'placeholder' => 'Inserisci lo slug',
      'help' => 'URL-friendly versione del nome',
      'description' => 'slug',
      'helper_text' => 'slug',
    ),
    'type' => 
    array (
      'label' => 'Tipo',
      'placeholder' => 'Seleziona tipo',
      'options' => 
      array (
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
      ),
      'description' => 'type',
      'helper_text' => 'type',
    ),
    'images' => 
    array (
      'label' => 'Immagini',
      'placeholder' => 'Carica immagini',
      'help' => 'Allega immagini al ticket',
      'description' => 'images',
      'helper_text' => 'images',
    ),
    'search' => 
    array (
      'label' => 'Cerca',
      'placeholder' => 'Cerca nei ticket...',
    ),
    'location' => 
    array (
      'description' => 'location',
      'helper_text' => 'location',
      'placeholder' => 'location',
      'label' => 'location',
    ),
    'longitude' => 
    array (
      'description' => 'longitude',
      'helper_text' => 'longitude',
      'placeholder' => 'longitude',
      'label' => 'longitude',
    ),
    'latitude' => 
    array (
      'description' => 'latitude',
      'helper_text' => 'latitude',
      'placeholder' => 'latitude',
      'label' => 'latitude',
    ),
  ),
  'actions' => 
  array (
    'create' => 
    array (
      'label' => 'Crea Ticket',
    ),
    'edit' => 'Modifica Ticket',
    'delete' => 'Elimina Ticket',
    'view' => 'Visualizza Ticket',
    'generateTickets' => 
    array (
      'label' => 'generateTickets',
    ),
  ),
  'messages' =>
  array (
    'created' =>
    array (
      'text' => 'Ticket creato con successo',
    ),
    'updated' =>
    array (
      'text' => 'Ticket aggiornato con successo',
    ),
    'deleted' =>
    array (
      'text' => 'Ticket eliminato con successo',
    ),
    'no_tickets' =>
    array (
      'text' => 'Nessun ticket trovato.',
    ),
    'images_uploaded' =>
    array (
      'text' => '{0} Nessuna immagine caricata|{1} :count immagine caricata|[2,*] :count immagini caricate',
    ),
  ),
  'sections' =>
  array (
    'empty' =>
    array (
      'heading' => 'empty',
      'label' => 'empty',
    ),
    'summary' =>
    array (
      'label' => 'Riepilogo Segnalazione',
      'description' => 'Verifica i dati prima dell\'invio',
    ),
    'images' =>
    array (
      'label' => 'Immagini Allegate',
    ),
  ),
  'notifications' =>
  array (
    'submit_failed' =>
    array (
      'title' => 'Errore',
      'body' => 'Si è verificato un errore durante l\'invio. Riprova.',
    ),
  ),
  'navigation' => 
  array (
    'label' => 'ticket.navigation',
    'sort' => 89,
  ),
  'model' => 
  array (
    'label' => 'ticket.model',
  ),
  'rules' =>
  array (
    'image' =>
    array (
      'max_files' => 10,
      'allowed_types' => 'jpeg, png, jpg, gif, webp',
    ),
  ),
);
