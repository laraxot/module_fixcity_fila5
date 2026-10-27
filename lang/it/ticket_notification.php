<?php

declare(strict_types=1);

// DCP-284: messaggi del ciclo di vita della segnalazione. Stesse chiavi e stessi segnaposto in it, en, de, es
// (test: tests/Unit/Notifications/TicketLifecycleNotificationLangTest.php).
return [
    'view_ticket' => 'Apri la segnalazione',
    // DCP-287: chi segue una segnalazione di altri riceve titolo e link al dettaglio, mai il codice di tracciamento.
    'status_changed_followed' => 'La segnalazione «:ticket_title» che segui è ora :status.',
    'lifecycle' => [
        'received' => [
            'title' => 'Segnalazione ricevuta',
            'body' => 'Abbiamo ricevuto la tua segnalazione :ticket_code. Ti avvisiamo quando cambia il suo stato.',
        ],
        'approved' => [
            'title' => 'Segnalazione approvata',
            'body' => 'La segnalazione :ticket_code è stata approvata ed è ora :status.',
        ],
        'rejected' => [
            'title' => 'Segnalazione scartata',
            'body' => 'La segnalazione :ticket_code è stata scartata.',
            'body_with_reason' => 'La segnalazione :ticket_code è stata scartata. Motivo: :reason',
        ],
        'assigned' => [
            'title' => 'Segnalazione presa in carico',
            'body' => 'La segnalazione :ticket_code è stata presa in carico da un operatore.',
        ],
        'closed' => [
            'title' => 'Segnalazione chiusa',
            'body' => 'La segnalazione :ticket_code è stata chiusa.',
        ],
    ],
    // Etichetta breve mostrata nell'elenco delle notifiche dell'area personale ("ha un nuovo stato: ...").
    'event' => [
        'received' => 'Ricevuta',
        'approved' => 'Approvata',
        'rejected' => 'Scartata',
        'assigned' => 'Presa in carico',
        'closed' => 'Chiusa',
    ],
    'staff' => [
        'received' => [
            'title' => 'Nuova segnalazione da esaminare',
            'body' => 'È arrivata la segnalazione n. :ticket_id, in attesa di approvazione.',
        ],
    ],
];
