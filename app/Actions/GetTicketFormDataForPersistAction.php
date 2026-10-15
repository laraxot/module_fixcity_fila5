<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;
use Spatie\QueueableAction\QueueableAction;

/**
 * Normalizza il payload raccolto dal form Filament prima della creazione del ticket nel backoffice.
 *
 * Chiamata dall'hook di `CreateTicket`, unico adapter del lifecycle XotBaseCreateRecord.
 * Il wizard frontoffice mantiene il proprio flusso di submit e non dipende dal Resource Filament.
 */
final class GetTicketFormDataForPersistAction
{
    use QueueableAction;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function execute(array $data): array
    {
        $data = app(NormalizeTicketLocationDataAction::class)->execute($data);
        $data = $this->reconcileWizardTypeField($data);

        if (! isset($data['owner_id']) && auth()->check()) {
            $data['owner_id'] = auth()->id();
        }

        if (! isset($data['status'])) {
            $data['status'] = TicketStatusEnum::PENDING->value;
        }

        return $data;
    }

    /**
     * Il wizard pubblico usa `type_id`; il modello e la create admin usano `type` (+ cast enum).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function reconcileWizardTypeField(array $data): array
    {
        if (\array_key_exists('type_id', $data) && ! \array_key_exists('type', $data)) {
            $data['type'] = $data['type_id'];
        }

        unset($data['type_id']);

        return $data;
    }
}
