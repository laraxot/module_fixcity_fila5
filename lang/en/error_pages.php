<?php

declare(strict_types=1);

// Front office error pages (DCP-151): translated text, no technical details. File: lang/en/error_pages.php
return [
    'not_found' => [
        'title' => 'Page not found',
        'message' => 'The address you opened does not exist or the page has moved. Check that it is spelled correctly or choose one of these options.',
        'home' => 'Go to the home page',
        'list' => 'See the reports',
        'create' => 'Report a problem',
    ],
    // 419 (session expired) and 403 (access denied): same page, same buttons (view errors/access.blade.php)
    'expired' => [
        'title' => 'Session expired',
        'message' => 'You were inactive for too long and the session ended for security reasons. Nothing was submitted: sign in again and try once more.',
    ],
    'forbidden' => [
        'title' => 'Access not allowed',
        'message' => 'You do not have permission to open this page. If you think this is a mistake, sign in with the right account or choose one of these options.',
    ],
    'login' => 'Sign in',
    'home' => 'Go to the home page',
];
