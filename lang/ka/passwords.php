<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/ka/passwords.php
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

    'reset' => 'თქვენი პაროლი განახლებულია!',
    'sent' => 'თქვენს ელ.ფოსტაზე მიიღებთ პაროლის განახლების ბმულს!',
    'throttled' => 'გთხოვთ, მოიცადოთ, სანამ ხელახლა ცდით.',
    'token' => 'პაროლის განახლების კოდი არასწორია.',
    'user' => 'მომხმარებელი ამ ელ.ფოსტით ვერ იქნა ნაპოვნი.',
];
