<?php

declare(strict_types=1);

/*
 * Erklärung zur Barrierefreiheit (DCP-132). `issues` = Angaben, die die Gemeinde im JSON der Seite `accessibilita.index` liefern muss
 * (Prüfung: AssessAccessibilityStatementAction); `form` = Feedbackformular; `mail` = Nachricht an das Postfach der Gemeinde.
 */
return [
    'issues' => [
        'status' => [
            'title' => 'Konformitätsstatus',
            'hint' => 'Konform, teilweise konform oder nicht konform, gemäß dem Ergebnis der Barrierefreiheitsprüfung (Gesetz 4/2004, Art. 3-quater).',
        ],
        'evaluation' => [
            'title' => 'Methode und Datum der Bewertung',
            'hint' => 'Selbstbewertung oder Bewertung durch Dritte und das Datum, an dem sie erfolgte (Format JJJJ-MM-TT).',
        ],
        'non_accessible' => [
            'title' => 'Nicht barrierefreie Inhalte',
            'hint' => 'Wenn die Website nicht konform ist: jeder nicht barrierefreie Teil mit Begründung.',
        ],
        'published_at' => [
            'title' => 'Veröffentlichungsdatum',
            'hint' => 'Datum, an dem die Erklärung veröffentlicht wurde (Format JJJJ-MM-TT).',
        ],
        'updated_at' => [
            'title' => 'Datum der letzten Aktualisierung',
            'hint' => 'Datum der letzten Überprüfung der Erklärung (Format JJJJ-MM-TT); sie muss jedes Jahr überprüft werden.',
        ],
        'entity' => [
            'title' => 'Bezeichnung der Körperschaft',
            'hint' => 'Name der Körperschaft (zum Beispiel „Gemeinde …“), im Schlüssel name der Datei ente.php des Mandanten: die einzige Quelle der Daten der Körperschaft.',
        ],
        'officer' => [
            'title' => 'Verantwortliche Person für Barrierefreiheit',
            'hint' => 'Name und Funktion (oder Stelle) der für Barrierefreiheit und die Bearbeitung von Meldungen verantwortlichen Person, im Feld officer der Erklärung.',
        ],
        'feedback_email' => [
            'title' => 'Adresse für Meldungen',
            'hint' => 'Postfach der Gemeinde, das Meldungen zur Barrierefreiheit erhält: ohne es kann das Feedbackformular nichts senden.',
        ],
        'procedure_url' => [
            'title' => 'Link zum Durchsetzungsverfahren',
            'hint' => 'https-Adresse des Verfahrens nach Art. 3-quinquies des Gesetzes 4/2004.',
        ],
        'document' => [
            'title' => 'Zu ersetzende Beispielwerte',
            'hint' => 'Die Erklärung enthält noch Beispielwerte (example.it, xxxx, [[...]]).',
        ],
    ],
    'reason' => [
        'missing' => 'Angabe fehlt',
        'invalid' => 'ungültiger Wert',
        'placeholder' => 'Beispielwert',
    ],
    'form' => [
        'attributes' => ['problem' => 'Problembeschreibung', 'page' => 'betroffene Seite', 'contact' => 'E-Mail für die Antwort'],
        'title' => 'Ein Barrierefreiheitsproblem melden',
        'problem' => 'Beschreiben Sie das Problem',
        'problem_hint' => 'Was Sie nicht lesen oder tun können und mit welchem Hilfsmittel (zum Beispiel Screenreader oder nur Tastatur).',
        'page' => 'Betroffene Seite (optional)',
        'contact' => 'E-Mail für die Antwort (optional)',
        'contact_hint' => 'Ohne Kontakt können wir nicht antworten. Wir verwenden die Adresse nur, um auf diese Meldung zu antworten.',
        'submit' => 'Meldung senden',
        'sent' => 'Meldung gesendet. Danke: Die Gemeinde wird sie prüfen.',
        'throttled' => 'Sie haben in kurzer Zeit zu viele Meldungen gesendet. Bitte versuchen Sie es später erneut.',
        'unavailable' => 'Die Meldung kann nicht gesendet werden: Die Gemeinde hat das Postfach für Meldungen noch nicht angegeben.',
        'inactive' => 'Das Formular wird aktiv, sobald die Gemeinde das Postfach für Meldungen angibt.',
    ],
    'mail' => [
        'subject' => 'Meldung zur Barrierefreiheit von der Website',
        'intro' => 'Meldung zur Barrierefreiheit, eingegangen über das Formular der Erklärung zur Barrierefreiheit.',
        'problem' => 'Gemeldetes Problem',
        'page' => 'Betroffene Seite',
        'contact' => 'Kontakt für die Antwort',
        'none' => 'nicht angegeben',
        'procedure' => 'Gibt es innerhalb von 30 Tagen keine Antwort oder ist die Antwort nicht zufriedenstellend, kann die Person das Verfahren nach Art. 3-quinquies des Gesetzes 4/2004 einleiten.',
    ],
];
