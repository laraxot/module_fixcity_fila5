<?php

declare(strict_types=1);

/*
 * Dati dell'ente titolare di questo tenant (DCP-289): UNICA sorgente di nome, regione, recapiti, DPO, social e orari.
 *
 * Tutte le chiavi sono VUOTE: le compila l'ente. Dove un dato manca il front office nasconde il blocco che lo mostrerebbe
 * (barra alta, colonna Contatti del footer, "Seguici su", numero verde, link ai diritti sui dati): mai un segnaposto.
 * Non inventare valori e non copiare quelli del sito di riferimento Design Comuni.
 *
 * Letto da Modules\Tenant\Actions\Ente\GetEnteDataAction, che normalizza e scarta cio' che non e' valido:
 *  - telefono, numero verde: solo cifre (con `+` iniziale), 3-15 cifre; spazi, punti, trattini e parentesi sono ammessi;
 *  - email, pec, dpo_email: un indirizzo semplice, senza `?`, `&`, `%`;
 *  - region_url e url dei social: solo http o https;
 *  - social: elenco di ['network' => rete, 'url' => indirizzo, 'label' => testo facoltativo]; reti ammesse: facebook, instagram,
 *    linkedin, mastodon, telegram, tiktok, twitter, whatsapp, youtube, rss;
 *  - ogni testo puo' essere una stringa oppure una mappa per lingua: ['it' => '...', 'en' => '...'].
 *
 * `hours` e `address` possono andare su piu' righe (a capo con \n).
 */
return [
    // Denominazione dell'ente, per esempio "Comune di ...".
    'name' => '',
    // Barra alta del sito: nome e indirizzo del portale della regione. Senza nome la barra mostra solo lingua e accesso.
    'region_name' => '',
    'region_url' => '',
    // Colonna "Contatti" del footer.
    'address' => '',
    'hours' => '',
    'phone' => '',
    'email' => '',
    'pec' => '',
    'tax_code' => '',
    'vat' => '',
    // Numero verde: riquadro "Hai bisogno di aiuto?" / "Contatta il comune".
    'toll_free' => '',
    // Responsabile della protezione dei dati: esercizio dei diritti GDPR (Impostazioni dell'area personale).
    'dpo_name' => '',
    'dpo_email' => '',
    // "Seguici su" in intestazione e nel footer.
    'social' => [],
];
