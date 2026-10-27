<?php

declare(strict_types=1);

use Illuminate\Support\Str;
use Modules\Fixcity\Actions\InterpolateEnteTokensAction;
use Modules\Fixcity\Tests\TestCase;

uses(TestCase::class);

/*
 * I documenti legali prendono titolare e DPO da config('ente'): mai un valore inventato, mai Markdown o HTML iniettato da un valore.
 */
it('replaces ente tokens with the configured values', function (): void {
    config(['ente' => ['name' => 'Comune di Prova', 'dpo_email' => 'dpo@comune.prova.it']]);

    expect(app(InterpolateEnteTokensAction::class)->execute('Titolare: {{ente.name}}, DPO: {{ ente.dpo_email }}.'))
        ->toBe('Titolare: Comune di Prova, DPO: dpo@comune.prova.it.');
});

it('leaves a visible placeholder for an empty or unknown field', function (): void {
    config(['ente' => ['name' => '   ']]);

    expect(app(InterpolateEnteTokensAction::class)->execute('{{ente.name}} {{ente.pec}} {{ente.password}}'))
        ->toBe('[[ente.name]] [[ente.pec]] [[ente.password]]');
});

it('renders a hostile value as literal text', function (): void {
    config(['ente' => ['name' => "Comune <script>alert(1)</script> [x](javascript:alert(1)) **bold**\n# Titolo", 'address' => "Via Roma 1\n00100 Prova"]]);

    $markdown = app(InterpolateEnteTokensAction::class)->execute('Titolare: {{ente.name}}, sede {{ente.address}}.');
    $html = (string) Str::markdown($markdown, ['html_input' => 'strip', 'allow_unsafe_links' => false]);

    expect($html)->not->toContain('<script')
        ->not->toContain('<a ')
        ->not->toContain('<strong>')
        ->not->toContain('<h1')
        ->toContain('&lt;script&gt;')
        ->toContain('Via Roma 1, 00100 Prova');
});
