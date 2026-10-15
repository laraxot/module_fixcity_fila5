<?php

declare(strict_types=1);

return [
    'title' => 'Aviso enviado',
    'breadcrumb' => ['Inicio', 'Avisos', 'Aviso enviado'],
    'contacts' => ['title' => 'Contactar con el ayuntamiento', 'assistance' => 'Solicitar ayuda'],
    'stepper' => [
        'title' => 'Comunicar un problema',
        'steps' => [
            ['title' => 'Privacidad', 'description' => 'Información y consentimiento'],
            ['title' => 'Datos', 'description' => 'Tus datos'],
            ['title' => 'Detalles', 'description' => 'Describe el problema'],
            ['title' => 'Confirmación', 'description' => 'Aviso enviado'],
        ],
    ],
    'summary' => [
        'with_code' => 'Gracias, hemos recibido tu <strong>aviso</strong>. Código de seguimiento: <strong>:code</strong>.',
        'without_code' => 'Gracias, hemos recibido tu <strong>aviso</strong>.',
    ],
    'visibility' => 'Tu aviso aparecerá en la <a href=":url" class="t-primary">lista de avisos</a> cuando la administración lo haya asumido.',
    'email_intro' => 'Puedes seguir el estado del aviso desde esta página o desde tu área personal.',
    'receipt' => [
        'with_code' => 'Seguir aviso :code',
        'without_code' => 'Seguir aviso',
    ],
    'reserved_area' => [
        'link_label' => 'Consultar la solicitud',
        'text' => 'en tu área personal',
    ],
];
