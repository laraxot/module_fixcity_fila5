<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Filament\Actions\ChangePriority;
use Modules\Fixcity\Filament\Actions\ChangeStatus;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;
use Webmozart\Assert\Assert;

class ViewTicket extends XotBaseViewRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            'assign' => Action::make('assign')
                ->authorize('assign')
                ->label(__('fixcity::ticket.actions.assign.label'))
                ->icon('heroicon-o-user-plus')
                ->schema([
                    Select::make('responsible_id')
                        ->label(__('fixcity::ticket.fields.responsible_id.label'))
                        ->options(static function (): array {
                            $userClass = XotData::make()->getUserClass();

                            return $userClass::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->map(static fn (mixed $name): string => is_scalar($name) ? (string) $name : '')
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->default(function (): ?string {
                            if (! $this->record instanceof Ticket || $this->record->responsible_id === null) {
                                return null;
                            }

                            return is_scalar($this->record->responsible_id)
                                ? (string) $this->record->responsible_id
                                : null;
                        }),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    $responsibleId = $data['responsible_id'] ?? null;
                    if ($responsibleId !== null && ! is_string($responsibleId)) {
                        $responsibleId = SafeStringCastAction::cast($responsibleId);
                    }
                    Assert::nullOrString($responsibleId);
                    app(AssignTicketAction::class)->execute($this->record, $responsibleId);
                }),
            'changeStatus' => ChangeStatus::make(),
            'changePriority' => ChangePriority::make(),
            'edit' => EditAction::make(),
            'delete' => DeleteAction::make()
                ->authorize('delete')
                ->action(function (): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    app(DeleteTicketAction::class)->execute($this->record);
                }),
        ];
    }
}
