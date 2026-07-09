<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/el/passwords.php
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

    'reset' => 'Έχει γίνει επαναφορά του συνθηματικού!',
    'sent' => 'Η υπενθύμιση του συνθηματικού εστάλη!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'Το κλειδί αρχικοποίησης του συνθηματικού δεν είναι έγκυρο.',
    'user' => 'Δεν βρέθηκε χρήστης με το συγκεκριμένο email.',
];
