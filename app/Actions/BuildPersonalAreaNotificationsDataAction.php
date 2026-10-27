<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Carbon\CarbonInterface;
use Modules\Fixcity\Enums\TicketLifecycleEventEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\User\Models\Notification;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Modules\Xot\Contracts\UserContract;
use Spatie\QueueableAction\QueueableAction;

/**
 * Dati della pagina `/{lang}/area-personale/notifiche` (DCP-111): gli aggiornamenti di stato che il sistema ha davvero
 * registrato per il cittadino. Stato e link si ricalcolano nella lingua corrente a partire da `ticket_code` e `status`
 * (il testo salvato e' nella lingua di chi ha cambiato lo stato); il link del proprietario usa il codice di tracciamento, mai l'id del
 * database. Le notifiche di chi SEGUE (DCP-287) non hanno il codice, che e' una capability: `ticket_code` e' vuoto, `ticket_label` e' il
 * titolo della segnalazione e `track_url` e' il link al dettaglio pubblico. `ticket_label` e' sempre il nome da mostrare.
 * Dal DCP-284 le righe del ciclo di vita (`event`: ricevuta, approvata, scartata, presa in carico, chiusa) mostrano l'etichetta
 * dell'evento al posto di quella di stato: la frase del tema resta "ha un nuovo stato: <etichetta>".
 */
final class BuildPersonalAreaNotificationsDataAction
{
    use QueueableAction;

    public const int LIMIT = 50;

    /**
     * @return array{
     *     unread_count: int,
     *     has_more: bool,
     *     items: list<array{id: string, ticket_code: string, ticket_label: string, status_label: string, occurred_at: string, occurred_iso: string, is_read: bool, track_url: string}>
     * }
     */
    public function execute(UserContract $user): array
    {
        $query = app(BuildAuthenticatedUserTicketNotificationsQueryAction::class)->execute($user);

        $notifications = (clone $query)->latest()->orderByDesc('id')->limit(self::LIMIT + 1)->get();
        $items = [];
        foreach ($notifications->take(self::LIMIT) as $notification) {
            $item = $this->item($notification);
            if ($item !== null) {
                $items[] = $item;
            }
        }

        return [
            'unread_count' => (clone $query)->whereNull('read_at')->count(),
            'has_more' => $notifications->count() > self::LIMIT,
            'items' => $items,
        ];
    }

    /** @return array{id: string, ticket_code: string, ticket_label: string, status_label: string, occurred_at: string, occurred_iso: string, is_read: bool, track_url: string}|null */
    private function item(Notification $notification): ?array
    {
        $data = $notification->getAttribute('data');
        if (! is_array($data)) {
            return null;
        }
        $code = is_string($data['ticket_code'] ?? null) ? trim($data['ticket_code']) : '';
        $ticketId = is_string($data['ticket_id'] ?? null) && ctype_digit($data['ticket_id']) ? (int) $data['ticket_id'] : null;
        $title = is_string($data['ticket_title'] ?? null) ? trim($data['ticket_title']) : '';
        $status = is_string($data['status'] ?? null) ? TicketStatusEnum::tryFrom($data['status']) : null;
        if (! $status instanceof TicketStatusEnum || ($code === '' && ($ticketId === null || $title === ''))) {
            return null;
        }
        $event = is_string($data['event'] ?? null) ? TicketLifecycleEventEnum::tryFrom($data['event']) : null;
        $createdAt = $notification->getAttribute('created_at');

        return [
            'id' => SafeStringCastAction::cast($notification->getKey()),
            'ticket_code' => $code,
            'ticket_label' => $code !== '' ? $code : (string) __('fixcity::personal_area_settings.notifications.followed_label', ['title' => $title]),
            'status_label' => $event instanceof TicketLifecycleEventEnum ? $event->label() : $status->getLabel(),
            'occurred_at' => $createdAt instanceof CarbonInterface ? $createdAt->translatedFormat('d F Y H:i') : '',
            'occurred_iso' => $createdAt instanceof CarbonInterface ? $createdAt->toIso8601String() : '',
            'is_read' => $notification->getAttribute('read_at') !== null,
            'track_url' => $code !== '' || $ticketId === null
                ? app(BuildTicketTrackUrlAction::class)->execute($code)
                : app(BuildTicketDetailUrlAction::class)->execute($ticketId),
        ];
    }
}
