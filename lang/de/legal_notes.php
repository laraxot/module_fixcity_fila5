<?php

declare(strict_types=1);

/*
 * Note legali (disclaimer e condizioni di utilizzo, DCP-162). `sections` = sezioni del modello: il titolo e' l'intestazione che il testo del tenant
 * (`lang/<lingua>/legal-notes.md`) deve avere (verifica: AssessPrivacyPolicyAction con documento `legal_notes`); `hint` dice all'ente che cosa scrivere.
 */
return [
    'page_title' => 'Rechtliche Hinweise',
    'sections' => [
        'owner' => [
            'title' => 'Inhaber und Betreiber der Website',
            'hint' => 'Name, Sitz, Steuernummer oder USt-IdNr., zertifizierte E-Mail (PEC) und Kontakt der betreibenden Stelle; Verweis auf das Bürgerbüro und auf die Beauftragte oder den Beauftragten für den digitalen Wandel.',
        ],
        'purpose' => [
            'title' => 'Zweck des Dienstes',
            'hint' => 'Wofür der Dienst gedacht ist und was er nicht ist, insbesondere dass er kein Notrufkanal ist.',
        ],
        'use' => [
            'title' => 'Nutzungsbedingungen',
            'hint' => 'Wie der Dienst genutzt werden darf und was verboten ist: rechtswidrige Nutzung, falsche Meldungen, Missbrauch.',
        ],
        'contributions' => [
            'title' => 'Meldungen und von Nutzern gesendete Inhalte',
            'hint' => 'Was mit gesendeten Texten und Fotos geschieht: Veröffentlichung, Moderation, Verbot von Daten Dritter, der Gemeinde eingeräumte Lizenz.',
        ],
        'liability' => [
            'title' => 'Haftungsbeschränkung und Gewährleistungsausschluss',
            'hint' => 'Ausschlüsse und Grenzen der Haftung der Gemeinde für Inhalte, Verfügbarkeit, Fristen und Ergebnis; mit dem Rechtsberater der Gemeinde im Rahmen des geltenden Rechts festzulegen.',
        ],
        'links' => [
            'title' => 'Links zu Websites und Diensten Dritter',
            'hint' => 'Verantwortung für externe Links und für Dienste Dritter, die die Website nutzt (Karten und Adresssuche).',
        ],
        'ip' => [
            'title' => 'Geistiges Eigentum, Inhaltslizenz und Weiterverwendung',
            'hint' => 'Die Inhaltslizenz anstelle des Platzhalters angeben: Das Modell Design Comuni (Anforderung C.SI.3.4) verlangt den Abschnitt „Licenza dei contenuti“ mit dem Wortlaut „open by default“ (CAD Artikel 52) und CC-BY 4.0. Der Rest des Abschnitts ist bereits geschrieben.',
        ],
        'privacy' => [
            'title' => 'Personenbezogene Daten, Cookies und Barrierefreiheit',
            'hint' => 'Verweise auf die Dokumente zu diesen Themen; deren Inhalt hier nicht wiederholen.',
        ],
        'changes' => [
            'title' => 'Änderungen dieser Bedingungen',
            'hint' => 'Wie und wann sich die Bedingungen ändern und wie Nutzer informiert werden; Datum der letzten Aktualisierung.',
        ],
        'law' => [
            'title' => 'Anwendbares Recht und Gerichtsstand',
            'hint' => 'Den von der Gemeinde gewählten Gerichtsstand angeben oder bestätigen, dass die gesetzlichen Regeln gelten. Eine Abweichung vom Gerichtsstand gilt nur, wenn sie ausdrücklich schriftlich genehmigt wurde (Artikel 1341 Absatz 2 des italienischen Zivilgesetzbuchs): mit dem Rechtsberater zu prüfen.',
        ],
        'contacts' => [
            'title' => 'Kontakt zur Website',
            'hint' => 'E-Mail- oder PEC-Adresse, die Löschungsanträge und Meldungen zur Website empfängt: Das Amt muss den Eingang bestätigen und die Entscheidung mitteilen können.',
        ],
    ],
];
