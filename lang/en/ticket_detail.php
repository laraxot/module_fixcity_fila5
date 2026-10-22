<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/en/ticket_detail.php
// Texts of the public detail page /{lang}/tickets/{id} and of the "Follow" message (story DCP-148).
return [
    'not_found' => 'Report not found or not visible.',
    'not_available' => 'N/A',
    'images' => 'Images',
    'image_alt' => 'Image for report :name',
    'breadcrumb' => [
        'aria' => 'Breadcrumb',
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
    'next_steps' => [
        'title' => 'Next steps',
        'deadline_not_configured' => 'Timing depends on the responsible office and will be added when available.',
        'moderation' => ['title' => 'Report review', 'description' => 'A moderator will check the report before it becomes operational.'],
        'review' => ['title' => 'Taking charge', 'description' => 'An operator is checking the data and will identify the responsible office.'],
        'processing' => ['title' => 'In progress', 'description' => 'The report is being handled. Updates will appear here.'],
        'waiting' => ['title' => 'Waiting', 'description' => 'Work is waiting for information or an external response.'],
        'confirmation' => ['title' => 'Resolution check', 'description' => 'The resolution was recorded; the ticket closes only after the expected check.'],
        'closed' => ['title' => 'Process completed', 'description' => 'The report was resolved and closed.'],
    ],
    'timeline' => [
        'update' => 'Update', 'assignment' => 'Report assigned', 'approved' => 'Report approved',
        'rejected' => 'Report rejected', 'mail_sent' => 'Message sent', 'mail_failed' => 'Message delivery failed', 'mail_received' => 'Reply received',
        'followup' => 'Follow-up sent', 'media_added' => 'Attachment added', 'recipient' => 'Recipient: :recipient', 'attachment' => 'File: :file', 'closed' => 'Report closed',
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
    'comments' => ['heading' => 'Comments and updates'],
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
