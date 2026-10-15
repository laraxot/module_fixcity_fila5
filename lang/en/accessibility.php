<?php

declare(strict_types=1);

/*
 * Accessibility statement (DCP-132). `issues` = data the municipality must supply in the JSON of the page `accessibilita.index`
 * (check: AssessAccessibilityStatementAction); `form` = feedback form; `mail` = message to the municipality's mailbox.
 */
return [
    'issues' => [
        'status' => [
            'title' => 'Compliance status',
            'hint' => 'Compliant, partially compliant or non-compliant, according to the outcome of the accessibility evaluation (Law 4/2004, art. 3-quater).',
        ],
        'evaluation' => [
            'title' => 'Evaluation method and date',
            'hint' => 'Self-assessment or third-party assessment, and the date it was done (format YYYY-MM-DD).',
        ],
        'non_accessible' => [
            'title' => 'Inaccessible content',
            'hint' => 'If the site is not compliant: each inaccessible part, with the reason.',
        ],
        'published_at' => [
            'title' => 'Publication date',
            'hint' => 'Date the statement was published (format YYYY-MM-DD).',
        ],
        'updated_at' => [
            'title' => 'Date of last update',
            'hint' => 'Date of the last review of the statement (format YYYY-MM-DD); it must be reviewed every year.',
        ],
        'feedback_email' => [
            'title' => 'Address for reports',
            'hint' => 'Mailbox of the municipality that receives accessibility reports: without it the feedback form cannot send.',
        ],
        'procedure_url' => [
            'title' => 'Link to the enforcement procedure',
            'hint' => 'https address of the procedure of art. 3-quinquies of Law 4/2004.',
        ],
        'document' => [
            'title' => 'Example values to replace',
            'hint' => 'The statement still contains example values (example.it, xxxx, [[...]]).',
        ],
    ],
    'reason' => [
        'missing' => 'missing',
        'invalid' => 'invalid value',
        'placeholder' => 'example value',
    ],
    'form' => [
        'attributes' => ['problem' => 'Problem description', 'page' => 'Page concerned', 'contact' => 'Email for the reply'],
        'title' => 'Report an accessibility problem',
        'problem' => 'Describe the problem',
        'problem_hint' => 'What you cannot read or do, and with which tool (for example screen reader or keyboard only).',
        'page' => 'Page concerned (optional)',
        'contact' => 'Email for the reply (optional)',
        'contact_hint' => 'Without a contact we cannot reply. We use the address only to reply to this report.',
        'submit' => 'Send the report',
        'sent' => 'Report sent. Thank you: the municipality will review it.',
        'throttled' => 'You have sent too many reports in a short time. Please try again later.',
        'unavailable' => 'The report cannot be sent: the municipality has not yet provided the mailbox that receives reports.',
        'inactive' => 'The form will be active once the municipality provides the mailbox that receives reports.',
    ],
    'mail' => [
        'subject' => 'Accessibility report from the website',
        'intro' => 'Accessibility report received from the accessibility statement form.',
        'problem' => 'Problem reported',
        'page' => 'Page concerned',
        'contact' => 'Contact for the reply',
        'none' => 'not provided',
        'procedure' => 'If there is no reply within 30 days or the reply is not satisfactory, the person can start the procedure of art. 3-quinquies of Law 4/2004.',
    ],
];
