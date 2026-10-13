<?php

declare(strict_types=1);

// Chiavi del wizard tickets/create (DCP-55 difetto 2); glossario: Themes/Sixteen/docs/i18n/01-glossario.md.
return [
    'author' => [
        'card_title' => 'Meldende Person',
        'group_title' => 'Allgemeine Angaben',
    ],
    'contacts' => [
        'fields' => [
            'phone' => 'Telefon',
            'email' => 'E-Mail',
        ],
        'title' => 'Kontakt',
    ],
    'report' => [
        'card_title' => 'Störung',
        'edit_label' => 'Bearbeiten',
        'fields' => [
            'address' => 'Adresse',
            'type' => 'Art der Störung',
            'name' => 'Titel',
            'content' => 'Details',
            'images' => 'Bilder',
        ],
        'map_label' => 'Standort der Störung auf der Karte',
        'title' => 'Meldung',
    ],
];
