<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/es/ticket_detail.php
// Textos de la página pública de detalle /{lang}/tickets/{id} y del mensaje de "Seguir" (story DCP-148).
return [
    'breadcrumb' => [
        'home' => 'Home',
        'list' => 'Lista de incidencias',
    ],
    'meta' => [
        'number' => 'Incidencia n.º :id',
        'type' => 'Tipo',
        'date' => 'Comunicada el',
    ],
    'status' => [
        'label' => 'Estado',
        'title' => 'Estado de la incidencia',
        'history' => 'Historial de actualizaciones',
        'previous' => 'Antes: :status',
        'empty' => 'Todavía no hay actualizaciones: la incidencia está «:status».',
    ],
    'where' => [
        'title' => 'Dónde',
        'address' => 'Dirección',
        'no_address' => 'Dirección no indicada',
        'map' => 'Mapa con la ubicación de la incidencia',
        'map_at' => 'Mapa con la ubicación de la incidencia: :address',
    ],
    'description' => [
        'title' => 'Descripción',
        'empty' => 'No hay descripción.',
    ],
    'actions' => [
        'title' => 'Qué puedes hacer',
        'back' => 'Volver a la lista',
        'new' => 'Informar de otro problema',
        'follow' => 'Seguir esta incidencia',
        'unfollow' => 'Dejar de seguir',
        'login' => 'Inicia sesión para seguirla',
        'follow_help' => 'Si la sigues, la encontrarás en «Incidencias que sigo» y recibirás una notificación cuando cambie su estado.',
        'guest_help' => 'Para seguir una incidencia, inicia sesión con tu cuenta.',
    ],
    'follow' => [
        'followed' => 'Ahora sigues esta incidencia.',
        'unfollowed' => 'Ya no sigues esta incidencia.',
        'list_link' => 'Ir a las incidencias que sigues',
    ],
];
