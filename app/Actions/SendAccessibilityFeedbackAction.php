<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Illuminate\Support\Facades\Mail;
use Modules\Fixcity\Emails\AccessibilityFeedbackMail;
use Spatie\QueueableAction\QueueableAction;

/**
 * Spedisce alla casella dell'ente la segnalazione di accessibilita' (DCP-132). Il destinatario lo sceglie il chiamante leggendo la
 * dichiarazione lato server: questa Action non lo prende mai da dati del visitatore.
 */
final class SendAccessibilityFeedbackAction
{
    use QueueableAction;

    public function execute(string $recipient, string $problem, string $page = '', string $contact = ''): void
    {
        Mail::to($recipient)->send(new AccessibilityFeedbackMail($problem, $page, $contact));
    }
}
