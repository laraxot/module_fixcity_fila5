<?php

declare(strict_types=1);

/*
 * Dichiarazione di accessibilita' (DCP-132). `issues` = dati che l'ente deve fornire nel JSON della pagina `accessibilita.index`
 * (verifica: AssessAccessibilityStatementAction); `form` = modulo di feedback; `mail` = messaggio alla casella dell'ente.
 */
return [
    'issues' => [
        'status' => [
            'title' => 'Stato di conformità',
            'hint' => 'Conforme, parzialmente conforme o non conforme, secondo l’esito della valutazione di accessibilità (L. 4/2004, art. 3-quater).',
        ],
        'evaluation' => [
            'title' => 'Metodo e data della valutazione',
            'hint' => 'Autovalutazione o valutazione di terzi, e la data in cui è stata fatta (formato AAAA-MM-GG).',
        ],
        'non_accessible' => [
            'title' => 'Contenuti non accessibili',
            'hint' => 'Se il sito non è conforme: ogni parte non accessibile, con la motivazione.',
        ],
        'published_at' => [
            'title' => 'Data di pubblicazione',
            'hint' => 'Data in cui la dichiarazione è stata pubblicata (formato AAAA-MM-GG).',
        ],
        'updated_at' => [
            'title' => 'Data dell’ultimo aggiornamento',
            'hint' => 'Data dell’ultima revisione della dichiarazione (formato AAAA-MM-GG); va riesaminata ogni anno.',
        ],
        'feedback_email' => [
            'title' => 'Indirizzo per le segnalazioni',
            'hint' => 'Casella email dell’ente che riceve le segnalazioni di accessibilità: senza, il modulo di feedback non può inviare.',
        ],
        'procedure_url' => [
            'title' => 'Link alla procedura di attuazione',
            'hint' => 'Indirizzo https della procedura dell’art. 3-quinquies della L. 4/2004.',
        ],
        'document' => [
            'title' => 'Valori d’esempio da sostituire',
            'hint' => 'La dichiarazione contiene ancora valori d’esempio (example.it, xxxx, [[...]]).',
        ],
    ],
    'reason' => [
        'missing' => 'dato mancante',
        'invalid' => 'valore non valido',
        'placeholder' => 'valore d’esempio',
    ],
    'form' => [
        'attributes' => ['problem' => 'Descrizione del problema', 'page' => 'Pagina interessata', 'contact' => 'Email per la risposta'],
        'title' => 'Segnala un problema di accessibilità',
        'problem' => 'Descrivi il problema',
        'problem_hint' => 'Che cosa non riesci a leggere o a fare, e con quale strumento (per esempio lettore di schermo o solo tastiera).',
        'page' => 'Pagina interessata (facoltativo)',
        'contact' => 'Email per la risposta (facoltativo)',
        'contact_hint' => 'Senza un contatto non possiamo risponderti. Usiamo l’indirizzo solo per rispondere a questa segnalazione.',
        'submit' => 'Invia la segnalazione',
        'sent' => 'Segnalazione inviata. Grazie: l’ente la esaminerà.',
        'throttled' => 'Hai inviato troppe segnalazioni in poco tempo. Riprova più tardi.',
        'unavailable' => 'La segnalazione non può essere inviata: l’ente non ha ancora indicato la casella che riceve le segnalazioni.',
        'inactive' => 'Il modulo sarà attivo quando l’ente indicherà la casella che riceve le segnalazioni.',
    ],
    'mail' => [
        'subject' => 'Segnalazione di accessibilità dal sito',
        'intro' => 'Segnalazione di accessibilità ricevuta dal modulo della dichiarazione di accessibilità.',
        'problem' => 'Problema segnalato',
        'page' => 'Pagina interessata',
        'contact' => 'Contatto per la risposta',
        'none' => 'non indicato',
        'procedure' => 'Se non c’è risposta entro 30 giorni o la risposta non è soddisfacente, la persona può attivare la procedura dell’art. 3-quinquies della L. 4/2004.',
    ],
];
