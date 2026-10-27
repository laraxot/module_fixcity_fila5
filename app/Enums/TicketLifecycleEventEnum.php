<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Modules\Fixcity\Notifications\TicketStatusChangedNotification;

/**
 * Eventi del ciclo di vita di cui il proprietario di una segnalazione viene avvisato (DCP-284, A-01).
 *
 * Il cambio di stato pubblico (`status_change`) NON e' qui: ha gia' la sua notifica ({@see TicketStatusChangedNotification})
 * e tenerlo fuori da questo insieme e' cio' che impedisce due messaggi per lo stesso cambio. Gli allegati (`media_added`) non
 * notificano: nel wizard li carica il proprietario stesso.
 */
enum TicketLifecycleEventEnum: string
{
    case Received = 'received';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Assigned = 'assigned';
    case Closed = 'closed';

    /**
     * L'evento del ciclo di vita che una riga di audit rappresenta, o `null` se non genera un avviso al proprietario.
     * `Received` non nasce da un'attivita' (la segnalazione appena creata non ne ha): arriva da `TicketCreatedEvent`.
     */
    public static function fromActivityEventType(string $eventType): ?self
    {
        return match ($eventType) {
            TicketActivityEventTypeEnum::Approved->value => self::Approved,
            TicketActivityEventTypeEnum::Rejected->value => self::Rejected,
            TicketActivityEventTypeEnum::Assignment->value => self::Assigned,
            TicketActivityEventTypeEnum::Closed->value => self::Closed,
            default => null,
        };
    }

    /**
     * Etichetta breve nella lingua corrente ("ha un nuovo stato: Presa in carico"), usata anche dall'elenco dell'area personale.
     */
    public function label(): string
    {
        return (string) __('fixcity::ticket_notification.event.'.$this->value);
    }
}
