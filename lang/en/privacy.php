<?php

declare(strict_types=1);

/*
 * Privacy notice. `sections` = mandatory sections of GDPR art. 13: the title is the heading the tenant text
 * (`lang/<locale>/policy.md`) must have (check: AssessPrivacyPolicyAction); `hint` tells the controller what to write.
 */
return [
    'page_title' => 'Privacy notice',
    'not_configured' => 'The privacy notice is not available yet.',
    'sections' => [
        'controller' => [
            'title' => 'Data controller',
            'hint' => 'Name, address and contact details of the municipality acting as controller (art. 13 par. 1 let. a).',
        ],
        'dpo' => [
            'title' => 'Data protection officer',
            'hint' => 'Contact details of the data protection officer (DPO) appointed by the municipality (art. 13 par. 1 let. b; art. 37 par. 1 let. a).',
        ],
        'purposes' => [
            'title' => 'Purposes and legal basis of the processing',
            'hint' => 'Purposes for which report data is processed and the legal basis (art. 13 par. 1 let. c; art. 6 par. 1).',
        ],
        'recipients' => [
            'title' => 'Recipients of the data',
            'hint' => 'Recipients or categories of recipients, including service providers (art. 13 par. 1 let. e).',
        ],
        'transfers' => [
            'title' => 'Transfers to third countries',
            'hint' => 'If data leaves the European Economic Area: country and safeguards; otherwise state that there are no transfers (art. 13 par. 1 let. f).',
        ],
        'retention' => [
            'title' => 'Retention period',
            'hint' => 'How long reports, accounts, consents and logs are kept, or the criteria used to decide it (art. 13 par. 2 let. a).',
        ],
        'rights' => [
            'title' => 'Rights of the data subject',
            'hint' => 'Access, rectification, erasure, restriction, objection, portability and, if the legal basis is consent, withdrawal; how to exercise them (art. 13 par. 2 let. b and c; arts. 15-21).',
        ],
        'complaint' => [
            'title' => 'Complaint to the supervisory authority',
            'hint' => 'Right to lodge a complaint with the Italian data protection authority (Garante) (art. 13 par. 2 let. d).',
        ],
        'mandatory' => [
            'title' => 'Whether providing the data is mandatory',
            'hint' => 'Whether providing the data is a legal or contractual requirement or necessary to file a report, and the consequences of not providing it (art. 13 par. 2 let. e).',
        ],
        'automated' => [
            'title' => 'Automated decision-making',
            'hint' => 'Whether automated decision-making, including profiling, exists: logic and consequences; otherwise state that it does not (art. 13 par. 2 let. f).',
        ],
        'document' => [
            'title' => 'Fields still to be completed in the text',
            'hint' => 'The text still contains placeholders (for example [[...]]) outside the mandatory sections.',
        ],
    ],
    'reason' => [
        'missing' => 'section missing or empty',
        'placeholder' => 'still contains fields to be completed',
    ],
];
