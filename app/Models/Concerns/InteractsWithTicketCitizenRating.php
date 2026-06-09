<?php

declare(strict_types=1);

namespace Modules\Fixcity\Models\Concerns;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Carbon;
use Modules\Fixcity\Actions\TicketCitizenRating\GetTicketCitizenRatingMorphAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Rating\Models\Contracts\HasRatingContract;
use Modules\Rating\Models\Traits\HasRating;

/**
 * Valutazione cittadino 1–5: persistenza su RatingMorph (modulo Rating), non colonne tickets.*.
 */
trait InteractsWithTicketCitizenRating
{
    use HasRating;

    /**
     * @return Attribute<?int, never>
     */
    protected function citizenRating(): Attribute
    {
        return Attribute::make(
            get: function (): ?int {
                $morph = app(GetTicketCitizenRatingMorphAction::class)->executeForTicket($this);
                if ($morph === null || $morph->value === null) {
                    return null;
                }

                return (int) $morph->value;
            },
        );
    }

    /**
     * @return Attribute<?Carbon, never>
     */
    protected function citizenRatedAt(): Attribute
    {
        return Attribute::make(
            get: function (): ?Carbon {
                $morph = app(GetTicketCitizenRatingMorphAction::class)->executeForTicket($this);

                return $morph?->created_at;
            },
        );
    }

    public function needsCitizenRatingPrompt(): bool
    {
        if ($this->citizen_rating !== null) {
            return false;
        }

        if (! auth()->check() || ! $this->isOwnedByAuthenticatedUser()) {
            return false;
        }

        $status = TicketStatusEnum::tryFrom($this->resolveTicketStatusValue());

        return $status === TicketStatusEnum::RESOLVED || $status === TicketStatusEnum::CLOSED;
    }
}
