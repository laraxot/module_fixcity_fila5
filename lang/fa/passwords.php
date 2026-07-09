<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/fa/passwords.php
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

    'reset' => 'رمز عبور شما بازگردانی شد!',
    'sent' => 'لینک بازگردانی رمز عبور به ایمیل شما ارسال شد.',
    'throttled' => 'پیش از تلاش مجدد کمی صبر کنید.',
    'token' => 'مشخصه‌ی بازگردانی رمز عبور معتبر نیست.',
    'user' => 'ما کاربری با این نشانی ایمیل نداریم!',
];
