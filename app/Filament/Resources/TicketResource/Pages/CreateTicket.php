<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Modules\Fixcity\Actions\GetTicketFormDataForPersistAction;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

class CreateTicket extends XotBaseCreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return app(GetTicketFormDataForPersistAction::class)->execute($data);
    }
}
