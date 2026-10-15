<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/tr/auth.php
return [
    /*
    |--------------------------------------------------------------------------
    | Kimlik Doğrulama Dil Satırları
    |--------------------------------------------------------------------------
    |
    | Kimlik doğrulama sırasında kullanıcıya görüntülememiz gereken çeşitli
    | mesajlar için aşağıdaki dil satırları kullanılır. Bu dil satırlarını
    | uygulamanızın gereksinimlerine göre, kolayca, değiştirebilirsiniz.
    |
    */

    'failed' => 'Bu kimlik bilgileri kayıtlarımızla eşleşmiyor.',
    'throttle' => 'Çok fazla giriş denemesi. :seconds saniye sonra lütfen tekrar deneyin.',
];
