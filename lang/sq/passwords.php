<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/sq/passwords.php
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

    'reset' => 'Fjalëkalimi u ndryshua!',
    'sent' => 'Adresa për ndryshimin e fjalëkalimit u dërgua!',
    'throttled' => 'Ju lutemi prisni para se të provoni përsëri.',
    'token' => 'Ky tallon për ndryshimin e fjalëkalimit është i pasaktë.',
    'user' => 'Nuk mund të gjejmë një përdorues me atë adresë email-i.',
];
