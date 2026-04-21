<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;

test('create ticket wizard uses module blade view for design comuni sidebar layout', function (): void {
    $prop = new ReflectionProperty(CreateTicketWizardWidget::class, 'view');

    expect($prop->getDefaultValue())->toBe('fixcity::filament.widgets.ticket-create-wizard');
});
