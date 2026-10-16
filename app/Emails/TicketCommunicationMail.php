<?php

declare(strict_types=1);

namespace Modules\Fixcity\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

final class TicketCommunicationMail extends Mailable
{
    public function __construct(
        public readonly string $body,
        private readonly string $subjectLine,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->subjectLine);
    }

    public function content(): Content
    {
        return new Content(text: 'fixcity::emails.ticket-communication');
    }
}
