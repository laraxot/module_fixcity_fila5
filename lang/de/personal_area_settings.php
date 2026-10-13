<?php

declare(strict_types=1);

// Texte der Seiten des persönlichen Bereichs „Benachrichtigungen“ und „Einstellungen und Datenschutz“ (DCP-111).
// Die Theme-Views (components/blocks/personal-area-settings/*) enthalten keinen Text: alles kommt von hier.
// Die Schlüssel sind in it, en, de, es identisch (Prüfung: PersonalAreaSettingsLangParityTest).
return [
    'breadcrumb' => [
        'label' => 'Brotkrumennavigation',
        'home' => 'Startseite',
    ],
    'notifications' => [
        'title' => 'Benachrichtigungen',
        'subtitle' => 'Die Statusänderungen, die du zu deinen Meldungen und zu den Meldungen, denen du folgst, erhalten hast.',
        'list_title' => 'Erhaltene Aktualisierungen',
        'count' => '{0} Keine Benachrichtigungen|{1} :count Benachrichtigung|[2,*] :count Benachrichtigungen',
        'unread' => '{0} keine ungelesen|{1} :count ungelesen|[2,*] :count ungelesen',
        'item_text' => 'Die Meldung :code hat einen neuen Status: :status.',
        'new_badge' => 'Neu',
        'open_report' => 'Meldung öffnen',
        'open_report_aria' => 'Meldung öffnen: :code',
        'mark_read' => 'Als gelesen markieren',
        'mark_read_aria' => 'Als gelesen markieren: Benachrichtigung zur Meldung :code',
        'mark_all_read' => 'Alle als gelesen markieren',
        'marked_read' => 'Benachrichtigung als gelesen markiert.',
        'marked_all_read' => '{1} :count Benachrichtigung als gelesen markiert.|[2,*] :count Benachrichtigungen als gelesen markiert.',
        'has_more' => 'Angezeigt werden die letzten :count Benachrichtigungen.',
        'empty_title' => 'Du hast noch keine Benachrichtigungen erhalten',
        'empty_text' => 'Wenn sich der Status einer deiner Meldungen oder einer Meldung, der du folgst, ändert, erscheint die Aktualisierung hier.',
        'my_reports' => 'Meine Meldungen',
        'settings_link' => 'Benachrichtigungseinstellungen verwalten',
        'settings_hint' => 'Hier siehst du immer alle Aktualisierungen. Ob du sie zusätzlich per E-Mail erhältst, legst du in den Einstellungen fest.',
    ],
    'settings' => [
        'title' => 'Einstellungen und Datenschutz',
        'subtitle' => 'Lege fest, wie du Aktualisierungen erhältst, prüfe die Daten deines Kontos und erfahre, wie du deine Rechte ausübst.',
        'nav_title' => 'Auf dieser Seite',
        'sections' => [
            'preferences' => 'Benachrichtigungen zu Meldungen',
            'account' => 'Deine Daten',
            'rights' => 'Deine Rechte an deinen Daten',
        ],
        'email_verified' => 'Deine E-Mail-Adresse ist bestätigt: Bei aktivierter Option erhältst du bei jeder Statusänderung eine E-Mail.',
        'email_unverified' => 'Deine E-Mail-Adresse ist noch nicht bestätigt: Solange du sie nicht bestätigst, erhältst du keine E-Mails, auch nicht bei aktivierter Option. Aktualisierungen bleiben immer unter Benachrichtigungen sichtbar.',
        'notifications_link' => 'Deine Benachrichtigungen ansehen',
        'account' => [
            'intro' => 'Dies sind die Daten deines Kontos, die das System speichert. Nur du kannst sie sehen.',
            'card_title' => 'Kontodaten',
            'fields' => [
                'name' => 'Name',
                'email' => 'E-Mail',
                'phone' => 'Telefon',
            ],
            'note' => 'Auf dieser Seite kannst du sie nicht ändern: Um sie zu berichtigen, folge den Hinweisen im Abschnitt „Deine Rechte an deinen Daten“.',
        ],
        'rights' => [
            'intro' => 'Die Verordnung (EU) 2016/679 (DSGVO) gibt dir diese Rechte an deinen personenbezogenen Daten.',
            'article' => 'Art. :article',
            'items' => [
                'access' => ['title' => 'Auskunft', 'article' => '15', 'text' => 'Erfahren, ob wir deine Daten verarbeiten, und eine Kopie erhalten.'],
                'rectification' => ['title' => 'Berichtigung', 'article' => '16', 'text' => 'Unrichtige Daten berichtigen oder unvollständige Daten vervollständigen lassen.'],
                'erasure' => ['title' => 'Löschung', 'article' => '17', 'text' => 'In den gesetzlich vorgesehenen Fällen die Löschung deiner Daten verlangen.'],
                'restriction' => ['title' => 'Einschränkung', 'article' => '18', 'text' => 'In bestimmten Fällen die Einschränkung der Verarbeitung deiner Daten verlangen, zum Beispiel während ihre Richtigkeit überprüft wird.'],
                'portability' => ['title' => 'Datenübertragbarkeit', 'article' => '20', 'text' => 'Die Daten, die du uns bereitgestellt hast, in einem strukturierten Format erhalten, in den gesetzlich vorgesehenen Fällen.'],
                'objection' => ['title' => 'Widerspruch', 'article' => '21', 'text' => 'Der Verarbeitung deiner Daten aus Gründen, die sich aus deiner besonderen Situation ergeben, widersprechen.'],
            ],
            'limits' => 'Einige Rechte unterliegen Bedingungen und Grenzen der Verordnung: Die Behörde prüft jeden Antrag.',
            'how_title' => 'So übst du sie aus',
            'how_text' => 'Wende dich an die Gemeinde als Verantwortliche für die Verarbeitung und nutze die Angaben im Bereich Kontakt am Ende dieser Seite. Nenne das Recht, das du ausüben möchtest, und die E-Mail-Adresse deines Kontos.',
            'how_link' => 'Zu den Kontaktdaten der Gemeinde',
            'how_today' => 'Derzeit kannst du deine Daten im persönlichen Bereich nicht selbst herunterladen oder löschen: Der Antrag wird an die Behörde gerichtet.',
            'complaint_text' => 'Wenn du der Meinung bist, dass die Verarbeitung nicht den gesetzlichen Vorgaben entspricht, kannst du bei der italienischen Datenschutzbehörde (Garante per la protezione dei dati personali, Art. 77) Beschwerde einlegen.',
            'complaint_link' => 'Website der italienischen Datenschutzbehörde',
            'complaint_url' => 'https://www.garanteprivacy.it/',
        ],
    ],
];
