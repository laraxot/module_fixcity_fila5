<?php

declare(strict_types=1);

/**
 * Área personal: "Mis incidencias" e "Incidencias que sigo" (DCP-110).
 *
 * Clave de espacio de nombres: fixcity::personal_area. Las mismas claves existen en it, en, de, es (prueba de
 * paridad: PersonalAreaLangParityTest). `status.*` tiene una entrada por cada valor de TicketStatusEnum.
 */
return [
    'breadcrumb' => ['home' => 'Inicio'],
    'nav' => [
        'aria_label' => 'Área personal',
        'practices' => 'Mis incidencias',
        'followed' => 'Incidencias que sigo',
        'notifications' => 'Notificaciones',
        'settings' => 'Ajustes y privacidad',
    ],
    'new_report' => 'Nueva incidencia',
    'index_title' => 'Índice de la página',
    'groups' => [
        'in_progress' => 'En curso',
        'concluded' => 'Concluidas',
    ],
    'status' => [
        'draft' => 'Borrador',
        'pending' => 'Pendiente',
        'open' => 'Abierta',
        'in_review' => 'En revisión',
        'in_progress' => 'En tramitación',
        'on_hold' => 'En espera',
        'reopened' => 'Reabierta',
        'resolved' => 'Resuelta',
        'closed' => 'Cerrada',
    ],
    'ticket' => [
        'code' => 'Código de seguimiento:',
        'detail' => 'Ver el detalle',
        'detail_aria' => 'Ver el detalle de la incidencia :title',
        'track' => 'Seguir la incidencia',
        'track_aria' => 'Seguir la incidencia :title',
        'tracking_unavailable' => 'El código de seguimiento no está disponible para esta incidencia.',
        'status_prefix' => 'Estado:',
    ],
    'truncated' => 'Se muestran las últimas :shown de :total incidencias.',
    'practices' => [
        'title' => 'Mis incidencias',
        'subtitle' => 'Las incidencias que has enviado, con su estado. Abre una incidencia para ver su detalle o seguir su avance.',
        'empty' => [
            'title' => 'Todavía no has enviado ninguna incidencia',
            'text' => 'Cuando envíes una incidencia la encontrarás aquí, con su estado y el código de seguimiento para seguir su avance.',
            'cta' => 'Envía tu primera incidencia',
        ],
        'notifications' => [
            'text' => '¿Quieres recibir un aviso cuando cambie el estado de una incidencia?',
            'link' => 'Gestionar las notificaciones',
        ],
    ],
    'followed' => [
        'title' => 'Incidencias que sigo',
        'subtitle' => 'Las incidencias cuyas novedades has elegido seguir. De cada una encontrarás su estado y el enlace para seguirla con su código.',
        'section' => 'Incidencias seguidas',
        'empty' => [
            'title' => 'No hay incidencias que mostrar',
            'text' => 'Aquí aparecen las incidencias públicas cuyas novedades has elegido seguir. Abre una incidencia de la lista pública y elige seguir sus novedades para encontrarla aquí. Las incidencias que la administración aún no ha asumido aparecen solo cuando pasan a ser públicas.',
            'cta' => 'Explorar las incidencias',
        ],
    ],
];
