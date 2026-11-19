<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

enum TicketActivityEventTypeEnum: string
{
    case StatusChange = 'status_change';

    case Assignment = 'assignment';

    /** A moderator approved the ticket (visible only to owner + moderator). */
    case Approved = 'approved';

    /** A moderator rejected the ticket. */
    case Rejected = 'rejected';

    /** An outbound email was sent to the assignee. */
    case MailSent = 'mail_sent';

    /** An outbound email attempt failed. */
    case MailFailed = 'mail_failed';

    /** A reply from the assignee was received. */
    case MailReceived = 'mail_received';

    /** A follow-up email was sent to nudge the assignee. */
    case Followup = 'followup';

    /** New media (photos/documents) were attached to the ticket. */
    case MediaAdded = 'media_added';

    /** Ticket was closed with a resolution reason. */
    case Closed = 'closed';
}
