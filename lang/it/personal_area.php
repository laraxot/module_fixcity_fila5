<?php

declare(strict_types=1);

/**
 * Area personale: "Le mie segnalazioni" e "Segnalazioni che seguo" (DCP-110).
 *
 * Chiave namespace: fixcity::personal_area. Le stesse chiavi esistono in it, en, de, es (test di parita':
 * PersonalAreaLangParityTest). `status.*` ha una voce per ogni valore di TicketStatusEnum.
 */
return [
    'breadcrumb' => ['home' => 'Home'],
    'nav' => [
        'aria_label' => 'Area personale',
        'practices' => 'Le mie segnalazioni',
        'followed' => 'Segnalazioni che seguo',
        'notifications' => 'Notifiche',
        'settings' => 'Impostazioni e privacy',
    ],
    'new_report' => 'Nuova segnalazione',
    'index_title' => 'Indice della pagina',
    'groups' => [
        'in_progress' => 'In corso',
        'concluded' => 'Concluse',
    ],
    'status' => [
        'draft' => 'Bozza',
        'pending' => 'In attesa',
        'open' => 'Aperta',
        'in_review' => 'In revisione',
        'in_progress' => 'In lavorazione',
        'on_hold' => 'In sospeso',
        'reopened' => 'Riaperta',
        'resolved' => 'Risolta',
        'closed' => 'Chiusa',
    ],
    'ticket' => [
        'code' => 'Codice di tracciamento:',
        'detail' => 'Vedi il dettaglio',
        'detail_aria' => 'Vedi il dettaglio della segnalazione :title',
        'track' => 'Traccia la segnalazione',
        'track_aria' => 'Traccia la segnalazione :title',
        'tracking_unavailable' => 'Il codice di tracciamento non è disponibile per questa segnalazione.',
        'status_prefix' => 'Stato:',
    ],
    'truncated' => 'Sono mostrate le ultime :shown segnalazioni su :total.',
    'practices' => [
        'title' => 'Le mie segnalazioni',
        'subtitle' => 'Le segnalazioni che hai inviato, con il loro stato. Apri una segnalazione per vederne il dettaglio o seguirne l’avanzamento.',
        'empty' => [
            'title' => 'Non hai ancora inviato segnalazioni',
            'text' => 'Quando invii una segnalazione la trovi qui, con il suo stato e il codice di tracciamento per seguirne l’avanzamento.',
            'cta' => 'Invia la tua prima segnalazione',
        ],
        'notifications' => [
            'text' => 'Vuoi essere avvisato quando cambia lo stato di una segnalazione?',
            'link' => 'Gestisci le notifiche',
        ],
    ],
    'followed' => [
        'title' => 'Segnalazioni che seguo',
        'subtitle' => 'Le segnalazioni di cui hai scelto di seguire gli aggiornamenti. Per ciascuna trovi lo stato e il collegamento per tracciarla con il suo codice.',
        'section' => 'Segnalazioni seguite',
        'empty' => [
            'title' => 'Nessuna segnalazione da mostrare',
            'text' => 'Qui compaiono le segnalazioni pubbliche di cui hai scelto di seguire gli aggiornamenti. Apri una segnalazione dall’elenco pubblico e scegli di seguirne gli aggiornamenti per ritrovarla qui. Le segnalazioni non ancora prese in carico dall’amministrazione compaiono solo quando diventano pubbliche.',
            'cta' => 'Esplora le segnalazioni',
        ],
    ],
];
