<?php

declare(strict_types=1);

/*
 * Informativa privacy. `sections` = sezioni obbligatorie dell'art. 13 GDPR: il titolo e' l'intestazione che il testo del tenant
 * (`lang/<lingua>/policy.md`) deve avere (verifica: AssessPrivacyPolicyAction); `hint` dice al titolare che cosa scrivere.
 */
return [
    'page_title' => 'Informativa privacy',
    'not_configured' => 'L’informativa privacy non è ancora disponibile.',
    'sections' => [
        'controller' => [
            'title' => 'Titolare del trattamento',
            'hint' => 'Denominazione, sede e recapiti del Comune titolare del trattamento (art. 13 par. 1 lett. a).',
        ],
        'dpo' => [
            'title' => 'Responsabile della protezione dei dati',
            'hint' => 'Recapiti del responsabile della protezione dei dati (DPO) designato dal Comune (art. 13 par. 1 lett. b; art. 37 par. 1 lett. a).',
        ],
        'purposes' => [
            'title' => 'Finalità e base giuridica del trattamento',
            'hint' => 'Finalità per cui si trattano i dati delle segnalazioni e relativa base giuridica (art. 13 par. 1 lett. c; art. 6 par. 1).',
        ],
        'recipients' => [
            'title' => 'Destinatari dei dati',
            'hint' => 'Destinatari o categorie di destinatari, compresi i fornitori del servizio (art. 13 par. 1 lett. e).',
        ],
        'transfers' => [
            'title' => 'Trasferimenti verso paesi terzi',
            'hint' => 'Se i dati escono dallo Spazio economico europeo: paese e garanzie; altrimenti dichiarare che non ci sono trasferimenti (art. 13 par. 1 lett. f).',
        ],
        'retention' => [
            'title' => 'Periodo di conservazione',
            'hint' => 'Per quanto tempo si conservano segnalazioni, account, consensi e registri, o i criteri per stabilirlo (art. 13 par. 2 lett. a).',
        ],
        'rights' => [
            'title' => 'Diritti dell’interessato',
            'hint' => 'Accesso, rettifica, cancellazione, limitazione, opposizione, portabilità e, se la base giuridica è il consenso, revoca; come esercitarli (art. 13 par. 2 lett. b e c; artt. 15-21).',
        ],
        'complaint' => [
            'title' => 'Reclamo all’autorità di controllo',
            'hint' => 'Diritto di proporre reclamo al Garante per la protezione dei dati personali (art. 13 par. 2 lett. d).',
        ],
        'mandatory' => [
            'title' => 'Natura del conferimento dei dati',
            'hint' => 'Se il conferimento è obbligatorio per legge o per contratto o necessario per la segnalazione, e le conseguenze del rifiuto (art. 13 par. 2 lett. e).',
        ],
        'automated' => [
            'title' => 'Processi decisionali automatizzati',
            'hint' => 'Se esiste un processo decisionale automatizzato, compresa la profilazione: logica e conseguenze; altrimenti dichiarare che non esiste (art. 13 par. 2 lett. f).',
        ],
        'document' => [
            'title' => 'Campi ancora da compilare nel testo',
            'hint' => 'Il testo contiene ancora segnaposto (per esempio [[...]]) fuori dalle sezioni obbligatorie.',
        ],
    ],
    'reason' => [
        'missing' => 'sezione assente o vuota',
        'placeholder' => 'contiene ancora campi da compilare',
    ],
];
