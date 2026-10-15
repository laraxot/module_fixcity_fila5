<?php

declare(strict_types=1);

/*
 * Aviso de privacidad. `sections` = secciones obligatorias del art. 13 RGPD: el título es el encabezado que debe tener el texto del
 * inquilino (`lang/<idioma>/policy.md`) (comprobación: AssessPrivacyPolicyAction); `hint` indica al responsable qué debe escribir.
 */
return [
    'page_title' => 'Aviso de privacidad',
    'not_configured' => 'El aviso de privacidad aún no está disponible.',
    'sections' => [
        'controller' => [
            'title' => 'Responsable del tratamiento',
            'hint' => 'Denominación, sede y datos de contacto del Ayuntamiento responsable del tratamiento (art. 13 ap. 1 letra a).',
        ],
        'dpo' => [
            'title' => 'Delegado de protección de datos',
            'hint' => 'Datos de contacto del delegado de protección de datos designado por el Ayuntamiento (art. 13 ap. 1 letra b; art. 37 ap. 1 letra a).',
        ],
        'purposes' => [
            'title' => 'Fines y base jurídica del tratamiento',
            'hint' => 'Fines para los que se tratan los datos de las incidencias y su base jurídica (art. 13 ap. 1 letra c; art. 6 ap. 1).',
        ],
        'recipients' => [
            'title' => 'Destinatarios de los datos',
            'hint' => 'Destinatarios o categorías de destinatarios, incluidos los proveedores del servicio (art. 13 ap. 1 letra e).',
        ],
        'transfers' => [
            'title' => 'Transferencias a terceros países',
            'hint' => 'Si los datos salen del Espacio Económico Europeo: país y garantías; en caso contrario, declarar que no hay transferencias (art. 13 ap. 1 letra f).',
        ],
        'retention' => [
            'title' => 'Plazo de conservación',
            'hint' => 'Durante cuánto tiempo se conservan incidencias, cuentas, consentimientos y registros, o los criterios para determinarlo (art. 13 ap. 2 letra a).',
        ],
        'rights' => [
            'title' => 'Derechos del interesado',
            'hint' => 'Acceso, rectificación, supresión, limitación, oposición, portabilidad y, si la base jurídica es el consentimiento, retirada; cómo ejercerlos (art. 13 ap. 2 letras b y c; arts. 15 a 21).',
        ],
        'complaint' => [
            'title' => 'Reclamación ante la autoridad de control',
            'hint' => 'Derecho a presentar una reclamación ante la autoridad italiana de protección de datos (Garante) (art. 13 ap. 2 letra d).',
        ],
        'mandatory' => [
            'title' => 'Carácter obligatorio de la comunicación de datos',
            'hint' => 'Si la comunicación es un requisito legal o contractual o necesaria para presentar la incidencia, y las consecuencias de no facilitarla (art. 13 ap. 2 letra e).',
        ],
        'automated' => [
            'title' => 'Decisiones automatizadas',
            'hint' => 'Si existen decisiones automatizadas, incluida la elaboración de perfiles: lógica y consecuencias; en caso contrario, declarar que no existen (art. 13 ap. 2 letra f).',
        ],
        'document' => [
            'title' => 'Campos aún por completar en el texto',
            'hint' => 'El texto aún contiene marcadores (por ejemplo [[...]]) fuera de las secciones obligatorias.',
        ],
    ],
    'reason' => [
        'missing' => 'sección ausente o vacía',
        'placeholder' => 'aún contiene campos por completar',
    ],
];
