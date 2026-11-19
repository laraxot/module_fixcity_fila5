<?php

declare(strict_types=1);

return [
    'fields' => [
        'title' => ['label' => '¿Qué claridad tienen las informaciones de esta página?'],
        'subtitle' => ['label' => 'Tus comentarios nos ayudan a mejorar el servicio.'],
        'star' => [
            'legend' => ['label' => 'Valora esta página de 1 a 5 estrellas'],
            'labels' => [
                1 => ['label' => 'Valorar 1 estrella de 5'],
                2 => ['label' => 'Valorar 2 estrellas de 5'],
                3 => ['label' => 'Valorar 3 estrellas de 5'],
                4 => ['label' => 'Valorar 4 estrellas de 5'],
                5 => ['label' => 'Valorar 5 estrellas de 5'],
            ],
        ],
        'positive_question' => ['label' => '¿Qué te gusta más de esta página?'],
        'positive_options' => ['options' => [
            1 => ['label' => 'La información es clara'],
            2 => ['label' => 'La información es completa'],
            3 => ['label' => 'Es fácil encontrar lo que busco'],
            4 => ['label' => 'El diseño es atractivo'],
            5 => ['label' => 'Otro'],
        ]],
        'negative_question' => ['label' => '¿Qué no funciona en esta página?'],
        'negative_options' => ['options' => [
            1 => ['label' => 'La información no es clara'],
            2 => ['label' => 'La información está incompleta'],
            3 => ['label' => 'Es difícil encontrar lo que busco'],
            4 => ['label' => 'El diseño no es atractivo'],
            5 => ['label' => 'Otro'],
        ]],
        'text_question' => ['label' => '¿Quieres añadir más detalles?'],
        'text_field' => ['label' => ['label' => 'Detalles'], 'help_text' => ['text' => 'Máximo 200 caracteres']],
    ],
    'actions' => ['back' => ['label' => 'Atrás'], 'next' => ['label' => 'Siguiente'], 'submit' => ['label' => 'Enviar']],
    'messages' => ['thank_you' => ['text' => '¡Gracias por tus comentarios!']],
];
