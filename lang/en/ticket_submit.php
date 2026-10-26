<?php

declare(strict_types=1);

// DCP-286: messages for sending the report wizard (send already in progress, session expired). Same keys in it, en, de, es.
return [
    'in_progress' => 'We are still sending this report. Wait a few seconds and check your reports in your personal area before trying again.',
    'session_expired' => [
        'title' => 'Session expired',
        'text' => 'Your session has expired, so the report was not sent. What you entered is still on this page: sign in again in a new tab, then come back here and press Submit.',
        'login_again' => 'Sign in again',
        'new_tab' => 'opens in a new tab',
    ],
];
