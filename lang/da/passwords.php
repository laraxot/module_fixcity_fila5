<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/da/passwords.php
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

    'reset' => 'Adgangskoden er blevet nulstillet!',
    'sent' => 'Vi har sendt dig en e-mail til at nulstille din adgangskode!',
    'throttled' => 'Vent venligst inden du prøver igen.',
    'token' => 'Koden til nulstilling af adgangskoden er ugyldig.',
    'user' => 'Vi kan ikke finde en bruger med den e-mailadresse.',
];
