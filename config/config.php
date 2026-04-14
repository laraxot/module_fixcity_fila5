<?php

return [
    'name' => 'Fixcity',
    'icon' => 'fixcity-fixcity', // icon on dashboard
    'navigation_sort' => 1,
    'wizard' => [
        /** Consenti ?step=2|3 sulla pagina segnalazione-crea oltre a local / app.debug */
        'allow_step_query_override' => (bool) env('FIXCITY_WIZARD_ALLOW_STEP_QUERY', false),
        'confirmation_slug' => 'segnalazione-04-conferma',
    ],
];
