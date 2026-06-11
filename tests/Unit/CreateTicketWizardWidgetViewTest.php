<?php

declare(strict_types=1);




use ReflectionProperty;
use function Safe\file_get_contents;
use ReflectionMethod;
use Modules\Fixcity\Tests\TestCase;

use PHPUnit\Framework\Assert;
use Modules\Fixcity\Filament\Resources\TicketResource;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Xot\Filament\Widgets\XotBaseWidget;

uses(TestCase::class);
test('create ticket wizard leaves view resolution to xot base widget', function (): void {
    /** @var TestCase $this */
    $prop = new ReflectionProperty(CreateTicketWizardWidget::class, 'view');

    Assert::assertSame(XotBaseWidget::class, $prop->getDeclaringClass()->getName());

    Assert::assertSame('xot::filament.widgets.base', $prop->getDefaultValue());
});

test('create ticket wizard binds ticket resource for filament wizard', function (): void {
    /** @var TestCase $this */
    Assert::assertSame(TicketResource::class, CreateTicketWizardWidget::$resource);
});

test('sixteen theme wizard wrapper avoids nested form around filament wizard', function (): void {
    /** @var TestCase $this */
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    Assert::assertTrue(is_file($path));

    $contents = (string) file_get_contents($path);

    Assert::assertStringContainsString('{{ $this->form }}', $contents);

    Assert::assertStringContainsString('<x-filament-actions::modals />', $contents);

    Assert::assertStringContainsString('cmp-wizard-widget', $contents);

    $formMarker = strpos($contents, '{{ $this->form }}');
    $modals = strpos($contents, '<x-filament-actions::modals');

    Assert::assertLessThan($modals, $formMarker);
});

test('fixcity module wizard view avoids nested form around filament wizard', function (): void {
    /** @var TestCase $this */
    $path = dirname(__DIR__, 2).'/resources/views/filament/widgets/create-ticket-wizard.blade.php';

    Assert::assertTrue(is_file($path));

    $contents = (string) file_get_contents($path);

    Assert::assertStringContainsString('{{ $this->form }}', $contents);

    Assert::assertStringContainsString('<x-filament-actions::modals />', $contents);

    $formMarker = strpos($contents, '{{ $this->form }}');
    $modals = strpos($contents, '<x-filament-actions::modals');

    Assert::assertLessThan($modals, $formMarker);
});

test('xot base wizard widget exposes submit action hook from has xot form trait', function (): void {
    /** @var TestCase $this */
    $method = new ReflectionMethod(\Modules\Xot\Filament\Widgets\XotBaseWizardWidget::class, 'getSubmitFormAction');

    Assert::assertTrue($method->isProtected());
});

test('sixteen theme provides design comuni wizard submit button view', function (): void {
    /** @var TestCase $this */
    $path = dirname(__DIR__, 4).'/Themes/Sixteen/resources/views/filament/wizard/submit-button.blade.php';

    Assert::assertTrue(is_file($path));

    $contents = (string) file_get_contents($path);

    Assert::assertStringContainsString('type="submit"', $contents);

    Assert::assertStringContainsString('steppers-btn-confirm', $contents);
});
