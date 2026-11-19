<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Modules\Fixcity\Actions\ChangeStatus as ActionChangeStatus;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Webmozart\Assert\Assert;

class ChangeStatus extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        // Etichette da lang (filament-blueprint Fixcity): niente stringhe hardcoded —
        // l'operatore registra motivo e stato nella cronologia visibile al cittadino.
        $this->translateLabel()
            ->authorize('changeStatus')
            ->visible(static fn (?Model $record): bool => $record instanceof Ticket && self::targetsFor($record) !== [])
            ->action(
                static function (Ticket $record, array $data): void {
                    $status = $data['status'] instanceof TicketStatusEnum
                        ? $data['status']->value
                        : $data['status'];
                    Assert::string($status);
                    Assert::string($data['reason']);

                    app(ActionChangeStatus::class)->execute($record, $status, $data['reason']);
                }
            )
            ->schema([
                Select::make('status')
                    ->options(static fn (?Model $record): array => $record instanceof Ticket ? self::optionsFor($record) : [])
                    ->required(),
                TextInput::make('reason')
                    ->label(__('fixcity::ticket.actions.changeStatus.reason_label'))
                    ->helperText(__('fixcity::ticket.actions.changeStatus.reason_help'))
                    ->required(),
            ])
            ->icon('ui-status')
            ->tooltip(__('fixcity::ticket.actions.changeStatus.tooltip'))
            ->modalHeading(__('fixcity::ticket.actions.changeStatus.modal_heading'))
            ->modalSubmitActionLabel(__('fixcity::ticket.actions.changeStatus.modal_submit'));
    }

    /**
     * Target consentiti dalla matrice di `TicketStatusEnum` per lo stato corrente del ticket (DCP-281, A-13).
     * Stato vuoto: nessun vincolo, come in `Actions\ChangeStatus`. Stato non riconosciuto: nessun target.
     *
     * @return list<TicketStatusEnum>
     */
    private static function targetsFor(Ticket $ticket): array
    {
        $current = $ticket->resolveTicketStatusValue();

        if ($current === '') {
            return TicketStatusEnum::cases();
        }

        return TicketStatusEnum::tryFrom($current)?->allowedTargets() ?? [];
    }

    /**
     * @return array<string, string> valore => etichetta, nel formato della Select
     */
    private static function optionsFor(Ticket $ticket): array
    {
        $options = [];
        foreach (self::targetsFor($ticket) as $target) {
            $options[$target->value] = $target->getLabel();
        }

        return $options;
    }

    public static function getDefaultName(): ?string
    {
        return 'changeStatus';
    }
}
