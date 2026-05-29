<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions\TicketCitizenRating;

use Modules\Rating\Models\Rating;

/**
 * Definizione canonica Rating per valutazione cittadino su ticket (modulo Rating, non colonne su tickets).
 */
final class EnsureTicketCitizenRatingDefinitionAction
{
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
