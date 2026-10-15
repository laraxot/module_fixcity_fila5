<?php

declare(strict_types=1);

/*
 * Note legali (disclaimer e condizioni di utilizzo, DCP-162). `sections` = sezioni del modello: il titolo e' l'intestazione che il testo del tenant
 * (`lang/<lingua>/legal-notes.md`) deve avere (verifica: AssessPrivacyPolicyAction con documento `legal_notes`); `hint` dice all'ente che cosa scrivere.
 */
return [
    'page_title' => 'Note legali',
    'sections' => [
        'owner' => [
            'title' => 'Titolare e gestore del sito',
            'hint' => 'Denominazione, sede, codice fiscale o partita IVA, PEC e recapiti dell\'ente che gestisce il servizio; riferimento all\'URP e al responsabile per la transizione digitale (CAD art. 17).',
        ],
        'purpose' => [
            'title' => 'Finalità del servizio',
            'hint' => 'A che cosa serve il servizio e che cosa non è, in particolare che non è un canale di emergenza.',
        ],
        'use' => [
            'title' => 'Condizioni di utilizzo',
            'hint' => 'Come si può usare il servizio e che cosa è vietato: usi illeciti, segnalazioni false, abuso del servizio.',
        ],
        'contributions' => [
            'title' => 'Segnalazioni e contenuti inviati dagli utenti',
            'hint' => 'Che cosa succede ai testi e alle foto inviati: pubblicazione, moderazione, divieto di dati di terzi, licenza concessa all\'ente.',
        ],
        'liability' => [
            'title' => 'Limitazione di responsabilità e assenza di garanzie',
            'hint' => 'Esclusioni e limiti di responsabilità dell\'ente per contenuti, disponibilità del servizio, tempi ed esiti; da definire con il legale dell\'ente nei limiti della normativa vigente.',
        ],
        'links' => [
            'title' => 'Collegamenti a siti e servizi di terzi',
            'hint' => 'Responsabilità per i link esterni e per i servizi di terzi usati dal sito (mappe e ricerca degli indirizzi).',
        ],
        'ip' => [
            'title' => 'Proprietà intellettuale, licenza dei contenuti e riuso',
            'hint' => 'Diritti sui testi, immagini e dati del sito, licenza di riuso, marchi e software di terzi con le relative attribuzioni.',
        ],
        'privacy' => [
            'title' => 'Dati personali, cookie e accessibilità',
            'hint' => 'Rimandi ai documenti che trattano questi temi; non ripetere qui il loro contenuto.',
        ],
        'changes' => [
            'title' => 'Modifiche alle condizioni',
            'hint' => 'Come e quando le condizioni cambiano e come l\'utente ne viene informato; data dell\'ultimo aggiornamento.',
        ],
        'law' => [
            'title' => 'Legge applicabile e foro competente',
            'hint' => 'Legge applicabile e giurisdizione o foro competente per le controversie; da indicare dall\'ente.',
        ],
        'contacts' => [
            'title' => 'Contatti per segnalazioni sul sito',
            'hint' => 'A chi scrivere per chiedere la rimozione di un contenuto, segnalare una violazione di diritti o un problema del sito.',
        ],
    ],
];
