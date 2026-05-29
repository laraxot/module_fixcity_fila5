<?php

declare(strict_types=1);

namespace Modules\Fixcity\Support;

/**
 * Mitiga CSV formula injection per export Filament (FR-024).
 */
final class SpreadsheetCellSanitizer
{
    public static function sanitize(string|int|float|null $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        $string = (string) $value;

        if (preg_match('/^[=+\-@\t\r]/', $string) === 1) {
            return "'".$string;
        }

        return $string;
    }
}
