<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/lv/passwords.php
return [
    /*
    |--------------------------------------------------------------------------
    | Password Reset Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'Jūsu parole ir atjaunināta!',
    'sent' => 'Mēs nosūtījām paroles maiņas linku uz jūsu e-pastu!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'Šāda zīme pie paroles maiņas nav atļauta.',
    'user' => 'Mēs nevaram atrast lietotāju ar tādu e-pasta adresi.',
];
