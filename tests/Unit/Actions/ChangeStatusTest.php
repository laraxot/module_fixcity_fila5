<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Fixcity\Actions\ChangeStatus;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

class ChangeStatusTest extends TestCase
{
    use RefreshDatabase;

    private ChangeStatus $action;

    protected function setUp(): void
    {
        parent::setUp();
        $this->action = new ChangeStatus;
    }

    /** @test */
    public function it_changes_ticket_status_successfully(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $this->action->execute($ticket, 'resolved', 'Issue has been resolved by the team');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::RESOLVED, $ticket->status);
    }

    /** @test */
    public function it_handles_status_transition_to_in_progress(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $this->action->execute($ticket, 'in_progress', 'Customer reported critical issue');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS, $ticket->status);
    }

    /** @test */
    public function it_handles_status_transition_to_closed(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::RESOLVED]);

        $this->action->execute($ticket, 'closed', 'Ticket completed successfully');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::CLOSED, $ticket->status);
    }

    /** @test */
    public function it_preserves_existing_ticket_data_during_status_change(): void
    {
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Original Title',
            'content' => 'Original Description',
            'priority' => 'high',
            'responsible_id' => null,
        ]);

        $this->action->execute($ticket, 'in_progress', 'Work started on this ticket');

        $ticket->refresh();
        Assert::assertSame('Original Title', $ticket->name);
        Assert::assertSame('Original Description', $ticket->content);
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS, $ticket->status);
    }

    /** @test */
    public function it_ignores_invalid_status_values(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $this->action->execute($ticket, 'invalid_status', 'Testing invalid status handling');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::OPEN, $ticket->status);
    }

    /** @test */
    public function it_updates_ticket_timestamps_when_status_changes(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);
        $originalUpdatedAt = $ticket->updated_at;
        Assert::assertNotNull($originalUpdatedAt);

        sleep(1);

        $this->action->execute($ticket, 'resolved', 'Status change test');

        $ticket->refresh();
        Assert::assertNotNull($ticket->updated_at);
        Assert::assertGreaterThan($originalUpdatedAt->timestamp, $ticket->updated_at->timestamp);
    }

    /** @test */
    public function it_handles_empty_reason_string(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);

        $this->action->execute($ticket, 'pending', '');

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
    }

    /** @test */
    public function it_handles_long_reason_strings(): void
    {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::OPEN]);
        $longReason = str_repeat('A very long reason that tests the handling of extended text content. ', 10);

        $this->action->execute($ticket, 'on_hold', $longReason);

        $ticket->refresh();
        Assert::assertSame(TicketStatusEnum::ON_HOLD, $ticket->status);
    }
}
