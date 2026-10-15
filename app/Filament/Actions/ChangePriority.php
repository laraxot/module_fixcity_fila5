<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Modules\Fixcity\Actions\ChangeTicketPriorityAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Models\Ticket;
use Webmozart\Assert\Assert;

final class ChangePriority extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->translateLabel()
            ->label('Change Priority')
            ->icon('heroicon-o-flag')
            ->authorize('changePriority')
            ->schema([
                Select::make('priority')
                    ->options(TicketPriorityEnum::class)
                    ->required(),
            ])
            ->action(static function (Ticket $record, array $data): void {
                $priority = $data['priority'] instanceof TicketPriorityEnum
                    ? $data['priority']->value
                    : $data['priority'];
                Assert::string($priority);
                app(ChangeTicketPriorityAction::class)->execute($record, $priority);
            });
    }

    public static function getDefaultName(): string
    {
        return 'changePriority';
    }
}
