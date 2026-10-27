<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT.
// File: lang/de/ticket_list.php
// Öffentliche Meldungsliste unter /{lang}/tickets (Block ticket-list/design-comuni, Story DCP-104). Die Formularbezeichnungen des Admin-Bereichs fallen auf `it` zurück.
return [
    'breadcrumb' => [
        'home' => 'Startseite',
        'current' => 'Liste der Meldungen',
    ],
    'heading' => [
        'title' => 'Liste der Meldungen',
        'subtitle' => '{0} In den letzten 12 Monaten wurde noch keine Meldung erledigt.|{1} In den letzten 12 Monaten wurde :count Meldung erledigt.|[2,*] In den letzten 12 Monaten wurden :count Meldungen erledigt.',
    ],
    'results' => [
        'count' => '{0} 0 Ergebnisse|{1} 1 Ergebnis|[2,*] :count Ergebnisse',
        'summary' => 'Meldungen :from bis :to von :total',
    ],
    'filters' => [
        'region' => 'Meldungen suchen und filtern',
        'search_label' => 'Meldungen durchsuchen',
        'search_help' => 'Titel, Beschreibung oder Adresse',
        'category' => 'kategorie',
        'status' => 'status',
        'period' => 'zeitraum',
        'sort' => 'sortieren nach',
        'more' => 'Status, Zeitraum und Sortierung',
        'apply' => 'Filter anwenden',
        'open' => 'Filtern',
        'reset' => 'Alle Filter entfernen',
        'modal_title' => 'Meldungen filtern',
        'close' => 'Dialogfenster schließen',
        'periods' => [
            'all' => 'Beliebiges Datum',
            '7d' => 'Letzte 7 Tage',
            '30d' => 'Letzte 30 Tage',
            '12m' => 'Letzte 12 Monate',
        ],
        'sorts' => [
            'recent' => 'Neueste zuerst',
            'oldest' => 'Älteste zuerst',
            'updated' => 'Zuletzt aktualisiert',
        ],
    ],
    'tabs' => [
        'label' => 'Ansicht der Ergebnisse',
        'map' => 'Karte',
        'list' => 'Liste',
    ],
    'map' => [
        'alt' => 'Karte der öffentlichen Meldungen',
        'noscript' => 'Die Karte benötigt JavaScript.',
        'noscript_link' => 'Zur Liste der Meldungen',
    ],
    'card' => [
        'type' => 'Art der Meldung',
        'status' => 'Status',
        'show_all' => 'Alles anzeigen',
        'address' => 'Adresse',
        'detail' => 'Details',
        'date' => 'Gemeldet am',
    ],
    'empty' => [
        'title' => 'Keine Meldungen gefunden.',
        'text' => 'Es gibt noch keine öffentlichen Meldungen: Sie erscheinen hier, sobald die Gemeinde sie bearbeitet.',
        'filtered' => 'Keine Meldung entspricht den gewählten Filtern. Ändern oder entfernen Sie die Filter.',
    ],
    'pagination' => [
        'label' => 'Seitennavigation der Liste',
        'previous' => 'Zurück',
        'next' => 'Weiter',
        'previous_label' => 'Vorherige Seite',
        'next_label' => 'Nächste Seite',
        'page_label' => 'Zu Seite :page',
        'current' => 'Seite :page von :last',
    ],
    'invite' => [
        'title' => 'Eine Meldung abgeben',
        'text' => 'Um eine Meldung hinzuzufügen, melden Sie sich mit Ihrem Konto an: Wir führen Sie Schritt für Schritt und Sie können den Stand verfolgen.',
        'button' => 'Missstand melden',
    ],
    'feedback' => [
        'title' => 'Wie verständlich sind die Informationen auf dieser Seite?',
        'legend' => 'Bewerten Sie die Seite mit 1 bis 5 Sternen',
        'star_label' => 'Bewerten Sie mit %d von 5 Sternen',
        'thanks' => 'Danke, Ihre Rückmeldung hilft uns, den Dienst zu verbessern!',
        'positive' => [
            'question' => 'Was hat Ihnen am besten gefallen?',
            'answers' => [
                'Die Hinweise waren klar',
                'Die Hinweise waren vollständig',
                'Ich wusste immer, dass ich richtig vorgehe',
                'Ich hatte keine technischen Probleme',
                'Sonstiges',
            ],
        ],
        'negative' => [
            'question' => 'Wo hatten Sie die größten Schwierigkeiten?',
            'answers' => [
                'Manchmal waren die Hinweise nicht klar',
                'Manchmal waren die Hinweise nicht vollständig',
                'Manchmal wusste ich nicht, ob ich richtig vorgehe',
                'Ich hatte technische Probleme',
                'Sonstiges',
            ],
        ],
        'detail' => [
            'question' => 'Möchten Sie weitere Details hinzufügen?',
            'label' => 'Details',
            'help' => 'Höchstens 200 Zeichen eingeben',
        ],
        'back' => 'Zurück',
        'next' => 'Weiter',
    ],
    'contacts' => [
        'title' => 'Brauchen Sie Hilfe?',
        'faq' => 'Häufige Fragen lesen',
        'track' => 'Meldung verfolgen',
        'account' => 'Ihre Meldungen ansehen',
        'phone' => 'Kostenlose Nummer :value anrufen',
    ],
];
