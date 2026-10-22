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
    'next_steps' => [
        'title' => 'Nächste Schritte',
        'deadline_not_configured' => 'Die Dauer hängt von der zuständigen Stelle ab und wird ergänzt, sobald sie verfügbar ist.',
        'moderation' => ['title' => 'Prüfung der Meldung', 'description' => 'Eine Moderation prüft die Meldung, bevor sie bearbeitet wird.'],
        'review' => ['title' => 'Übernahme', 'description' => 'Ein Bearbeiter prüft die Angaben und bestimmt die zuständige Stelle.'],
        'processing' => ['title' => 'In Bearbeitung', 'description' => 'Die Meldung wird bearbeitet. Aktualisierungen erscheinen hier.'],
        'waiting' => ['title' => 'Warten', 'description' => 'Die Bearbeitung wartet auf Informationen oder eine externe Antwort.'],
        'confirmation' => ['title' => 'Prüfung der Lösung', 'description' => 'Die Lösung wurde erfasst; das Ticket wird erst nach der vorgesehenen Prüfung geschlossen.'],
        'closed' => ['title' => 'Vorgang abgeschlossen', 'description' => 'Die Meldung wurde gelöst und geschlossen.'],
    ],
    'timeline' => [
        'update' => 'Aktualisierung', 'assignment' => 'Meldung zugewiesen', 'approved' => 'Meldung genehmigt',
        'rejected' => 'Meldung abgelehnt', 'mail_sent' => 'Nachricht gesendet', 'mail_failed' => 'Nachricht konnte nicht gesendet werden', 'mail_received' => 'Antwort erhalten',
        'followup' => 'Erinnerung gesendet', 'media_added' => 'Anhang hinzugefügt', 'recipient' => 'Empfänger: :recipient', 'attachment' => 'Datei: :file', 'closed' => 'Meldung geschlossen',
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
    'comments' => ['heading' => 'Kommentare und Aktualisierungen'],
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
