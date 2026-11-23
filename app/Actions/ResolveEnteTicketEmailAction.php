<?php

declare(strict_types=1);

namespace Modules\Fixcity\Actions;

use Spatie\QueueableAction\QueueableAction;

/**
 * Resolve the tenant's explicit report-intake mailbox without falling back to public contact addresses.
 *
 * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
 */
final class ResolveEnteTicketEmailAction
{
    use QueueableAction;

    /**
     * @see docs/stories/dcp-284-notifiche-del-ciclo-di-vita-della-segnalazione.story.md
     */
    public function execute(): ?string
    {
        $configured = config('ente.ticket_reports_email');

        if (! is_string($configured)) {
            return null;
        }

        $email = trim($configured);
        if ($email === '' || preg_match('/[\r\n]/', $email) === 1 || strpbrk($email, '?&%') !== false) {
            return null;
        }

        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false ? $email : null;
    }
}
