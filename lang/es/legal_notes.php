<?php

declare(strict_types=1);

/*
 * Note legali (disclaimer e condizioni di utilizzo, DCP-162). `sections` = sezioni del modello: il titolo e' l'intestazione che il testo del tenant
 * (`lang/<lingua>/legal-notes.md`) deve avere (verifica: AssessPrivacyPolicyAction con documento `legal_notes`); `hint` dice all'ente che cosa scrivere.
 */
return [
    'page_title' => 'Aviso legal',
    'sections' => [
        'owner' => [
            'title' => 'Titular y gestor del sitio',
            'hint' => 'Denominación, sede, código fiscal o número de IVA, correo certificado (PEC) y datos de contacto del ente que gestiona el servicio; referencia a la oficina de atención al ciudadano y al responsable de la transición digital.',
        ],
        'purpose' => [
            'title' => 'Finalidad del servicio',
            'hint' => 'Para qué sirve el servicio y qué no es, en particular que no es un canal de emergencia.',
        ],
        'use' => [
            'title' => 'Condiciones de uso',
            'hint' => 'Cómo se puede usar el servicio y qué está prohibido: usos ilícitos, comunicaciones falsas, abuso del servicio.',
        ],
        'contributions' => [
            'title' => 'Comunicaciones y contenidos enviados por los usuarios',
            'hint' => 'Qué ocurre con los textos y fotos enviados: publicación, moderación, prohibición de datos de terceros, licencia concedida al ayuntamiento.',
        ],
        'liability' => [
            'title' => 'Limitación de responsabilidad y ausencia de garantías',
            'hint' => 'Exclusiones y límites de responsabilidad del ayuntamiento por contenidos, disponibilidad, plazos y resultados; a definir con el asesor jurídico del ayuntamiento dentro de los límites de la ley.',
        ],
        'links' => [
            'title' => 'Enlaces a sitios y servicios de terceros',
            'hint' => 'Responsabilidad por los enlaces externos y por los servicios de terceros que usa el sitio (mapas y búsqueda de direcciones).',
        ],
        'ip' => [
            'title' => 'Propiedad intelectual, licencia de los contenidos y reutilización',
            'hint' => 'Indicar la licencia de los contenidos en lugar del marcador: el modelo Design Comuni (requisito C.SI.3.4) pide la sección «Licenza dei contenuti» con la fórmula open by default (CAD artículo 52) y CC-BY 4.0. El resto de la sección ya está redactado.',
        ],
        'privacy' => [
            'title' => 'Datos personales, cookies y accesibilidad',
            'hint' => 'Remisiones a los documentos que tratan estos temas; no repetir aquí su contenido.',
        ],
        'changes' => [
            'title' => 'Modificaciones de las condiciones',
            'hint' => 'Cómo y cuándo cambian las condiciones y cómo se informa al usuario; fecha de la última actualización.',
        ],
        'law' => [
            'title' => 'Ley aplicable y jurisdicción',
            'hint' => 'Indicar el foro elegido por el ayuntamiento o confirmar que se aplican las reglas de la ley. Una excepción al foro solo vale si se aprueba específicamente por escrito (artículo 1341, párrafo segundo, del Código Civil italiano): a valorar con el asesor jurídico.',
        ],
        'contacts' => [
            'title' => 'Contactos sobre el sitio',
            'hint' => 'Dirección de correo electrónico o PEC que recibe las solicitudes de retirada y las comunicaciones sobre el sitio: la oficina debe poder confirmar la recepción y comunicar la decisión.',
        ],
    ],
];
