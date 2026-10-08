<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/hu/passwords.php
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

    'reset' => 'Az új jelszó beállítva!',
    'sent' => 'Jelszó-emlékeztető elküldve!',
    'throttled' => 'Kérjük várjon, mielőtt újra megpróbálná.',
    'token' => 'Ez az új jelszó generálásához tartozó token érvénytelen.',
    'user' => 'Nem található felhasználó a megadott email címmel.',
];
