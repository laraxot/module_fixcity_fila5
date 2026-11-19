<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/fi/passwords.php
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

    'reset' => 'Salasana on resetoitu!',
    'sent' => 'Resetointilinkki lähetetty sähköpostitse!',
    'throttled' => 'Odota, ennen kuin yrität uudelleen',
    'token' => 'Resetointitunniste on viallinen.',
    'user' => 'Sähköpostiosoitteella ei löydy käyttäjää.',
];
