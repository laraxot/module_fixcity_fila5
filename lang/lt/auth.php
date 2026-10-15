<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: ≥5% comment lines on files >100 LOC.
// Canon: Modules/Fixcity/docs/wiki — domain i18n only.
// File: lang/lt/auth.php
return [
    /*
    |--------------------------------------------------------------------------
    | Prisijungimo vertimai
    |--------------------------------------------------------------------------
    |
    | Šios eilutės naudojamos vertimams, kurie
    | rodomi atliekant prisijungimo veiksmą.
    |
    */

    'failed' => 'Prisijungimo duomenys neatitinka.',
    'password' => 'Pateiktas slaptažodis yra neteisingas.',
    'throttle' => 'Per daug bandymų prisijungti. Bandykite po :seconds sec.',
];
