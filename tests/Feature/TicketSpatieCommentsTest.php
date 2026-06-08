<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Comment\Http\Livewire\CommentsComponent;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\User;
use Modules\Comment\Models\Comment;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

describe('Ticket Spatie comments FO', function () {
    it('creates a spatie comment on ticket via HasComments trait', function () {
        $user = User::factory()->create([
            'name' => 'Cittadino Test',
            'email' => 'citizen-'.uniqid('', true).'@test.local',
            'password' => bcrypt('password'),
        ]);
        $ticket = Ticket::factory()->create([
            'owner_id' => $user->id,
            'status' => TicketStatusEnum::OPEN,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'type_id' => null,
        ]);

        $this->actingAs($user);

        $comment = $ticket->comment('Commento cittadino di test');

        expect($comment)->toBeInstanceOf(Comment::class)
            ->and((string) $comment->commentable_id)->toBe((string) $ticket->getKey())
            ->and($ticket->comments()->count())->toBe(1);
    });

    it('renders livewire comments component on ticket detail page', function () {
        $user = User::factory()->create([
            'name' => 'Cittadino Test',
            'email' => 'citizen-'.uniqid('', true).'@test.local',
            'password' => bcrypt('password'),
        ]);
        $ticket = Ticket::factory()->create([
            'owner_id' => $user->id,
            'status' => TicketStatusEnum::OPEN,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'type_id' => null,
        ]);

        $this->actingAs($user);
        $ticket->comment('Primo commento visibile');

        $response = $this->get('/it/tickets/'.$ticket->getKey());

        $response->assertOk();
        $response->assertSee('Primo commento visibile');
        $response->assertSee('comment-section', false);
    });

    it('submits comment via Livewire CommentsComponent when authenticated', function () {
        $user = User::factory()->create([
            'name' => 'Cittadino Test',
            'email' => 'citizen-'.uniqid('', true).'@test.local',
            'password' => bcrypt('password'),
        ]);
        $ticket = Ticket::factory()->create([
            'owner_id' => $user->id,
            'status' => TicketStatusEnum::OPEN,
            'type' => TicketTypeEnum::ROAD_MAINTENANCE,
            'type_id' => null,
        ]);

        $before = $ticket->comments()->whereNotNull('approved_at')->count();

        Livewire::actingAs($user)
            ->test(CommentsComponent::class, ['model' => $ticket, 'readOnly' => false])
            ->set('text', 'Commento via Livewire test')
            ->call('comment')
            ->assertHasNoErrors();

        expect($ticket->fresh()->comments()->whereNotNull('approved_at')->count())->toBe($before + 1);
    });
});
