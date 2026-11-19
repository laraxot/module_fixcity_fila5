<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

/*
 * PRD-02: la pagina /tickets/create e' servita dal tema, quindi il middleware della pagina Folio del modulo non gira.
 * Il widget controlla l'identita' gia' all'apertura, non solo all'invio: nessuno compila il modulo per niente.
 */
it('sends a user with an unconfirmed email to the verification page as soon as the wizard opens', function (): void {
    /** @var TestCase $this */
    $this->actingAs(UserFactory::new()->unverified()->createOne());

    $component = Livewire::test(CreateTicketWizardWidget::class);
    $component->assertRedirect(route('verification.notice'));
});

it('opens the wizard for a user with a confirmed email', function (): void {
    /** @var TestCase $this */
    $this->actingAs(UserFactory::new()->createOne());

    $component = Livewire::test(CreateTicketWizardWidget::class);
    $component->assertNoRedirect();
});
