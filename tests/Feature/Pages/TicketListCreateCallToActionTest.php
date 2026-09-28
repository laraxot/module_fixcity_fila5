<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

it('links the public reports page to canonical ticket creation', function (): void {
    /** @var TestCase $this */
    $response = $this->get('/it/tickets');

    $response->assertOk()
        ->assertSee('<title>Elenco segnalazioni</title>', false)
        ->assertSee('/it/tickets/create', false)
        ->assertDontSee('href="/it/segnalazione-crea"', false);

    $content = $response->getContent();
    Assert::assertIsString($content);

    expect(substr_count($content, 'id="create-ticket-heading"'))->toBe(1);
});

it('does not render the obsolete modal on the canonical ticket listing', function (): void {
    /** @var TestCase $this */
    $response = $this->get('/it/tickets');

    $response->assertOk()
        ->assertDontSee('id="modal-disservizio"', false)
        ->assertDontSee('data-bs-target="#modal-disservizio"', false);
});
