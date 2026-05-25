<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

test('create ticket wizard leaves view resolution to xot base widget', function (): void {
    $prop = new ReflectionProperty(CreateTicketWizardWidget::class, 'view');

    expect($prop->getDeclaringClass()->getName())->toBe(XotBaseWidget::class)
        ->and($prop->getDefaultValue())->toBe('xot::filament.widgets.base');
});

test('create ticket wizard extends xot base wizard widget', function (): void {
    expect(is_subclass_of(CreateTicketWizardWidget::class, XotBaseWidget::class))->toBeTrue();
});

test('sixteen theme wizard wrapper avoids nested form around filament wizard', function (): void {
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('{{ $this->form }}')
        ->and($contents)->toContain('<x-filament-actions::modals />')
        ->and($contents)->toContain('cmp-wizard-widget')
        ->and(strpos($contents, '<form '))->toBeFalse('outer Blade shell must not add a wrapping <form>; Filament renders one per wizard step');

    $formMarker = strpos($contents, '{{ $this->form }}');
    $modals = strpos($contents, '<x-filament-actions::modals');

    expect($formMarker)->not->toBeFalse()
        ->and($modals)->not->toBeFalse()
        ->and($formMarker)->toBeLessThan($modals);
});

test('fixcity module wizard view avoids nested form around filament wizard', function (): void {
    $path = dirname(__DIR__, 2).'/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('{{ $this->form }}')
        ->and($contents)->toContain('<x-filament-actions::modals />')
        ->and(strpos($contents, '<form '))->toBeFalse('outer Blade shell must not wrap {{ $this->form }}');

    $formMarker = strpos($contents, '{{ $this->form }}');
    $modals = strpos($contents, '<x-filament-actions::modals');

    expect($formMarker)->not->toBeFalse()
        ->and($modals)->not->toBeFalse()
        ->and($formMarker)->toBeLessThan($modals);
});

test('xot base wizard widget exposes submit action hook from has xot form trait', function (): void {
    $method = new ReflectionMethod(\Modules\Xot\Filament\Widgets\XotBaseWizardWidget::class, 'getSubmitFormAction');

    expect($method->isProtected())->toBeTrue();
});

test('sixteen theme provides design comuni wizard submit button view', function (): void {
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/wizard/submit-button.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('type="submit"')
        ->and($contents)->toContain('steppers-btn-confirm');
});
