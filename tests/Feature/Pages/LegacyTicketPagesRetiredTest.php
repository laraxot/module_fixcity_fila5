<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Laravel\Folio\FolioManager;
use Laravel\Folio\Router;
use Modules\Fixcity\Tests\TestCase;

/*
 * DCP-281 (A-09, A-37, A-38): il dettaglio pubblico di una segnalazione vive nel tema (DCP-148, `BuildPublicTicketDetailAction`).
 * Le vecchie pagine Folio del modulo `tickets/[slug]` (solo `auth`, `withTrashed`, nome ed email dell'utente come autore) e
 * `tickets/[id]` (testi fissi, modello intero) erano oscurate dal tema e vanno ritirate con `.old`, non cancellate. Il blocco
 * `Blocks\TicketList` aveva conteggi di categoria inventati e nessun punto di istanza.
 */

uses(TestCase::class);

function dcp281ModuleRoot(): string
{
    return dirname(__DIR__, 3);
}

/**
 * Il file che Folio userebbe per l'URL, con lo stesso ordine di `RequestHandler` (primo percorso montato che combacia).
 */
function dcp281FolioViewFor(string $path): ?string
{
    $request = Request::create($path);

    foreach (app(FolioManager::class)->mountPaths() as $mountPath) {
        $uri = '/'.ltrim(substr('/'.$request->path(), strlen($mountPath->baseUri)), '/');
        $matched = app()->make(Router::class, ['mountPath' => $mountPath])->match($request, $uri);

        if ($matched !== null) {
            return $matched->path;
        }
    }

    return null;
}

it('resolves every public ticket URL with the theme, never with a module page', function (string $lang, string $segment): void {
    $view = dcp281FolioViewFor('/'.$lang.'/tickets/'.$segment);

    expect($view)->not->toBeNull()
        ->and($view)->toContain('/Themes/')
        ->and($view)->not->toContain('/Modules/Fixcity/resources/views/pages/tickets/');
})->with([
    'it numeric' => ['it', '29'],
    'it slug' => ['it', 'buca-in-via-solferino'],
    'en numeric' => ['en', '29'],
    'de slug' => ['de', 'buca-in-via-solferino'],
    'es numeric' => ['es', '29'],
]);

it('keeps no active Folio page named [id] or [slug] in the module tickets directory', function (): void {
    $active = collect(File::files(dcp281ModuleRoot().'/resources/views/pages/tickets'))
        ->map(static fn (SplFileInfo $file): string => $file->getFilename())
        ->filter(static fn (string $name): bool => str_ends_with($name, '.blade.php'))
        ->filter(static fn (string $name): bool => str_starts_with($name, '[id]') || str_starts_with($name, '[slug]'))
        ->values()
        ->all();

    expect($active)->toBe([]);
});

it('keeps the retired legacy pages next to where they were, with the .old suffix', function (): void {
    $dir = dcp281ModuleRoot().'/resources/views/pages/tickets';

    expect(File::exists($dir.'/[id].blade.php.old'))->toBeTrue()
        ->and(File::exists($dir.'/[slug].blade.php.old2'))->toBeTrue()
        ->and(File::exists($dir.'/[slug].blade.php.old'))->toBeTrue();
});

it('has no active PHP in the module that carries the invented category counts', function (): void {
    $withInventedCounts = collect(File::allFiles(dcp281ModuleRoot().'/app'))
        ->filter(static fn (SplFileInfo $file): bool => $file->getExtension() === 'php')
        ->filter(static fn (SplFileInfo $file): bool => str_contains(File::get($file->getPathname()), 'problemi fognari ('))
        ->map(static fn (SplFileInfo $file): string => $file->getFilename())
        ->values()
        ->all();

    expect($withInventedCounts)->toBe([]);
});
