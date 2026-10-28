<?php

declare(strict_types=1);

use Illuminate\Http\Testing\File as FakeFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\MessageBag;
use Livewire\Features\SupportFileUploads\FileUploadConfiguration;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Modules\Fixcity\Actions\GetPublishedPrivacyPolicyAction;
use Modules\Fixcity\Enums\TicketTypeEnum;
use Modules\Fixcity\Filament\Widgets\CreateTicketWizardWidget;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Tests\TestCase;
use Modules\Media\Models\Media;
use Modules\User\Database\Factories\UserFactory;
use PHPUnit\Framework\Assert;

use function Safe\file_put_contents;
use function Safe\imagecreatefromstring;
use function Safe\imagecreatetruecolor;
use function Safe\imagejpeg;
use function Safe\imagesetpixel;
use function Safe\ob_get_clean;
use function Safe\ob_start;
use function Safe\tempnam;
use function Safe\unlink;

uses(TestCase::class);

/**
 * SEC-04 (DCP-49), AC: "il server verifica MIME e contenuto, non soltanto estensione o MIME dichiarato dal browser; un file contraffatto
 * viene rifiutato; cancel/replace/upload fallito non creano ticket o media orfani".
 *
 * Perche' file veri e non `UploadedFile::fake()`: `Illuminate\Http\Testing\File::getMimeType()` restituisce il MIME DICHIARATO (o quello
 * dedotto dall'estensione), mai il contenuto; Livewire in test copia quel valore in un `.json` accanto al file temporaneo e
 * `TemporaryUploadedFile::getMimeType()` lo rilegge. Un test col fake non puo' quindi distinguere un controllo sul contenuto da uno
 * sull'estensione (vedi il primo test). Qui si usa un vero `UploadedFile` costruito da un file temporaneo: `getMimeType()` guarda i
 * byte (finfo), il MIME dichiarato dal "browser" e' solo `getClientMimeType()` e non conta.
 */

/** @return list<string> */
function &forgedUploadTempFiles(): array
{
    static $files = [];

    return $files;
}

function forgedUploadPath(string $name): string
{
    $path = FileUploadConfiguration::path($name);
    if (! is_string($path)) {
        throw new LogicException('The Livewire temporary upload path must be a string.');
    }

    return $path;
}

afterEach(function (): void {
    foreach (forgedUploadTempFiles() as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
    $files = &forgedUploadTempFiles();
    $files = [];
});

/**
 * Un vero UploadedFile (non fake) con `$bytes` come contenuto. `$name` pubblico perche' `Livewire\Testable::upload()` lo legge dal fake.
 */
function forgedUploadFile(string $bytes, string $name, ?string $declaredMime): UploadedFile
{
    $path = (string) tempnam(sys_get_temp_dir(), 'forged');
    file_put_contents($path, $bytes);
    forgedUploadTempFiles()[] = $path;

    $file = new class($path, $name, $declaredMime, null, true) extends UploadedFile
    {
        public string $name = '';
    };
    $file->name = $name;

    return $file;
}

/**
 * JPEG vero generato con GD; `$noise` lo rende abbastanza grosso da poterlo troncare in punti diversi.
 */
function forgedJpegBytes(int $width = 64, int $height = 48, bool $noise = false): string
{
    if ($width < 1 || $height < 1) {
        throw new InvalidArgumentException('JPEG dimensions must be positive.');
    }

    $image = imagecreatetruecolor($width, $height);
    mt_srand(7);
    for ($x = 0; $x < $width; $x++) {
        for ($y = 0; $y < $height; $y++) {
            $color = $noise ? imagecolorallocate($image, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255)) : imagecolorallocate($image, $x * 3 % 256, $y * 5 % 256, 120);
            imagesetpixel($image, $x, $y, (int) $color);
        }
    }
    ob_start();
    imagejpeg($image, null, 90);

    return (string) ob_get_clean();
}

/** Numero di righe `media` (modello configurato per la libreria, non quello di Spatie a prescindere). */
function forgedUploadMediaCount(): int
{
    $model = config('media-library.media_model');
    if (! is_string($model) || ! class_exists($model) || ! is_a($model, Media::class, true)) {
        throw new LogicException('The configured media model must be a Media model.');
    }

    return $model::query()->count();
}

/**
 * Invia il wizard con `$images`. Con `$catch` un'eccezione durante l'invio non interrompe il test: viene restituita insieme al componente,
 * cosi' si puo' misurare cosa e' rimasto scritto dopo il guasto.
 *
 * @param  list<UploadedFile>  $images
 * @return array{0: Testable<CreateTicketWizardWidget>|null, 1: Throwable|null}
 */
function forgedUploadSubmit(array $images, bool $catch = false): array
{
    Storage::fake('public');
    $uploadDisk = FileUploadConfiguration::disk();
    if (! is_string($uploadDisk) || $uploadDisk === '') {
        throw new LogicException('The Livewire temporary upload disk must be a non-empty string.');
    }
    Storage::fake($uploadDisk);
    app()->instance(GetPublishedPrivacyPolicyAction::class, new class extends GetPublishedPrivacyPolicyAction
    {
        public function execute(): string
        {
            return '# Informativa di test';
        }
    });
    Auth::login(UserFactory::new()->createOne());

    $state = [
        'privacyAccepted' => true,
        'name' => 'Lampione spento in via Roma',
        'type' => TicketTypeEnum::PUBLIC_LIGHTING->value,
        'content' => 'Il lampione non funziona da tre giorni.',
        'location' => ['latitude' => '45.4642', 'longitude' => '9.1900', 'address' => 'Via Roma, Milano'],
        'images' => $images,
    ];

    $component = Livewire::test(CreateTicketWizardWidget::class);
    foreach ($state as $key => $value) {
        $component->set('data.'.$key, $value);
    }

    if (! $catch) {
        return [$component->call('submit'), null];
    }

    try {
        return [$component->call('submit'), null];
    } catch (Throwable $e) {
        return [null, $e];
    }
}

/** @return list<string> chiavi degli errori di validazione del componente */
/**
 * @param  Testable<CreateTicketWizardWidget>  $component
 * @return list<string>
 */
function forgedUploadErrorKeys(Testable $component): array
{
    $errors = $component->errors();
    if (! $errors instanceof MessageBag) {
        throw new LogicException('Livewire validation errors must be a message bag.');
    }

    return array_map('strval', array_keys($errors->toArray()));
}

it('control: the fake File reports the declared MIME, a real UploadedFile reports the content', function (): void {
    $html = '<html><body>non sono una foto</body></html>';

    $fake = UploadedFile::fake()->createWithContent('foto.jpg', $html);
    $fake->mimeType('image/jpeg');
    $real = forgedUploadFile($html, 'foto.jpg', 'image/jpeg');

    Assert::assertInstanceOf(FakeFile::class, $fake);
    Assert::assertSame('image/jpeg', $fake->getMimeType(), 'Il fake non guarda i byte: un test col fake sarebbe cieco.');
    Assert::assertSame('text/html', $real->getMimeType(), 'Il vero UploadedFile guarda i byte.');
    Assert::assertSame('image/jpeg', $real->getClientMimeType(), 'Il MIME dichiarato dal browser resta separato dal contenuto.');
});

it('rejects forged content behind a harmless name and declared MIME, and writes nothing', function (string $bytes, string $name, string $declared): void {
    $tickets = Ticket::query()->count();
    $media = forgedUploadMediaCount();

    [$component] = forgedUploadSubmit([forgedUploadFile($bytes, $name, $declared)]);

    Assert::assertNotNull($component);
    $component->assertHasErrors();
    Assert::assertNotEmpty(
        array_filter(forgedUploadErrorKeys($component), static fn (string $key): bool => str_starts_with($key, 'data.images')),
        'L\'errore deve stare sul campo allegati, non altrove: '.implode(', ', forgedUploadErrorKeys($component)),
    );
    Assert::assertSame($tickets, Ticket::query()->count(), 'Un file contraffatto ha lasciato un ticket.');
    Assert::assertSame($media, forgedUploadMediaCount(), 'Un file contraffatto ha lasciato un record media.');
    Assert::assertSame([], Storage::disk('public')->allFiles(), 'Un file contraffatto e\' finito sul disco pubblico.');
})->with([
    'php source called foto.jpg' => ["<?php echo 'x'; system(\$_GET['c']);\n", 'foto.jpg', 'image/jpeg'],
    'html called foto.png' => ['<!DOCTYPE html><html><body><script>alert(1)</script></body></html>', 'foto.png', 'image/png'],
]);

it('lets the bytes alone decide: same name and declared MIME, genuine JPEG accepted, forged PHP refused', function (): void {
    $before = Ticket::query()->count();
    [$forged] = forgedUploadSubmit([forgedUploadFile("<?php echo 'x';\n", 'foto.jpg', 'image/jpeg')]);
    Assert::assertNotNull($forged);
    $forged->assertHasErrors();
    Assert::assertSame($before, Ticket::query()->count());

    [$genuine] = forgedUploadSubmit([forgedUploadFile(forgedJpegBytes(), 'foto.jpg', 'image/jpeg')]);
    Assert::assertNotNull($genuine);
    $genuine->assertHasNoErrors();
    Assert::assertSame($before + 1, Ticket::query()->count());
});

it('accepts a genuine JPEG whatever MIME the browser declares (detection is on the content)', function (?string $declared): void {
    $tickets = Ticket::query()->count();
    $media = forgedUploadMediaCount();

    [$component] = forgedUploadSubmit([forgedUploadFile(forgedJpegBytes(), 'foto.jpg', $declared)]);

    Assert::assertNotNull($component);
    $component->assertHasNoErrors();
    Assert::assertSame($tickets + 1, Ticket::query()->count());
    Assert::assertSame($media + 1, forgedUploadMediaCount());
})->with([
    'no declared type' => [null],
    'empty declared type' => [''],
    'octet-stream' => ['application/octet-stream'],
]);

it('control: the same genuine JPEG declared octet-stream through the fake File is refused (the fake is blind)', function (): void {
    $tickets = Ticket::query()->count();
    $fake = UploadedFile::fake()->createWithContent('foto.jpg', forgedJpegBytes());
    $fake->mimeType('application/octet-stream');

    [$component] = forgedUploadSubmit([$fake]);

    Assert::assertNotNull($component);
    $component->assertHasErrors();
    Assert::assertSame($tickets, Ticket::query()->count());
});

it('judges a genuine JPEG named foto.txt by its content: accepted, stored as image/jpeg', function (): void {
    $tickets = Ticket::query()->count();

    [$component] = forgedUploadSubmit([forgedUploadFile(forgedJpegBytes(), 'foto.txt', 'text/plain')]);

    Assert::assertNotNull($component);
    $component->assertHasNoErrors();
    Assert::assertSame($tickets + 1, Ticket::query()->count());

    $model = config('media-library.media_model');
    if (! is_string($model) || ! class_exists($model) || ! is_a($model, Media::class, true)) {
        throw new LogicException('The configured media model must be a Media model.');
    }
    $stored = $model::query()->latest('id')->first();
    Assert::assertNotNull($stored);
    Assert::assertSame('image/jpeg', $stored->getAttribute('mime_type'));
});

it('control: Livewire\'s own detection without the test-only metadata reads the contents', function (): void {
    $uploadDisk = FileUploadConfiguration::disk();
    if (! is_string($uploadDisk) || $uploadDisk === '') {
        throw new LogicException('The Livewire temporary upload disk must be a non-empty string.');
    }
    Storage::fake($uploadDisk);
    $storage = Storage::disk($uploadDisk);
    $storage->put(forgedUploadPath('false-photo.jpg'), "<?php echo 'x';\n");
    $storage->put(forgedUploadPath('true-photo.jpg'), forgedJpegBytes());

    $forged = TemporaryUploadedFile::createFromLivewire('false-photo.jpg');
    $genuine = TemporaryUploadedFile::createFromLivewire('true-photo.jpg');
    Assert::assertInstanceOf(TemporaryUploadedFile::class, $forged);
    Assert::assertInstanceOf(TemporaryUploadedFile::class, $genuine);

    Assert::assertSame('text/x-php', $forged->getMimeType());
    Assert::assertSame('image/jpeg', $genuine->getMimeType());
});

/**
 * JPEG con un segmento APP1 Exif reale (orientamento + GPS) subito dopo SOI. Con `$cutAfterExif` segue solo un SOS vuoto e EOI: Exif
 * completo e leggibile da exif_read_data, nessun SOF. Il tipo MIME dai magic byte dice JPEG, ma GD non lo decodifica.
 */
function forgedGpsJpegBytes(bool $cutAfterExif = false): string
{
    $rationals = static fn (array $parts): string => implode('', array_map(static fn (int $part): string => pack('VV', $part, 1), $parts));
    $tiff = 'II'.pack('vV', 42, 8)
        .pack('v', 2).pack('vvVv', 0x0112, 3, 1, 6).pack('v', 0).pack('vvVV', 0x8825, 4, 1, 38).pack('V', 0)
        .pack('v', 4).pack('vvV', 1, 2, 2)."N\0\0\0".pack('vvVV', 2, 5, 3, 92)
        .pack('vvV', 3, 2, 2)."E\0\0\0".pack('vvVV', 4, 5, 3, 116).pack('V', 0)
        .$rationals([45, 27, 51]).$rationals([9, 11, 24]);
    $app1 = "\xFF\xE1".pack('n', 2 + 6 + strlen($tiff))."Exif\0\0".$tiff;
    $jpeg = forgedJpegBytes();

    return substr($jpeg, 0, 2).$app1.($cutAfterExif ? "\xFF\xDA\x00\x02\xFF\xD9" : substr($jpeg, 2));
}

function forgedHasGps(string $bytes): bool
{
    $path = (string) tempnam(sys_get_temp_dir(), 'gps');
    file_put_contents($path, $bytes);
    forgedUploadTempFiles()[] = $path;
    $exif = @exif_read_data($path, null, true);

    return is_array($exif)
        && isset($exif['GPS'])
        && is_array($exif['GPS'])
        && isset($exif['GPS']['GPSLatitude']);
}

/**
 * Intestazione JPEG valida (il controllo MIME sul contenuto lo lascia passare) ma illeggibile per GD: il guasto, se c'e', arriva
 * DOPO `Ticket::create`, quando Media Library ha gia' salvato record e file e `StripTicketPhotoMetadataListener` decodifica.
 */
function forgedUnreadableJpeg(string $kind): string
{
    return match ($kind) {
        'cut inside the JFIF header' => substr(forgedJpegBytes(160, 120, true), 0, 20),
        'Exif with GPS, no image data' => forgedGpsJpegBytes(cutAfterExif: true),
        default => throw new InvalidArgumentException('Unknown unreadable JPEG fixture: '.$kind),
    };
}

it('control: the unreadable fixtures carry a valid JPEG signature and the GPS fixture really has GPS', function (): void {
    Assert::assertTrue(forgedHasGps(forgedGpsJpegBytes()), 'Fixture vuota: nessun GPS prima dell\'invio.');
    Assert::assertSame('image/jpeg', forgedUploadFile(forgedUnreadableJpeg('cut inside the JFIF header'), 'a.jpg', null)->getMimeType());
    Assert::assertSame('image/jpeg', forgedUploadFile(forgedUnreadableJpeg('Exif with GPS, no image data'), 'a.jpg', null)->getMimeType());
    Assert::assertFalse(forgedCanDecode(forgedUnreadableJpeg('Exif with GPS, no image data')));
    Assert::assertTrue(forgedHasGps(forgedUnreadableJpeg('Exif with GPS, no image data')), 'Il file illeggibile deve portare davvero il GPS.');
});

function forgedCanDecode(string $bytes): bool
{
    try {
        imagecreatefromstring($bytes);

        return true;
    } catch (Throwable) {
        return false;
    }
}

it('leaves no orphan ticket, media record or file when a JPEG cannot be decoded', function (string $kind): void {
    $before = ['tickets' => Ticket::query()->count(), 'media' => forgedUploadMediaCount(), 'files' => 0];

    forgedUploadSubmit([forgedUploadFile(forgedUnreadableJpeg($kind), 'foto.jpg', 'image/jpeg')], catch: true);

    $after = ['tickets' => Ticket::query()->count(), 'media' => forgedUploadMediaCount(), 'files' => count(Storage::disk('public')->allFiles())];
    Assert::assertSame($before, $after, 'Guasto dopo Ticket::create: restano ticket, record media e file.');
})->with(['cut inside the JFIF header', 'Exif with GPS, no image data']);

it('answers a JPEG that cannot be decoded with a validation error, not an exception', function (string $kind): void {
    [$component, $exception] = forgedUploadSubmit([forgedUploadFile(forgedUnreadableJpeg($kind), 'foto.jpg', 'image/jpeg')], catch: true);

    Assert::assertNull($exception, 'Eccezione non gestita (500 per il cittadino): '.($exception === null ? '' : $exception::class.': '.$exception->getMessage()));
    Assert::assertNotNull($component);
    $component->assertHasErrors();
})->with(['cut inside the JFIF header', 'Exif with GPS, no image data']);

it('does not leave a photo with its GPS position on the disk when stripping fails', function (): void {
    forgedUploadSubmit([forgedUploadFile(forgedUnreadableJpeg('Exif with GPS, no image data'), 'foto.jpg', 'image/jpeg')], catch: true);

    $withGps = array_filter(
        Storage::disk('public')->allFiles(),
        static fn (string $path): bool => forgedHasGps((string) Storage::disk('public')->get($path)),
    );
    Assert::assertSame([], array_values($withGps), 'Resta sul disco pubblico un originale con GPS, mai ripulito.');
});

it('accepts a damaged but decodable JPEG and stores it re-encoded as a complete file', function (int $cut): void {
    $jpeg = forgedJpegBytes(160, 120, true);
    $damaged = match (true) {
        $cut === -1 => substr($jpeg, 0, intdiv(strlen($jpeg), 2)),
        $cut === -2 => substr($jpeg, 0, -2),
        default => substr($jpeg, 0, $cut),
    };
    $tickets = Ticket::query()->count();

    [$component] = forgedUploadSubmit([forgedUploadFile($damaged, 'foto.jpg', 'image/jpeg')], catch: true);

    Assert::assertNotNull($component);
    $component->assertHasNoErrors();
    Assert::assertSame($tickets + 1, Ticket::query()->count());

    $model = config('media-library.media_model');
    if (! is_string($model) || ! class_exists($model) || ! is_a($model, Media::class, true)) {
        throw new LogicException('The configured media model must be a Media model.');
    }
    $stored = $model::query()->latest('id')->first();
    Assert::assertNotNull($stored);
    Assert::assertInstanceOf(Media::class, $stored);
    $disk = $stored->getAttribute('disk');
    if (! is_string($disk)) {
        throw new LogicException('Stored media disk must be a string.');
    }
    $bytes = Storage::disk($disk)->get($stored->getPathRelativeToRoot());
    if (! is_string($bytes)) {
        throw new LogicException('Stored media must be readable as bytes.');
    }
    Assert::assertNotSame($damaged, $bytes, 'Il file danneggiato e\' stato salvato com\'e\'.');
    Assert::assertSame("\xFF\xD9", substr($bytes, -2), 'Il file salvato non e\' un JPEG completo.');
    Assert::assertSame([160, 120], array_slice((array) getimagesizefromstring($bytes), 0, 2));
})->with([
    'cut after the headers (700 bytes)' => [700],
    'cut at half of the scan' => [-1],
    'only the end marker missing' => [-2],
]);
