<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// Passo "riepilogo" del wizard /tickets/create (DCP-33): testi di BuildTicketReviewSummaryAction.
// Le stringhe sono quelle di design-comuni-pagine-statiche/sito/segnalazione-03-riepilogo.html.
// File: lang/it/ticket_review.php
return [
    'report' => [
        'title' => 'Segnalazione',
        'card_title' => 'Disservizio',
        'edit_label' => 'Modifica',
        'map_label' => 'Posizione del disservizio sulla mappa',
        'fields' => [
            'address' => 'Indirizzo',
            'type' => 'Tipo di disservizio',
            'name' => 'Titolo',
            'content' => 'Dettagli',
            'images' => 'Immagini',
        ],
    ],
    'author' => [
        'group_title' => 'Dati Generali',
        'card_title' => 'Autore della segnalazione',
    ],
    'contacts' => [
        'title' => 'Contatti',
        'fields' => [
            'phone' => 'Telefono',
            'email' => 'Email',
        ],
    ],
];
