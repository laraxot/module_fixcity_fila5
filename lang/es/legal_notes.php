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
            'hint' => 'Derechos sobre textos, imágenes y datos del sitio, licencia de reutilización, marcas y software de terceros con sus atribuciones.',
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
            'hint' => 'Ley aplicable y órgano jurisdiccional o foro competente para las controversias; a indicar por el ayuntamiento.',
        ],
        'contacts' => [
            'title' => 'Contactos sobre el sitio',
            'hint' => 'A quién escribir para pedir la retirada de un contenido o comunicar una infracción de derechos o un problema del sitio.',
        ],
    ],
];
