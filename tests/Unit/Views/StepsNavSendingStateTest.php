<?php

declare(strict_types=1);

use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * DCP-286 (FO-3-02): il pulsante finale del wizard ("Invia", `$wire.submit()`) non aveva stato di caricamento: un doppio clic lo faceva partire due
 * volte (due segnalazioni). Ora e' disabilitato mentre il metodo Livewire e' in corso. La garanzia vera e' lato server (SubmitTicketOnceAction).
 */

/**
 * @param  array<string, mixed>  $nav
 */
function renderStepsNavForSendingTest(array $nav): string
{
    return view('pub_theme::components.blocks.report-partials.steps-nav', [
        'd' => static fn (string $key, mixed $default = ''): mixed => data_get(['nav' => $nav], $key, $default),
        'icon' => static fn (string $name): string => '#'.$name,
    ])->render();
}

it('disables the final button while its Livewire method runs', function (string $method): void {
    $html = renderStepsNavForSendingTest(['next' => 'Invia', 'next_click' => '$wire.'.$method.'()', 'next_class' => 'send']);

    expect($html)
        ->toContain('x-on:click="$wire.'.$method.'()"')
        ->toContain('wire:loading.attr="disabled"')
        ->toContain('wire:target="'.$method.'"');
})->with(['submit', 'save']);

it('adds no loading state to buttons that do not call a Livewire method', function (): void {
    expect(renderStepsNavForSendingTest(['next' => 'Avanti', 'next_click' => 'requestNextStep()']))
        ->not->toContain('wire:loading')
        ->and(renderStepsNavForSendingTest(['next' => 'Avanti', 'next_target' => '#modal-terms']))
        ->not->toContain('wire:loading')
        ->toContain('data-bs-target="#modal-terms"');
});
