<?php

/**
 * Ticket Status Enum - Stati del ticket.
 */

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

enum TicketStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case DRAFT = 'draft';
    case PENDING = 'pending';
    case IN_REVIEW = 'in_review';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REOPENED = 'reopened';
    case OPEN = 'open';

    /** @return list<self> */
    public static function canViewByAll(): array
    {
        return [
            self::OPEN,
            self::IN_PROGRESS,
            self::ON_HOLD,
            self::RESOLVED,
            self::CLOSED,
            self::REOPENED,
        ];
    }

    /** @return list<self> */
    public static function canNoViewByAll(): array
    {
        return [
            self::DRAFT,
            self::PENDING,
            self::IN_REVIEW,
            self::IN_PROGRESS,
        ];
    }

    public static function default(): static
    {
        return self::OPEN;
    }
}