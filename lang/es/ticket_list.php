<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT.
// File: lang/es/ticket_list.php
// Lista pública de incidencias en /{lang}/tickets (bloque ticket-list/design-comuni, story DCP-104). Las etiquetas del formulario de administración recurren a `it`.
return [
    'breadcrumb' => [
        'home' => 'Inicio',
        'current' => 'Lista de incidencias',
    ],
    'heading' => [
        'title' => 'Lista de incidencias',
        'subtitle' => '{0} Todavía no se ha resuelto ninguna incidencia en los últimos 12 meses.|{1} En los últimos 12 meses se ha resuelto :count incidencia.|[2,*] En los últimos 12 meses se han resuelto :count incidencias.',
    ],
    'results' => [
        'count' => '{0} 0 resultados|{1} 1 resultado|[2,*] :count resultados',
        'summary' => 'Incidencias de la :from a la :to de :total',
    ],
    'filters' => [
        'region' => 'Buscar y filtrar incidencias',
        'search_label' => 'Buscar en las incidencias',
        'search_help' => 'Título, descripción o dirección',
        'category' => 'categoría',
        'status' => 'estado',
        'period' => 'periodo',
        'sort' => 'ordenar por',
        'more' => 'Estado, periodo y orden',
        'apply' => 'Aplicar filtros',
        'open' => 'Filtrar',
        'reset' => 'Quitar todos los filtros',
        'modal_title' => 'Filtrar las incidencias',
        'close' => 'Cerrar ventana modal',
        'periods' => [
            'all' => 'Cualquier fecha',
            '7d' => 'Últimos 7 días',
            '30d' => 'Últimos 30 días',
            '12m' => 'Últimos 12 meses',
        ],
        'sorts' => [
            'recent' => 'Más recientes',
            'oldest' => 'Más antiguas',
            'updated' => 'Actualizadas recientemente',
        ],
    ],
    'tabs' => [
        'label' => 'Vista de los resultados',
        'map' => 'Mapa',
        'list' => 'Lista',
    ],
    'map' => [
        'alt' => 'Mapa de las incidencias públicas',
        'noscript' => 'El mapa necesita JavaScript.',
        'noscript_link' => 'Consulta la lista de incidencias',
    ],
    'card' => [
        'type' => 'Tipo de incidencia',
        'status' => 'Estado',
        'show_all' => 'Mostrar todo',
        'address' => 'Dirección',
        'detail' => 'Detalle',
        'date' => 'Comunicada el',
    ],
    'empty' => [
        'title' => 'No se ha encontrado ninguna incidencia.',
        'text' => 'Todavía no hay incidencias públicas: aparecerán aquí cuando el ayuntamiento las haya atendido.',
        'filtered' => 'Ninguna incidencia coincide con los filtros elegidos. Prueba a cambiarlos o a quitarlos.',
    ],
    'pagination' => [
        'label' => 'Paginación de la lista',
        'previous' => 'Anterior',
        'next' => 'Siguiente',
        'previous_label' => 'Página anterior',
        'next_label' => 'Página siguiente',
        'page_label' => 'Ir a la página :page',
        'current' => 'Página :page de :last',
    ],
    'invite' => [
        'title' => 'Comunica una incidencia',
        'text' => 'Para añadir una incidencia, inicia sesión con tu cuenta: te guiamos paso a paso y podrás seguir su estado.',
        'button' => 'Comunicar una incidencia',
    ],
    'feedback' => [
        'title' => '¿Qué tan claras son las informaciones de esta página?',
        'legend' => 'Valora la página de 1 a 5 estrellas',
        'star_label' => 'Valora %d estrellas de 5',
        'thanks' => '¡Gracias, tu opinión nos ayudará a mejorar el servicio!',
        'positive' => [
            'question' => '¿Qué aspectos te han gustado más?',
            'answers' => [
                'Las indicaciones eran claras',
                'Las indicaciones eran completas',
                'Siempre entendía que avanzaba correctamente',
                'No he tenido problemas técnicos',
                'Otro',
            ],
        ],
        'negative' => [
            'question' => '¿Dónde has encontrado las mayores dificultades?',
            'answers' => [
                'A veces las indicaciones no eran claras',
                'A veces las indicaciones no eran completas',
                'A veces no entendía si avanzaba correctamente',
                'He tenido problemas técnicos',
                'Otro',
            ],
        ],
        'detail' => [
            'question' => '¿Quieres añadir más detalles?',
            'label' => 'Detalle',
            'help' => 'Introduce un máximo de 200 caracteres',
        ],
        'back' => 'Atrás',
        'next' => 'Siguiente',
    ],
    'contacts' => [
        'title' => '¿Necesitas ayuda?',
        'faq' => 'Lee las preguntas frecuentes',
        'track' => 'Sigue una incidencia',
        'account' => 'Consulta tus incidencias',
        'phone' => 'Llama al número gratuito :value',
    ],
];
