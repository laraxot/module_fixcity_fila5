<?php

declare(strict_types=1);

return [
    'title' => 'Meldung übermittelt',
    'breadcrumb' => ['Startseite', 'Meldungen', 'Meldung übermittelt'],
    'contacts' => ['title' => 'Gemeinde kontaktieren', 'assistance' => 'Hilfe anfordern'],
    'stepper' => [
        'title' => 'Problem melden',
        'steps' => [
            ['title' => 'Datenschutz', 'description' => 'Information und Einwilligung'],
            ['title' => 'Daten', 'description' => 'Ihre Angaben'],
            ['title' => 'Details', 'description' => 'Problem beschreiben'],
            ['title' => 'Bestätigung', 'description' => 'Meldung übermittelt'],
        ],
    ],
    'summary' => [
        'with_code' => 'Vielen Dank. Wir haben Ihre <strong>Meldung</strong> erhalten. Referenzcode: <strong>:code</strong>.',
        'without_code' => 'Vielen Dank. Wir haben Ihre <strong>Meldung</strong> erhalten.',
    ],
    'visibility' => 'Ihre Meldung wird in der <a href=":url" class="t-primary">Meldungsliste</a> angezeigt, sobald sie von der Verwaltung übernommen wurde.',
    'email_intro' => 'Sie können den Status Ihrer Meldung auf dieser Seite oder in Ihrem persönlichen Bereich verfolgen.',
    'receipt' => [
        'with_code' => 'Meldung :code verfolgen',
        'without_code' => 'Meldung verfolgen',
    ],
    'reserved_area' => [
        'link_label' => 'Anfrage anzeigen',
        'text' => 'in Ihrem persönlichen Bereich',
    ],
];
