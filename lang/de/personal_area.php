<?php

declare(strict_types=1);

/**
 * Persönlicher Bereich: "Meine Meldungen" und "Meldungen, denen ich folge" (DCP-110).
 *
 * Namespace-Schlüssel: fixcity::personal_area. Dieselben Schlüssel gibt es in it, en, de, es (Paritätstest:
 * PersonalAreaLangParityTest). `status.*` hat einen Eintrag für jeden Wert von TicketStatusEnum.
 */
return [
    'breadcrumb' => ['home' => 'Startseite'],
    'nav' => [
        'aria_label' => 'Persönlicher Bereich',
        'practices' => 'Meine Meldungen',
        'followed' => 'Meldungen, denen ich folge',
        'notifications' => 'Benachrichtigungen',
        'settings' => 'Einstellungen und Datenschutz',
    ],
    'new_report' => 'Neue Meldung',
    'index_title' => 'Seitenübersicht',
    'groups' => [
        'in_progress' => 'Laufend',
        'concluded' => 'Abgeschlossen',
    ],
    'ticket' => [
        'code' => 'Verfolgungscode:',
        'detail' => 'Details ansehen',
        'detail_aria' => 'Details der Meldung :title ansehen',
        'track' => 'Meldung verfolgen',
        'track_aria' => 'Meldung :title verfolgen',
        'tracking_unavailable' => 'Der Verfolgungscode ist für diese Meldung nicht verfügbar.',
        'status_prefix' => 'Status:',
    ],
    'truncated' => 'Angezeigt werden die letzten :shown von :total Meldungen.',
    'practices' => [
        'title' => 'Meine Meldungen',
        'subtitle' => 'Die Meldungen, die Sie gesendet haben, mit ihrem Status. Öffnen Sie eine Meldung, um Details zu sehen oder ihren Fortschritt zu verfolgen.',
        'empty' => [
            'title' => 'Sie haben noch keine Meldung gesendet',
            'text' => 'Wenn Sie eine Meldung senden, finden Sie sie hier, mit ihrem Status und dem Verfolgungscode, um ihren Fortschritt zu verfolgen.',
            'cta' => 'Ihre erste Meldung senden',
        ],
        'notifications' => [
            'text' => 'Möchten Sie benachrichtigt werden, wenn sich der Status einer Meldung ändert?',
            'link' => 'Benachrichtigungen verwalten',
        ],
    ],
    'followed' => [
        'title' => 'Meldungen, denen ich folge',
        'subtitle' => 'Die Meldungen, deren Aktualisierungen Sie verfolgen. Zu jeder finden Sie den Status und den Link, um sie mit ihrem Code zu verfolgen.',
        'section' => 'Verfolgte Meldungen',
        'empty' => [
            'title' => 'Keine Meldungen vorhanden',
            'text' => 'Hier erscheinen die öffentlichen Meldungen, deren Aktualisierungen Sie verfolgen. Öffnen Sie eine Meldung aus der öffentlichen Liste und wählen Sie, ihren Aktualisierungen zu folgen, um sie hier wiederzufinden. Meldungen, die die Verwaltung noch nicht übernommen hat, erscheinen erst, wenn sie öffentlich werden.',
            'cta' => 'Meldungen durchsuchen',
        ],
    ],
];
