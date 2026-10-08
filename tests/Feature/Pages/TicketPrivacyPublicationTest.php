<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('returns a localized unavailable response while the tenant policy is a template', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): ?string
        {
            return null;
        }
    });

    $response = $this->get('/it/privacy');
    $response
        ->assertStatus(503)
        ->assertSee('Informativa privacy')
        ->assertSee('L’informativa privacy non è ancora disponibile.')
        ->assertDontSee('Firenze');
});

it('renders the tenant policy as sanitized markdown when it is published', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return "# Informativa pubblicata\n\nContenuto fornito dall'ente.\n\n<script>alert('unsafe')</script>";
        }
    });

    $this->get('/en/privacy')
        ->assertOk()
        ->assertSee('Privacy notice')
        ->assertSee('Informativa pubblicata')
        ->assertSeeText("Contenuto fornito dall'ente.")
        ->assertDontSee("<script>alert('unsafe')</script>", false)
        ->assertDontSee("alert('unsafe')", false);
});

it('rejects a forged wizard consent while the tenant policy is unpublished', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): ?string
        {
            return null;
        }
    });

    $user = UserFactory::new()->createOne();
    $this->actingAs($user);

    Livewire::test(CreateTicketWizardWidget::class)
        ->set('data.privacyAccepted', true)
        ->call('save')
        ->assertHasErrors(['data.privacyAccepted']);

    Livewire::test(CreateTicketWidget::class)
        ->set('data.accept_terms', true)
        ->set('data.name', 'Segnalazione senza policy')
        ->set('data.content', 'Tentativo di invio senza informativa.')
        ->call('submit')
        ->assertHasErrors(['data.accept_terms']);

    expect(Ticket::query()->where('owner_id', $user->getAuthIdentifier())->count())->toBe(0);
});
