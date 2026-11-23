<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Fixcity\Emails\TicketCommunicationMail;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Modules\Xot\Actions\Cast\SafeStringCastAction;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

/**
 * Optional tenant email intake. A successful SMTP handoff is NOT an acknowledgement from the authority.
 *
 * The mailbox must be explicitly enabled in ente.ticket_reports_email. Neither the generic
 * contact address nor a PEC address is used implicitly. Only the internal ticket number
 * is transmitted; citizen identity, tracking code, photos and location are not included.
 *
 * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
 */
final class SendTicketToEnteAction
{
    use QueueableAction;

    /**
     * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
     */
    public function execute(Ticket $ticket): bool
    {
        $recipient = app(ResolveEnteTicketEmailAction::class)->execute();
        if ($recipient === null) {
            return false;
        }

        if ($this->wasSent($ticket)) {
            return true;
        }

        try {
            Mail::to($recipient)->send(new TicketCommunicationMail(
                'Nuova segnalazione FixCity #'.SafeStringCastAction::cast($ticket->getKey()).".\n"
                    .'Accedere al pannello riservato per consultare la segnalazione.',
                'FixCity - Nuova segnalazione #'.SafeStringCastAction::cast($ticket->getKey()),
            ));
        } catch (Throwable $exception) {
            $this->recordTransportResult($ticket, TicketActivityEventTypeEnum::MailFailed);
            Log::warning('Fixcity authority ticket email transport failed.', [
                'ticket_id' => $ticket->getKey(),
                'exception' => $exception::class,
            ]);

            return false;
        }

        $this->recordTransportResult($ticket, TicketActivityEventTypeEnum::MailSent);

        return true;
    }

    /**
     * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
     */
    private function wasSent(Ticket $ticket): bool
    {
        return TicketActivity::query()
            ->where('ticket_id', $ticket->getKey())
            ->where('event_type', TicketActivityEventTypeEnum::MailSent->value)
            ->get()
            ->contains(static fn (TicketActivity $activity): bool => (($activity->payload ?? [])['source'] ?? null) === 'ente_ticket_intake');
    }

    /**
     * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
     */
    private function recordTransportResult(Ticket $ticket, TicketActivityEventTypeEnum $eventType): void
    {
        $status = TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;

        try {
            app(RecordTicketActivityAction::class)->execute(
                $ticket,
                $eventType,
                $status,
                $status,
                $eventType === TicketActivityEventTypeEnum::MailSent
                    ? 'Invio alla casella ente accettato dal trasporto email'
                    : 'Invio alla casella ente non riuscito',
                TicketActivityVisibilityEnum::Internal,
                null,
                ['source' => 'ente_ticket_intake', 'channel' => 'mail'],
            );
        } catch (Throwable $exception) {
            Log::error('Fixcity authority ticket email audit failed.', [
                'ticket_id' => $ticket->getKey(),
                'exception' => $exception::class,
            ]);
        }
    }
}
