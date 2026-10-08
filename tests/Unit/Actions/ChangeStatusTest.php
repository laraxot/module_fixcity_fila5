<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Modules\Fixcity\Actions\ChangeStatus;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);
// Laraxot — see module docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.
// Laraxot module file — see docs/wiki for domain contract.

describe('Change Status', function (): void {
    test('_changes_ticket_status_successfully', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $action->execute($ticket, 'pending', 'Issue has been received by the team');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    });

    test('_records_reason_and_public_visibility_for_public_status_changes', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);

        $action->execute($ticket, TicketStatusEnum::IN_REVIEW->value, 'Presa in carico dal Comune');

        $activity = TicketActivity::query()
            ->where('ticket_id', $ticket->getKey())
            ->latest('id')
            ->first();

        Assert::assertInstanceOf(TicketActivity::class, $activity);
        Assert::assertSame('Presa in carico dal Comune', $activity->reason);
        Assert::assertSame(TicketActivityVisibilityEnum::Public->value, $activity->visibility);
        Assert::assertSame(TicketStatusEnum::PENDING->value, $activity->payload['from'] ?? null);
        Assert::assertSame(TicketStatusEnum::IN_REVIEW->value, $activity->payload['to'] ?? null);
    });

    test('_handles_status_transition_to_in_progress', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);

        $action->execute($ticket, 'in_review', 'Customer reported critical issue');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::IN_REVIEW, $ticket->status);
    });

    test('_handles_status_transition_to_closed', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);

        $action->execute($ticket, 'closed', 'Ticket completed successfully');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::CLOSED, $ticket->status);
    });

    test('_preserves_existing_ticket_data_during_status_change', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Original Title',
            'content' => 'Original Description',
            'priority' => 'high',
            'responsible_id' => null,
            'status' => TicketStatusEnum::IN_REVIEW,
        ]);

        $action->execute($ticket, 'in_progress', 'Work started on this ticket');

        $ticket->refresh();
        Assert::assertSame('Original Title', $ticket->name);
        Assert::assertSame('Original Description', $ticket->content);
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS, $ticket->status);
    });

    test('_ignores_invalid_status_values', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        expect(fn () => $action->execute($ticket, 'invalid_status', 'Testing invalid status handling'))
            ->toThrow(\DomainException::class);
    });

    test('_updates_ticket_timestamps_when_status_changes', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::PENDING]);
        $originalUpdatedAt = $ticket->updated_at;
        Assert::assertNotNull($originalUpdatedAt);

        sleep(1);

        $action->execute($ticket, 'in_review', 'Status change test');

        $ticket->refresh();
        Assert::assertNotNull($ticket->updated_at);
        Assert::assertGreaterThan($originalUpdatedAt->timestamp, $ticket->updated_at->timestamp);
    });

    test('_handles_empty_reason_string', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $action->execute($ticket, 'pending', '');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    });

    test('_handles_long_reason_strings', function (): void {
        $action = new ChangeStatus;
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_REVIEW]);
        $longReason = str_repeat('A very long reason that tests the handling of extended text content. ', 10);

        $action->execute($ticket, 'on_hold', $longReason);

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::ON_HOLD, $ticket->status);
    });
});
