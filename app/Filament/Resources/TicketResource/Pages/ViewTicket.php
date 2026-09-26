<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Filament\Actions\ChangeStatus;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\User\Models\User;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;
use Webmozart\Assert\Assert;

class ViewTicket extends XotBaseViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            'assign' => Action::make('assign')
                ->label(__('fixcity::ticket.actions.assign.label'))
                ->icon('heroicon-o-user-plus')
                ->schema([
                    Select::make('responsible_id')
                        ->label(__('fixcity::ticket.fields.responsible_id.label'))
                        ->options(static fn (): array => User::query()
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->map(static fn (mixed $name): string => is_scalar($name) ? (string) $name : '')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->default(fn (): ?int => $this->record instanceof Ticket && $this->record->responsible_id !== null
                            ? $this->record->responsible_id
                            : null),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    $responsibleId = $data['responsible_id'] ?? null;
                    if (is_string($responsibleId) && is_numeric($responsibleId)) {
                        $responsibleId = (int) $responsibleId;
                    }
                    Assert::nullOrInteger($responsibleId);
                    app(AssignTicketAction::class)->execute($this->record, $responsibleId);
                }),
            'changeStatus' => ChangeStatus::make(),
            'edit' => EditAction::make(),
        ];
    }
}
