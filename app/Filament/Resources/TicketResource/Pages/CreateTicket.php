<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fixcity\Filament\Resources\TicketResource;

class CreateTicket extends CreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return TicketResource::prepareFormDataBeforePersist($data);
    }
}
