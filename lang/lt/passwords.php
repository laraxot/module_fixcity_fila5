<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/lt/passwords.php
return [
    /*
    |--------------------------------------------------------------------------
    | Slaptažodžio priminimo kalbos eilutės
    |--------------------------------------------------------------------------
    |
    | Sekančios kalbos eilutės yra numatytos elutės, atitinkančios priežastims,
    | pateikiamoms slatažodžių tarpininko, kai nepavyksta slaptažodžio atnaujinimo
    | bandymas, tokioms kaip negaliojanti žymė ar neteisingas naujas slaptažodis..
    |
    */

    'reset' => 'Nustatytas naujas slaptažodis!',
    'sent' => 'Naujo slaptažodžio nustatymo nuoroda išsiųsta',
    'throttled' => 'Palaukite prieš tęsdami.',
    'token' => 'Šis slaptažodžio raktas yra neteisingas.',
    'user' => 'Vartotojas su tokiu el. paštu nerastas.',
];
