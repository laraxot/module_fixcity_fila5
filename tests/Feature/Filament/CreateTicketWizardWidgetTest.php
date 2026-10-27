<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Fixcity\Actions\BuildTicketConfirmationDataAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Resources\TicketResource\Schemas\TicketForm;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;
use Modules\Xot\Actions\View\GetViewByClassAction;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

describe('CreateTicketWizardWidget view resolution', function (): void {
    it('resolves pub_theme wrapper with wire submit', function (): void {
        $view = app(GetViewByClassAction::class)->execute(CreateTicketWizardWidget::class);

        Assert::assertSame('pub_theme::filament.widgets.create-ticket-wizard', $view);
        Livewire::test(CreateTicketWizardWidget::class, [
            'blockData' => [
                'name' => 'Segnalazione disservizio',
                'content' => '',
            ],
        ])->assertSeeHtml('$wire.save()');
    });
});

describe('CreateTicketWizardWidget submit', function (): void {
    it('exposes submit and save entry points on the widget class', function (): void {
        Assert::assertContains('submit', get_class_methods(CreateTicketWizardWidget::class));
        Assert::assertContains('save', get_class_methods(CreateTicketWizardWidget::class));
    });

    it('does not ask for personal fields that the ticket cannot persist', function (): void {
        $summaryKeys = array_keys(TicketForm::getSummarySchema());
        $defaults = TicketForm::getDefaultFormState();

        Assert::assertSame(['warningSection', 'summarySection'], $summaryKeys);
        Assert::assertArrayNotHasKey('author_name', $defaults);
        Assert::assertArrayNotHasKey('author_fiscal_code', $defaults);
        Assert::assertArrayNotHasKey('author_phone', $defaults);
        Assert::assertArrayNotHasKey('author_email', $defaults);

        Livewire::test(CreateTicketWizardWidget::class)
            ->assertDontSee('author_fiscal_code')
            ->assertDontSee('author_phone');
    });

    it('persists a valid wizard submission and prepares its confirmation', function (): void {
        Storage::fake('public');
        app()->setLocale('it');
        $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
        {
            public function execute(): string
            {
                return '# Informativa di test';
            }
        });

        $user = UserFactory::new()->createOne();
        $this->actingAs($user);

        $component = Livewire::test(CreateTicketWizardWidget::class);
        $component->set('data.privacyAccepted', true);
        $component->set('data.name', 'Lampione spento in via Roma');
        $component->set('data.type', TicketTypeEnum::PUBLIC_LIGHTING->value);
        $component->set('data.content', 'Il lampione non funziona da tre giorni.');
        $component->set('data.location', [
            'latitude' => '45.4642',
            'longitude' => '9.1900',
            'address' => 'Via Roma, Milano',
        ]);
        $component->set('data.images', [UploadedFile::fake()->image('streetlight.jpg')]);
        app()->setLocale('en');
        $component->call('submit');
        $component->assertHasNoErrors();
        $component->assertRedirectContains('/it/tickets/confirmation');

        $ticket = Ticket::query()->where('owner_id', $user->getAuthIdentifier())->latest('id')->first();
        Assert::assertInstanceOf(Ticket::class, $ticket);
        Assert::assertSame('Lampione spento in via Roma', $ticket->name);
        Assert::assertEqualsWithDelta(45.4642, (float) $ticket->latitude, 0.00001);
        Assert::assertEqualsWithDelta(9.19, (float) $ticket->longitude, 0.00001);
        Assert::assertNotSame('', (string) $ticket->code);
        Assert::assertSame(TicketStatusEnum::PENDING, $ticket->status);
        $attachments = $ticket->getMedia('attachments');
        Assert::assertCount(1, $attachments);
        $attachment = $attachments->first();
        Assert::assertNotNull($attachment);
        Assert::assertTrue(Storage::disk('public')->exists($attachment->getPathRelativeToRoot()));
        Assert::assertSame(
            $user->email,
            app(BuildTicketConfirmationDataAction::class)->execute($ticket)['email'],
        );

        $this->get('/it/tickets/confirmation')
            ->assertOk()
            ->assertSee('<title>Segnalazione inviata</title>', false)
            ->assertSee((string) $ticket->code)
            ->assertSee($user->email);
    });
});
