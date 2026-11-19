<?php

declare(strict_types=1);

use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Modules\Fixcity\Actions\TicketCitizenRating\EnsureTicketCitizenRatingDefinitionAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Filament\Widgets\CitizenRatingOverviewWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\Rating\Models\RatingMorph;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('renders the citizen rating aggregate in the PA ticket queue', function (): void {
    $owner = UserFactory::new()->createOne();
    $ticket = TicketFactory::new()->resolved()->createOne(['owner_id' => $owner->getKey()]);
    $ratingConnection = DB::connection('rating');
    $ratingConnection->beginTransaction();

    try {
        $definition = app(EnsureTicketCitizenRatingDefinitionAction::class)->execute();
        RatingMorph::query()->create([
            'rating_id' => $definition->getKey(),
            'model_type' => (new Ticket)->getMorphClass(),
            'model_id' => $ticket->getKey(),
            'user_id' => $owner->getKey(),
            'value' => 5,
        ]);

        Filament::setCurrentPanel('fixcity::admin');
        $this->actingAs($owner);

        Livewire::test(CitizenRatingOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee(__('fixcity::ticket_citizen_rating.admin.stats.average.label'))
            ->assertSee(__('fixcity::ticket_citizen_rating.admin.stats.count.label'))
            ->assertSee('5/5')
            ->assertSee('1');
    } finally {
        $ratingConnection->rollBack();
    }
});

it('shows an explicit empty aggregate when no citizen has rated a ticket', function (): void {
    $operator = UserFactory::new()->createOne();
    $ratingConnection = DB::connection('rating');
    $ratingConnection->beginTransaction();

    try {
        Filament::setCurrentPanel('fixcity::admin');
        $this->actingAs($operator);

        Livewire::test(CitizenRatingOverviewWidget::class)
            ->assertSuccessful()
            ->assertSee(__('fixcity::ticket_citizen_rating.admin.stats.average.label'))
            ->assertSee(__('fixcity::ticket_citizen_rating.admin.stats.count.label'))
            ->assertSee('—')
            ->assertSee('0');
    } finally {
        $ratingConnection->rollBack();
    }
});
