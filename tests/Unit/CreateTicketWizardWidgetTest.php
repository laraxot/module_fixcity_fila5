<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;

describe('CreateTicketWizardWidget', function (): void {
    it('is the canonical FixCity wizard widget', function (): void {
        expect(CreateTicketWizardWidget::class)
            ->toBe('Modules\\Fixcity\\Filament\\Widgets\\CreateTicketWizardWidget');
    });

    it('exposes the public wizard contract', function (): void {
        $reflection = new ReflectionClass(CreateTicketWizardWidget::class);

        expect($reflection->hasMethod('mount'))->toBeTrue()
            ->and($reflection->getMethod('mount')->isPublic())->toBeTrue()
            ->and($reflection->hasMethod('getSteps'))->toBeTrue()
            ->and($reflection->getMethod('getSteps')->isPublic())->toBeTrue()
            ->and($reflection->hasMethod('save'))->toBeTrue()
            ->and($reflection->getMethod('save')->isPublic())->toBeTrue();
    });
});
