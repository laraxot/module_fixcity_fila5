<?php

declare(strict_types=1);

return [
    'name' => 'Fixcity',
    'icon' => 'fixcity-fixcity', // icon on dashboard
    'navigation_sort' => 1,
    'wizard' => [
        /** Consenti ?step=2|3 sulla pagina segnalazione-crea oltre a local / app.debug */
        'allow_step_query_override' => filter_var(
            $_ENV['FIXCITY_WIZARD_ALLOW_STEP_QUERY'] ?? false,
            FILTER_VALIDATE_BOOL,
        ),
        'confirmation_slug' => 'segnalazione-04-conferma',
    ],
    'media' => [
        /*
         * Disco privato dei NUOVI allegati dei ticket (SEC-04, DB-19), servito da /api/ticket-media/... con autorizzazione.
         * Stringa vuota = disattivato. Vedi ResolveTicketMediaDiskAction.
         * In produzione conta `filesystems.fixcity_ticket_media_disk` (config/filesystems.php): con la config in cache
         * $_ENV qui e' vuoto, quindi questo valore vale solo come ripiego.
         */
        'private_disk' => $_ENV['FIXCITY_TICKET_MEDIA_DISK'] ?? 'fixcity_ticket_media',
    ],
];
