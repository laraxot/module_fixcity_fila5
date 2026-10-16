<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/ca/passwords.php
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

    'reset' => 'La contrasenya s\'ha restablert!',
    'sent' => 'Li hem enviat un correu electrònic amb un enllaç per a reiniciar la teva contrasenya!',
    'throttled' => 'Si us plau, esperi abans de tornar-ho a intentar.',
    'token' => 'Aquest token de recuperació de contrasenya és invàlid.',
    'user' => 'No existeix cap usuari amb aquest correu.',
];
