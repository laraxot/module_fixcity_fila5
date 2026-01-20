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
            ->action(
                function (Ticket $record, array $data): void {

                    Assert::string($data['status']);
                    Assert::string($data['reason']);

                    app(ActionChangeStatus::class)->execute($record, $data['status'], $data['reason']);
                }
            )
            ->schema([
                Select::make('status')
                    ->options(TicketStatusEnum::class)
                    ->required(),
                TextInput::make('reason')
                    ->label('Per quale motivo stai modificando lo stato?')
                    ->helperText('La motivazione verrà visualizzata nel dettaglio del ticket')
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
