<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Modules\Fixcity\Actions\NormalizeTicketLocationDataAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
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
        $normalized = app(NormalizeTicketLocationDataAction::class)->execute($data);

        if (! isset($normalized['owner_id']) && auth()->check()) {
            $normalized['owner_id'] = auth()->id();
        }

        if (! isset($normalized['status'])) {
            $normalized['status'] = TicketStatusEnum::PENDING->value;
        }

        return $normalized;
    }
}
