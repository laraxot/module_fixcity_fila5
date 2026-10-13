<?php

declare(strict_types=1);

use Livewire\Component;
use Livewire\Livewire;
use Modules\Fixcity\Tests\TestCase;
use Modules\Xot\Actions\Cast\SafeStringCastAction;

uses(TestCase::class);

/*
 * DCP-90: con `bootstrap/cache/routes-v7.php` costruita dall'ambiente di sviluppo, l'URI di update di Livewire
 * (`/livewire-<hash APP_KEY>/update`) non coincide con quello dell'ambiente di test: il POST risponde 405,
 * Livewire\Testable lo ignora (stato vuoto, `effects` vuoti) e `call()`/`set()` diventano no-op silenziosi.
 */
it('completes a Livewire update round trip in the test environment', function (): void {
    $component = Livewire::test(new class extends Component
    {
        public string $name = '';

        public function render(): string
        {
            return '<div>{{ $name }}</div>';
        }
    })->set('name', 'dopo-update');

    expect($component->instance())->not->toBeNull(
        'POST su '.SafeStringCastAction::cast(app('livewire')->getUpdateUri()).' non ha risposto 200: route cache obsoleta (vedi DCP-90, phpunit.xml APP_ROUTES_CACHE).',
    );
    $component->assertSet('name', 'dopo-update')->assertSee('dopo-update');
});
