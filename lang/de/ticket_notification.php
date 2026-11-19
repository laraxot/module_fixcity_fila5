<?php

declare(strict_types=1);

// DCP-284: Meldungen zum Ablauf einer Meldung. Gleiche Schlüssel und Platzhalter in it, en, de, es
// (Test: tests/Unit/Notifications/TicketLifecycleNotificationLangTest.php).
return [
    'view_ticket' => 'Meldung öffnen',
    // DCP-287: chi segue una segnalazione di altri riceve titolo e link al dettaglio, mai il codice di tracciamento.
    'status_changed_followed' => 'Die Meldung „:ticket_title“, der Sie folgen, hat jetzt den Status :status.',
    'lifecycle' => [
        'received' => [
            'title' => 'Meldung eingegangen',
            'body' => 'Wir haben Ihre Meldung :ticket_code erhalten. Wir informieren Sie, sobald sich ihr Status ändert.',
        ],
        'approved' => [
            'title' => 'Meldung genehmigt',
            'body' => 'Die Meldung :ticket_code wurde genehmigt. Aktueller Status: :status.',
        ],
        'rejected' => [
            'title' => 'Meldung abgelehnt',
            'body' => 'Die Meldung :ticket_code wurde abgelehnt.',
            'body_with_reason' => 'Die Meldung :ticket_code wurde abgelehnt. Grund: :reason',
        ],
        'assigned' => [
            'title' => 'Meldung übernommen',
            'body' => 'Die Meldung :ticket_code wurde von einem Sachbearbeiter übernommen.',
        ],
        'closed' => [
            'title' => 'Meldung geschlossen',
            'body' => 'Die Meldung :ticket_code wurde geschlossen.',
        ],
    ],
    // Kurzbezeichnung in der Liste der Benachrichtigungen im persönlichen Bereich ("hat einen neuen Status: ...").
    'event' => [
        'received' => 'Eingegangen',
        'approved' => 'Genehmigt',
        'rejected' => 'Abgelehnt',
        'assigned' => 'Übernommen',
        'closed' => 'Geschlossen',
    ],
    'staff' => [
        'received' => [
            'title' => 'Neue Meldung zur Prüfung',
            'body' => 'Die Meldung Nr. :ticket_id ist eingegangen und wartet auf Genehmigung.',
        ],
    ],
];
