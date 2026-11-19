<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/ro/passwords.php
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

    'reset' => 'Parola a fost resetată!',
    'sent' => 'Am trimis un e-mail cu link-ul de resetare a parolei!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'Codul de resetare a parolei este greșit.',
    'user' => 'Nu există niciun utilizator cu această adresă de e-mail.',
];
