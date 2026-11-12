<?php

declare(strict_types=1);

// Fehlerseiten des Front Office (DCP-151): übersetzte Texte, keine technischen Details. Datei: lang/de/error_pages.php
return [
    'not_found' => [
        'title' => 'Seite nicht gefunden',
        'message' => 'Die aufgerufene Adresse existiert nicht oder die Seite wurde verschoben. Prüfen Sie die Schreibweise oder wählen Sie eine dieser Möglichkeiten.',
        'home' => 'Zur Startseite',
        'list' => 'Meldungen ansehen',
        'create' => 'Ein Problem melden',
    ],
    // 419 (Sitzung abgelaufen) und 403 (Zugriff verweigert): dieselbe Seite, dieselben Schaltflächen (View errors/access.blade.php)
    'expired' => [
        'title' => 'Sitzung abgelaufen',
        'message' => 'Sie waren zu lange inaktiv und die Sitzung wurde aus Sicherheitsgründen beendet. Es wurden keine Daten gesendet: Melden Sie sich erneut an und versuchen Sie es noch einmal.',
    ],
    'forbidden' => [
        'title' => 'Zugriff nicht erlaubt',
        'message' => 'Sie haben keine Berechtigung, diese Seite zu öffnen. Wenn Sie einen Fehler vermuten, melden Sie sich mit dem richtigen Konto an oder wählen Sie eine dieser Möglichkeiten.',
    ],
    'login' => 'Anmelden',
    'home' => 'Zur Startseite',
];
