<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Modules\Fixcity\Actions\CanViewTicketMediaAction;
use Modules\Fixcity\Actions\ResolveTicketMediaDiskAction;
use Modules\Fixcity\Actions\SignTicketMediaUrlAction;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Symfony\Component\HttpFoundation\Response;
use function Laravel\Folio\middleware;
use function Laravel\Folio\name;
use function Laravel\Folio\render;

/*
 * GET /api/ticket-media/{media_id}/{file} (anche `.../conversions/...`): allegati dei ticket sul disco privato (SEC-04, DB-19).
 *
 * L'id nel percorso e' enumerabile ma non basta: ogni richiesta passa da CanViewTicketMediaAction (foto: TicketPolicy::view;
 * PDF: proprietario o back office), oppure, per le sole foto, da una firma a scadenza (SignTicketMediaUrlAction: pagina track con
 * codice di tracciamento). Qualunque rifiuto risponde 404, cosi' non si scopre quali id esistono.
 * I media ancora sul disco `public` non passano di qui: restano su /storage finche' non vengono spostati (story
 * public-api-media-and-geojson, sezione Aperto).
 */
name('api.ticket-media');
middleware(['throttle:240,1']);

render(function (array $segments): Response {
    $privateDisk = app(ResolveTicketMediaDiskAction::class)->configuredName();
    abort_if($privateDisk === null, 404);

    /** @var list<string> $parts */
    $parts = array_values(array_map(static fn (mixed $part): string => SafeStringCastAction::cast($part), $segments));
    foreach ($parts as $part) {
        abort_if($part === '' || $part === '.' || $part === '..' || str_contains($part, '\\') || str_contains($part, "\0"), 404);
    }

    // Con `media-library.prefix` (MEDIA_PREFIX) il percorso e' `<prefix>/<id>/<file>`: l'id viene dopo il prefisso.
    $prefixParts = array_values(array_filter(explode('/', SafeStringCastAction::cast(config('media-library.prefix', ''))), static fn (string $p): bool => $p !== ''));
    abort_unless(array_slice($parts, 0, count($prefixParts)) === $prefixParts, 404);
    $idParts = array_slice($parts, count($prefixParts));
    abort_if(count($idParts) < 2 || ! ctype_digit($idParts[0]), 404);

    $media = Media::query()->whereKey((int) $idParts[0])->first();
    abort_unless($media instanceof Media, 404);

    $ticket = Ticket::query()->whereKey($media->model_id)->first();
    abort_unless($ticket instanceof Ticket, 404);
    $canView = app(CanViewTicketMediaAction::class);
    abort_unless($canView->belongsTo($media, $ticket), 404);
    $isImage = str_starts_with(SafeStringCastAction::cast($media->mime_type), 'image/');

    // Solo l'originale o i file derivati dentro la cartella di QUESTO media.
    $requested = implode('/', $parts);
    $hasValidSignature = $isImage && app(SignTicketMediaUrlAction::class)->isValid(
        ResolveTicketMediaDiskAction::URL_PREFIX.'/'.$requested,
        request()->query('expires'),
        request()->query('signature'),
    );
    abort_unless($hasValidSignature || $canView->execute($media, $ticket, auth()->user()), 404);

    $original = ltrim($media->getPathRelativeToRoot(), '/');
    $mediaDir = dirname($original);
    $isDerived = str_starts_with($requested, $mediaDir.'/conversions/')
        || str_starts_with($requested, $mediaDir.'/responsive-images/');
    abort_unless($requested === $original || $isDerived, 404);

    // Originale da `disk`, miniature da `conversions_disk` (un tenant puo' separarli): si serve solo cio' che sta sul disco privato.
    $sourceDisk = $isDerived
        ? SafeStringCastAction::cast($media->conversions_disk ?: $media->disk)
        : SafeStringCastAction::cast($media->disk);
    abort_unless($sourceDisk === $privateDisk, 404);

    $disk = Storage::disk($sourceDisk);
    abort_unless($disk->exists($requested), 404);

    return $disk->response(
        $requested,
        basename($requested),
        [
            // privato: mai in cache condivise (proxy/CDN), il permesso dipende da chi guarda e dallo stato del ticket
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ],
        $isImage ? 'inline' : 'attachment',
    );
});
