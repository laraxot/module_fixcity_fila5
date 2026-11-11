<?php

declare(strict_types=1);

return [
    'actions' => [
        'approve' => [
            'label' => 'Aprobar',
            'reason_label' => 'Motivo de la aprobación',
        ],
        'assign' => [
            'label' => 'Asignar operador',
        ],
        'changeStatus' => [
            'modal_heading' => 'Cambiar estado',
            'modal_submit' => 'Guardar estado',
            'reason_help' => 'El motivo queda en el historial. Las actualizaciones públicas son visibles para la ciudadanía.',
            'reason_label' => 'Motivo del cambio de estado',
            'tooltip' => 'Cambiar el estado del aviso',
        ],
        'close' => [
            'label' => 'Cerrar expediente',
            'reason_label' => 'Motivo del cierre',
        ],
        'record_inbound' => [
            'label' => 'Registrar respuesta recibida',
            'sender' => 'Remitente',
            'summary' => 'Resumen de la respuesta',
            'summary_help' => 'Se guarda en el historial del expediente; no se envía ningún correo',
        ],
        'reject' => [
            'label' => 'Descartar',
            'reason_label' => 'Motivo del descarte',
        ],
        'resolve' => [
            'label' => 'Marcar como resuelto',
            'reason_label' => 'Cómo se resolvió',
        ],
        'send_communication' => [
            'assignee_fallback' => 'Persona asignada',
            'body' => 'Mensaje',
            'label' => 'Enviar comunicación',
            'office_fallback' => 'Oficina',
            'recipient_email' => 'Correo del destinatario',
            'recipient_help' => 'Opcional: vincula el correo a una persona ya asignada al expediente',
            'recipient_label' => 'Destinatario (persona asignada)',
            'subject' => 'Asunto',
        ],
        'send_followup' => [
            'label' => 'Solicitar actualización',
            'office' => 'Oficina / etiqueta del destinatario',
        ],
        'create' => [
            'label' => 'Enviar una nueva incidencia',
        ],
    ],
    'contacts' => [
        'block_title' => [
            'label' => 'Contactar con el ayuntamiento',
        ],
    ],
    'fields' => [
        'address' => [
            'label' => 'Lugar*',
        ],
        'content' => [
            'label' => 'Contenido*',
        ],
        'email' => [
            'label' => 'Correo electrónico',
        ],
        'images' => [
            'label' => 'Imágenes',
        ],
        'name' => [
            'label' => 'Nombre completo',
        ],
        'responsible_id' => [
            'label' => 'Operador responsable',
        ],
        'title' => [
            'label' => 'Título*',
        ],
        'type' => [
            'label' => 'Tipo de incidencia*',
        ],
        'code' => [
            'label' => 'Código de incidencia',
        ],
        'status' => [
            'label' => 'Estado',
        ],
    ],
    'filters' => [
        'legend' => [
            'label' => 'Filtrar por categoría',
        ],
        'status' => [
            'legend' => [
                'label' => 'Filtrar por estado',
            ],
        ],
    ],
    'heading' => [
        'subtitle' => [
            'text' => 'Consulta los avisos abiertos en el municipio y filtra los resultados por categoría.',
        ],
        'title' => [
            'label' => 'Lista de avisos',
        ],
    ],
    'map' => [
        'cta' => [
            'button' => [
                'label' => 'Notificar una incidencia',
            ],
            'text' => [
                'label' => 'Envía un nuevo aviso y ayuda al ayuntamiento a intervenir más rápido.',
            ],
            'title' => [
                'label' => '¿Has detectado una incidencia?',
            ],
        ],
        'image' => [
            'alt' => 'Mapa de avisos',
        ],
    ],
    'notification' => [
        'assigned_to_you' => 'El aviso n.º :ticket_id se te ha asignado.',
        'assignment_title' => 'Nuevo aviso asignado',
        'status_changed' => 'El aviso :ticket_code ahora está :status.',
        'title' => 'Actualización del aviso',
    ],
    'privacy' => [
        'not_configured' => 'El ayuntamiento todavía no ha publicado la política de privacidad del servicio. El envío de avisos está desactivado temporalmente.',
        'open_notice' => 'Leer la política de privacidad',
        'detail_prefix' => [
            'text' => 'Para más detalles sobre el tratamiento de los datos personales, consulta la ',
        ],
        'link' => [
            'label' => 'política de privacidad.',
        ],
    ],
    'sections' => [
        'author' => [
            'label' => 'Autor del aviso',
        ],
        'contacts' => [
            'edit_action' => 'Editar',
        ],
        'summary' => [
            'label' => 'Resumen',
        ],
    ],
    'subscription' => [
        'discover_tickets' => 'Explorar los avisos',
        'follow' => 'Seguir las actualizaciones',
        'followed_count' => '{0} Ningún aviso seguido|{1} :count aviso seguido|[2,*] :count avisos seguidos',
        'followed_description' => 'Encuentra los avisos cuyas actualizaciones has decidido seguir.',
        'followed_empty' => 'No sigues ningún aviso. Abre un aviso y elige «Seguir las actualizaciones».',
        'followed_title' => 'Avisos seguidos',
        'following_status' => 'Este aviso está en tu lista de seguidos.',
        'heading' => 'Actualizaciones del aviso',
        'login_to_follow' => 'Inicia sesión para seguir las actualizaciones.',
        'not_following_status' => 'No sigues este aviso.',
        'personal_area_navigation' => 'Navegación del área personal',
        'tracking_unavailable' => 'El código de seguimiento no está disponible para este aviso.',
        'unfollow' => 'Dejar de seguir',
    ],
    'warning' => [
        'summary_declaration' => [
            'text' => 'La información que has facilitado tiene valor de declaración. Comprueba que sea correcta.',
        ],
        'title' => [
            'label' => 'Atención',
        ],
    ],
    'pagination' => [
        'aria' => 'Paginación de incidencias',
        'page' => 'Página :page',
        'previous' => 'Página anterior',
        'next' => 'Página siguiente',
    ],
    'page' => [
        'title' => [
            'label' => 'Comunicar una incidencia',
        ],
    ],
    'track' => [
        'title' => 'Seguir una incidencia',
        'account_subtitle' => 'Estás consultando una incidencia vinculada a tu cuenta.',
        'subtitle' => 'Introduce el código de confirmación para consultar el estado.',
        'submit' => 'Buscar',
        'help' => 'Introduce el código completo: TCK- seguido de 16 caracteres.',
        'empty_hint' => '¿Aún no tienes un código? Puedes enviar una nueva incidencia.',
        'create_cta' => 'Señalar un problema',
        'retry' => 'Nueva búsqueda',
        'not_found' => 'No se ha encontrado ninguna incidencia con este código.',
        'not_found_help' => 'Comprueba que el código esté completo o envía una nueva incidencia.',
        'timeline_title' => 'Actualizaciones',
        'timeline_empty' => 'Todavía no hay actualizaciones públicas.',
        'photos_title' => 'Fotos adjuntas',
        'photo_alt' => 'Foto :number de la incidencia «:title»',
        'photo_opens_new_window' => '(abre la imagen completa en una ventana nueva)',
        'rate_limited_title' => 'Demasiadas solicitudes',
        'rate_limited_message' => 'Has buscado demasiadas veces en poco tiempo. Espera un minuto e inténtalo de nuevo.',
        'rate_limited_retry' => 'Volver a la búsqueda del código',
    ],
    'pratiche' => [
        'description' => 'Tus incidencias enviadas.',
        'count' => '{0} Ninguna incidencia|{1} :count incidencia|[2,*] :count incidencias',
        'empty' => 'Todavía no has enviado ninguna incidencia.',
    ],
    'steps' => [
        'active' => [
            'label' => 'Activo',
        ],
        'confirmed' => [
            'label' => 'Confirmado',
        ],
    ],
];
