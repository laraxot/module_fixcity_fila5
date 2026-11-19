<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Widgets;

use Modules\Fixcity\Actions\BuildTicketTimelineAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

/**
 * Cronologia BO basata su TicketActivity (SSoT).
 * Non usa TimelineStep / timeline_attachments (seconda verità vietata dal blueprint).
 *
 * @property Ticket|null $record
 */
final class TimelineStepper extends XotBaseWidget
{
    protected string $view = 'fixcity::filament.widgets.timeline-stepper';

    protected static bool $isDiscovered = false;

    public ?Ticket $record = null;

    public function mount(?Ticket $record = null): void
    {
        $this->record = $record;
    }

    /**
     * @return array{steps: list<array{occurred_at: string, occurred_date: string, occurred_on: string, from_label: string|null, to_label: string|null, reason: string|null}>}
     */
    protected function getViewData(): array
    {
        if (! $this->record instanceof Ticket) {
            return ['steps' => []];
        }

        return [
            'steps' => app(BuildTicketTimelineAction::class)->execute(
                $this->record,
                auth()->user(),
            ),
        ];
    }
}
