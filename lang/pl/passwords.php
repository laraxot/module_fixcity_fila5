<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/pl/passwords.php
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

    'reset' => 'Hasło zostało zresetowane!',
    'sent' => 'Przypomnienie hasła zostało wysłane!',
    'throttled' => 'Proszę zaczekać zanim spróbujesz ponownie.',
    'token' => 'Token resetowania hasła jest nieprawidłowy.',
    'user' => 'Nie znaleziono użytkownika z takim adresem e-mail.',
];
