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
                    ->label('Per quale motivo stai modificando lo stato?')
                    ->helperText('La motivazione viene registrata nella cronologia. Gli aggiornamenti pubblici sono visibili al cittadino.')
                    ->required(),
            ])
            ->label('Change Status')
            ->icon('ui-status')
            ->tooltip('Change Status')
            ->modalHeading('Modifica Status')
            // ->requiresConfirmation()
            ->modalSubmitActionLabel('Modifica');
    }

    public static function getDefaultName(): ?string
    {
        return 'changeStatus';
    }
}
