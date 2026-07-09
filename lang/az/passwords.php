<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/az/passwords.php
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

    'reset' => 'Şifrə yeniləndi',
    'sent' => 'Şifrə yeniləmə adresi sizə email olaraq göndərildi',
    'throttled' => 'Please wait before retrying.',
    'token' => 'Bu şifrə yeniləmə kodu yanlışdır',
    'user' => "Bu email'ə uyğun istifadəçi tapılmadı",
];
