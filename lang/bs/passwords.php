<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/bs/passwords.php
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

    'password' => 'Lozinka mora imati osam znakova i slagati se sa potvrdnom lozinkom.',
    'reset' => 'Lozinka je resetovana!',
    'sent' => 'Poslan vam je e-mail za povrat lozinke!',
    'throttled' => 'Molimo sačekajte prije ponovnog pokušaja.',
    'token' => 'Ovaj token za resetovanje lozinke nije ispravan.',
    'user' => 'Ne može se pronaći korisnik sa tom e-mail adresom.',
];
