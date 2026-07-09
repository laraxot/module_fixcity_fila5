<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/it/passwords.php
return [
    /*
    |--------------------------------------------------------------------------
    | Password Reminder Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are the default lines which match reasons
    | that are given by the password broker for a password update attempt
    | has failed, such as for an invalid token or invalid new password.
    |
    */

    'reset' => 'La password è stata reimpostata!',
    'sent' => 'Ti abbiamo inviato una email con il link per il reset della password!',
    'throttled' => 'Per favore, attendi prima di riprovare.',
    'token' => 'Questo token di reset della password non è valido.',
    'user' => 'Non riusciamo a trovare un utente con questo indirizzo email.',
];
