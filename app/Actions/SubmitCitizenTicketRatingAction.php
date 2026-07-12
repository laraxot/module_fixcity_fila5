<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Actions\TicketCitizenRating\EnsureTicketCitizenRatingDefinitionAction;
use Modules\Fixcity\Actions\TicketCitizenRating\GetTicketCitizenRatingMorphAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Rating\Models\RatingMorph;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Valutazione cittadino 1–5 su ticket risolto — persistenza RatingMorph (modulo Rating).
 */
final class SubmitCitizenTicketRatingAction
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
{
    use QueueableAction;

    public function execute(Ticket $ticket, int $rating): Ticket
    {
        if ($rating < 1 || $rating > 5) {
            throw ValidationException::withMessages([
                'rating' => [__('fixcity::ticket_citizen_rating.validation.rating_between.label')],
            ]);
        }

        $user = Auth::user();
        if (! $user instanceof UserContract) {
            throw ValidationException::withMessages([
                'auth' => [__('fixcity::ticket_citizen_rating.validation.auth_required.label')],
            ]);
        }

        $userId = $user->getKey();
        if ($userId === null) {
            throw ValidationException::withMessages([
                'auth' => [__('fixcity::ticket_citizen_rating.validation.auth_required.label')],
            ]);
        }

        $userIdString = SafeStringCastAction::cast($userId);

        if (app(GetTicketCitizenRatingMorphAction::class)->execute($ticket, $userIdString) !== null) {
            throw ValidationException::withMessages([
                'rating' => [__('fixcity::ticket_citizen_rating.validation.already_rated.label')],
            ]);
        }

        if (! $this->ticketAllowsCitizenRating($ticket)) {
            throw ValidationException::withMessages([
                'status' => [__('fixcity::ticket_citizen_rating.validation.status_not_resolved.label')],
            ]);
        }

        if (! $this->userOwnsTicket($ticket, $user)) {
            throw ValidationException::withMessages([
                'owner' => [__('fixcity::ticket_citizen_rating.validation.not_owner.label')],
            ]);
        }

        $definition = app(EnsureTicketCitizenRatingDefinitionAction::class)->execute();

        RatingMorph::query()->create([
            'rating_id' => $definition->id,
            'model_type' => $ticket->getMorphClass(),
            'model_id' => $ticket->getKey(),
            'user_id' => $userIdString,
            'value' => $rating,
        ]);

        return $ticket->refresh();
    }

    private function ticketAllowsCitizenRating(Ticket $ticket): bool
    {
        $statusValue = $this->resolveStatusValue($ticket);
        if ($statusValue === '') {
            return false;
        }

        $status = TicketStatusEnum::tryFrom($statusValue);

        return $status === TicketStatusEnum::RESOLVED || $status === TicketStatusEnum::CLOSED;
    }

    private function resolveStatusValue(Ticket $ticket): string
    {
        $currentStatus = $ticket->currentStatus();
        if (is_object($currentStatus) && isset($currentStatus->name) && is_string($currentStatus->name)) {
            return $currentStatus->name;
        }

        $raw = $ticket->getRawOriginal('status');

        return is_string($raw) ? $raw : '';
    }

    private function userOwnsTicket(Ticket $ticket, UserContract $user): bool
    {
        $userId = $user->getKey();
        if ($userId === null) {
            return false;
        }

        if ($ticket->owner_id !== null && SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($userId)) {
            return true;
        }

        return in_array(SafeStringCastAction::cast($userId), [
            SafeStringCastAction::cast($ticket->created_by),
            SafeStringCastAction::cast($ticket->updated_by),
        ], true);
    }
}
