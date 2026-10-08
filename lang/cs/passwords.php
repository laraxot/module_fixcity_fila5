<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/cs/passwords.php
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

    'reset' => 'Heslo bylo obnoveno!',
    'sent' => 'E-mail s instrukcemi k obnovení hesla byl odeslán!',
    'throttled' => 'Počkejte prosím a zkuste to znovu.',
    'token' => 'Klíč pro obnovu hesla je nesprávný.',
    'user' => 'Nepodařilo se najít uživatele s touto e-mailovou adresou.',
];
