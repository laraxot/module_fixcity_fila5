<?php

declare(strict_types=1);

/**
 * Personal area: "My reports" and "Reports I follow" (DCP-110).
 *
 * Namespace key: fixcity::personal_area. The same keys exist in it, en, de, es (parity test:
 * PersonalAreaLangParityTest). `status.*` has one entry for every TicketStatusEnum value.
 */
return [
    'breadcrumb' => ['home' => 'Home'],
    'nav' => [
        'aria_label' => 'Personal area',
        'practices' => 'My reports',
        'followed' => 'Reports I follow',
        'notifications' => 'Notifications',
        'settings' => 'Settings and privacy',
    ],
    'new_report' => 'New report',
    'index_title' => 'Page index',
    'groups' => [
        'in_progress' => 'In progress',
        'concluded' => 'Concluded',
    ],
    'status' => [
        'draft' => 'Draft',
        'pending' => 'Pending',
        'open' => 'Open',
        'in_review' => 'In review',
        'in_progress' => 'In progress',
        'on_hold' => 'On hold',
        'reopened' => 'Reopened',
        'resolved' => 'Resolved',
        'closed' => 'Closed',
    ],
    'ticket' => [
        'code' => 'Tracking code:',
        'detail' => 'View details',
        'detail_aria' => 'View details of the report :title',
        'track' => 'Track the report',
        'track_aria' => 'Track the report :title',
        'tracking_unavailable' => 'The tracking code is not available for this report.',
        'status_prefix' => 'Status:',
    ],
    'truncated' => 'Showing the latest :shown of :total reports.',
    'practices' => [
        'title' => 'My reports',
        'subtitle' => 'The reports you have sent, with their status. Open a report to see its details or follow its progress.',
        'empty' => [
            'title' => 'You have not sent any reports yet',
            'text' => 'When you send a report you will find it here, with its status and the tracking code to follow its progress.',
            'cta' => 'Send your first report',
        ],
        'notifications' => [
            'text' => 'Do you want to be notified when the status of a report changes?',
            'link' => 'Manage notifications',
        ],
    ],
    'followed' => [
        'title' => 'Reports I follow',
        'subtitle' => 'The reports whose updates you chose to follow. For each one you find its status and the link to track it with its code.',
        'section' => 'Followed reports',
        'empty' => [
            'title' => 'No reports to show',
            'text' => 'Here you find the public reports whose updates you chose to follow. Open a report from the public list and choose to follow its updates to find it here. Reports not yet taken on by the administration appear only once they become public.',
            'cta' => 'Browse the reports',
        ],
    ],
];
