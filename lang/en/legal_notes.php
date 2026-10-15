<?php

declare(strict_types=1);

/*
 * Note legali (disclaimer e condizioni di utilizzo, DCP-162). `sections` = sezioni del modello: il titolo e' l'intestazione che il testo del tenant
 * (`lang/<lingua>/legal-notes.md`) deve avere (verifica: AssessPrivacyPolicyAction con documento `legal_notes`); `hint` dice all'ente che cosa scrivere.
 */
return [
    'page_title' => 'Legal notice',
    'sections' => [
        'owner' => [
            'title' => 'Owner and operator of the site',
            'hint' => 'Name, address, tax code or VAT number, certified email (PEC) and contact details of the body running the service; reference to the public relations office and to the digital transition officer.',
        ],
        'purpose' => [
            'title' => 'Purpose of the service',
            'hint' => 'What the service is for and what it is not, in particular that it is not an emergency channel.',
        ],
        'use' => [
            'title' => 'Conditions of use',
            'hint' => 'How the service may be used and what is forbidden: unlawful use, false reports, abuse of the service.',
        ],
        'contributions' => [
            'title' => 'Reports and content sent by users',
            'hint' => 'What happens to the texts and photos sent: publication, moderation, ban on third-party data, licence granted to the municipality.',
        ],
        'liability' => [
            'title' => 'Limitation of liability and no warranties',
            'hint' => 'Exclusions and limits of the municipality\'s liability for content, availability, timing and outcome; to be settled with the municipality\'s legal adviser within the limits of the law.',
        ],
        'links' => [
            'title' => 'Links to third-party sites and services',
            'hint' => 'Responsibility for external links and for third-party services used by the site (maps and address search).',
        ],
        'ip' => [
            'title' => 'Intellectual property, content licence and reuse',
            'hint' => 'Rights in the texts, images and data of the site, reuse licence, trademarks and third-party software with attributions.',
        ],
        'privacy' => [
            'title' => 'Personal data, cookies and accessibility',
            'hint' => 'Pointers to the documents that cover these topics; do not repeat their content here.',
        ],
        'changes' => [
            'title' => 'Changes to these terms',
            'hint' => 'How and when the terms change and how users are told; date of the last update.',
        ],
        'law' => [
            'title' => 'Governing law and jurisdiction',
            'hint' => 'Governing law and the court or forum for disputes; to be stated by the municipality.',
        ],
        'contacts' => [
            'title' => 'Contacts about the site',
            'hint' => 'Whom to write to in order to ask for the removal of content, or to report an infringement of rights or a problem with the site.',
        ],
    ],
];
