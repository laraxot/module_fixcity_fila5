<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/tr/passwords.php
return [
    /*
    |--------------------------------------------------------------------------
    | Parola Sıfırlama Dil Satırları
    |--------------------------------------------------------------------------
    |
    | Aşağıdaki dil satırları, geçersiz kod veya geçersiz yeni parola gibi
    | bir parola güncelleme girişimi sırasında parola aracısı tarafından
    | verilen nedenlerle eşleşen, değiştirebileceğiniz, satırlardır.
    |
    */

    'reset' => 'Parolanız sıfırlandı!',
    'sent' => 'Parola sıfırlama bağlantınız e-posta ile gönderildi!',
    'throttled' => 'Tekrar denemeden önce lütfen bekleyin.',
    'token' => 'Parola sıfırlama kodu geçersiz.',
    'user' => 'Bu e-posta adresi ile kayıtlı bir üye bulunamadı.',
];
