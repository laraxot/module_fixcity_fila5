<?php

declare(strict_types=1);

use Laravel\Boost\BoostServiceProvider;
use Laravel\Mcp\Server\McpServiceProvider;
use Modules\Fixcity\Tests\TestCase;
use PHPUnit\Framework\Assert;
use Symfony\Component\Process\Process;

use function Safe\json_decode;

uses(TestCase::class);

/*
 * F-01 (DCP-282): Boost e Mcp sono pacchetti `require-dev`, ma `bootstrap/providers.php` li elencava a mano senza condizioni.
 * Con `composer install --no-dev` la classe non esiste, il framework prova a istanziarla e il boot cade con un errore fatale.
 * Qui si simula l'installazione senza dev: un processo PHP a parte carica `providers.php` con i namespace dei due pacchetti
 * resi invisibili all'autoloader (non si tocca `vendor/`, non si cambiano dipendenze).
 */
const DEV_ONLY_PROVIDER_PREFIXES = ['Laravel\\Boost\\', 'Laravel\\Mcp\\'];

/**
 * @return list<string> provider restituiti da `bootstrap/providers.php` quando i pacchetti dev non ci sono
 */
function providersWithoutDevPackages(): array
{
    $script = <<<'PHP'
        $root = $argv[1];
        $loader = require $root.'/vendor/autoload.php';
        spl_autoload_unregister([$loader, 'loadClass']);
        spl_autoload_register(static function (string $class) use ($loader): void {
            if (str_starts_with($class, 'Laravel\\Boost\\') || str_starts_with($class, 'Laravel\\Mcp\\')) {
                return;
            }
            $loader->loadClass($class);
        });
        $providers = require $root.'/bootstrap/providers.php';
        echo json_encode(['hidden' => ! class_exists('Laravel\\Boost\\BoostServiceProvider'), 'providers' => array_values($providers)]);
        PHP;

    $process = new Process([PHP_BINARY, '-r', $script, '--', base_path()]);
    $process->mustRun();

    $decoded = json_decode($process->getOutput(), true);
    Assert::assertIsArray($decoded);
    Assert::assertTrue($decoded['hidden'] ?? false, 'La simulazione senza pacchetti dev non ha nascosto Boost: il test non prova nulla.');
    Assert::assertIsArray($decoded['providers']);

    /** @var list<string> $providers */
    $providers = array_values(array_filter($decoded['providers'], is_string(...)));

    return $providers;
}

it('lists no dev-only provider when the dev packages are not installed', function (): void {
    $providers = providersWithoutDevPackages();

    foreach ($providers as $provider) {
        foreach (DEV_ONLY_PROVIDER_PREFIXES as $prefix) {
            Assert::assertFalse(str_starts_with($provider, $prefix), $provider.' e un pacchetto require-dev: con --no-dev il boot cade.');
        }
        Assert::assertTrue(class_exists($provider), $provider.' non esiste: il framework la istanzierebbe e darebbe un errore fatale.');
    }
    Assert::assertNotEmpty($providers, 'providers.php deve restituire comunque i provider dell\'applicazione.');
});

it('keeps registering the dev providers where the dev packages are installed', function (): void {
    if (! class_exists(BoostServiceProvider::class) || ! class_exists(McpServiceProvider::class)) {
        $this->markTestSkipped('Pacchetti dev non installati: niente da confrontare.');
    }

    $providers = require base_path('bootstrap/providers.php');

    Assert::assertIsArray($providers);
    Assert::assertContains(BoostServiceProvider::class, $providers);
    Assert::assertContains(McpServiceProvider::class, $providers);
});
