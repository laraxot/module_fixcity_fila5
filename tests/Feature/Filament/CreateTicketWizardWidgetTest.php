<?php

declare(strict_types=1);

use PHPUnit\Framework\Assert;
use Livewire\Livewire;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Xot\Actions\View\GetViewByClassAction;
use Modules\Fixcity\Tests\TestCase;

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
        Assert::assertTrue(method_exists(CreateTicketWizardWidget::class, 'submit'));
        Assert::assertTrue(method_exists(CreateTicketWizardWidget::class, 'save'));
    });
});
