<?php

declare(strict_types=1);

// Fixcity translations — LangServiceProvider SSoT (never ->label() in Filament PHP).
// claude-audit static: split from lang.php for maintainability (<500 LOC).
// Canon: Modules/Fixcity/docs/wiki/concepts/claude-audit-static.md
// File: lang/nl/lang_loader.php
return merge_translation_files(__DIR__.'/lang_part01.php', __DIR__.'/lang_part02.php', __DIR__.'/lang_part03.php', __DIR__.'/lang_part04.php', __DIR__.'/lang_part05.php', __DIR__.'/lang_part06.php', __DIR__.'/lang_part07.php', __DIR__.'/lang_part08.php', __DIR__.'/lang_part09.php', __DIR__.'/lang_part10.php'
);
