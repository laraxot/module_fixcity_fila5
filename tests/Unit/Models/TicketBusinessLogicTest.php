<?php

declare(strict_types=1);

namespace Modules\Fixcity\Tests\Unit\Models;

use Modules\Comment\Models\Comment;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Database\Factories\TicketHourFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\User\Models\User;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

/**
 * Business logic del model Ticket (ore, media, slug, relazioni, stati).
 */
final class TicketBusinessLogicTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = (new UserFactory)->createOne();
        $this->ticket = (new TicketFactory)->createOne([
            'owner_id' => $this->user->id,
            'estimation' => 2.5,
        ]);
    }

    public function test_calculates_total_logged_hours_correctly(): void
    {
        $ticket = $this->ticket();
        $user = $this->authUser();

        (new TicketHourFactory)->createOne([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'value' => 1.5,
        ]);

        (new TicketHourFactory)->createOne([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'value' => 2.0,
        ]);

        Assert::assertSame(3.5, $ticket->total_logged_in_hours);
    }

    public function test_returns_zero_when_no_hours_are_logged(): void
    {
        Assert::assertSame(0.0, $this->ticket()->total_logged_in_hours);
    }

    public function test_handles_decimal_hour_values_correctly(): void
    {
        $ticket = $this->ticket();
        $user = $this->authUser();

        (new TicketHourFactory)->createOne([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
            'value' => 0.75,
        ]);

        Assert::assertSame(0.75, $ticket->total_logged_in_hours);
    }

    public function test_converts_estimation_to_human_readable_format(): void
    {
        $result = $this->ticket()->estimation_for_humans;

        Assert::assertIsString($result);
        Assert::assertNotSame('', $result);
    }

    public function test_handles_null_estimation_gracefully(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'owner_id' => $this->authUser()->id,
            'estimation' => null,
        ]);

        Assert::assertIsString($ticket->estimation_for_humans);
    }

    public function test_registers_media_collections_with_correct_configuration(): void
    {
        $ticket = $this->ticket();
        $ticket->registerMediaCollections();

        $collection = $ticket->getMediaCollection('attachments');

        Assert::assertNotNull($collection);
        Assert::assertContains('image/jpeg', $collection->acceptsMimeTypes);
        Assert::assertContains('image/png', $collection->acceptsMimeTypes);
        Assert::assertContains('application/pdf', $collection->acceptsMimeTypes);
    }

    public function test_provides_correct_commentable_name(): void
    {
        Assert::assertSame('Segnalazione', $this->ticket()->commentableName());
    }

    public function test_provides_comment_url(): void
    {
        Assert::assertStringEndsWith(
            '/it/tickets/'.SafeStringCastAction::cast($this->ticket()->getKey()),
            $this->ticket()->commentUrl(),
        );
    }

    public function test_provides_correct_latitude_longitude_attribute_mapping(): void
    {
        Assert::assertSame([
            'lat' => 'latitude',
            'lng' => 'longitude',
        ], Ticket::getLatLngAttributes());
    }

    public function test_generates_slug_automatically_from_name(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'name' => 'Test Ticket Name With Spaces',
            'owner_id' => $this->authUser()->id,
        ]);

        Assert::assertNotNull($ticket->slug);
        Assert::assertStringContainsString('test', (string) $ticket->slug);
        Assert::assertStringContainsString('ticket', (string) $ticket->slug);
        Assert::assertStringContainsString('name', (string) $ticket->slug);
    }

    public function test_sets_default_status_to_pending_when_creating(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'name' => 'Test Default Status',
            'owner_id' => $this->authUser()->id,
        ]);

        Assert::assertInstanceOf(TicketStatusEnum::class, $ticket->status);
        Assert::assertSame('pending', $ticket->status->value);
    }

    public function test_generates_icon_data_for_valid_ticket_types(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'owner_id' => $this->authUser()->id,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
        ]);

        $iconData = $ticket->getIconData();

        Assert::assertIsArray($iconData);
        Assert::assertArrayHasKey('url', $iconData);
        Assert::assertArrayHasKey('type', $iconData);
        Assert::assertArrayHasKey('scale', $iconData);
    }

    public function test_correctly_associates_with_owner(): void
    {
        $owner = $this->ticket()->owner;

        Assert::assertInstanceOf(User::class, $owner);
        Assert::assertSame($this->authUser()->id, $owner->id);
    }

    public function test_can_have_multiple_hours_logged(): void
    {
        $ticket = $this->ticket();
        $user = $this->authUser();

        (new TicketHourFactory)->count(3)->create([
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ]);

        Assert::assertCount(3, $ticket->hours);
    }

    public function test_comments_relation_is_has_many(): void
    {
        Assert::assertSame(Comment::class, $this->ticket()->comments()->getRelated()::class);
    }

    public function test_can_transition_between_statuses(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'owner_id' => $this->authUser()->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        $ticket->update(['status' => TicketStatusEnum::IN_PROGRESS]);
        $inProgress = $ticket->fresh();
        Assert::assertNotNull($inProgress);
        Assert::assertInstanceOf(TicketStatusEnum::class, $inProgress->status);
        Assert::assertSame('in_progress', $inProgress->status->value);

        $ticket->update(['status' => TicketStatusEnum::RESOLVED]);
        $resolved = $ticket->fresh();
        Assert::assertNotNull($resolved);
        Assert::assertInstanceOf(TicketStatusEnum::class, $resolved->status);
        Assert::assertSame('resolved', $resolved->status->value);
    }

    public function test_maintains_enum_type_integrity_during_transitions(): void
    {
        $ticket = (new TicketFactory)->createOne([
            'owner_id' => $this->authUser()->id,
            'status' => TicketStatusEnum::PENDING,
        ]);

        $ticket->update(['status' => TicketStatusEnum::CLOSED]);

        $status = $ticket->fresh()?->status;
        Assert::assertInstanceOf(TicketStatusEnum::class, $status);
        Assert::assertSame('closed', $status->value);
    }
}
