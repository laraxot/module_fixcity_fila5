<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// BO lifecycle labels for operators (ticket ViewTicket header actions).
return [
    'actions' => [
        'create' => [
            'label' => 'Create a report',
            'tooltip' => 'Create a new report',
        ],
        'assign' => [
            'label' => 'Assign operator',
            'tooltip' => 'Assign a responsible operator to the report',
        ],
        'approve' => [
            'label' => 'Approve',
            'reason_label' => 'Reason for approval',
        ],
        'reject' => [
            'label' => 'Reject',
            'reason_label' => 'Reason for rejection',
        ],
        'resolve' => [
            'label' => 'Mark as resolved',
            'reason_label' => 'How it was resolved',
        ],
        'close' => [
            'label' => 'Close case',
            'reason_label' => 'Reason for closing',
        ],
        'changeStatus' => [
            'label' => 'Change status',
            'tooltip' => 'Change the report status',
            'reason_label' => 'Reason for the status change',
            'reason_help' => 'The reason is kept in the timeline. Public updates are visible to the citizen.',
            'modal_heading' => 'Change status',
            'modal_submit' => 'Save status',
        ],
        'changePriority' => [
            'label' => 'Change priority',
            'tooltip' => 'Update the operational priority of the report',
        ],
        'send_communication' => [
            'label' => 'Send communication',
            'recipient_label' => 'Recipient (assignee)',
            'recipient_help' => 'Optional: link the email to an assignee already on the case',
            'recipient_email' => 'Recipient email',
            'subject' => 'Subject',
            'body' => 'Message',
            'assignee_fallback' => 'Assignee',
            'office_fallback' => 'Office',
        ],
        'record_inbound' => [
            'label' => 'Record inbound reply',
            'sender' => 'Sender',
            'summary' => 'Reply summary',
            'summary_help' => 'Stored in the case timeline; this does not send an email',
        ],
        'send_followup' => [
            'label' => 'Send follow-up',
            'office' => 'Office / recipient label',
        ],
        'back' => [
            'label' => 'Back',
        ],
        'save' => [
            'label' => 'Save Report',
        ],
        'save_short' => [
            'label' => 'Save',
        ],
        'save_draft' => [
            'label' => 'Save draft',
        ],
        'next' => [
            'label' => 'Next',
        ],
        'show_all' => [
            'label' => 'Show all',
        ],
        'remove_file' => [
            'aria' => [
                'label' => 'Remove file',
            ],
        ],
        'remove_image' => [
            'aria' => [
                'label' => 'Remove image',
            ],
        ],
        'submit' => [
            'label' => 'Confirm and submit',
        ],
    ],
];
