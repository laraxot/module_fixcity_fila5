<?php

declare(strict_types=1);

// Páginas de error del front office (DCP-151): texto traducido, sin detalles técnicos. Archivo: lang/es/error_pages.php
return [
    'not_found' => [
        'title' => 'Página no encontrada',
        'message' => 'La dirección que has abierto no existe o la página se ha movido. Comprueba que esté bien escrita o elige una de estas opciones.',
        'home' => 'Ir a la página de inicio',
        'list' => 'Ver las incidencias',
        'create' => 'Comunicar un problema',
    ],
    // 419 (sesión caducada) y 403 (acceso denegado): misma página, mismos botones (vista errors/access.blade.php)
    'expired' => [
        'title' => 'Sesión caducada',
        'message' => 'Has estado inactivo demasiado tiempo y, por seguridad, la sesión ha terminado. No se ha enviado ningún dato: inicia sesión de nuevo e inténtalo otra vez.',
    ],
    'forbidden' => [
        'title' => 'Acceso no permitido',
        'message' => 'No tienes permiso para abrir esta página. Si crees que es un error, inicia sesión con la cuenta correcta o elige una de estas opciones.',
    ],
    'login' => 'Iniciar sesión',
    'home' => 'Ir a la página de inicio',
];
