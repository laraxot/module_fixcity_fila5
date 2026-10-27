<?php

declare(strict_types=1);

// DCP-286: messaggi dell'invio del wizard di segnalazione (invio gia' in corso, sessione scaduta). Stesse chiavi in it, en, de, es.
return [
    'in_progress' => 'Stiamo ancora inviando questa segnalazione. Attendi qualche secondo e controlla le tue segnalazioni nell’area personale prima di riprovare.',
    'session_expired' => [
        'title' => 'Sessione scaduta',
        'text' => 'La tua sessione è scaduta, quindi la segnalazione non è stata inviata. I dati che hai inserito sono ancora in questa pagina: accedi di nuovo in una nuova scheda, poi torna qui e premi Invia.',
        'login_again' => 'Accedi di nuovo',
        'new_tab' => 'si apre in una nuova scheda',
    ],
];
