<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Modules\Fixcity\Actions\ChangeStatus as ActionChangeStatus;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Webmozart\Assert\Assert;

class ChangeStatus extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        // Etichette da lang (filament-blueprint Fixcity): niente stringhe hardcoded —
        // l'operatore registra motivo e stato nella cronologia visibile al cittadino.
        $this->translateLabel()
            ->authorize('changeStatus')
            ->action(
                static function (Ticket $record, array $data): void {
                    $status = $data['status'] instanceof TicketStatusEnum
                        ? $data['status']->value
                        : $data['status'];
                    Assert::string($status);
                    Assert::string($data['reason']);

                    app(ActionChangeStatus::class)->execute($record, $status, $data['reason']);
                }
            )
            ->schema([
                Select::make('status')
                    ->options(TicketStatusEnum::class)
                    ->required(),
                TextInput::make('reason')
                    ->label(__('fixcity::ticket.actions.changeStatus.reason_label'))
                    ->helperText(__('fixcity::ticket.actions.changeStatus.reason_help'))
                    ->required(),
            ])
            ->icon('ui-status')
            ->tooltip(__('fixcity::ticket.actions.changeStatus.tooltip'))
            ->modalHeading(__('fixcity::ticket.actions.changeStatus.modal_heading'))
            ->modalSubmitActionLabel(__('fixcity::ticket.actions.changeStatus.modal_submit'));
    }

    public static function getDefaultName(): ?string
    {
        return 'changeStatus';
    }
}
