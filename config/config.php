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
];
