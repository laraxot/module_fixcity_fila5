<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\Export;

use Spatie\QueueableAction\QueueableAction;

use function Safe\preg_match;

/**
 * Mitiga CSV formula injection per export Filament (FR-024).
 */
final class SanitizeSpreadsheetCellAction
{
    use QueueableAction;

    public function execute(string|int|float|null $value): string
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
