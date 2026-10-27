<?php

declare(strict_types=1);

// Textos de las páginas del área personal «Notificaciones» y «Ajustes y privacidad» (DCP-111).
// Las vistas del tema (components/blocks/personal-area-settings/*) no contienen texto: todo viene de aquí.
// Las claves son idénticas en it, en, de, es (guardia: PersonalAreaSettingsLangParityTest).
return [
    'breadcrumb' => [
        'label' => 'Ruta de navegación',
        'home' => 'Inicio',
    ],
    'notifications' => [
        'title' => 'Notificaciones',
        'subtitle' => 'Las actualizaciones de estado que has recibido sobre tus incidencias y sobre las que sigues.',
        'list_title' => 'Actualizaciones recibidas',
        'count' => '{0} Ninguna notificación|{1} :count notificación|[2,*] :count notificaciones',
        'unread' => '{0} ninguna sin leer|{1} :count sin leer|[2,*] :count sin leer',
        'item_text' => 'La incidencia :code tiene un nuevo estado: :status.',
        'followed_label' => '«:title»',
        'new_badge' => 'Nueva',
        'open_report' => 'Abrir la incidencia',
        'open_report_aria' => 'Abrir la incidencia :code',
        'mark_read' => 'Marcar como leída',
        'mark_read_aria' => 'Marcar como leída la notificación sobre la incidencia :code',
        'mark_all_read' => 'Marcar todas como leídas',
        'marked_read' => 'Notificación marcada como leída.',
        'marked_all_read' => '{1} :count notificación marcada como leída.|[2,*] :count notificaciones marcadas como leídas.',
        'has_more' => 'Se muestran las últimas :count notificaciones.',
        'empty_title' => 'Todavía no has recibido notificaciones',
        'empty_text' => 'Cuando cambie el estado de una de tus incidencias, o de una que sigues, la actualización aparecerá aquí.',
        'my_reports' => 'Mis incidencias',
        'settings_link' => 'Gestionar las preferencias de notificación',
        'settings_hint' => 'Aquí ves siempre todas las actualizaciones. Para elegir si recibirlas también por correo electrónico, abre los ajustes.',
    ],
    'settings' => [
        'title' => 'Ajustes y privacidad',
        'subtitle' => 'Elige cómo recibir las actualizaciones, revisa los datos de tu cuenta y descubre cómo ejercer tus derechos.',
        'nav_title' => 'En esta página',
        'sections' => [
            'preferences' => 'Notificaciones de las incidencias',
            'account' => 'Tus datos',
            'rights' => 'Tus derechos sobre los datos',
            'deletion' => 'Eliminar la cuenta',
        ],
        'email_verified' => 'Tu dirección de correo electrónico está verificada: con la opción activada recibes un correo con cada actualización de estado.',
        'email_unverified' => 'Tu dirección de correo electrónico aún no está verificada: mientras no la verifiques no recibirás correos, ni siquiera con la opción activada. Las actualizaciones siempre siguen visibles en Notificaciones.',
        'notifications_link' => 'Ver tus notificaciones',
        'account' => [
            'intro' => 'Estos son los datos de tu cuenta que el sistema conserva. Solo tú puedes verlos.',
            'card_title' => 'Datos de la cuenta',
            'fields' => [
                'name' => 'Nombre',
                'email' => 'Correo electrónico',
                'phone' => 'Teléfono',
            ],
            'note' => 'No puedes modificarlos desde esta página: para corregirlos, sigue las indicaciones de la sección «Tus derechos sobre los datos».',
        ],
        'deletion' => [
            'intro' => 'Puedes solicitar la eliminación de tu cuenta. La desactivamos enseguida y cerramos tu sesión: ya no podrás acceder con esta cuenta.',
            'keep' => 'Los datos de la cuenta se conservan solo hasta que el ayuntamiento cierre tus avisos abiertos; después se eliminan. Los avisos, los comentarios y las valoraciones quedan en el archivo del ayuntamiento, como se describe en la política de privacidad.',
            'reason_label' => 'Motivo (opcional)',
            'confirm_label' => 'Lo entiendo: quiero eliminar mi cuenta y ya no podré acceder.',
            'submit' => 'Eliminar mi cuenta',
        ],
        'rights' => [
            'intro' => 'El Reglamento (UE) 2016/679 (RGPD) te reconoce estos derechos sobre tus datos personales.',
            'article' => 'Art. :article',
            'items' => [
                'access' => ['title' => 'Acceso', 'article' => '15', 'text' => 'Saber si tratamos tus datos y obtener una copia.'],
                'rectification' => ['title' => 'Rectificación', 'article' => '16', 'text' => 'Hacer corregir los datos inexactos o completar los incompletos.'],
                'erasure' => ['title' => 'Supresión', 'article' => '17', 'text' => 'Pedir que se supriman tus datos en los casos previstos por la ley.'],
                'restriction' => ['title' => 'Limitación', 'article' => '18', 'text' => 'Pedir que se limite el tratamiento de tus datos en algunos casos, por ejemplo mientras se comprueba su exactitud.'],
                'portability' => ['title' => 'Portabilidad', 'article' => '20', 'text' => 'Recibir en un formato estructurado los datos que nos has facilitado, en los casos previstos por la ley.'],
                'objection' => ['title' => 'Oposición', 'article' => '21', 'text' => 'Oponerte al tratamiento de tus datos por motivos relacionados con tu situación particular.'],
            ],
            'limits' => 'Algunos derechos tienen condiciones y límites previstos por el Reglamento: la administración valora cada solicitud.',
            'how_title' => 'Cómo ejercerlos',
            'how_text' => 'Dirígete al Ayuntamiento, responsable del tratamiento, usando los datos de la sección Contactos al final de esta página. Indica el derecho que quieres ejercer y la dirección de correo electrónico de tu cuenta.',
            'how_link' => 'Ir a los contactos del Ayuntamiento',
            'how_today' => 'Puedes eliminar tu cuenta en esta página, en la sección «Eliminar la cuenta». Para descargar tus datos o ejercer los demás derechos, la solicitud se envía a la administración.',
            'complaint_text' => 'Si consideras que el tratamiento no cumple la normativa, puedes presentar una reclamación ante la autoridad italiana de protección de datos (Garante per la protezione dei dati personali, art. 77).',
            'complaint_link' => 'Sitio web de la autoridad italiana de protección de datos',
            'complaint_url' => 'https://www.garanteprivacy.it/',
        ],
    ],
];
