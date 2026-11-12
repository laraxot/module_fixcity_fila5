<?php

declare(strict_types=1);

use Modules\Fixcity\Actions\BuildTicketPublicImageUrlsAction;
use Modules\Fixcity\Tests\TestCase;
use Modules\Media\Models\Media;
use PHPUnit\Framework\Assert;

uses(TestCase::class);

/*
 * module_fixcity_fila5#71: le foto dei ticket su un disco locale non devono portare l'host di APP_URL. Con APP_URL diverso dal
 * dominio pubblico il link portava a "Impossibile raggiungere il sito" e l'immagine risultava rotta.
 */
it('returns a host-less path for media on a local disk', function (string $disk): void {
    config([
        'app.url' => 'http://localhost:8000',
        'filesystems.disks.'.$disk => ['driver' => 'local', 'root' => storage_path('app/'.$disk), 'url' => 'http://localhost:8000/files'],
    ]);

    $media = new Media;
    $media->forceFill(['id' => 42, 'disk' => $disk, 'conversions_disk' => $disk, 'file_name' => 'buca.jpg', 'mime_type' => 'image/jpeg']);

    $url = app(BuildTicketPublicImageUrlsAction::class)->url($media);

    Assert::assertStringStartsWith('/', $url);
    Assert::assertStringNotContainsString('localhost', $url);
    Assert::assertStringEndsWith('/42/buca.jpg', $url);
})->with(['public', 'tenant_local']);
