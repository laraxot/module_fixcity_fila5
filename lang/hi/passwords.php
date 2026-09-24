<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/hi/passwords.php
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

    'reset' => 'आपका पासवर्ड रीसेट कर दिया गया है!',
    'sent' => 'हमने आपको एक पासवर्ड रीसेट लिंक ई-मेल किया है!',
    'throttled' => 'Please wait before retrying.',
    'token' => 'यह पासवर्ड रीसेट टोकन अमान्य है।',
    'user' => 'हमें उस ई-मेल पते के साथ एक उपयोगकर्ता नहीं मिल सकता है।',
];
