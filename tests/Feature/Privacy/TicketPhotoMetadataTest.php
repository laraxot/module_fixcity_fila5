<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Database\Factories\TicketFactory;
use Modules\Fixcity\Tests\TestCase;

use function Safe\file_put_contents;
use function Safe\filesize;
use function Safe\getimagesize;
use function Safe\imagecreatetruecolor;
use function Safe\imagejpeg;
use function Safe\ob_get_clean;
use function Safe\ob_start;
use function Safe\tempnam;

uses(TestCase::class);

/*
 * Privacy notice: EXIF and GPS data are removed from the photos a citizen attaches to a report.
 * Story: Modules/Fixcity/docs/stories/privacy-notice-retention-exif-account-deletion.story.md
 */

/** A real 8x8 JPEG whose APP1 segment carries a camera make and a GPS latitude reference. */
function jpegWithGps(): string
{
    $image = imagecreatetruecolor(8, 8);
    ob_start();
    imagejpeg($image);
    $jpeg = ob_get_clean();
    $tiff = 'II'.pack('v', 42).pack('V', 8)
        .pack('v', 2).pack('vvVV', 0x010F, 2, 6, 38).pack('vvVV', 0x8825, 4, 1, 44).pack('V', 0)
        ."Canon\0"
        .pack('v', 1).pack('vvV', 0x0001, 2, 2)."N\0\0\0".pack('V', 0);
    $path = tempnam(sys_get_temp_dir(), 'gps').'.jpg';
    file_put_contents($path, substr($jpeg, 0, 2)."\xFF\xE1".pack('n', strlen($tiff) + 8)."Exif\0\0".$tiff.substr($jpeg, 2));

    return $path;
}

beforeEach(function (): void {
    Storage::fake('public');
});

it('builds a fixture that really carries GPS data', function (): void {
    $exif = exif_read_data(jpegWithGps());

    expect($exif)->toBeArray()->toHaveKey('GPSLatitudeRef', 'N')->toHaveKey('Make', 'Canon');
});

it('stores a report photo without EXIF and GPS data', function (): void {
    $ticket = TicketFactory::new()->createOne();

    $media = $ticket->addMedia(jpegWithGps())->toMediaCollection('attachments');

    $stored = Storage::disk('public')->path($media->getPathRelativeToRoot());
    $exif = exif_read_data($stored);
    expect($exif)->toBeArray()->not->toHaveKey('GPSLatitudeRef')->not->toHaveKey('Make')
        ->and(getimagesize($stored))->toBeArray()
        ->and($media->fresh()?->size)->toBe(filesize($stored));
});

it('leaves a report PDF untouched', function (): void {
    $ticket = TicketFactory::new()->createOne();
    $pdf = tempnam(sys_get_temp_dir(), 'pdf').'.pdf';
    $bytes = "%PDF-1.4\n1 0 obj<<>>endobj\ntrailer<<>>\n%%EOF\n";
    file_put_contents($pdf, $bytes);

    $media = $ticket->addMedia($pdf)->toMediaCollection('attachments');

    expect(Storage::disk('public')->get($media->getPathRelativeToRoot()))->toBe($bytes);
});
