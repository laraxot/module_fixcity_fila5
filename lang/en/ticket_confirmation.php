<?php

declare(strict_types=1);

return [
    'title' => 'Report submitted',
    'breadcrumb' => ['Home', 'Reports', 'Report submitted'],
    'contacts' => ['title' => 'Contact the municipality', 'assistance' => 'Request assistance'],
    'stepper' => [
        'title' => 'Report a problem',
        'steps' => [
            ['title' => 'Privacy', 'description' => 'Information and consent'],
            ['title' => 'Data', 'description' => 'Your details'],
            ['title' => 'Details', 'description' => 'Describe the problem'],
            ['title' => 'Confirmation', 'description' => 'Report submitted'],
        ],
    ],
    'summary' => [
        'with_code' => 'Thank you, we have received your <strong>report</strong>. Tracking code: <strong>:code</strong>.',
        'without_code' => 'Thank you, we have received your <strong>report</strong>.',
    ],
    'visibility' => 'Your report will appear in the <a href=":url" class="t-primary">reports list</a> once it has been taken in charge by the administration.',
    'email_intro' => 'You can follow the report status from this page or your personal area.',
    'receipt' => [
        'with_code' => 'Track report :code',
        'without_code' => 'Track report',
    ],
    'reserved_area' => [
        'link_label' => 'View your request',
        'text' => 'in your personal area',
    ],
];
