<?php

declare(strict_types=1);

use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\User\Database\Factories\UserFactory;
use Modules\Comment\Models\Comment;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('Ticket native comments FO', function () {
    it('creates a native comment on ticket via HasComments trait', function () {
        /** @var TestCase $this */
        $user = UserFactory::new()->createOne([
            'name' => 'Cittadino Test',
            'email' => 'citizen-'.uniqid('', true).'@test.local',
            'password' => bcrypt('password'),
        ]);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $user->id,
            'status' => TicketStatusEnum::OPEN,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'type_id' => null,
        ]);

        $this->actingAs($user);

        $comment = $ticket->comment('Commento cittadino di test');

        Assert::assertInstanceOf(Comment::class, $comment);
        Assert::assertSame(SafeStringCastAction::cast($ticket->getKey()), SafeStringCastAction::cast($comment->commentable_id));
        Assert::assertSame(1, $ticket->comments()->count());
    });

    it('persists approved comments on ticket model', function () {
        /** @var TestCase $this */
        $user = UserFactory::new()->createOne([
            'name' => 'Cittadino Test',
            'email' => 'citizen-'.uniqid('', true).'@test.local',
            'password' => bcrypt('password'),
        ]);
        $ticket = TicketFactory::new()->createOne([
            'owner_id' => $user->id,
            'status' => TicketStatusEnum::OPEN,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'type_id' => null,
        ]);

        $this->actingAs($user);
        $ticket->comment('Primo commento visibile');

        $refreshed = $ticket->fresh();
        Assert::assertNotNull($refreshed);
        Assert::assertSame(1, $refreshed->comments()->whereNotNull('approved_at')->count());
    });
});
