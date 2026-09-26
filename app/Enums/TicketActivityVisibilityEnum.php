<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

enum TicketActivityVisibilityEnum: string
{
    case Public = 'public';
    case Internal = 'internal';
    case AuthorOnly = 'author_only';
}
