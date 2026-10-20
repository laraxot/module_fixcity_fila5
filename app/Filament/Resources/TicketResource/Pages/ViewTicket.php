<?php

declare(strict_types=1);

namespace Modules\Fixcity\Filament\Resources\TicketResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Modules\Fixcity\Actions\ApproveTicketAction;
use Modules\Fixcity\Actions\AssignTicketAction;
use Modules\Fixcity\Actions\CloseTicketAction;
use Modules\Fixcity\Actions\DeleteTicketAction;
use Modules\Fixcity\Actions\RecordTicketCommunicationAction;
use Modules\Fixcity\Actions\RejectTicketAction;
use Modules\Fixcity\Actions\ResolveTicketAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Actions\ChangePriority;
use Modules\Fixcity\Filament\Actions\ChangeStatus;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Models\Ticket;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Datas\XotData;
use Modules\Xot\Filament\Resources\Pages\XotBaseViewRecord;
use Webmozart\Assert\Assert;

/**
 * Gate facts (ECC):
 * 1. Callers — TicketResource.php:24 route view; TicketResourceTest / TicketDeletionAuthorizationTest Livewire::test(ViewTicket).
 * 2. Existing page file (unique); no second ViewTicket. Fixes parse error (double return + missing TextInput).
 * 3. No data-file I/O; Eloquent Ticket + TicketActivity via Actions.
 * 4. User: "implementa cosa manca utilizzando bmad + second brain + eec + filament blueprint"
 */
class ViewTicket extends XotBaseViewRecord
{
    protected static string $resource = TicketResource::class;

    /**
     * @return array<string, Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            'assign' => Action::make('assign')
                ->authorize('assign')
                ->label(__('fixcity::ticket.actions.assign.label'))
                ->icon('heroicon-o-user-plus')
                ->schema([
                    Select::make('responsible_id')
                        ->label(__('fixcity::ticket.fields.responsible_id.label'))
                        ->options(static function (): array {
                            $userClass = XotData::make()->getUserClass();

                            return $userClass::query()
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->map(static fn (mixed $name): string => is_scalar($name) ? (string) $name : '')
                                ->all();
                        })
                        ->searchable()
                        ->preload()
                        ->nullable()
                        ->default(function (): ?string {
                            if (! $this->record instanceof Ticket || $this->record->responsible_id === null) {
                                return null;
                            }

                            return is_scalar($this->record->responsible_id)
                                ? (string) $this->record->responsible_id
                                : null;
                        }),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    $responsibleId = $data['responsible_id'] ?? null;
                    if ($responsibleId !== null && ! is_string($responsibleId)) {
                        $responsibleId = SafeStringCastAction::cast($responsibleId);
                    }
                    Assert::nullOrString($responsibleId);
                    app(AssignTicketAction::class)->execute($this->record, $responsibleId);
                }),
            'approve' => Action::make('approve')
                ->authorize('changeStatus')
                ->label(__('fixcity::ticket.actions.approve.label'))
                ->icon('heroicon-o-check-circle')
                ->visible(fn (): bool => $this->record instanceof Ticket
                    && TicketStatusEnum::tryFrom($this->record->resolveTicketStatusValue()) === TicketStatusEnum::PENDING)
                ->schema([
                    Textarea::make('reason')
                        ->label(__('fixcity::ticket.actions.approve.reason_label'))
                        ->required()
                        ->maxLength(2_000),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['reason'] ?? null);
                    app(ApproveTicketAction::class)->execute($this->record, $data['reason']);
                }),
            'reject' => Action::make('reject')
                ->authorize('changeStatus')
                ->label(__('fixcity::ticket.actions.reject.label'))
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn (): bool => $this->record instanceof Ticket
                    && in_array(
                        TicketStatusEnum::tryFrom($this->record->resolveTicketStatusValue()),
                        [TicketStatusEnum::PENDING, TicketStatusEnum::DRAFT, TicketStatusEnum::IN_REVIEW],
                        true,
                    ))
                ->schema([
                    Textarea::make('reason')
                        ->label(__('fixcity::ticket.actions.reject.reason_label'))
                        ->required()
                        ->maxLength(2_000),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['reason'] ?? null);
                    app(RejectTicketAction::class)->execute($this->record, $data['reason']);
                }),
            'resolve' => Action::make('resolve')
                ->authorize('changeStatus')
                ->label(__('fixcity::ticket.actions.resolve.label'))
                ->icon('heroicon-o-flag')
                ->visible(fn (): bool => $this->record instanceof Ticket
                    && in_array(
                        TicketStatusEnum::tryFrom($this->record->resolveTicketStatusValue()),
                        [TicketStatusEnum::IN_PROGRESS, TicketStatusEnum::ON_HOLD],
                        true,
                    ))
                ->schema([
                    Textarea::make('reason')
                        ->label(__('fixcity::ticket.actions.resolve.reason_label'))
                        ->required()
                        ->maxLength(2_000),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['reason'] ?? null);
                    app(ResolveTicketAction::class)->execute($this->record, $data['reason']);
                }),
            'close' => Action::make('close')
                ->authorize('changeStatus')
                ->label(__('fixcity::ticket.actions.close.label'))
                ->icon('heroicon-o-lock-closed')
                ->visible(fn (): bool => $this->record instanceof Ticket
                    && TicketStatusEnum::tryFrom($this->record->resolveTicketStatusValue()) === TicketStatusEnum::RESOLVED)
                ->schema([
                    Textarea::make('reason')
                        ->label(__('fixcity::ticket.actions.close.reason_label'))
                        ->required()
                        ->maxLength(2_000),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['reason'] ?? null);
                    app(CloseTicketAction::class)->execute($this->record, $data['reason']);
                }),
            'changeStatus' => ChangeStatus::make(),
            'sendCommunication' => Action::make('sendCommunication')
                ->authorize('communicate')
                ->label(__('fixcity::ticket.actions.send_communication.label'))
                ->icon('heroicon-o-envelope')
                ->schema([
                    Select::make('recipient_label')
                        ->label(__('fixcity::ticket.actions.send_communication.recipient_label'))
                        ->options(function (): array {
                            if (! $this->record instanceof Ticket || $this->record->assignee === null) {
                                return [];
                            }

                            $name = $this->record->assignee->name;

                            $key = $this->record->assignee->getKey();
                            if (! is_int($key) && ! is_string($key)) {
                                return [];
                            }

                            return [$key => is_string($name) ? $name : __('fixcity::ticket.actions.send_communication.assignee_fallback')];
                        })
                        ->helperText(__('fixcity::ticket.actions.send_communication.recipient_help'))
                        ->required(false),
                    TextInput::make('recipient')
                        ->label(__('fixcity::ticket.actions.send_communication.recipient_email'))
                        ->email()
                        ->required(),
                    TextInput::make('subject')
                        ->label(__('fixcity::ticket.actions.send_communication.subject'))
                        ->maxLength(255)
                        ->required(),
                    Textarea::make('body')
                        ->label(__('fixcity::ticket.actions.send_communication.body'))
                        ->rows(8)
                        ->maxLength(20_000)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['recipient'] ?? null);
                    $recipientLabel = $data['recipient_label'] ?? __('fixcity::ticket.actions.send_communication.office_fallback');
                    Assert::string($recipientLabel);
                    Assert::string($data['subject'] ?? null);
                    Assert::string($data['body'] ?? null);
                    app(RecordTicketCommunicationAction::class)->send(
                        $this->record,
                        $data['recipient'],
                        $recipientLabel,
                        $data['subject'],
                        $data['body'],
                    );
                }),
            'recordInboundReply' => Action::make('recordInboundReply')
                ->authorize('communicate')
                ->label(__('fixcity::ticket.actions.record_inbound.label'))
                ->icon('heroicon-o-inbox-arrow-down')
                ->schema([
                    TextInput::make('sender_label')
                        ->label(__('fixcity::ticket.actions.record_inbound.sender'))
                        ->maxLength(255)
                        ->required(),
                    Textarea::make('summary')
                        ->label(__('fixcity::ticket.actions.record_inbound.summary'))
                        ->helperText(__('fixcity::ticket.actions.record_inbound.summary_help'))
                        ->rows(6)
                        ->maxLength(2_000)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['sender_label'] ?? null);
                    Assert::string($data['summary'] ?? null);
                    app(RecordTicketCommunicationAction::class)->recordInbound(
                        $this->record,
                        $data['sender_label'],
                        $data['summary'],
                    );
                }),
            'sendFollowup' => Action::make('sendFollowup')
                ->authorize('communicate')
                ->label(__('fixcity::ticket.actions.send_followup.label'))
                ->icon('heroicon-o-arrow-path')
                ->schema([
                    TextInput::make('recipient')
                        ->label(__('fixcity::ticket.actions.send_communication.recipient_email'))
                        ->email()
                        ->required(),
                    TextInput::make('recipient_label')
                        ->label(__('fixcity::ticket.actions.send_followup.office'))
                        ->maxLength(255)
                        ->required(),
                    TextInput::make('subject')
                        ->label(__('fixcity::ticket.actions.send_communication.subject'))
                        ->maxLength(255)
                        ->required(),
                    Textarea::make('body')
                        ->label(__('fixcity::ticket.actions.send_communication.body'))
                        ->rows(8)
                        ->maxLength(20_000)
                        ->required(),
                ])
                ->action(function (array $data): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    Assert::string($data['recipient'] ?? null);
                    Assert::string($data['recipient_label'] ?? null);
                    Assert::string($data['subject'] ?? null);
                    Assert::string($data['body'] ?? null);
                    app(RecordTicketCommunicationAction::class)->send(
                        $this->record,
                        $data['recipient'],
                        $data['recipient_label'],
                        $data['subject'],
                        $data['body'],
                        true,
                    );
                }),
            'changePriority' => ChangePriority::make(),
            'edit' => EditAction::make(),
            'delete' => DeleteAction::make()
                ->authorize('delete')
                ->action(function (): void {
                    Assert::isInstanceOf($this->record, Ticket::class);
                    app(DeleteTicketAction::class)->execute($this->record);
                }),
        ];
    }
}
