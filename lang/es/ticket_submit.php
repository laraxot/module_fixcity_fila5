<?php

declare(strict_types=1);

// DCP-286: mensajes al enviar el asistente de incidencias (envío ya en curso, sesión caducada). Mismas claves en it, en, de, es.
return [
    'in_progress' => 'Todavía estamos enviando esta incidencia. Espera unos segundos y revisa tus incidencias en el área personal antes de volver a intentarlo.',
    'session_expired' => [
        'title' => 'Sesión caducada',
        'text' => 'Tu sesión ha caducado, por lo que la incidencia no se ha enviado. Lo que has escrito sigue en esta página: inicia sesión de nuevo en una pestaña nueva, vuelve aquí y pulsa Enviar.',
        'login_again' => 'Iniciar sesión de nuevo',
        'new_tab' => 'se abre en una pestaña nueva',
    ],
];
