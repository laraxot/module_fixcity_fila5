<?php

declare(strict_types=1);

// Chiavi del wizard tickets/create (DCP-55 difetto 2); glossario: Themes/Sixteen/docs/i18n/01-glossario.md.
return [
    'ticket' => [
        'actions' => [
            'next' => [
                'label' => 'Siguiente',
            ],
            'save' => [
                'label' => 'Enviar aviso',
            ],
        ],
        'fields' => [
            'accept_terms' => [
                'helper' => 'Debes aceptar para continuar',
                'label' => 'He leído y comprendido la política de privacidad',
            ],
            'issue' => [
                'label' => 'Incidencia*',
            ],
        ],
        'steps' => [
            'auth' => [
                'description' => 'Lee y acepta las condiciones para continuar',
                'label' => 'Autorizaciones y condiciones',
            ],
            'data' => [
                'description' => 'Introduce los detalles del aviso',
                'label' => 'Datos de la incidencia',
            ],
            'summary' => [
                'label' => 'Resumen',
            ],
        ],
        'validation' => [
            'accept_terms' => 'Debes aceptar la política de privacidad para continuar.',
        ],
    ],
];
