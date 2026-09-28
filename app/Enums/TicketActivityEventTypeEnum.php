<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

enum TicketActivityEventTypeEnum: string
{
    case StatusChange = 'status_change';

    case Assignment = 'assignment';
}
