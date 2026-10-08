<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/sl/passwords.php
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

    'reset' => 'Geslo je bilo spremenjeno!',
    'sent' => 'Opomnik za geslo poslano!',
    'throttled' => 'Počakajte pred ponovnim poskusom.',
    'token' => 'Ponastavitveni žeton je neveljaven.',
    'user' => 'Ne moremo najti uporabnika s tem e-poštnim naslovom.',
];
