<?php

declare(strict_types=1);

// Texte der Website-Suche /{lang}/ricerca (Block search-results/design-comuni, Live-Modus).
// Story: Modules/Fixcity/docs/stories/site-wide-front-office-search.story.md
return [
    'breadcrumb' => ['home' => 'Startseite', 'current' => 'Suche'],
    'heading' => 'Website durchsuchen',
    'form' => [
        'label' => 'Meldungen, Neuigkeiten, Veranstaltungen und Seiten durchsuchen',
        'placeholder' => 'Website durchsuchen',
        'button' => 'Suchen',
    ],
    'results' => [
        'hidden_title' => 'Suchergebnisse',
        'count' => '{0} 0 Ergebnisse für „:query“|{1} 1 Ergebnis für „:query“|[2,*] :count Ergebnisse für „:query“',
        'empty_query' => 'Geben Sie ein oder mehrere Wörter ein und drücken Sie Suchen, um öffentliche Meldungen, Neuigkeiten, Veranstaltungen und Seiten zu finden.',
        'none' => 'Keine Ergebnisse. Versuchen Sie andere oder allgemeinere Wörter.',
        'more_tickets' => 'Alle :count gefundenen Meldungen anzeigen',
    ],
    'types' => [
        'ticket' => 'Meldung',
        'post' => 'Neuigkeit',
        'event' => 'Veranstaltung',
        'page' => 'Seite',
    ],
];
