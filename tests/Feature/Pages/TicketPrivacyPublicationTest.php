<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedLegalDocumentAction;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Filament\Widgets\CreateTicketWidget;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/*
 * DCP-130: `/{lingua}/privacy` non e' piu' una pagina Folio che risponde 503 senza testo (cancellata dal commit 30d521c): e' la pagina CMS
 * `privacy.index` e risponde sempre 200, con un avviso che elenca che cosa deve fornire il titolare. Test dettagliati:
 * PrivacyPolicyPageTest. Il testo dell'ente arriva da GetPublishedLegalDocumentAction (GetPublishedPrivacyPolicyAction la usa per il wizard).
 */
it('returns a 200 page with a warning while the tenant policy is a template', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedLegalDocumentAction::class, new class extends GetPublishedLegalDocumentAction
    {
        public function execute(string $file): ?string
        {
            return null;
        }
    });

    $this->get('/it/privacy')
        ->assertOk()
        ->assertSee('Informativa privacy')
        ->assertSee('data-element="legal-incomplete"', false)
        ->assertDontSee('Firenze');
});

it('renders the tenant policy as sanitized markdown when it is published', function (): void {
    /** @var TestCase $this */
    $this->instance(GetPublishedLegalDocumentAction::class, new class extends GetPublishedLegalDocumentAction
    {
        public function execute(string $file): string
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
