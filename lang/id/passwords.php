<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/id/passwords.php
return [
    /*
    |---------------------------------------------------------------------------------------
    | Baris Bahasa untuk Pengingat Kata Sandi
    |---------------------------------------------------------------------------------------
    |
    | Baris bahasa berikut adalah baris standar yang cocok dengan alasan yang
    | diberikan oleh pembongkar kata sandi yang telah gagal dalam upaya pembaruan
    | kata sandi, misalnya token tidak valid atau kata sandi baru tidak valid.
    |
    */

    'reset' => 'Kata sandi Anda sudah direset!',
    'sent' => 'Kami sudah mengirim surel yang berisi tautan untuk mereset kata sandi Anda!',
    'throttled' => 'Harap tunggu sebelum mencoba lagi.',
    'token' => 'Token pengaturan ulang kata sandi tidak sah.',
    'user' => 'Kami tidak dapat menemukan pengguna dengan alamat surel tersebut.',
];
