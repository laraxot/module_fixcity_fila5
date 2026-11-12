<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Contracts\Auth\Authenticatable;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Media\Models\Media;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;

use function Safe\parse_url;

/**
 * URL delle foto di un ticket da mostrare in front office: UNICA fonte per dettaglio pubblico, popup della mappa
 * (/api/ticket-details/{id}), elenco (/api/tickets/list) e blocco `segnalazioni` del tema (SEC-04).
 *
 * - Solo se il ticket e' pubblico (stato in `TicketStatusEnum::canViewByAll()`) o se chi guarda e' il proprietario.
 * - Solo immagini (`image/*`): i PDF e gli altri file non escono mai dalle API pubbliche.
 * - Percorsi relativi (DCP-379, `APP_URL` di produzione non e' l'host pubblico):
 *   disco `public` -> `/storage/...`; disco privato dei ticket -> `/api/ticket-media/...` (route autorizzata).
 *
 * `$accessGranted = true` solo quando il chiamante ha GIA' autorizzato chi guarda per altra via (codice di tracciamento,
 * `Gate::authorize('view')` nella pagina track): allora escono le foto in qualunque stato e quelle del disco privato hanno una
 * firma a scadenza ({@see SignTicketMediaUrlAction}), perche' la route dei media non conosce il codice.
 *
 * Il chiamante che elabora molti ticket deve caricare `media` con `with('media')` (DB-13).
 */
final class BuildTicketPublicImageUrlsAction
{
    use QueueableAction;

    /** Collezioni lette, in ordine: la seconda solo se la prima e' vuota (dati demo/legacy). */
    public const array COLLECTIONS = ['attachments', 'ticket'];

    public function __construct(
        private readonly ResolveTicketMediaDiskAction $resolveDisk,
        private readonly SignTicketMediaUrlAction $signUrl,
    ) {}

    /**
     * @return list<string>
     */
    public function execute(Ticket $ticket, ?Authenticatable $viewer = null, bool $accessGranted = false): array
    {
        if (! $accessGranted && ! $this->isPublic($ticket) && ! $this->isOwner($ticket, $viewer)) {
            return [];
        }

        $mediaItems = $ticket->getMedia(self::COLLECTIONS[0]);
        if ($mediaItems->isEmpty()) {
            $mediaItems = $ticket->getMedia(self::COLLECTIONS[1]);
        }

        $urls = [];
        foreach ($mediaItems as $media) {
            if (! $media instanceof Media || ! str_starts_with(SafeStringCastAction::cast($media->mime_type), 'image/')) {
                continue;
            }

            $url = $this->url($media, $accessGranted);
            if ($url !== '') {
                $urls[] = $url;
            }
        }

        return array_values(array_unique($urls));
    }

    public function url(Media $media, bool $signed = false): string
    {
        $privateDisk = $this->resolveDisk->configuredName();
        if ($privateDisk !== null && $media->disk === $privateDisk) {
            // Costruito qui e non con getUrl(): non dipende dalla chiave `url` del disco (Storage::fake la perde).
            $path = ResolveTicketMediaDiskAction::URL_PREFIX.'/'.ltrim($media->getPathRelativeToRoot(), '/');

            return $signed ? $this->signUrl->execute($path) : $path;
        }

        // Dischi locali (public o altri `local`): solo il percorso. L'URL assoluto nasce da APP_URL e, se APP_URL non e' il
        // dominio pubblico, il browser non raggiunge l'host (issue module_fixcity_fila5#71). Assoluto solo per dischi remoti (S3...).
        if ($media->disk === 'public' || config('filesystems.disks.'.$media->disk.'.driver') === 'local') {
            $path = parse_url($media->getUrl(), PHP_URL_PATH);

            return is_string($path) ? $path : '';
        }

        return $media->getFullUrl();
    }

    private function isPublic(Ticket $ticket): bool
    {
        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue());

        return $status instanceof TicketStatusEnum && in_array($status, TicketStatusEnum::canViewByAll(), true);
    }

    private function isOwner(Ticket $ticket, ?Authenticatable $viewer): bool
    {
        if ($viewer === null || $ticket->owner_id === null) {
            return false;
        }

        return SafeStringCastAction::cast($ticket->owner_id) === SafeStringCastAction::cast($viewer->getAuthIdentifier());
    }
}
