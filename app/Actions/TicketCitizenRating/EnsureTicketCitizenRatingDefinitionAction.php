<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\TicketCitizenRating;

use Modules\Rating\Models\Rating;
use Spatie\QueueableAction\QueueableAction;

/**
 * Definizione canonica Rating per valutazione cittadino su ticket (modulo Rating, non colonne su tickets).
 */
final class EnsureTicketCitizenRatingDefinitionAction
{
    use QueueableAction;

    public const SLUG = 'fixcity-ticket-citizen-satisfaction';

    public function execute(): Rating
    {
        /** @var Rating */
        return Rating::query()->firstOrCreate(
            ['slug' => self::SLUG],
            ['title' => 'Valutazione cittadino segnalazione'],
        );
    }
}
