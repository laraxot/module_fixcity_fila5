<?php

declare(strict_types=1);

return [
    'pagination' => ['aria' => 'Paginación de incidencias', 'page' => 'Página :page', 'previous' => 'Página anterior', 'next' => 'Página siguiente'],
    'page' => ['title' => ['label' => 'Comunicar una incidencia']],
    'track' => ['title' => 'Seguir una incidencia', 'account_subtitle' => 'Estás consultando una incidencia vinculada a tu cuenta.', 'subtitle' => 'Introduce el código de confirmación para consultar el estado.', 'submit' => 'Buscar', 'help' => 'Introduce el código completo: TCK- seguido de 16 caracteres.', 'empty_hint' => '¿Aún no tienes un código? Puedes enviar una nueva incidencia.', 'create_cta' => 'Señalar un problema', 'retry' => 'Nueva búsqueda', 'not_found' => 'No se ha encontrado ninguna incidencia con este código.', 'not_found_help' => 'Comprueba que el código esté completo o envía una nueva incidencia.', 'timeline_title' => 'Actualizaciones', 'timeline_empty' => 'Todavía no hay actualizaciones públicas.', 'photos_title' => 'Fotos adjuntas', 'photo_alt' => 'Foto :number de la incidencia «:title»', 'photo_opens_new_window' => '(abre la imagen completa en una ventana nueva)', 'rate_limited_title' => 'Demasiadas solicitudes', 'rate_limited_message' => 'Has buscado demasiadas veces en poco tiempo. Espera un minuto e inténtalo de nuevo.', 'rate_limited_retry' => 'Volver a la búsqueda del código'],
    'fields' => ['code' => ['label' => 'Código de incidencia'], 'status' => ['label' => 'Estado']],
    'actions' => ['create' => ['label' => 'Enviar una nueva incidencia']],
    'pratiche' => ['description' => 'Tus incidencias enviadas.', 'count' => '{0} Ninguna incidencia|{1} :count incidencia|[2,*] :count incidencias', 'empty' => 'Todavía no has enviado ninguna incidencia.'],
    'privacy' => ['detail_prefix' => ['text' => 'Para más detalles sobre el tratamiento de los datos personales, consulta la '], 'link' => ['label' => 'política de privacidad.']],
    'steps' => ['active' => ['label' => 'Activo'], 'confirmed' => ['label' => 'Confirmado']],
];
