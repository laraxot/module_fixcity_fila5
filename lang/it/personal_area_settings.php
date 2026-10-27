<?php

declare(strict_types=1);

// Testi delle pagine area personale "Notifiche" e "Impostazioni e privacy" (DCP-111).
// Le viste del tema (components/blocks/personal-area-settings/*) non contengono testo: tutto arriva da qui.
// Le chiavi sono identiche in it, en, de, es (guardia: PersonalAreaSettingsLangParityTest).
return [
    'breadcrumb' => [
        'label' => 'Percorso di navigazione',
        'home' => 'Home',
    ],
    'notifications' => [
        'title' => 'Notifiche',
        'subtitle' => 'Gli aggiornamenti di stato che hai ricevuto sulle tue segnalazioni e su quelle che segui.',
        'list_title' => 'Aggiornamenti ricevuti',
        'count' => '{0} Nessuna notifica|{1} :count notifica|[2,*] :count notifiche',
        'unread' => '{0} nessuna da leggere|{1} :count da leggere|[2,*] :count da leggere',
        'item_text' => 'La segnalazione :code ha un nuovo stato: :status.',
        'followed_label' => '«:title»',
        'new_badge' => 'Nuova',
        'open_report' => 'Apri la segnalazione',
        'open_report_aria' => 'Apri la segnalazione :code',
        'mark_read' => 'Segna come letta',
        'mark_read_aria' => 'Segna come letta la notifica sulla segnalazione :code',
        'mark_all_read' => 'Segna tutte come lette',
        'marked_read' => 'Notifica segnata come letta.',
        'marked_all_read' => '{1} :count notifica segnata come letta.|[2,*] :count notifiche segnate come lette.',
        'has_more' => 'Sono mostrate le ultime :count notifiche.',
        'empty_title' => 'Non hai ancora ricevuto notifiche',
        'empty_text' => 'Quando cambia lo stato di una tua segnalazione, o di una che segui, trovi qui l’aggiornamento.',
        'my_reports' => 'Le mie segnalazioni',
        'settings_link' => 'Gestisci le preferenze di notifica',
        'settings_hint' => 'Qui vedi sempre tutti gli aggiornamenti. Per scegliere se riceverli anche via email apri le impostazioni.',
    ],
    'settings' => [
        'title' => 'Impostazioni e privacy',
        'subtitle' => 'Scegli come ricevere gli aggiornamenti, controlla i dati del tuo account e scopri come esercitare i tuoi diritti.',
        'nav_title' => 'In questa pagina',
        'sections' => [
            'preferences' => 'Notifiche delle segnalazioni',
            'account' => 'I tuoi dati',
            'rights' => 'I tuoi diritti sui dati',
            'deletion' => 'Eliminare l’account',
        ],
        'email_verified' => 'Il tuo indirizzo email è verificato: con l’opzione attiva ricevi un’email a ogni aggiornamento di stato.',
        'email_unverified' => 'Il tuo indirizzo email non è ancora verificato: finché non lo verifichi non ricevi email, anche con l’opzione attiva. Gli aggiornamenti restano sempre visibili nelle Notifiche.',
        'notifications_link' => 'Vedi le tue notifiche',
        'account' => [
            'intro' => 'Questi sono i dati del tuo account che il sistema conserva. Li vedi solo tu.',
            'card_title' => 'Dati dell’account',
            'fields' => [
                'name' => 'Nome',
                'email' => 'Email',
                'phone' => 'Telefono',
            ],
            'note' => 'Da questa pagina non puoi modificarli: per correggerli segui le indicazioni nella sezione «I tuoi diritti sui dati».',
        ],
        'deletion' => [
            'intro' => 'Puoi chiedere di eliminare il tuo account. Lo disattiviamo subito ed esci dal servizio: con questo account non potrai più accedere.',
            'keep' => 'I dati dell’account restano solo finché il Comune chiude le tue segnalazioni aperte, poi sono cancellati. Le segnalazioni, i commenti e le valutazioni restano nell’archivio del Comune, come descritto nell’informativa privacy.',
            'reason_label' => 'Motivo (facoltativo)',
            'confirm_label' => 'Ho capito: voglio eliminare il mio account e non potrò più accedere.',
            'submit' => 'Elimina il mio account',
        ],
        'rights' => [
            'intro' => 'Il Regolamento (UE) 2016/679 (GDPR) ti riconosce questi diritti sui tuoi dati personali.',
            'article' => 'Art. :article',
            'items' => [
                'access' => ['title' => 'Accesso', 'article' => '15', 'text' => 'Sapere se trattiamo i tuoi dati e ottenerne una copia.'],
                'rectification' => ['title' => 'Rettifica', 'article' => '16', 'text' => 'Far correggere i dati inesatti o completare quelli incompleti.'],
                'erasure' => ['title' => 'Cancellazione', 'article' => '17', 'text' => 'Chiedere di cancellare i tuoi dati nei casi previsti dalla legge.'],
                'restriction' => ['title' => 'Limitazione', 'article' => '18', 'text' => 'Chiedere di limitare il trattamento dei tuoi dati in alcuni casi, ad esempio mentre se ne verifica l’esattezza.'],
                'portability' => ['title' => 'Portabilità', 'article' => '20', 'text' => 'Ricevere in un formato strutturato i dati che ci hai fornito, nei casi previsti dalla legge.'],
                'objection' => ['title' => 'Opposizione', 'article' => '21', 'text' => 'Opporti al trattamento dei tuoi dati per motivi legati alla tua situazione particolare.'],
            ],
            'limits' => 'Alcuni diritti hanno condizioni e limiti previsti dal Regolamento: l’ente valuta ogni richiesta.',
            'how_title' => 'Come esercitarli',
            'how_text' => 'Rivolgiti al Comune, titolare del trattamento, usando i riferimenti nella sezione Contatti in fondo a questa pagina. Indica il diritto che vuoi esercitare e l’indirizzo email del tuo account.',
            'how_link' => 'Vai ai contatti del Comune',
            'how_today' => 'Puoi eliminare il tuo account in questa pagina, nella sezione «Eliminare l’account». Per scaricare i tuoi dati e per gli altri diritti la richiesta si invia all’ente.',
            'complaint_text' => 'Se ritieni che il trattamento non rispetti la normativa, puoi proporre reclamo al Garante per la protezione dei dati personali (art. 77).',
            'complaint_link' => 'Sito del Garante per la protezione dei dati personali',
            'complaint_url' => 'https://www.garanteprivacy.it/',
        ],
    ],
];
