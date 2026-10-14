<?php

declare(strict_types=1);

// Fixcity translations - LangServiceProvider SSoT.
// File: lang/de/ticket_detail.php
// Texte der öffentlichen Detailseite /{lang}/tickets/{id} und der Meldung zu "Folgen" (Story DCP-148).
return [
    'breadcrumb' => [
        'home' => 'Home',
        'list' => 'Liste der Meldungen',
    ],
    'meta' => [
        'number' => 'Meldung Nr. :id',
        'type' => 'Art',
        'date' => 'Gemeldet am',
    ],
    'status' => [
        'label' => 'Status',
        'title' => 'Status der Meldung',
        'history' => 'Verlauf der Aktualisierungen',
        'previous' => 'Zuvor: :status',
        'empty' => 'Es gibt noch keine Aktualisierungen: Die Meldung hat den Status „:status“.',
    ],
    'where' => [
        'title' => 'Wo',
        'address' => 'Adresse',
        'no_address' => 'Adresse nicht angegeben',
        'map' => 'Karte mit dem Ort der Meldung',
        'map_at' => 'Karte mit dem Ort der Meldung: :address',
    ],
    'description' => [
        'title' => 'Beschreibung',
        'empty' => 'Keine Beschreibung angegeben.',
    ],
    'actions' => [
        'title' => 'Was Sie tun können',
        'back' => 'Zurück zur Liste',
        'new' => 'Ein weiteres Problem melden',
        'follow' => 'Dieser Meldung folgen',
        'unfollow' => 'Nicht mehr folgen',
        'login' => 'Zum Folgen anmelden',
        'follow_help' => 'Wenn Sie ihr folgen, finden Sie sie unter „Meldungen, denen ich folge“ und erhalten eine Benachrichtigung, sobald sich der Status ändert.',
        'guest_help' => 'Um einer Meldung zu folgen, melden Sie sich mit Ihrem Konto an.',
    ],
    'follow' => [
        'followed' => 'Sie folgen dieser Meldung jetzt.',
        'unfollowed' => 'Sie folgen dieser Meldung nicht mehr.',
        'list_link' => 'Zu den Meldungen, denen Sie folgen',
    ],
];
