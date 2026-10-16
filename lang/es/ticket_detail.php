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
    'next_steps' => [
        'title' => 'Próximos pasos',
        'deadline_not_configured' => 'El plazo depende de la oficina responsable y se añadirá cuando esté disponible.',
        'moderation' => ['title' => 'Revisión de la incidencia', 'description' => 'Un moderador comprobará la incidencia antes de tramitarla.'],
        'review' => ['title' => 'Asunción del caso', 'description' => 'Un operador está verificando los datos y localizará la oficina responsable.'],
        'processing' => ['title' => 'En trámite', 'description' => 'La incidencia está siendo gestionada. Las actualizaciones aparecerán aquí.'],
        'waiting' => ['title' => 'En espera', 'description' => 'La tramitación espera información o una respuesta externa.'],
        'confirmation' => ['title' => 'Comprobación de la resolución', 'description' => 'La resolución se ha registrado; el ticket se cerrará tras la comprobación prevista.'],
        'closed' => ['title' => 'Proceso finalizado', 'description' => 'La incidencia se ha resuelto y cerrado.'],
    ],
    'timeline' => [
        'update' => 'Actualización', 'assignment' => 'Incidencia asignada', 'approved' => 'Incidencia aprobada',
        'rejected' => 'Incidencia descartada', 'mail_sent' => 'Mensaje enviado', 'mail_failed' => 'No se pudo enviar el mensaje', 'mail_received' => 'Respuesta recibida',
        'followup' => 'Recordatorio enviado', 'media_added' => 'Adjunto añadido', 'recipient' => 'Destinatario: :recipient', 'attachment' => 'Archivo: :file', 'closed' => 'Incidencia cerrada',
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
