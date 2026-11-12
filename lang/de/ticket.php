<?php

declare(strict_types=1);

return [
    'actions' => [
        'approve' => [
            'label' => 'Genehmigen',
            'reason_label' => 'Grund der Genehmigung',
        ],
        'assign' => [
            'label' => 'Mitarbeiter zuweisen',
        ],
        'changeStatus' => [
            'modal_heading' => 'Status ändern',
            'modal_submit' => 'Status speichern',
            'reason_help' => 'Die Begründung bleibt im Verlauf. Öffentliche Aktualisierungen sind für die Bürgerin oder den Bürger sichtbar.',
            'reason_label' => 'Grund der Statusänderung',
            'tooltip' => 'Status der Meldung ändern',
        ],
        'close' => [
            'label' => 'Vorgang schließen',
            'reason_label' => 'Grund der Schließung',
        ],
        'record_inbound' => [
            'label' => 'Erhaltene Antwort erfassen',
            'sender' => 'Absender',
            'summary' => 'Zusammenfassung der Antwort',
            'summary_help' => 'Wird im Verlauf des Vorgangs gespeichert, es wird keine E-Mail gesendet',
        ],
        'reject' => [
            'label' => 'Verwerfen',
            'reason_label' => 'Grund der Ablehnung',
        ],
        'resolve' => [
            'label' => 'Als gelöst markieren',
            'reason_label' => 'Wie sie gelöst wurde',
        ],
        'send_communication' => [
            'assignee_fallback' => 'Zuständige Person',
            'body' => 'Nachricht',
            'label' => 'Mitteilung senden',
            'office_fallback' => 'Amt',
            'recipient_email' => 'E-Mail des Empfängers',
            'recipient_help' => 'Optional: Verknüpfen Sie die E-Mail mit einer bereits zugewiesenen Person',
            'recipient_label' => 'Empfänger (zuständige Person)',
            'subject' => 'Betreff',
        ],
        'send_followup' => [
            'label' => 'Aktualisierung anfordern',
            'office' => 'Amt / Empfängerbezeichnung',
        ],
        'create' => [
            'label' => 'Neue Meldung senden',
        ],
    ],
    'contacts' => [
        'block_title' => [
            'label' => 'Gemeinde kontaktieren',
        ],
    ],
    'fields' => [
        'address' => [
            'label' => 'Ort*',
        ],
        'content' => [
            'label' => 'Inhalt*',
        ],
        'email' => [
            'label' => 'E-Mail',
        ],
        'images' => [
            'label' => 'Bilder',
        ],
        'name' => [
            'label' => 'Vollständiger Name',
        ],
        'responsible_id' => [
            'label' => 'Zuständiger Mitarbeiter',
        ],
        'title' => [
            'label' => 'Titel*',
        ],
        'type' => [
            'label' => 'Art der Störung*',
        ],
        'code' => [
            'label' => 'Meldungscode',
        ],
        'status' => [
            'label' => 'Status',
        ],
    ],
    'filters' => [
        'legend' => [
            'label' => 'Nach Kategorie filtern',
        ],
        'status' => [
            'legend' => [
                'label' => 'Nach Status filtern',
            ],
        ],
    ],
    'heading' => [
        'subtitle' => [
            'text' => 'Sehen Sie die offenen Meldungen im Gemeindegebiet und filtern Sie die Ergebnisse nach Kategorie.',
        ],
        'title' => [
            'label' => 'Liste der Meldungen',
        ],
    ],
    'map' => [
        'cta' => [
            'button' => [
                'label' => 'Störung melden',
            ],
            'text' => [
                'label' => 'Senden Sie eine neue Meldung und helfen Sie der Gemeinde, schneller einzugreifen.',
            ],
            'title' => [
                'label' => 'Haben Sie eine Störung bemerkt?',
            ],
        ],
        'image' => [
            'alt' => 'Karte der Meldungen',
        ],
    ],
    'notification' => [
        'assigned_to_you' => 'Die Meldung Nr. :ticket_id wurde Ihnen zugewiesen.',
        'assignment_title' => 'Neue Meldung zugewiesen',
        'status_changed' => 'Die Meldung :ticket_code ist jetzt :status.',
        'title' => 'Aktualisierung der Meldung',
    ],
    'privacy' => [
        'not_configured' => 'Das System hat noch keine Datenschutzerklärung veröffentlicht. Das Senden von Meldungen ist vorübergehend nicht möglich.',
        'open_notice' => 'Datenschutzerklärung lesen',
        'detail_prefix' => [
            'text' => 'Einzelheiten zur Verarbeitung personenbezogener Daten finden Sie in der ',
        ],
        'link' => [
            'label' => 'Datenschutzerklärung.',
        ],
        'links_prefix' => [
            'text' => 'Mehr erfahren:',
        ],
        'terms_label' => [
            'label' => 'Nutzungsbedingungen und Haftungshinweis',
        ],
        'cookie_label' => [
            'label' => 'Cookie-Richtlinie',
        ],
        'legal_label' => [
            'label' => 'Impressum',
        ],
    ],
    'gdpr_notice' => [
        'text' => 'FixCity verwendet die von Ihnen eingegebenen Daten, um Ihre Meldung zu einem lokalen Problem entgegenzunehmen, zu bearbeiten und Sie über Aktualisierungen zu informieren.',
        'text_no_name' => 'FixCity verwendet die von Ihnen eingegebenen Daten, um Ihre Meldung zu einem lokalen Problem entgegenzunehmen, zu bearbeiten und Sie über Aktualisierungen zu informieren.',
        'privacy_link' => 'Datenschutzerklärung',
    ],
    'sections' => [
        'author' => [
            'label' => 'Verfasser der Meldung',
        ],
        'contacts' => [
            'edit_action' => 'Bearbeiten',
        ],
        'summary' => [
            'label' => 'Zusammenfassung',
        ],
    ],
    'subscription' => [
        'discover_tickets' => 'Meldungen entdecken',
        'follow' => 'Aktualisierungen folgen',
        'followed_count' => '{0} Keine verfolgten Meldungen|{1} :count verfolgte Meldung|[2,*] :count verfolgte Meldungen',
        'followed_description' => 'Hier finden Sie die Meldungen, deren Aktualisierungen Sie folgen.',
        'followed_empty' => 'Sie folgen keiner Meldung. Öffnen Sie eine Meldung und wählen Sie „Aktualisierungen folgen“.',
        'followed_title' => 'Verfolgte Meldungen',
        'following_status' => 'Diese Meldung steht in Ihrer Liste der verfolgten Meldungen.',
        'heading' => 'Aktualisierungen der Meldung',
        'login_to_follow' => 'Melden Sie sich an, um Aktualisierungen zu folgen.',
        'not_following_status' => 'Sie folgen dieser Meldung nicht.',
        'personal_area_navigation' => 'Navigation im persönlichen Bereich',
        'tracking_unavailable' => 'Für diese Meldung ist kein Verfolgungscode verfügbar.',
        'unfollow' => 'Nicht mehr folgen',
    ],
    'warning' => [
        'summary_declaration' => [
            'text' => 'Ihre Angaben gelten als Erklärung. Prüfen Sie, ob sie korrekt sind.',
        ],
        'title' => [
            'label' => 'Achtung',
        ],
    ],
    'pagination' => [
        'aria' => 'Seitennavigation der Meldungen',
        'page' => 'Seite :page',
        'previous' => 'Vorherige Seite',
        'next' => 'Nächste Seite',
    ],
    'page' => [
        'title' => [
            'label' => 'Störung melden',
        ],
    ],
    'track' => [
        'title' => 'Meldung verfolgen',
        'account_subtitle' => 'Sie sehen eine Meldung, die mit Ihrem Konto verknüpft ist.',
        'subtitle' => 'Geben Sie den Bestätigungscode ein, um den Status zu prüfen.',
        'submit' => 'Suchen',
        'help' => 'Geben Sie den vollständigen Code ein: TCK- gefolgt von 16 Zeichen.',
        'empty_hint' => 'Haben Sie noch keinen Code? Sie können eine neue Meldung senden.',
        'create_cta' => 'Problem melden',
        'retry' => 'Neue Suche',
        'not_found' => 'Für diesen Code wurde keine Meldung gefunden.',
        'not_found_help' => 'Prüfen Sie den vollständigen Code oder senden Sie eine neue Meldung.',
        'timeline_title' => 'Aktualisierungen',
        'timeline_empty' => 'Es gibt noch keine öffentlichen Aktualisierungen.',
        'photos_title' => 'Angehängte Fotos',
        'photo_alt' => 'Foto :number der Meldung „:title“',
        'photo_opens_new_window' => '(öffnet das ganze Bild in einem neuen Fenster)',
        'rate_limited_title' => 'Zu viele Anfragen',
        'rate_limited_message' => 'Sie haben in kurzer Zeit zu oft gesucht. Warten Sie eine Minute und versuchen Sie es erneut.',
        'rate_limited_retry' => 'Zurück zur Codesuche',
    ],
    'pratiche' => [
        'description' => 'Ihre eingereichten Meldungen.',
        'count' => '{0} Keine Meldungen|{1} :count Meldung|[2,*] :count Meldungen',
        'empty' => 'Sie haben noch keine Meldungen eingereicht.',
    ],
    'steps' => [
        'active' => [
            'label' => 'Aktiv',
        ],
        'confirmed' => [
            'label' => 'Bestätigt',
        ],
    ],
];
