<?php

declare(strict_types=1);

return [
    'pagination' => ['aria' => 'Paginación de incidencias', 'page' => 'Página :page', 'previous' => 'Página anterior', 'next' => 'Página siguiente'],
    'page' => ['title' => ['label' => 'Comunicar una incidencia']],
    'track' => ['title' => 'Seguir una incidencia', 'account_subtitle' => 'Estás consultando una incidencia vinculada a tu cuenta.', 'subtitle' => 'Introduce el código de confirmación para consultar el estado.', 'submit' => 'Buscar', 'help' => 'Introduce el código completo: TCK- seguido de 16 caracteres.', 'not_found' => 'No se ha encontrado ninguna incidencia con este código.', 'timeline_title' => 'Actualizaciones', 'timeline_empty' => 'Todavía no hay actualizaciones públicas.'],
    'fields' => ['code' => ['label' => 'Código de incidencia'], 'status' => ['label' => 'Estado']],
    'actions' => ['create' => ['label' => 'Enviar una nueva incidencia']],
    'pratiche' => ['description' => 'Tus incidencias enviadas.', 'count' => '{0} Ninguna incidencia|{1} :count incidencia|[2,*] :count incidencias', 'empty' => 'Todavía no has enviado ninguna incidencia.'],
];