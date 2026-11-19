<?php

declare(strict_types=1);

// DCP-284: mensajes del ciclo de vida de una incidencia. Mismas claves y marcadores en it, en, de, es
// (test: tests/Unit/Notifications/TicketLifecycleNotificationLangTest.php).
return [
    'view_ticket' => 'Abrir la incidencia',
    // DCP-287: chi segue una segnalazione di altri riceve titolo e link al dettaglio, mai il codice di tracciamento.
    'status_changed_followed' => 'La incidencia «:ticket_title» que sigues está ahora en estado :status.',
    'lifecycle' => [
        'received' => [
            'title' => 'Incidencia recibida',
            'body' => 'Hemos recibido tu incidencia :ticket_code. Te avisaremos cuando cambie su estado.',
        ],
        'approved' => [
            'title' => 'Incidencia aprobada',
            'body' => 'La incidencia :ticket_code ha sido aprobada. Estado actual: :status.',
        ],
        'rejected' => [
            'title' => 'Incidencia descartada',
            'body' => 'La incidencia :ticket_code ha sido descartada.',
            'body_with_reason' => 'La incidencia :ticket_code ha sido descartada. Motivo: :reason',
        ],
        'assigned' => [
            'title' => 'Incidencia en trámite',
            'body' => 'Un operador se ha hecho cargo de la incidencia :ticket_code.',
        ],
        'closed' => [
            'title' => 'Incidencia cerrada',
            'body' => 'La incidencia :ticket_code ha sido cerrada.',
        ],
    ],
    // Etiqueta breve en la lista de notificaciones del área personal ("tiene un nuevo estado: ...").
    'event' => [
        'received' => 'Recibida',
        'approved' => 'Aprobada',
        'rejected' => 'Descartada',
        'assigned' => 'En trámite',
        'closed' => 'Cerrada',
    ],
    'staff' => [
        'received' => [
            'title' => 'Nueva incidencia por revisar',
            'body' => 'Ha llegado la incidencia n.º :ticket_id y está pendiente de aprobación.',
        ],
    ],
];
