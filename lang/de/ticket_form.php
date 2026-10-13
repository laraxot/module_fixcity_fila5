<?php

declare(strict_types=1);

return [
    'summaries' => [
        'images_none' => 'Keine Bilder angehängt',
        'images_choice' => '{1} Ein Bild angehängt|[2,*] :count Bilder angehängt',
    ],
    'fields' => [
        'location' => [
            'label' => 'Ort',
            'invalid' => 'Geben Sie einen gültigen Punkt auf der Karte an: Breitengrad zwischen -90 und 90, Längengrad zwischen -180 und 180.',
            'placeholder' => 'Ort suchen',
            'helper_text' => 'Geben Sie an, wo die Störung aufgetreten ist',
            'address' => [
                'label' => 'Adresse der Störung',
                'placeholder' => 'Adresse suchen',
                'helper_text' => 'Suchen Sie die Adresse oder wählen Sie den Punkt auf der Karte',
                'description' => 'Adresse der Störung',
            ],
            'description' => 'location',
        ],
        'type' => [
            'label' => 'Art der Störung',
            'placeholder' => 'Wählen Sie die Art der Störung',
            'helper_text' => 'Wählen Sie die Kategorie der Störung',
            'description' => 'Kategorie der Störung',
        ],
        'content' => [
            'label' => 'Details',
            'placeholder' => 'Beschreiben Sie das Problem',
            'helper_text' => 'Geben Sie höchstens 200 Zeichen ein',
            'description' => 'Beschreiben Sie die Störung klar und verständlich',
        ],
        'images' => [
            'label' => 'Bilder',
            'helper_text' => 'Wählen Sie ein oder mehrere Bilder aus, die der Meldung beigefügt werden sollen',
            'placeholder' => 'Bilder hinzufügen (optional)',
            'description' => 'Bilder zur Unterstützung der Meldung',
        ],
        'author' => [
            'show_more' => 'Alle anzeigen',
        ],
        'author_fiscal_code' => [
            'label' => 'Steuernummer',
            'description' => 'Steuernummer der meldenden Person',
            'helper_text' => 'Mit Ihrem Profil verknüpfte Angabe',
            'placeholder' => 'Steuernummer',
        ],
        'name' => [
            'label' => 'Name',
            'placeholder' => 'Geben Sie den Namen ein',
            'helper_text' => 'Geben Sie eine Bezeichnung ein',
            'description' => 'Bezeichnung der Meldung',
        ],
        'privacy' => [
            'description' => 'Zustimmung zur Datenschutzerklärung',
            'label' => 'Einwilligungen und Bedingungen',
            'helper_text' => 'Sie müssen die Datenschutzerklärung akzeptieren, um fortzufahren',
        ],
        'privacyAccepted' => [
            'label' => 'Ich habe die Datenschutzerklärung gelesen und verstanden',
            'placeholder' => '',
            'helper_text' => 'Erforderlich, um die Meldung zu senden',
            'description' => 'Allineato a Design Comuni segnalazione-01-privacy',
        ],
        'email' => [
            'label' => 'E-Mail',
            'placeholder' => 'Geben Sie Ihre E-Mail-Adresse ein',
            'helper_text' => 'Ihre E-Mail-Adresse, unter der wir Sie kontaktieren können',
            'description' => 'E-Mail-Adresse der meldenden Person',
        ],
        'review_images' => [
            'label' => 'Angehängte Bilder',
            'description' => 'Vorschau der der Meldung angehängten Bilder',
        ],
        'review_location' => [
            'label' => 'Standort',
            'description' => 'Angegebener Ort oder Koordinaten',
        ],
        'review_email' => [
            'label' => 'Kontakt-E-Mail',
            'description' => 'Vorschau der angegebenen E-Mail-Adresse',
        ],
        'review_content' => [
            'label' => 'Details der Meldung',
            'description' => 'Beschreibung der Störung',
        ],
        'review_name' => [
            'label' => 'Titel / Bezeichnung der Störung',
            'description' => 'Kurztitel, wie von der meldenden Person angegeben',
        ],
        'review_type' => [
            'label' => 'Art der Meldung',
            'description' => 'Vorschau der gewählten Kategorie',
        ],
        'review_priority' => [
            'label' => 'Priorität',
            'description' => 'Vorschau der gewählten Priorität',
        ],
        'review_author_name' => [
            'label' => 'Vor- und Nachname',
            'description' => 'Zusammenfassung der meldenden Person',
        ],
        'review_author_fiscal_code' => [
            'label' => 'Steuernummer',
            'description' => 'Zusammenfassung der Steuernummer der meldenden Person',
        ],
        'review_contact_phone' => [
            'label' => 'Telefon',
            'description' => 'Zusammenfassung der Telefonnummer',
        ],
        'review_contact_email' => [
            'label' => 'Kontakt-E-Mail',
            'description' => 'Zusammenfassung der E-Mail-Adresse',
        ],
        'author_phone' => [
            'label' => 'author_phone',
            'description' => 'author_phone',
            'helper_text' => 'author_phone',
            'placeholder' => 'author_phone',
        ],
        'author_name' => [
            'label' => 'author_name',
            'description' => 'author_name',
            'helper_text' => 'author_name',
            'placeholder' => 'author_name',
        ],
        'author_email' => [
            'label' => 'author_email',
            'description' => 'author_email',
            'helper_text' => 'author_email',
            'placeholder' => 'author_email',
        ],
        'priority' => [
            'description' => 'priority',
            'helper_text' => '',
            'label' => 'priority',
            'placeholder' => 'priority',
        ],
        'slug' => [
            'label' => 'slug',
            'placeholder' => 'slug',
            'helper_text' => '',
            'description' => 'slug',
        ],
        'type_legacy' => [
            'label' => 'type',
            'placeholder' => 'type',
            'helper_text' => '',
            'description' => 'type',
        ],
        'type_id' => [
            'description' => 'type_id',
            'helper_text' => '',
            'label' => 'type_id',
            'placeholder' => 'type_id',
        ],
        'gdpr_text' => [
            'label' => 'gdpr_text',
        ],
        'status' => [
            'label' => 'status',
            'placeholder' => 'status',
            'helper_text' => 'status',
            'description' => 'status',
        ],
    ],
    'sections' => [
        'Riepilogo' => [
            'heading' => 'Zusammenfassung',
            'label' => 'Zusammenfassung',
        ],
        'Autore della segnalazione' => [
            'heading' => 'Meldende Person',
            'label' => 'Meldende Person',
        ],
        'Luogo' => [
            'label' => 'Ort',
            'heading' => 'Ort',
        ],
        'Disservizio' => [
            'label' => 'Störung',
            'heading' => 'Störung',
        ],
        'empty' => [
            'heading' => '',
            'label' => '',
        ],
        'Contatti' => [
            'heading' => 'Kontakt',
        ],
        'location' => [
            'heading' => 'Ort',
            'description' => 'Geben Sie an, wo die Störung aufgetreten ist',
        ],
        'issue' => [
            'heading' => 'Störung',
            'description' => 'Beschreiben Sie das gemeldete Problem',
        ],
        'author' => [
            'heading' => 'Meldende Person',
            'description' => 'Angaben zu Ihrer Person',
        ],
        'Ort' => [
            'heading' => 'Ort',
            'label' => 'Ort',
        ],
        'Störung' => [
            'heading' => 'Störung',
            'label' => 'Störung',
        ],
        'Zusammenfassung' => [
            'heading' => 'Zusammenfassung',
            'label' => 'Zusammenfassung',
        ],
        'Meldende Person' => [
            'heading' => 'Meldende Person',
            'label' => 'Meldende Person',
        ],
        'Kontakt' => [
            'heading' => 'Kontakt',
            'label' => 'Kontakt',
        ],
    ],
    'steps' => [
        'privacy' => [
            'label' => 'Einwilligungen und Bedingungen',
            'tooltip' => 'Zustimmung zur Datenschutzerklärung',
            'description' => 'Lesen Sie die Datenschutzerklärung und stimmen Sie ihr zu',
            'color' => 'success',
            'icon' => 'heroicon-o-shield-check',
        ],
        'dati-di-segnalazione' => [
            'label' => 'Angaben zur Meldung',
            'tooltip' => 'Füllen Sie die Angaben zur Meldung aus',
            'description' => 'Geben Sie die Einzelheiten der Störung ein',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'riepilogo' => [
            'label' => 'Zusammenfassung',
            'tooltip' => 'Abschließende Zusammenfassung der Meldung',
            'description' => 'Prüfen Sie die Angaben vor dem Absenden',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'summary' => [
            'label' => 'Zusammenfassung',
            'tooltip' => 'Abschließende Zusammenfassung der Meldung',
            'description' => 'Prüfen Sie die Angaben vor dem Absenden',
            'color' => 'primary',
            'icon' => 'heroicon-o-check-circle',
        ],
        'data' => [
            'label' => 'Angaben zur Meldung',
            'tooltip' => 'Füllen Sie die Angaben zur Meldung aus',
            'description' => 'Geben Sie die Einzelheiten der Störung ein',
            'color' => 'primary',
            'icon' => 'heroicon-o-document-text',
        ],
        'Riepilogo' => [
            'label' => 'Zusammenfassung',
        ],
        'Dati di segnalazione' => [
            'label' => 'Angaben zur Meldung',
        ],
        'Autorizzazioni e condizioni' => [
            'label' => 'Einwilligungen und Bedingungen',
        ],
    ],
];
