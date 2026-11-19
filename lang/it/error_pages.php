<?php

declare(strict_types=1);

// Pagine di errore del front office (DCP-151): testi in lingua, senza dettagli tecnici. File: lang/it/error_pages.php
return [
    'not_found' => [
        'title' => 'Pagina non trovata',
        'message' => 'L’indirizzo che hai aperto non esiste o la pagina è stata spostata. Controlla che sia scritto correttamente oppure scegli una di queste strade.',
        'home' => 'Vai alla home',
        'list' => 'Guarda le segnalazioni',
        'create' => 'Segnala un problema',
    ],
    // 419 (sessione scaduta) e 403 (accesso negato): stessa pagina, stessi pulsanti (view errors/access.blade.php)
    'expired' => [
        'title' => 'Sessione scaduta',
        'message' => 'Sei rimasto inattivo troppo a lungo e per sicurezza la sessione è terminata. Nessun dato è stato inviato: accedi di nuovo e riprova.',
    ],
    'forbidden' => [
        'title' => 'Accesso non consentito',
        'message' => 'Non hai i permessi per aprire questa pagina. Se pensi sia un errore, accedi con l’account corretto oppure scegli una di queste strade.',
    ],
    'login' => 'Accedi',
    'home' => 'Vai alla home',
];
