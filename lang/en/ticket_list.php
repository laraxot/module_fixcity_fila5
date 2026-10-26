<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT.
// File: lang/en/ticket_list.php
// Public list of reports at /{lang}/tickets (block ticket-list/design-comuni, story DCP-104). Admin form labels fall back to `it`.
return [
    'breadcrumb' => [
        'home' => 'Home',
        'current' => 'Reported issues',
    ],
    'heading' => [
        'title' => 'Reported issues',
        'subtitle' => '{0} No report has been resolved in the last 12 months yet.|{1} :count report was resolved in the last 12 months.|[2,*] :count reports were resolved in the last 12 months.',
    ],
    'results' => [
        'count' => '{0} 0 results|{1} 1 result|[2,*] :count results',
        'summary' => 'Reports :from to :to of :total',
    ],
    'filters' => [
        'region' => 'Search and filter reports',
        'search_label' => 'Search reports',
        'search_help' => 'Title, description or address',
        'category' => 'category',
        'status' => 'status',
        'period' => 'period',
        'sort' => 'sort by',
        'more' => 'Status, period and sorting',
        'apply' => 'Apply filters',
        'open' => 'Filter',
        'reset' => 'Remove all filters',
        'modal_title' => 'Filter reports',
        'close' => 'Close modal window',
        'periods' => [
            'all' => 'Any date',
            '7d' => 'Last 7 days',
            '30d' => 'Last 30 days',
            '12m' => 'Last 12 months',
        ],
        'sorts' => [
            'recent' => 'Most recent',
            'oldest' => 'Oldest',
            'updated' => 'Recently updated',
        ],
    ],
    'tabs' => [
        'label' => 'Results view',
        'map' => 'Map',
        'list' => 'List',
    ],
    'map' => [
        'alt' => 'Map of public reports',
        'noscript' => 'The map needs JavaScript.',
        'noscript_link' => 'View the list of reports',
    ],
    'card' => [
        'type' => 'Report type',
        'status' => 'Status',
        'show_all' => 'Show all',
        'address' => 'Address',
        'detail' => 'Details',
        'date' => 'Reported on',
    ],
    'empty' => [
        'title' => 'No reports found.',
        'text' => 'There are no public reports yet: they will appear here once the municipality has taken them on.',
        'filtered' => 'No report matches the selected filters. Try changing or removing them.',
    ],
    'pagination' => [
        'label' => 'List pagination',
        'previous' => 'Previous',
        'next' => 'Next',
        'previous_label' => 'Previous page',
        'next_label' => 'Next page',
        'page_label' => 'Go to page :page',
        'current' => 'Page :page of :last',
    ],
    'invite' => [
        'title' => 'Make a report',
        'text' => 'To add a report, sign in with your account: we guide you step by step and you will be able to follow its status.',
        'button' => 'Report an issue',
    ],
    'feedback' => [
        'title' => 'How clear is the information on this page?',
        'legend' => 'Rate this page from 1 to 5 stars',
        'star_label' => 'Rate %d stars out of 5',
        'thanks' => 'Thank you, your feedback will help us improve the service!',
        'positive' => [
            'question' => 'What did you like the most?',
            'answers' => [
                'The instructions were clear',
                'The instructions were complete',
                'I always knew I was going in the right direction',
                'I had no technical problems',
                'Other',
            ],
        ],
        'negative' => [
            'question' => 'Where did you find the most difficulty?',
            'answers' => [
                'Sometimes the instructions were not clear',
                'Sometimes the instructions were not complete',
                'Sometimes I did not know if I was going in the right direction',
                'I had technical problems',
                'Other',
            ],
        ],
        'detail' => [
            'question' => 'Would you like to add more details?',
            'label' => 'Details',
            'help' => 'Enter 200 characters at most',
        ],
        'back' => 'Back',
        'next' => 'Next',
    ],
    'contacts' => [
        'title' => 'Need help?',
        'faq' => 'Read the FAQ',
        'track' => 'Track a report',
        'account' => 'View your reports',
        'phone' => 'Call the toll-free number :value',
    ],
];
