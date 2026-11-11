<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetTicketFormDataForPersistAction;
use Modules\Fixcity\Enums\TicketPriorityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Resources\Pages\XotBaseCreateRecord;

class CreateTicket extends XotBaseCreateRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * `status` e `priority` sono campi Hidden dello schema condiviso con il wizard: dal browser si possono riscrivere. Come in
     * {@see EditTicket::WORKFLOW_FIELDS} non si accettano dal form: lo stato iniziale lo decide il server (PENDING, priorita' LOW, come il
     * wizard del cittadino), poi cambia solo con le azioni di workflow.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data = app(GetTicketFormDataForPersistAction::class)->execute(EditTicket::withoutWorkflowFields($data));
        $data['status'] = TicketStatusEnum::PENDING->value;
        $data['priority'] = TicketPriorityEnum::LOW->value;

        return $data;
    }

    /**
     * Stesso percorso del wizard (DB-10): codice di tracciamento, transazione, `TicketCreatedEvent` dopo il commit (ricevuta al
     * segnalante, avviso alla PA). Nessun flash della pagina di conferma: e' solo del cittadino.
     *
     * Gli allegati (relazioni del form, es. SpatieMediaLibraryFileUpload) si salvano DENTRO la transazione di CreateTicketAction, prima
     * dell'evento: se un upload fallisce non resta un ticket senza allegati con la ricevuta gia' partita (DB-06, revisione 2026-11-10).
     * CreateRecord::create() richiama poi saveRelationships(): a quel punto i file temporanei sono gia' stati salvati e lo stato del
     * campo contiene gli uuid dei media, quindi la seconda chiamata non crea duplicati.
     *
     * @param  array<string, mixed>  $data
     */
    protected function handleRecordCreation(array $data): Model
    {
        return app(CreateTicketAction::class)->execute(
            $data,
            null,
            function (Ticket $ticket): void {
                $this->form->model($ticket)->saveRelationships();
            },
            false,
        );
    }
}
