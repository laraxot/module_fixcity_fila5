<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Contracts\UserContract;

/**
 * Valutazione cittadino 1–5 su ticket risolto (FR-021 / STORY-043).
 */
final class SubmitCitizenTicketRatingAction
{
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

        if ($ticket->citizen_rating !== null) {
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

        $ticket->citizen_rating = $rating;
        $ticket->citizen_rated_at = now();
        $ticket->save();

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

        if ($ticket->owner_id !== null && (string) $ticket->owner_id === (string) $userId) {
            return true;
        }

        return in_array((string) $userId, [(string) $ticket->created_by, (string) $ticket->updated_by], true);
    }
}
