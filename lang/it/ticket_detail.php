<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/it/ticket_detail.php
// Testi del dettaglio pubblico /{lang}/tickets/{id} e del messaggio di "Segui" (story DCP-148).
return [
    'not_found' => 'Segnalazione non trovata o non visibile.',
    'not_available' => 'N/D',
    'images' => 'Immagini',
    'image_alt' => 'Immagine della segnalazione :name',
    'breadcrumb' => [
        'aria' => 'Percorso di navigazione',
        'home' => 'Home',
        'list' => 'Elenco segnalazioni',
    ],
    'meta' => [
        'number' => 'Segnalazione n. :id',
        'type' => 'Tipologia',
        'date' => 'Segnalata il',
    ],
    'status' => [
        'label' => 'Stato',
        'title' => 'Stato della segnalazione',
        'history' => 'Cronologia degli aggiornamenti',
        'previous' => 'Prima: :status',
        'empty' => 'Per ora non ci sono aggiornamenti: la segnalazione è «:status».',
        'empty_concluded' => 'La segnalazione è «:status». Nella cronologia pubblica non ci sono altri aggiornamenti.',
    ],
    'next_steps' => [
        'title' => 'Prossimi passi',
        'deadline_not_configured' => 'I tempi dipendono dall’ufficio competente e saranno aggiornati nella cronologia quando disponibili.',
        'moderation' => ['title' => 'Valutazione della segnalazione', 'description' => 'La segnalazione sarà verificata da un moderatore prima di essere resa operativa.'],
        'review' => ['title' => 'Presa in carico', 'description' => 'Un operatore sta verificando i dati e individuerà l’ufficio competente.'],
        'processing' => ['title' => 'Lavorazione', 'description' => 'La segnalazione è stata presa in carico. Gli aggiornamenti compariranno qui.'],
        'reopened' => ['title' => 'Segnalazione riaperta', 'description' => 'La segnalazione è stata riaperta e sarà riesaminata. Gli aggiornamenti compariranno qui.'],
        'waiting' => ['title' => 'In attesa', 'description' => 'La lavorazione attende informazioni o un riscontro esterno.'],
        'confirmation' => ['title' => 'Verifica della risoluzione', 'description' => 'La risoluzione è stata registrata; il ticket sarà chiuso solo dopo la verifica prevista.'],
        'closed' => ['title' => 'Procedimento concluso', 'description' => 'La segnalazione è stata risolta e chiusa.'],
    ],
    'timeline' => [
        'update' => 'Aggiornamento',
        'assignment' => 'Segnalazione assegnata',
        'approved' => 'Segnalazione approvata',
        'rejected' => 'Segnalazione scartata',
        'mail_sent' => 'Comunicazione inviata',
        'mail_failed' => 'Invio della comunicazione non riuscito',
        'mail_received' => 'Risposta ricevuta',
        'followup' => 'Sollecito inviato',
        'media_added' => 'Allegato aggiunto',
        'recipient' => 'Destinatario: :recipient',
        'closed' => 'Segnalazione chiusa',
    ],
    'where' => [
        'title' => 'Dove',
        'address' => 'Indirizzo',
        'no_address' => 'Indirizzo non indicato',
        'map' => 'Mappa con la posizione della segnalazione',
        'map_at' => 'Mappa con la posizione della segnalazione: :address',
    ],
    'photos' => [
        'title' => ['label' => 'Foto'],
        'alt' => ['label' => 'Foto :number della segnalazione «:title»'],
        'new_window' => ['label' => '(apre la foto intera in una nuova finestra)'],
        'document' => ['label' => 'Documento :number (PDF)'],
    ],
    'description' => [
        'title' => 'Descrizione',
        'empty' => 'Nessuna descrizione.',
    ],
    'comments' => ['heading' => 'Commenti e aggiornamenti'],
    'owner' => [
        'title' => 'La tua segnalazione',
        'private' => 'Per ora la vedi solo tu: non è ancora visibile al pubblico.',
        'code' => 'Codice di tracciamento',
        'code_help' => 'Conservalo: ti serve per consultare lo stato anche senza accedere. Non condividerlo.',
    ],
    'actions' => [
        'title' => 'Cosa puoi fare',
        'back' => 'Torna all’elenco',
        'new' => 'Segnala un altro problema',
        'follow' => 'Segui questa segnalazione',
        'unfollow' => 'Smetti di seguire',
        'login' => 'Accedi per seguire',
        'follow_help' => 'Se la segui, la ritrovi in «Segnalazioni che seguo» e ricevi una notifica quando cambia stato.',
        'guest_help' => 'Per seguire una segnalazione accedi con il tuo account.',
    ],
    'follow' => [
        'followed' => 'Ora segui questa segnalazione.',
        'unfollowed' => 'Non segui più questa segnalazione.',
        'list_link' => 'Vai alle segnalazioni che segui',
    ],
];
