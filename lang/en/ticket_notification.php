<?php

declare(strict_types=1);

// DCP-284: lifecycle messages of a report. Same keys and placeholders in it, en, de, es
// (test: tests/Unit/Notifications/TicketLifecycleNotificationLangTest.php).
return [
    'view_ticket' => 'Open the ticket',
    // DCP-287: chi segue una segnalazione di altri riceve titolo e link al dettaglio, mai il codice di tracciamento.
    'status_changed_followed' => 'The report “:ticket_title” that you follow is now :status.',
    'lifecycle' => [
        'received' => [
            'title' => 'Report received',
            'body' => 'We have received your report :ticket_code. We will let you know when its status changes.',
        ],
        'approved' => [
            'title' => 'Report approved',
            'body' => 'Report :ticket_code has been approved. Current status: :status.',
        ],
        'rejected' => [
            'title' => 'Report rejected',
            'body' => 'Report :ticket_code has been rejected.',
            'body_with_reason' => 'Report :ticket_code has been rejected. Reason: :reason',
        ],
        'assigned' => [
            'title' => 'Report taken in charge',
            'body' => 'Report :ticket_code has been taken in charge by an operator.',
        ],
        'closed' => [
            'title' => 'Report closed',
            'body' => 'Report :ticket_code has been closed.',
        ],
    ],
    // Short label shown in the personal area notification list ("has a new status: ...").
    'event' => [
        'received' => 'Received',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'assigned' => 'Taken in charge',
        'closed' => 'Closed',
    ],
    'staff' => [
        'received' => [
            'title' => 'New report to review',
            'body' => 'Report no. :ticket_id has arrived and is waiting for approval.',
        ],
    ],
];
