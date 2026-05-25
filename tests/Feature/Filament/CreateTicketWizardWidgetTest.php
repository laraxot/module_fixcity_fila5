<?php

declare(strict_types=1);

use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Xot\Actions\View\GetViewByClassAction;
use Tests\TestCase;

uses(TestCase::class);

describe('CreateTicketWizardWidget view resolution', function (): void {
    it('resolves pub_theme wrapper with wire submit', function (): void {
        $view = app(GetViewByClassAction::class)->execute(CreateTicketWizardWidget::class);

        expect($view)->toBe('pub_theme::filament.widgets.create-ticket-wizard');

        Livewire::test(CreateTicketWizardWidget::class, [
            'blockData' => [
                'title' => 'Segnalazione disservizio',
                'description' => '',
            ],
        ])->assertSeeHtml('$wire.save()');
    });
});

describe('CreateTicketWizardWidget submit', function (): void {
    it('exposes submit and save entry points on the widget class', function (): void {
        expect(method_exists(CreateTicketWizardWidget::class, 'submit'))->toBeTrue()
            ->and(method_exists(CreateTicketWizardWidget::class, 'save'))->toBeTrue();
    });
});
