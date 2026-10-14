<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/it/ticket_detail.php
// Testi del dettaglio pubblico /{lang}/tickets/{id} e del messaggio di "Segui" (story DCP-148).
return [
    'breadcrumb' => [
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
    ],
    'where' => [
        'title' => 'Dove',
        'address' => 'Indirizzo',
        'no_address' => 'Indirizzo non indicato',
        'map' => 'Mappa con la posizione della segnalazione',
        'map_at' => 'Mappa con la posizione della segnalazione: :address',
    ],
    'description' => [
        'title' => 'Descrizione',
        'empty' => 'Nessuna descrizione.',
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
