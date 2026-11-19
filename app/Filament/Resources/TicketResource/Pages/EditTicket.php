<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Filament\Resources\Pages\XotBaseEditRecord;
use Webmozart\Assert\Assert;

class EditTicket extends XotBaseEditRecord
{
    /**
     * Campi che la modifica libera non può toccare: cambiano solo con le azioni di workflow
     * (Approva, Scarta, Cambia stato, Risolvi, Chiudi), che rispettano le transizioni consentite,
     * scrivono la cronologia e notificano il cittadino.
     *
     * @var list<string>
     */
    public const array WORKFLOW_FIELDS = ['status', 'priority'];

    protected static string $resource = TicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Stessa cancellazione della pagina di dettaglio: DeleteTicketAction imposta deleted_by e usa una transazione.
            'delete' => DeleteAction::make()
                ->authorize('delete')
                ->using(static function (Model $record): bool {
                    Assert::isInstanceOf($record, Ticket::class);
                    app(DeleteTicketAction::class)->execute($record);

                    return true;
                }),
        ];
    }

    /**
     * Lo schema condiviso con il wizard porta `status` e `priority` come campi Hidden: dal browser si potevano
     * riscrivere con `$wire.set()` saltando il workflow, le notifiche e la timeline (SEC-08).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        return parent::mutateFormDataBeforeSave(static::withoutWorkflowFields($data));
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function withoutWorkflowFields(array $data): array
    {
        return array_diff_key($data, array_flip(self::WORKFLOW_FIELDS));
    }
}
