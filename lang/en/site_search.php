<?php

declare(strict_types=1);

// Site-wide search texts for /{lang}/ricerca (search-results/design-comuni block, live mode).
// Story: Modules/Fixcity/docs/stories/site-wide-front-office-search.story.md
return [
    'breadcrumb' => ['home' => 'Home', 'current' => 'Search'],
    'heading' => 'Search the site',
    'form' => [
        'label' => 'Search reports, news, events and pages',
        'placeholder' => 'Search the site',
        'button' => 'Search',
    ],
    'results' => [
        'hidden_title' => 'Search results',
        'count' => '{0} 0 results for “:query”|{1} 1 result for “:query”|[2,*] :count results for “:query”',
        'empty_query' => 'Type one or more words and press Search to find public reports, news, events and pages of the site.',
        'none' => 'No results. Try different or more general words.',
        'more_tickets' => 'See all :count matching reports',
    ],
    'types' => [
        'ticket' => 'Report',
        'post' => 'News',
        'event' => 'Event',
        'page' => 'Page',
    ],
];
