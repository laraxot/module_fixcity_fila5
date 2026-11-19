<?php

declare(strict_types=1);

return [
    'prompt' => [
        'title' => ['label' => '¿Cómo valoras la resolución de esta incidencia?'],
        'description' => ['label' => 'Tu valoración de 1 a 5 estrellas nos ayuda a mejorar el servicio.'],
        'stars_legend' => ['label' => 'Valora la resolución de 1 a 5 estrellas'],
        'thank_you' => ['label' => 'Gracias por tu valoración.'],
    ],
    'actions' => ['submit' => ['label' => 'Enviar valoración']],
    'admin' => [
        'stats' => [
            'average' => [
                'label' => 'Valoración media',
                'description' => ['label' => 'Media de estrellas de las incidencias valoradas'],
            ],
            'count' => [
                'label' => 'Valoraciones recibidas',
                'description' => ['label' => 'Incidencias con comentarios de la ciudadanía'],
            ],
        ],
    ],
    'validation' => [
        'rating_between' => ['label' => 'La valoración debe estar entre 1 y 5 estrellas.'],
        'rating_required' => ['label' => 'Selecciona una valoración antes de enviarla.'],
        'auth_required' => ['label' => 'Inicia sesión para valorar esta incidencia.'],
        'already_rated' => ['label' => 'Ya has valorado esta incidencia.'],
        'status_not_resolved' => ['label' => 'Solo puedes valorar incidencias resueltas o cerradas.'],
        'not_owner' => ['label' => 'Solo la persona que creó la incidencia puede valorarla.'],
    ],
];
