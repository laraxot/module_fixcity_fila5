<?php

declare(strict_types=1);

use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

test('create ticket wizard declares module fallback view before theme resolution', function (): void {
    $prop = new ReflectionProperty(CreateTicketWizardWidget::class, 'view');

    expect($prop->getDefaultValue())->toBe('fixcity::filament.widgets.create-ticket-wizard');
});

test('create ticket wizard extends xot base wizard widget', function (): void {
    expect(is_subclass_of(CreateTicketWizardWidget::class, XotBaseWidget::class))->toBeTrue();
});

test('sixteen theme wizard wrapper uses wire submit on form', function (): void {
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('wire:submit="{{ $this->getFormSubmitAction() }}"')
        ->and($contents)->toContain('<x-filament-actions::modals />')
        ->and($contents)->toContain('cmp-wizard-widget');

    $formClose = strpos($contents, '</form>');
    $modals = strpos($contents, '<x-filament-actions::modals');

    expect($formClose)->not->toBeFalse()
        ->and($modals)->not->toBeFalse()
        ->and($formClose)->toBeLessThan($modals);
});

test('fixcity module wizard view follows livewire form stub contract', function (): void {
    $path = dirname(__DIR__, 2).'/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('wire:submit="{{ $this->getFormSubmitAction() }}"')
        ->and($contents)->toContain('{{ $this->form }}')
        ->and($contents)->toContain('<x-filament-actions::modals />');

    $formClose = strpos($contents, '</form>');
    $modals = strpos($contents, '<x-filament-actions::modals');

    expect($formClose)->not->toBeFalse()
        ->and($modals)->not->toBeFalse()
        ->and($formClose)->toBeLessThan($modals);
});

test('xot base wizard widget exposes form submit action for blade stub', function (): void {
    $method = new ReflectionMethod(\Modules\Xot\Filament\Widgets\XotBaseWizardWidget::class, 'getFormSubmitAction');

    expect($method->isPublic())->toBeTrue();
});

test('sixteen theme provides design comuni wizard submit button view', function (): void {
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/wizard/submit-button.blade.php';

    expect(is_file($path))->toBeTrue();

    $contents = (string) file_get_contents($path);

    expect($contents)->toContain('type="submit"')
        ->and($contents)->toContain('steppers-btn-confirm');
});
