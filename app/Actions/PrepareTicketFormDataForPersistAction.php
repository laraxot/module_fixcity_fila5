<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Modules\Fixcity\Enums\TicketStatusEnum;

/**
 * Trasforma lo stato Filament/schema (admin o wizard frontoffice) nel payload sicuro per `Ticket::create` / Creazione Filament Resource.
 *
 * Usata solo da {@see \Modules\Fixcity\Filament\Resources\TicketResource::prepareFormDataBeforePersist()}
 * (pagina Filament crea ticket). Il widget wizard frontoffice **non** passa da questa Action.
 */
final class PrepareTicketFormDataForPersistAction
{
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
