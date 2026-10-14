<?php

declare(strict_types=1);

/*
 * Declaración de accesibilidad (DCP-132). `issues` = datos que el Ayuntamiento debe aportar en el JSON de la página `accessibilita.index`
 * (comprobación: AssessAccessibilityStatementAction); `form` = formulario de comentarios; `mail` = mensaje al buzón del Ayuntamiento.
 */
return [
    'issues' => [
        'status' => [
            'title' => 'Estado de conformidad',
            'hint' => 'Conforme, parcialmente conforme o no conforme, según el resultado de la evaluación de accesibilidad (Ley 4/2004, art. 3-quater).',
        ],
        'evaluation' => [
            'title' => 'Método y fecha de la evaluación',
            'hint' => 'Autoevaluación o evaluación de terceros, y la fecha en que se hizo (formato AAAA-MM-DD).',
        ],
        'non_accessible' => [
            'title' => 'Contenidos no accesibles',
            'hint' => 'Si el sitio no es conforme: cada parte no accesible, con el motivo.',
        ],
        'published_at' => [
            'title' => 'Fecha de publicación',
            'hint' => 'Fecha en que se publicó la declaración (formato AAAA-MM-DD).',
        ],
        'updated_at' => [
            'title' => 'Fecha de la última actualización',
            'hint' => 'Fecha de la última revisión de la declaración (formato AAAA-MM-DD); debe revisarse cada año.',
        ],
        'feedback_email' => [
            'title' => 'Dirección para las comunicaciones',
            'hint' => 'Buzón del Ayuntamiento que recibe las comunicaciones de accesibilidad: sin él, el formulario no puede enviar.',
        ],
        'procedure_url' => [
            'title' => 'Enlace al procedimiento de aplicación',
            'hint' => 'Dirección https del procedimiento del art. 3-quinquies de la Ley 4/2004.',
        ],
        'document' => [
            'title' => 'Valores de ejemplo por sustituir',
            'hint' => 'La declaración aún contiene valores de ejemplo (example.it, xxxx, [[...]]).',
        ],
    ],
    'reason' => [
        'missing' => 'dato ausente',
        'invalid' => 'valor no válido',
        'placeholder' => 'valor de ejemplo',
    ],
    'form' => [
        'attributes' => ['problem' => 'Descripción del problema', 'page' => 'Página afectada', 'contact' => 'Correo para la respuesta'],
        'title' => 'Comunica un problema de accesibilidad',
        'problem' => 'Describe el problema',
        'problem_hint' => 'Qué no puedes leer o hacer, y con qué herramienta (por ejemplo lector de pantalla o solo teclado).',
        'page' => 'Página afectada (opcional)',
        'contact' => 'Correo para la respuesta (opcional)',
        'contact_hint' => 'Sin un contacto no podemos responderte. Usamos la dirección solo para responder a esta comunicación.',
        'submit' => 'Enviar la comunicación',
        'sent' => 'Comunicación enviada. Gracias: el Ayuntamiento la examinará.',
        'throttled' => 'Has enviado demasiadas comunicaciones en poco tiempo. Inténtalo más tarde.',
        'unavailable' => 'No se puede enviar la comunicación: el Ayuntamiento aún no ha indicado el buzón que las recibe.',
        'inactive' => 'El formulario se activará cuando el Ayuntamiento indique el buzón que recibe las comunicaciones.',
    ],
    'mail' => [
        'subject' => 'Comunicación de accesibilidad desde el sitio web',
        'intro' => 'Comunicación de accesibilidad recibida desde el formulario de la declaración de accesibilidad.',
        'problem' => 'Problema comunicado',
        'page' => 'Página afectada',
        'contact' => 'Contacto para la respuesta',
        'none' => 'no indicado',
        'procedure' => 'Si no hay respuesta en 30 días o la respuesta no es satisfactoria, la persona puede iniciar el procedimiento del art. 3-quinquies de la Ley 4/2004.',
    ],
];
