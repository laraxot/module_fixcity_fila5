<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\DeleteAction;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;

class EditTicket extends XotBaseEditRecord
{
    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            'delete' => DeleteAction::make(),
        ];
    }
}
