<?php

declare(strict_types=1);

// Texts of the personal area pages "Notifications" and "Settings and privacy" (DCP-111).
// The theme views (components/blocks/personal-area-settings/*) contain no text: everything comes from here.
// Keys are identical in it, en, de, es (guard: PersonalAreaSettingsLangParityTest).
return [
    'breadcrumb' => [
        'label' => 'Breadcrumb',
        'home' => 'Home',
    ],
    'notifications' => [
        'title' => 'Notifications',
        'subtitle' => 'The status updates you received about your reports and the ones you follow.',
        'list_title' => 'Updates received',
        'count' => '{0} No notifications|{1} :count notification|[2,*] :count notifications',
        'unread' => '{0} none unread|{1} :count unread|[2,*] :count unread',
        'item_text' => 'Report :code has a new status: :status.',
        'followed_label' => '“:title”',
        'new_badge' => 'New',
        'open_report' => 'Open the report',
        'open_report_aria' => 'Open the report :code',
        'mark_read' => 'Mark as read',
        'mark_read_aria' => 'Mark as read: notification about report :code',
        'mark_all_read' => 'Mark all as read',
        'marked_read' => 'Notification marked as read.',
        'marked_all_read' => '{1} :count notification marked as read.|[2,*] :count notifications marked as read.',
        'has_more' => 'The latest :count notifications are shown.',
        'empty_title' => 'You have not received any notifications yet',
        'empty_text' => 'When the status of one of your reports, or of one you follow, changes, the update appears here.',
        'my_reports' => 'My reports',
        'settings_link' => 'Manage notification preferences',
        'settings_hint' => 'You always see every update here. To choose whether to also receive them by email, open the settings.',
    ],
    'settings' => [
        'title' => 'Settings and privacy',
        'subtitle' => 'Choose how to receive updates, check your account data and learn how to exercise your rights.',
        'nav_title' => 'On this page',
        'sections' => [
            'preferences' => 'Report notifications',
            'account' => 'Your data',
            'rights' => 'Your data rights',
            'deletion' => 'Delete your account',
        ],
        'email_verified' => 'Your email address is verified: with the option on, you receive an email for every status update.',
        'email_unverified' => 'Your email address is not verified yet: until you verify it you will not receive emails, even with the option on. Updates always stay visible in Notifications.',
        'notifications_link' => 'See your notifications',
        'account' => [
            'intro' => 'This is the account data the system keeps about you. Only you can see it.',
            'card_title' => 'Account data',
            'fields' => [
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
            ],
            'note' => 'You cannot edit them from this page: to correct them, follow the instructions in the section “Your data rights”.',
        ],
        'deletion' => [
            'intro' => 'You can ask to delete your account. We deactivate it at once and sign you out: you will no longer be able to sign in with this account.',
            'keep' => 'Your account data is kept only until the municipality closes your open reports, then it is deleted. Reports, comments and ratings stay in the municipality’s archive, as described in the privacy notice.',
            'reason_label' => 'Reason (optional)',
            'confirm_label' => 'I understand: I want to delete my account and I will no longer be able to sign in.',
            'submit' => 'Delete my account',
        ],
        'rights' => [
            'intro' => 'Regulation (EU) 2016/679 (GDPR) gives you these rights over your personal data.',
            'article' => 'Art. :article',
            'items' => [
                'access' => ['title' => 'Access', 'article' => '15', 'text' => 'Know whether we process your data and get a copy of it.'],
                'rectification' => ['title' => 'Rectification', 'article' => '16', 'text' => 'Have inaccurate data corrected or incomplete data completed.'],
                'erasure' => ['title' => 'Erasure', 'article' => '17', 'text' => 'Ask for your data to be erased in the cases provided by law.'],
                'restriction' => ['title' => 'Restriction', 'article' => '18', 'text' => 'Ask to restrict the processing of your data in some cases, for example while its accuracy is checked.'],
                'portability' => ['title' => 'Portability', 'article' => '20', 'text' => 'Receive the data you gave us in a structured format, in the cases provided by law.'],
                'objection' => ['title' => 'Objection', 'article' => '21', 'text' => 'Object to the processing of your data on grounds relating to your particular situation.'],
            ],
            'limits' => 'Some rights have conditions and limits set by the Regulation: the authority assesses each request.',
            'how_title' => 'How to exercise them',
            'how_text' => 'Contact the Municipality, the data controller, using the details in the Contacts section at the bottom of this page. State the right you want to exercise and the email address of your account.',
            'how_link' => 'Go to the Municipality contacts',
            'how_today' => 'You can delete your account on this page, in the section «Delete your account». To download your data or exercise the other rights, send the request to the authority.',
            'complaint_text' => 'If you believe the processing does not comply with the law, you can lodge a complaint with the Data Protection Authority (Garante per la protezione dei dati personali, art. 77).',
            'complaint_link' => 'Website of the Italian Data Protection Authority',
            'complaint_url' => 'https://www.garanteprivacy.it/',
        ],
    ],
];
