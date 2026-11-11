<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Disco privato degli allegati dei ticket (SEC-04, DB-19).
 *
 * I file NON stanno sotto `/storage` (disco `public`): si leggono solo da GET /api/ticket-media/{media_id}/{file}
 * (`resources/views/pages/api/ticket-media/[...segments].blade.php`), che applica {@see CanViewTicketMediaAction}.
 *
 * Il nome del disco viene da `fixcity.config.media.private_disk` (env `FIXCITY_TICKET_MEDIA_DISK`); stringa vuota = disattivato,
 * i nuovi allegati tornano sul disco predefinito di Media Library. Se il disco non e' definito in `filesystems.disks`,
 * FixcityServiceProvider registra {@see self::defaultDiskConfig()}.
 */
final class ResolveTicketMediaDiskAction
{
    use QueueableAction;

    public const string DEFAULT_DISK = 'fixcity_ticket_media';

    /** Prefisso della route autorizzata che serve i file del disco privato. */
    public const string URL_PREFIX = '/api/ticket-media';

    /**
     * @return string|null nome del disco privato, null se disattivato o non configurato
     */
    public function execute(): ?string
    {
        $disk = $this->configuredName();
        if ($disk === null) {
            return null;
        }

        return is_array(config('filesystems.disks.'.$disk)) ? $disk : null;
    }

    /**
     * Nome configurato, anche se il disco non e' (ancora) registrato.
     */
    public function configuredName(): ?string
    {
        // Prima la config della radice (cache-safe, letta con env()); poi quella del modulo per i tenant che la impostano lì.
        $disk = config('filesystems.fixcity_ticket_media_disk', config('fixcity.config.media.private_disk', self::DEFAULT_DISK));

        return is_string($disk) && $disk !== '' ? $disk : null;
    }

    /**
     * Disco locale fuori dalla webroot, senza `serve`: l'unico accesso HTTP e' la route autorizzata.
     *
     * @return array{driver: string, root: string, url: string, visibility: string, throw: bool}
     */
    public static function defaultDiskConfig(): array
    {
        return [
            'driver' => 'local',
            'root' => storage_path('app/private/fixcity-ticket-media'),
            'url' => self::URL_PREFIX,
            'visibility' => 'private',
            'throw' => false,
        ];
    }
}
