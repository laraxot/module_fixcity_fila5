<?php

declare(strict_types=1);

return [
    'title' => 'Segnalazione inviata',
    'breadcrumb' => ['Home', 'Segnalazioni', 'Segnalazione inviata'],
    'contacts' => ['title' => 'Contatta il comune', 'assistance' => 'Richiedi assistenza'],
    'stepper' => [
        'title' => 'Segnalazione disservizio',
        'steps' => [
            ['title' => 'Privacy', 'description' => 'Informativa e consenso'],
            ['title' => 'Dati', 'description' => 'I tuoi dati'],
            ['title' => 'Dettagli', 'description' => 'Descrivi il problema'],
            ['title' => 'Conferma', 'description' => 'Segnalazione inviata'],
        ],
    ],
    'summary' => [
        'with_code' => 'Grazie, abbiamo ricevuto la tua <strong>segnalazione</strong>. Codice di tracciamento: <strong>:code</strong>.',
        'without_code' => 'Grazie, abbiamo ricevuto la tua <strong>segnalazione</strong>.',
    ],
    'visibility' => 'La segnalazione sarà visibile nella <a href=":url" class="t-primary">lista delle segnalazioni</a> quando sarà presa in carico dall’amministrazione.',
    'email_intro' => 'Puoi seguire lo stato della segnalazione da questa pagina o dalla tua area personale.',
    'receipt' => [
        'with_code' => 'Traccia la segnalazione :code',
        'without_code' => 'Traccia la segnalazione',
    ],
    'reserved_area' => [
        'link_label' => 'Consulta la richiesta',
        'text' => 'nella tua area riservata',
    ],
];
