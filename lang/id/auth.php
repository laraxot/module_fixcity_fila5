<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/id/auth.php
return [
    /*
    |--------------------------------------------------------------------------
    | Baris-baris bahasa untuk autentifikasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan selama proses autentifikasi untuk beberapa
    | pesan yang perlu kita tampilkan ke pengguna. Anda bebas untuk memodifikasi
    | baris bahasa sesuai dengan keperluan aplikasi anda.
    |
    */

    'failed' => 'Identitas tersebut tidak cocok dengan data kami.',
    'throttle' => 'Terlalu banyak upaya masuk. Silahkan coba lagi dalam :seconds detik.',
];
