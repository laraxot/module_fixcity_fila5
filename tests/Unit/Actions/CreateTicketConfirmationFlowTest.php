<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Session;
use Modules\Fixcity\Actions\AllocateTicketCodeAction;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\BuildTicketPublicDetailsPayloadAction;
use Modules\Fixcity\Actions\CreateTicketAction;
use Modules\Fixcity\Actions\GetPublicTicketByCodeAction;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('Ticket confirmation and tracking flow', function (): void {
    test('allocate ticket code returns unique prefixed code', function (): void {
        $code = app(AllocateTicketCodeAction::class)->execute('TCK');

        Assert::assertStringStartsWith('TCK-', $code);
        Assert::assertSame(20, strlen($code));
    });

    test('create ticket action allocates code and flashes confirmation bag', function (): void {
        $user = UserFactory::new()->createOne();
        $this->actingAs($user);

        $ticket = app(CreateTicketAction::class)->execute([
            'name' => 'Buca in via Roma',
            'content' => 'Buca pericolosa sul marciapiede',
            'status' => TicketStatusEnum::PENDING,
            'owner_id' => $user->id,
        ]);

        Assert::assertNotSame('', (string) $ticket->code);
        Assert::assertTrue(Session::has(BuildTicketConfirmationDataAction::SESSION_KEY));

        /** @var array<string, mixed> $bag */
        $bag = Session::get(BuildTicketConfirmationDataAction::SESSION_KEY);
        Assert::assertSame($ticket->code, $bag['code'] ?? null);
        Assert::assertIsArray($bag['receipt'] ?? null);

        $englishData = app(BuildTicketConfirmationDataAction::class)->execute($ticket, 'en');
        Assert::assertStringContainsString('Thank you', $englishData['summary_html']);
        Assert::assertSame('Track report '.$ticket->code, $englishData['receipt']['label']);
        Assert::assertStringContainsString('/en/tickets/track/', $englishData['receipt']['url']);
    });

    test('guest with the capability code can retrieve a pending ticket', function (): void {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::PENDING,
            'code' => 'TCK-PENDING1',
        ]);

        $found = app(GetPublicTicketByCodeAction::class)->execute('TCK-PENDING1');

        Assert::assertNotNull($found);
        Assert::assertSame($ticket->id, $found->id);
    });

    test('invalid capability code finds no ticket', function (): void {
        Assert::assertNull(app(GetPublicTicketByCodeAction::class)->execute('TCK-UNKNOWN01'));
    });

    test('guest can retrieve a ticket in a public status by code', function (): void {
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
            'code' => 'TCK-PUBLIC001',
        ]);

        $found = app(GetPublicTicketByCodeAction::class)->execute((string) $ticket->code);

        Assert::assertNotNull($found);
        Assert::assertSame($ticket->id, $found->id);
    });

    test('public details payload includes status and code', function (): void {
        $owner = UserFactory::new()->createOne();
        $this->actingAs($owner);
        $ticket = TicketFactory::new()->createOne([
            'status' => TicketStatusEnum::IN_PROGRESS,
            'code' => 'TCK-DETAIL1',
            'name' => 'Lampione spento',
            'owner_id' => $owner->id,
        ]);

        $payload = app(BuildTicketPublicDetailsPayloadAction::class)->execute($ticket);

        Assert::assertSame('TCK-DETAIL1', $payload['code']);
        Assert::assertSame(TicketStatusEnum::IN_PROGRESS->value, $payload['status']);
        Assert::assertSame('Lampione spento', $payload['title']);
    });

    test('confirmation page shows flashed code', function (): void {
        $user = UserFactory::new()->createOne();
        $this->actingAs($user);

        Session::flash(BuildTicketConfirmationDataAction::SESSION_KEY, [
            'code' => 'TCK-UIUX001',
            'slug' => 'buca-test',
            'status' => TicketStatusEnum::PENDING->value,
            'email' => $user->email,
            'summary_html' => 'Grazie, codice <strong>TCK-UIUX001</strong>.',
            'receipt' => [
                'url' => '/it/tickets/track/TCK-UIUX001',
                'label' => 'Traccia la segnalazione TCK-UIUX001',
                'icon' => 'it-search',
            ],
            'reserved_area' => [
                'link_label' => 'Consulta la richiesta',
                'text' => 'nella tua area riservata',
                'url' => '/it/area-personale/pratiche',
            ],
        ]);

        $this->get('/it/tickets/confirmation')
            ->assertOk()
            ->assertSee('<title>Segnalazione inviata</title>', false)
            ->assertSee('Passaggio 4 di 4')
            ->assertDontSee('fixcity::segnalazione.steps.current_of_total.label')
            ->assertSee('TCK-UIUX001', false);
    });

    test('confirmation page uses the English CMS title and copy', function (): void {
        app()->setLocale('en');

        Assert::assertSame(
            'Step 4 of 4',
            __('fixcity::segnalazione.steps.current_of_total.label', ['current' => 4, 'total' => 4]),
        );

        $user = UserFactory::new()->createOne();
        $this->actingAs($user);
        Session::flash(BuildTicketConfirmationDataAction::SESSION_KEY, [
            'code' => 'TCK-EN001',
            'slug' => 'lamp-post',
            'status' => TicketStatusEnum::PENDING->value,
            'email' => $user->email,
            'summary_html' => 'Thank you, report received. Code: <strong>TCK-EN001</strong>.',
            'receipt' => [
                'url' => '/en/tickets/track/TCK-EN001',
                'label' => 'Track report TCK-EN001',
                'icon' => 'it-search',
            ],
            'reserved_area' => [
                'link_label' => 'View your request',
                'text' => 'in your personal area',
                'url' => '/en/area-personale/pratiche',
            ],
        ]);

        $this->get('/en/tickets/confirmation')
            ->assertOk()
            ->assertSee('<title>Report submitted</title>', false)
            ->assertDontSee('fixcity::segnalazione.steps.current_of_total.label')
            ->assertSee('Track report TCK-EN001')
            ->assertDontSee('Traccia la segnalazione');
    });

    test('track page finds ticket by code query', function (): void {
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Rifiuti abbandonati',
            'status' => TicketStatusEnum::IN_PROGRESS,
            'code' => 'TCK-TRACK01',
        ]);
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
            'visibility' => TicketActivityVisibilityEnum::Public->value,
            'payload' => ['from' => 'in_review', 'to' => 'in_progress'],
            'reason' => 'La squadra è al lavoro.',
        ]);
        TicketActivity::create([
            'ticket_id' => $ticket->id,
            'event_type' => TicketActivityEventTypeEnum::StatusChange->value,
            'visibility' => TicketActivityVisibilityEnum::Internal->value,
            'payload' => ['from' => 'in_review', 'to' => 'in_progress'],
            'reason' => 'Nota interna riservata.',
        ]);

        $response = $this->get('/it/tickets/track?code=TCK-TRACK01');
        $response
            ->assertOk()
            ->assertSee('Rifiuti abbandonati')
            ->assertSee('TCK-TRACK01')
            ->assertSee('La squadra è al lavoro.')
            ->assertDontSee('Nota interna riservata.');
    });

    test('authenticated owner can track a ticket by id without entering its capability code', function (): void {
        $owner = UserFactory::new()->createOne();
        $this->actingAs($owner);
        $ticket = TicketFactory::new()->createOne([
            'name' => 'Marciapiede danneggiato',
            'status' => TicketStatusEnum::PENDING,
            'code' => 'TCK-IDLOOKUP01',
            'owner_id' => $owner->getAuthIdentifier(),
        ]);

        $this->get('/it/tickets/track?ticket_id='.$ticket->id)
            ->assertOk()
            ->assertSee('Marciapiede danneggiato')
            ->assertSee('TCK-IDLOOKUP01');
    });

    test('guest cannot track a ticket by its database id', function (): void {
        $ticket = TicketFactory::new()->createOne(['status' => TicketStatusEnum::IN_PROGRESS]);

        $this->get('/it/tickets/track?ticket_id='.$ticket->id)
            ->assertForbidden();
    });

    test('track code path redirects to query form', function (): void {
        $this->get('/it/tickets/track/TCK-REDIR01')
            ->assertRedirect();
    });
});
