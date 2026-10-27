<?php

declare(strict_types=1);

use Illuminate\Routing\Route as LaravelRoute;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * A-08 (DCP-282): `POST /livewire/form-create-ticket` portava a `CreateTicketWizardWidget@submit` fuori da Livewire, con il solo middleware
 * `web`: niente `auth`, niente `throttle`, e nessun chiamante nei temi, in Mobile, nei test o nei moduli (verificato con `rg --no-ignore`).
 * Il wizard parla con Livewire (`/livewire-<hash>/update`), quindi la rotta e' stata rimossa, non protetta.
 */
it('has no route to the orphan ticket form endpoint any more', function (): void {
    $uris = collect(Route::getRoutes()->getRoutes())->map(static fn (LaravelRoute $route): string => $route->uri())->all();

    Assert::assertNotContains('livewire/form-create-ticket', $uris);
});

it('refuses a guest posting to the removed endpoint without reaching the widget', function (): void {
    // 405: un'altra rotta (solo GET) risponde allo stesso percorso; 404 se nessuna. In entrambi i casi nessuna azione e' eseguita.
    $status = $this->post('/livewire/form-create-ticket', ['title' => 'x'])->getStatusCode();

    Assert::assertContains($status, [404, 405]);
});

it('keeps the wizard working through Livewire', function (): void {
    Livewire::test(CreateTicketWizardWidget::class, ['blockData' => ['name' => 'Segnalazione disservizio', 'content' => '']])
        ->assertOk()
        ->assertSeeHtml('$wire.save()');

    Assert::assertContains('submit', get_class_methods(CreateTicketWizardWidget::class));
});
