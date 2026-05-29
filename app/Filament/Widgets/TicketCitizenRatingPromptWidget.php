<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Filament\Schemas\Components\Component;
use Illuminate\Validation\ValidationException;
use Modules\Fixcity\Actions\SubmitCitizenTicketRatingAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

/**
 * Prompt valutazione 1–5 dopo risoluzione ticket (STORY-043).
 */
class TicketCitizenRatingPromptWidget extends XotBaseWidget
{
    protected string $view = 'fixcity::filament.widgets.ticket-citizen-rating-prompt';

    public int $ticketId;

    public ?int $selectedRating = null;

    public bool $submitted = false;

    public bool $showPrompt = false;

    public function mount(int $ticketId): void
    {
        $this->ticketId = $ticketId;
        $this->refreshPromptState();
    }

    /**
     * @return array<string, Component>
     */
    #[\Override]
    public function getFormSchema(): array
    {
        return [];
    }

    public function submitRating(): void
    {
        $rating = $this->selectedRating;
        if ($rating === null) {
            throw ValidationException::withMessages([
                'selectedRating' => [__('fixcity::ticket_citizen_rating.validation.rating_required.label')],
            ]);
        }

        $ticket = Ticket::query()->find($this->ticketId);
        if (! $ticket instanceof Ticket) {
            return;
        }

        app(SubmitCitizenTicketRatingAction::class)->execute($ticket, $rating);

        $this->submitted = true;
        $this->showPrompt = false;
    }

    private function refreshPromptState(): void
    {
        $ticket = Ticket::query()->find($this->ticketId);
        if (! $ticket instanceof Ticket) {
            $this->showPrompt = false;

            return;
        }

        $this->showPrompt = $ticket->needsCitizenRatingPrompt();
        $this->submitted = $ticket->citizen_rating !== null && ! $this->showPrompt;
    }
}
