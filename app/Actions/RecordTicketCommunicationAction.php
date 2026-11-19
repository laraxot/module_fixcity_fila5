<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Fixcity\Emails\TicketCommunicationMail;
use Modules\Fixcity\Enums\TicketActivityEventTypeEnum;
use Modules\Fixcity\Enums\TicketActivityVisibilityEnum;
use Modules\Fixcity\Enums\TicketStatusEnum;
use Modules\Fixcity\Models\Ticket;
use Modules\Fixcity\Models\TicketActivity;
use Spatie\QueueableAction\QueueableAction;
use Throwable;

final class RecordTicketCommunicationAction
{
    use QueueableAction;

    public function send(
        Ticket $ticket,
        string $recipient,
        string $recipientLabel,
        string $subject,
        string $body,
        bool $followUp = false,
        ?string $idempotencyKey = null,
    ): TicketActivity {
        Gate::authorize('communicate', $ticket);
        $recipient = trim($recipient);
        $recipientLabel = trim($recipientLabel);
        $subject = trim($subject);
        $body = trim($body);

        if (filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Il destinatario deve essere un indirizzo email valido.');
        }
        if ($recipientLabel === '' || $subject === '' || $body === '') {
            throw new \InvalidArgumentException('Destinatario, oggetto e messaggio sono obbligatori.');
        }
        if (mb_strlen($subject) > 255 || mb_strlen($body) > 20_000 || mb_strlen($recipientLabel) > 255) {
            throw new \InvalidArgumentException('La comunicazione supera la lunghezza consentita.');
        }

        $idempotencyKey = trim($idempotencyKey ?? '') ?: (string) Str::uuid();
        $existing = $this->findByIdempotencyKey($ticket, $idempotencyKey);
        if ($existing instanceof TicketActivity) {
            return $existing;
        }

        $eventType = $followUp
            ? TicketActivityEventTypeEnum::Followup
            : TicketActivityEventTypeEnum::MailSent;

        try {
            Mail::to($recipient)->send(new TicketCommunicationMail($body, $subject));
        } catch (Throwable $exception) {
            Log::error('Fixcity ticket communication failed.', [
                'ticket_id' => $ticket->getKey(),
                'recipient' => $recipient,
                'exception' => $exception::class,
            ]);

            return $this->record(
                $ticket,
                TicketActivityEventTypeEnum::MailFailed,
                $recipient,
                $recipientLabel,
                $subject,
                $idempotencyKey,
                'failed',
            );
        }

        return $this->record(
            $ticket,
            $eventType,
            $recipient,
            $recipientLabel,
            $subject,
            $idempotencyKey,
            'sent',
        );
    }

    public function recordInbound(
        Ticket $ticket,
        string $senderLabel,
        string $summary,
        ?string $idempotencyKey = null,
    ): TicketActivity {
        Gate::authorize('communicate', $ticket);
        $senderLabel = trim($senderLabel);
        $summary = trim($summary);
        if ($senderLabel === '' || $summary === '' || mb_strlen($summary) > 2_000) {
            throw new \InvalidArgumentException('Mittente e sintesi della risposta sono obbligatori.');
        }

        $idempotencyKey = trim($idempotencyKey ?? '') ?: (string) Str::uuid();
        $existing = $this->findByIdempotencyKey($ticket, $idempotencyKey);
        if ($existing instanceof TicketActivity) {
            return $existing;
        }

        $status = $this->currentStatus($ticket);
        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        return app(RecordTicketActivityAction::class)->execute(
            $ticket,
            TicketActivityEventTypeEnum::MailReceived,
            $status,
            $status,
            $summary,
            $this->visibility($status),
            $userId,
            [
                'sender_label' => $senderLabel,
                'idempotency_key' => $idempotencyKey,
                'status' => 'received',
            ],
        );
    }

    private function record(
        Ticket $ticket,
        TicketActivityEventTypeEnum $eventType,
        string $recipient,
        string $recipientLabel,
        string $subject,
        string $idempotencyKey,
        string $deliveryStatus,
    ): TicketActivity {
        $status = $this->currentStatus($ticket);
        $actorId = auth()->id();
        $userId = is_int($actorId) || is_string($actorId) ? $actorId : null;

        return app(RecordTicketActivityAction::class)->execute(
            $ticket,
            $eventType,
            $status,
            $status,
            null,
            $this->visibility($status),
            $userId,
            [
                // The raw address is internal activity data; the public mapper never renders it.
                'recipient' => $recipient,
                'recipient_label' => $recipientLabel,
                'subject' => $subject,
                'idempotency_key' => $idempotencyKey,
                'status' => $deliveryStatus,
            ],
        );
    }

    private function findByIdempotencyKey(Ticket $ticket, string $key): ?TicketActivity
    {
        return $ticket->activities()->get()->first(
            static fn (TicketActivity $activity): bool => ($activity->payload['idempotency_key'] ?? null) === $key,
        );
    }

    private function currentStatus(Ticket $ticket): TicketStatusEnum
    {
        return TicketStatusEnum::tryFrom($ticket->resolveTicketStatusValue()) ?? TicketStatusEnum::PENDING;
    }

    private function visibility(TicketStatusEnum $status): TicketActivityVisibilityEnum
    {
        return in_array($status, TicketStatusEnum::canViewByAll(), true)
            ? TicketActivityVisibilityEnum::Public
            : TicketActivityVisibilityEnum::AuthorOnly;
    }
}
