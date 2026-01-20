<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Modules\Fixcity\Filament\Resources\TicketResource;

class EditTicket extends EditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
