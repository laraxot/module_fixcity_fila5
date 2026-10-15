<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/en/ticket_detail.php
// Texts of the public detail page /{lang}/tickets/{id} and of the "Follow" message (story DCP-148).
return [
    'breadcrumb' => [
        'home' => 'Home',
        'list' => 'Reported issues',
    ],
    'meta' => [
        'number' => 'Report no. :id',
        'type' => 'Type',
        'date' => 'Reported on',
    ],
    'status' => [
        'label' => 'Status',
        'title' => 'Report status',
        'history' => 'Update history',
        'previous' => 'Before: :status',
        'empty' => 'There are no updates yet: the report is “:status”.',
    ],
    'where' => [
        'title' => 'Where',
        'address' => 'Address',
        'no_address' => 'Address not provided',
        'map' => 'Map showing the location of the report',
        'map_at' => 'Map showing the location of the report: :address',
    ],
    'description' => [
        'title' => 'Description',
        'empty' => 'No description provided.',
    ],
    'actions' => [
        'title' => 'What you can do',
        'back' => 'Back to the list',
        'new' => 'Report another problem',
        'follow' => 'Follow this report',
        'unfollow' => 'Stop following',
        'login' => 'Sign in to follow',
        'follow_help' => 'If you follow it, you will find it in “Reports I follow” and get a notification when its status changes.',
        'guest_help' => 'To follow a report, sign in with your account.',
    ],
    'follow' => [
        'followed' => 'You now follow this report.',
        'unfollowed' => 'You no longer follow this report.',
        'list_link' => 'Go to the reports you follow',
    ],
];
