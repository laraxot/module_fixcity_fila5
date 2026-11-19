<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildTicketTimelineAction;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * La cronologia di una segnalazione appena inviata parte da "ricevuta": prima l'unica voce era "Allegato aggiunto" (la foto
 * caricata insieme alla segnalazione), trovato provando il wizard da cittadino.
 */
it('records the submission as the first author-only timeline entry', function (): void {
    $user = UserFactory::new()->createOne();
    /** @var TestCase $this */
    $this->actingAs($user);

    $ticket = app(CreateTicketAction::class)->execute([
        'name' => 'Buca in via Roma',
        'content' => 'Buca profonda davanti al civico 12.',
        'type' => 'road_maintenance',
        'status' => TicketStatusEnum::PENDING->value,
    ], flashConfirmation: false);

    $first = TicketActivity::query()->where('ticket_id', $ticket->getKey())->orderBy('id')->first();
    Assert::assertInstanceOf(TicketActivity::class, $first);
    Assert::assertSame(TicketActivityEventTypeEnum::StatusChange->value, $first->getAttributes()['event_type']);
    Assert::assertSame(TicketActivityVisibilityEnum::AuthorOnly->value, $first->getAttributes()['visibility']);

    $ownerTimeline = app(BuildTicketTimelineAction::class)->execute($ticket, $user);
    Assert::assertNotEmpty($ownerTimeline);
    Assert::assertSame(TicketStatusEnum::PENDING->getLabel(), $ownerTimeline[0]['to_label']);

    // Il ticket non e' pubblico: chi non e' l'autore non vede la voce.
    Assert::assertSame([], app(BuildTicketTimelineAction::class)->execute($ticket, null));
});
