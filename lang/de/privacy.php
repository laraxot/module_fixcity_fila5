<?php

declare(strict_types=1);

/*
 * Datenschutzerklärung. `sections` = Pflichtabschnitte nach Art. 13 DSGVO: der Titel ist die Überschrift, die der Text des Mandanten
 * (`lang/<Sprache>/policy.md`) haben muss (Prüfung: AssessPrivacyPolicyAction); `hint` sagt dem Verantwortlichen, was er schreiben muss.
 */
return [
    'page_title' => 'Datenschutzerklärung',
    'not_configured' => 'Die Datenschutzerklärung ist noch nicht verfügbar.',
    'sections' => [
        'controller' => [
            'title' => 'Verantwortlicher',
            'hint' => 'Name, Anschrift und Kontaktdaten der Gemeinde als Verantwortlicher (Art. 13 Abs. 1 Buchst. a).',
        ],
        'dpo' => [
            'title' => 'Datenschutzbeauftragter',
            'hint' => 'Kontaktdaten des von der Gemeinde benannten Datenschutzbeauftragten (Art. 13 Abs. 1 Buchst. b; Art. 37 Abs. 1 Buchst. a).',
        ],
        'purposes' => [
            'title' => 'Zwecke und Rechtsgrundlage der Verarbeitung',
            'hint' => 'Zwecke, für die die Daten der Meldungen verarbeitet werden, und die Rechtsgrundlage (Art. 13 Abs. 1 Buchst. c; Art. 6 Abs. 1).',
        ],
        'recipients' => [
            'title' => 'Empfänger der Daten',
            'hint' => 'Empfänger oder Kategorien von Empfängern, einschließlich der Dienstleister (Art. 13 Abs. 1 Buchst. e).',
        ],
        'transfers' => [
            'title' => 'Übermittlung an Drittländer',
            'hint' => 'Wenn Daten den Europäischen Wirtschaftsraum verlassen: Land und Garantien; andernfalls erklären, dass es keine Übermittlungen gibt (Art. 13 Abs. 1 Buchst. f).',
        ],
        'retention' => [
            'title' => 'Speicherdauer',
            'hint' => 'Wie lange Meldungen, Konten, Einwilligungen und Protokolle gespeichert werden, oder die Kriterien dafür (Art. 13 Abs. 2 Buchst. a).',
        ],
        'rights' => [
            'title' => 'Rechte der betroffenen Person',
            'hint' => 'Auskunft, Berichtigung, Löschung, Einschränkung, Widerspruch, Übertragbarkeit und, wenn die Rechtsgrundlage die Einwilligung ist, Widerruf; wie sie ausgeübt werden (Art. 13 Abs. 2 Buchst. b und c; Art. 15 bis 21).',
        ],
        'complaint' => [
            'title' => 'Beschwerde bei der Aufsichtsbehörde',
            'hint' => 'Recht auf Beschwerde bei der italienischen Datenschutzbehörde (Garante) (Art. 13 Abs. 2 Buchst. d).',
        ],
        'mandatory' => [
            'title' => 'Pflicht zur Bereitstellung der Daten',
            'hint' => 'Ob die Bereitstellung gesetzlich oder vertraglich vorgeschrieben oder für die Meldung erforderlich ist, und die Folgen der Nichtbereitstellung (Art. 13 Abs. 2 Buchst. e).',
        ],
        'automated' => [
            'title' => 'Automatisierte Entscheidungsfindung',
            'hint' => 'Ob es eine automatisierte Entscheidungsfindung einschließlich Profiling gibt: Logik und Folgen; andernfalls erklären, dass es sie nicht gibt (Art. 13 Abs. 2 Buchst. f).',
        ],
        'document' => [
            'title' => 'Im Text noch auszufüllende Felder',
            'hint' => 'Der Text enthält außerhalb der Pflichtabschnitte noch Platzhalter (zum Beispiel [[...]]).',
        ],
    ],
    'reason' => [
        'missing' => 'Abschnitt fehlt oder ist leer',
        'placeholder' => 'enthält noch auszufüllende Felder',
    ],
];
