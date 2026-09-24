<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/nb/passwords.php
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

    'reset' => 'Passordet ditt har blitt nullstilt!',
    'sent' => 'Vi har sendt din lenke for å nullstille passordet!',
    'throttled' => 'Vennligst vent før du prøver på nytt.',
    'token' => 'Denne koden for å nullstille passordet er ugyldig.',
    'user' => 'Vi kan ikke finne en bruker med den e-postadressen.',
];
