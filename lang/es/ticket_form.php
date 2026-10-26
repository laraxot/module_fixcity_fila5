<?php

declare(strict_types=1);

return [
    'summaries' => [
        'images_none' => 'Ninguna imagen adjunta',
        'images_choice' => '{1} Una imagen adjunta|[2,*] :count imágenes adjuntas',
    ],
    'fields' => [
        'location' => [
            'label' => 'Lugar',
            'invalid' => 'Elige el punto en el mapa o busca una dirección: lo necesitamos para saber dónde está la incidencia.',
            'placeholder' => 'Busca un lugar',
            'helper_text' => 'Indica dónde se ha producido la incidencia',
            'address' => [
                'label' => 'Dirección de la incidencia',
                'placeholder' => 'Busca una dirección',
                'helper_text' => 'Busca la dirección o selecciona el punto en el mapa',
                'description' => 'Dirección de la incidencia',
            ],
            'description' => 'location',
        ],
        'type' => [
            'label' => 'Tipo de incidencia',
            'placeholder' => 'Selecciona el tipo de incidencia',
            'helper_text' => 'Selecciona la categoría de la incidencia',
            'description' => 'Categoría de la incidencia',
        ],
        'content' => [
            'label' => 'Detalles',
            'placeholder' => 'Describe el problema',
            'helper_text' => 'Introduce como máximo 200 caracteres',
            'description' => 'Describe la incidencia con claridad',
        ],
        'images' => [
            'upload_button' => 'Subir archivo',
            'upload_aria' => 'Subir archivos para la incidencia',
            'remove' => 'Eliminar :name',
            'uploading' => 'Subiendo',
            'error_type' => 'Formato no permitido: usa JPG, PNG o PDF',
            'error_size' => 'El archivo supera :max MB',
            'error_count' => 'Puedes adjuntar como máximo :max archivos',
            'error_upload' => 'No se pudo subir, inténtalo de nuevo',
            'label' => 'Imágenes',
            'helper_text' => 'Selecciona una o más imágenes para adjuntar a la incidencia',
            'placeholder' => 'Añadir imágenes (opcional)',
            'description' => 'Imágenes de apoyo a la incidencia',
        ],
        'author' => [
            'show_more' => 'Mostrar todo',
        ],
        'author_fiscal_code' => [
            'label' => 'Código fiscal',
            'description' => 'Código fiscal de la persona autora',
            'helper_text' => 'Dato asociado a tu perfil',
            'placeholder' => 'Código fiscal',
        ],
        'title' => [
            'label' => 'Título',
        ],
        'name' => [
            'label' => 'Nombre',
            'placeholder' => 'Introduce el nombre',
            'helper_text' => 'Introduce un nombre identificativo',
            'description' => 'Nombre identificativo de la incidencia',
        ],
        'privacy' => [
            'description' => 'Aceptación de la política de privacidad',
            'label' => 'Autorizaciones y condiciones',
            'helper_text' => 'Debes aceptar la política de privacidad para continuar',
        ],
        'privacyAccepted' => [
            'label' => 'He leído y comprendido la política de privacidad',
            'placeholder' => '',
            'helper_text' => 'Obligatorio para enviar la incidencia',
            'description' => 'Allineato a Design Comuni segnalazione-01-privacy',
        ],
        'email' => [
            'label' => 'Correo electrónico',
            'placeholder' => 'Introduce tu correo electrónico',
            'helper_text' => 'Tu correo electrónico para poder contactarte',
            'description' => 'Correo electrónico de la persona que comunica la incidencia',
        ],
        'review_images' => [
            'label' => 'Imágenes adjuntas',
            'description' => 'Vista previa de las imágenes adjuntas a la incidencia',
        ],
        'review_location' => [
            'label' => 'Ubicación',
            'description' => 'Lugar indicado o coordenadas',
        ],
        'review_email' => [
            'label' => 'Correo electrónico de contacto',
            'description' => 'Vista previa del correo electrónico indicado',
        ],
        'review_content' => [
            'label' => 'Detalle de la incidencia',
            'description' => 'Descripción de la incidencia',
        ],
        'review_name' => [
            'label' => 'Título / nombre de la incidencia',
            'description' => 'Título breve indicado por quien comunica la incidencia',
        ],
        'review_type' => [
            'label' => 'Tipo de incidencia',
            'description' => 'Vista previa del tipo elegido',
        ],
        'review_priority' => [
            'label' => 'Prioridad',
            'description' => 'Vista previa de la prioridad seleccionada',
        ],
        'review_author_name' => [
            'label' => 'Nombre y apellidos',
            'description' => 'Resumen de la persona que comunica la incidencia',
        ],
        'review_author_fiscal_code' => [
            'label' => 'Código fiscal',
            'description' => 'Resumen del código fiscal de la persona autora',
        ],
        'review_contact_phone' => [
            'label' => 'Teléfono',
            'description' => 'Resumen del teléfono de contacto',
        ],
        'review_contact_email' => [
            'label' => 'Correo electrónico de contacto',
            'description' => 'Resumen del correo electrónico',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_name' => [
            'label' => 'author_name',
            'description' => 'author_name',
            'helper_text' => 'author_name',
            'placeholder' => 'author_name',
        ],
        'author_email' => [
            'label' => 'author_email',
            'description' => 'author_email',
            'helper_text' => 'author_email',
            'placeholder' => 'author_email',
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => '',
            'label' => 'priority',
            'placeholder' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
            'placeholder' => 'slug',
            'helper_text' => '',
            'description' => 'slug',
        ],
        'type_legacy' => [
            'label' => 'type',
            'placeholder' => 'type',
            'helper_text' => '',
            'description' => 'type',
        ],
        'type_id' => [
            'description' => 'type_id',
            'helper_text' => '',
            'label' => 'type_id',
            'placeholder' => 'type_id',
        ],
        'gdpr_text' => [
            'label' => 'gdpr_text',
        ],
        'status' => [
            'label' => 'status',
            'placeholder' => 'status',
            'helper_text' => 'status',
            'description' => 'status',
        ],
    ],
    'required_notice' => 'Los campos marcados con un asterisco son obligatorios',
    'navscroll' => [
        'title' => 'INFORMACIÓN REQUERIDA',
    ],
    'nav' => [
        'aria_label' => 'Paso',
        'back' => 'Atrás',
        'next' => 'Siguiente',
    ],
    'hints' => [
        'max_length' => 'Introduce como máximo :max caracteres',
    ],
    'validation' => [
        'required' => 'El campo :attribute es obligatorio.',
        'min' => 'El campo :attribute debe tener al menos :min caracteres.',
        'max' => 'El campo :attribute no puede superar los :max caracteres.',
    ],
    'sections' => [
        'Riepilogo' => [
            'heading' => 'Resumen',
            'label' => 'Resumen',
        ],
        'Autore della segnalazione' => [
            'heading' => 'Persona autora de la incidencia',
            'label' => 'Persona autora de la incidencia',
        ],
        'Luogo' => [
            'label' => 'Lugar',
            'heading' => 'Lugar',
        ],
        'Disservizio' => [
            'label' => 'Incidencia',
            'heading' => 'Incidencia',
        ],
        'empty' => [
            'heading' => '',
            'label' => '',
        ],
        'Contatti' => [
            'heading' => 'Contacto',
        ],
        'location' => [
            'heading' => 'Lugar',
            'description' => 'Indica dónde se ha producido la incidencia',
        ],
        'issue' => [
            'heading' => 'Incidencia',
            'description' => 'Describe el problema comunicado',
        ],
        'author' => [
            'heading' => 'Persona autora de la incidencia',
            'description' => 'Información sobre ti',
        ],
        'Lugar' => [
            'heading' => 'Lugar',
            'label' => 'Lugar',
        ],
        'Incidencia' => [
            'heading' => 'Incidencia',
            'label' => 'Incidencia',
        ],
        'Resumen' => [
            'heading' => 'Resumen',
            'label' => 'Resumen',
        ],
        'Persona autora de la incidencia' => [
            'heading' => 'Persona autora de la incidencia',
            'label' => 'Persona autora de la incidencia',
        ],
        'Contacto' => [
            'heading' => 'Contacto',
            'label' => 'Contacto',
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'Autorizaciones y condiciones',
            'tooltip' => 'Aceptación de la política de privacidad',
            'description' => 'Lee y acepta la política de privacidad',
            'color' => 'success',
            'icon' => 'heroicon-o-shield-check',
        ],
        'dati-di-segnalazione' => [
            'label' => 'Datos de la incidencia',
            'tooltip' => 'Completa los datos de la incidencia',
            'description' => 'Introduce los detalles de la incidencia',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'riepilogo' => [
            'label' => 'Resumen',
            'tooltip' => 'Resumen final de la incidencia',
            'description' => 'Comprueba los datos antes de enviar',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'summary' => [
            'label' => 'Resumen',
            'tooltip' => 'Resumen final de la incidencia',
            'description' => 'Comprueba los datos antes de enviar',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'data' => [
            'label' => 'Datos de la incidencia',
            'tooltip' => 'Completa los datos de la incidencia',
            'description' => 'Introduce los detalles de la incidencia',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'Riepilogo' => [
            'label' => 'Resumen',
        ],
        'Dati di segnalazione' => [
            'label' => 'Datos de la incidencia',
        ],
        'Autorizzazioni e condizioni' => [
            'label' => 'Autorizaciones y condiciones',
        ],
    ],
];
