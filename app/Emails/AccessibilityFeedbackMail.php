<?php

declare(strict_types=1);

namespace Modules\Fixcity\Emails;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/**
 * Segnalazione di un difetto di accessibilita' inviata dal modulo della dichiarazione (DCP-132) alla casella indicata dall'ente.
 * Testo semplice: il corpo e' scritto da un visitatore e non diventa mai HTML. Il contatto del cittadino, se c'e', e' il reply-to.
 */
final class AccessibilityFeedbackMail extends Mailable
{
    public function __construct(
        public readonly string $problem,
        public readonly string $page,
        public readonly string $contact,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: (string) __('fixcity::accessibility.mail.subject'),
            replyTo: $this->contact === '' ? [] : [new Address($this->contact)],
        );
    }

    public function content(): Content
    {
        return new Content(text: 'fixcity::emails.accessibility-feedback');
    }
}
