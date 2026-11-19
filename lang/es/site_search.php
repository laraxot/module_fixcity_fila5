<?php

declare(strict_types=1);

// Textos de la búsqueda del sitio /{lang}/ricerca (bloque search-results/design-comuni, modo en vivo).
// Story: Modules/Fixcity/docs/stories/site-wide-front-office-search.story.md
return [
    'breadcrumb' => ['home' => 'Inicio', 'current' => 'Búsqueda'],
    'heading' => 'Buscar en el sitio',
    'form' => [
        'label' => 'Buscar incidencias, noticias, eventos y páginas',
        'placeholder' => 'Buscar en el sitio',
        'button' => 'Buscar',
    ],
    'results' => [
        'hidden_title' => 'Resultados de la búsqueda',
        'count' => '{0} 0 resultados para «:query»|{1} 1 resultado para «:query»|[2,*] :count resultados para «:query»',
        'empty_query' => 'Escribe una o más palabras y pulsa Buscar para encontrar incidencias públicas, noticias, eventos y páginas del sitio.',
        'none' => 'No hay resultados. Prueba con palabras diferentes o más generales.',
        'more_tickets' => 'Ver las :count incidencias encontradas',
    ],
    'types' => [
        'ticket' => 'Incidencia',
        'post' => 'Noticia',
        'event' => 'Evento',
        'page' => 'Página',
    ],
];
