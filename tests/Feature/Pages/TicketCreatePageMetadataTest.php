<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use Modules\User\Database\Factories\UserFactory;

uses(TestCase::class);

it('renders the Italian CMS title in the browser title for the authenticated wizard', function (): void {
    /** @var TestCase $this */
    $this->actingAs(UserFactory::new()->createOne())
        ->get('/it/tickets/create')
        ->assertOk()
        ->assertSee('<title>Segnala disservizio | ', false)
        ->assertSee('Autorizzazioni e condizioni')
        ->assertSee('Ho letto e compreso l\'informativa sulla privacy')
        ->assertSee('aria-invalid="false"', false)
        ->assertSee('aria-describedby="ticket-privacy-error"', false)
        ->assertDontSee('fixcity::');
});

it('renders the English CMS title in the browser title for the authenticated wizard', function (): void {
    /** @var TestCase $this */
    $this->actingAs(UserFactory::new()->createOne())
        ->get('/en/tickets/create')
        ->assertOk()
        ->assertSee('<title>Report an issue | ', false)
        ->assertSee('Privacy and terms')
        ->assertSee('I have read and understood the privacy policy')
        ->assertSee('aria-invalid="false"', false)
        ->assertSee('aria-describedby="ticket-privacy-error"', false)
        ->assertDontSee('fixcity::');
});
