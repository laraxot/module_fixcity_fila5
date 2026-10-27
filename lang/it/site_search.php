<?php

declare(strict_types=1);

// Testi della ricerca globale /{lang}/ricerca (blocco search-results/design-comuni, modalita' live).
// Story: Modules/Fixcity/docs/stories/site-wide-front-office-search.story.md
return [
    'breadcrumb' => ['home' => 'Home', 'current' => 'Ricerca'],
    'heading' => 'Cerca nel sito',
    'form' => [
        'label' => 'Cerca segnalazioni, notizie, eventi e pagine',
        'placeholder' => 'Cerca nel sito',
        'button' => 'Cerca',
    ],
    'results' => [
        'hidden_title' => 'Risultati della ricerca',
        'count' => '{0} 0 risultati per «:query»|{1} 1 risultato per «:query»|[2,*] :count risultati per «:query»',
        'empty_query' => 'Scrivi una o più parole e premi Cerca: troverai segnalazioni pubbliche, notizie, eventi e pagine del sito.',
        'none' => 'Nessun risultato. Prova con parole diverse o più generiche.',
        'more_tickets' => 'Vedi tutte le :count segnalazioni trovate',
    ],
    'types' => [
        'ticket' => 'Segnalazione',
        'post' => 'Notizia',
        'event' => 'Evento',
        'page' => 'Pagina',
    ],
];
