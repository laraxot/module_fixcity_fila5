<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Modules\Fixcity\Actions\SubmitCitizenTicketRatingAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Filament\Widgets\Ticket\ViewWidget;
use Modules\Fixcity\Filament\Widgets\TicketCitizenRatingPromptWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('shows the rating prompt on the resolved ticket detail to its owner', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);
    $ticket = TicketFactory::new()->resolved()->createOne(['owner_id' => $owner->getKey()]);

    Livewire::test(ViewWidget::class, ['slug0' => (string) $ticket->id])
        ->assertStatus(200)
        ->assertSee('ticket-rating-star-'.$ticket->id.'-5', false);
});

it('does not show the rating prompt to a different authenticated user', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $otherUser = UserFactory::new()->createOne();
    $this->actingAs($otherUser);
    $ticket = TicketFactory::new()->resolved()->createOne(['owner_id' => $owner->getKey()]);

    Livewire::test(ViewWidget::class, ['slug0' => (string) $ticket->id])
        ->assertStatus(200)
        ->assertDontSee('ticket-rating-star-'.$ticket->id.'-5', false);
});

it('does not show the rating prompt before the ticket is resolved', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);
    $ticket = TicketFactory::new()->createOne([
        'owner_id' => $owner->getKey(),
        'status' => TicketStatusEnum::IN_PROGRESS,
    ]);

    Livewire::test(ViewWidget::class, ['slug0' => (string) $ticket->id])
        ->assertStatus(200)
        ->assertDontSee('ticket-rating-star-'.$ticket->id.'-5', false);
});

it('rejects an out of range rating submitted through the livewire widget', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);
    $ticket = TicketFactory::new()->resolved()->createOne(['owner_id' => $owner->getKey()]);

    Livewire::test(TicketCitizenRatingPromptWidget::class, [
        'ticketId' => $ticket->id,
    ])->set('selectedRating', 6)
        ->call('submitRating')
        ->assertHasErrors(['rating']);
});

it('persists an eligible owner rating and displays confirmation', function (): void {
    /** @var TestCase $this */
    $owner = UserFactory::new()->createOne();
    $this->actingAs($owner);
    $ticket = TicketFactory::new()->resolved()->createOne(['owner_id' => $owner->getKey()]);
    $ratingConnection = DB::connection('rating');
    $ratingConnection->beginTransaction();

    try {
        $component = Livewire::test(TicketCitizenRatingPromptWidget::class, [
            'ticketId' => $ticket->id,
        ]);
        $component->set('selectedRating', 5);
        $component->call('submitRating')->assertHasNoErrors();
        $component->assertSee(__('fixcity::ticket_citizen_rating.prompt.thank_you.label'));

        try {
            app(SubmitCitizenTicketRatingAction::class)->execute($ticket, 4);
            $this->fail('A citizen must not submit more than one rating for a ticket.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('rating', $exception->errors());
        }

        $this->assertDatabaseHas('rating_morph', [
            'model_type' => $ticket->getMorphClass(),
            'model_id' => $ticket->id,
            'user_id' => $owner->id,
            'value' => 5,
        ], 'rating');
        $this->assertDatabaseCount('rating_morph', 1, 'rating');
    } finally {
        $ratingConnection->rollBack();
    }
});

it('does not allow audit metadata to grant rating ownership', function (): void {
    /** @var TestCase $this */
    $actor = UserFactory::new()->createOne();
    $realOwner = UserFactory::new()->createOne();
    $this->actingAs($actor);
    $ticket = TicketFactory::new()->resolved()->createOne([
        'owner_id' => $realOwner->getKey(),
        'created_by' => $actor->getKey(),
        'updated_by' => $actor->getKey(),
    ]);

    $ratingConnection = DB::connection('rating');
    $ratingConnection->beginTransaction();

    try {
        try {
            app(SubmitCitizenTicketRatingAction::class)->execute($ticket, 5);
            $this->fail('Audit metadata must not authorize ticket ratings.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('owner', $exception->errors());
        }

        $this->assertDatabaseMissing('rating_morph', [
            'model_type' => $ticket->getMorphClass(),
            'model_id' => $ticket->getKey(),
            'user_id' => $actor->getKey(),
        ], 'rating');
    } finally {
        $ratingConnection->rollBack();
    }
});
