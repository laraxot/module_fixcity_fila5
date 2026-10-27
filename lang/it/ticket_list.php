<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/ticket_list.php
//
// `fields`: etichette del form del blocco CMS `ticket_list`.
// Tutto il resto: testi dell'elenco pubblico /{lang}/tickets (blocco ticket-list/design-comuni, story DCP-104).
return [
    'fields' => [
        'title' => ['label' => 'title', 'placeholder' => 'title', 'helper_text' => 'title', 'description' => 'title'],
        'sub_title' => ['label' => 'sub_title', 'placeholder' => 'sub_title', 'helper_text' => 'sub_title', 'description' => 'sub_title'],
        'method' => ['label' => 'method', 'placeholder' => 'method', 'helper_text' => 'method', 'description' => 'method'],
        'limit' => ['label' => 'limit', 'placeholder' => 'limit', 'helper_text' => 'limit', 'description' => 'limit'],
        '_tpl' => ['label' => '_tpl'],
        'view' => ['label' => 'view', 'placeholder' => 'view', 'helper_text' => 'view', 'description' => 'view'],
    ],
    'breadcrumb' => [
        'home' => 'Home',
        'current' => 'Elenco segnalazioni',
    ],
    'heading' => [
        'title' => 'Elenco segnalazioni',
        'subtitle' => '{0} Nessuna segnalazione è stata ancora risolta negli ultimi 12 mesi.|{1} Negli ultimi 12 mesi è stata risolta :count segnalazione.|[2,*] Negli ultimi 12 mesi sono state risolte :count segnalazioni.',
    ],
    'results' => [
        'count' => '{0} 0 Risultati|{1} 1 Risultato|[2,*] :count Risultati',
        'summary' => 'Segnalazioni da :from a :to di :total',
    ],
    'filters' => [
        'region' => 'Cerca e filtra le segnalazioni',
        'search_label' => 'Cerca nelle segnalazioni',
        'search_help' => 'Titolo, descrizione o indirizzo',
        'category' => 'categoria',
        'status' => 'stato',
        'period' => 'periodo',
        'sort' => 'ordina per',
        'more' => 'Stato, periodo e ordinamento',
        'apply' => 'Applica filtri',
        'open' => 'Filtra',
        'reset' => 'Rimuovi tutti i filtri',
        'modal_title' => 'Filtra le segnalazioni',
        'close' => 'Chiudi finestra modale',
        'periods' => [
            'all' => 'Qualsiasi data',
            '7d' => 'Ultimi 7 giorni',
            '30d' => 'Ultimi 30 giorni',
            '12m' => 'Ultimi 12 mesi',
        ],
        'sorts' => [
            'recent' => 'Più recenti',
            'oldest' => 'Meno recenti',
            'updated' => 'Aggiornate di recente',
        ],
    ],
    'tabs' => [
        'label' => 'Vista dei risultati',
        'map' => 'Mappa',
        'list' => 'Elenco',
    ],
    'map' => [
        'alt' => 'Mappa delle segnalazioni pubbliche',
        'noscript' => 'La mappa richiede JavaScript.',
        'noscript_link' => 'Consulta l’elenco delle segnalazioni',
    ],
    'card' => [
        'type' => 'Tipologia di segnalazione',
        'status' => 'Stato',
        'show_all' => 'Mostra tutto',
        'address' => 'Indirizzo',
        'detail' => 'Dettaglio',
        'date' => 'Segnalata il',
    ],
    'empty' => [
        'title' => 'Nessuna segnalazione trovata.',
        'text' => 'Non ci sono ancora segnalazioni pubbliche: compariranno qui dopo la presa in carico da parte del Comune.',
        'filtered' => 'Nessuna segnalazione corrisponde ai filtri scelti. Prova a cambiarli o a rimuoverli.',
    ],
    'pagination' => [
        'label' => 'Paginazione dell’elenco',
        'previous' => 'Precedente',
        'next' => 'Successiva',
        'previous_label' => 'Pagina precedente',
        'next_label' => 'Pagina successiva',
        'page_label' => 'Vai a pagina :page',
        'current' => 'Pagina :page di :last',
    ],
    'invite' => [
        'title' => 'Fai una segnalazione',
        'text' => 'Per aggiungere una segnalazione accedi con il tuo account: ti guidiamo passo dopo passo e potrai seguirne lo stato.',
        'button' => 'Segnala disservizio',
    ],
    'feedback' => [
        'title' => 'Quanto sono chiare le informazioni su questa pagina?',
        'legend' => 'Valuta da 1 a 5 stelle la pagina',
        'star_label' => 'Valuta %d stelle su 5',
        'thanks' => 'Grazie, il tuo parere ci aiuterà a migliorare il servizio!',
        'positive' => [
            'question' => 'Quali sono stati gli aspetti che hai preferito?',
            'answers' => [
                'Le indicazioni erano chiare',
                'Le indicazioni erano complete',
                'Capivo sempre che stavo procedendo correttamente',
                'Non ho avuto problemi tecnici',
                'Altro',
            ],
        ],
        'negative' => [
            'question' => 'Dove hai incontrato le maggiori difficoltà?',
            'answers' => [
                'A volte le indicazioni non erano chiare',
                'A volte le indicazioni non erano complete',
                'A volte non capivo se stavo procedendo correttamente',
                'Ho avuto problemi tecnici',
                'Altro',
            ],
        ],
        'detail' => [
            'question' => 'Vuoi aggiungere altri dettagli?',
            'label' => 'Dettaglio',
            'help' => 'Inserire massimo 200 caratteri',
        ],
        'back' => 'Indietro',
        'next' => 'Avanti',
    ],
    'contacts' => [
        'title' => 'Hai bisogno di aiuto?',
        'faq' => 'Leggi le domande frequenti',
        'track' => 'Traccia una segnalazione',
        'account' => 'Consulta le tue segnalazioni',
        'phone' => 'Chiama il numero verde :value',
    ],
];
