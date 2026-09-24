<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

use Modules\Xot\Traits\EnumTrait;

enum TicketPriorityEnum: string implements HasColor, HasIcon, HasLabel
{
    use EnumTrait;

    case LOW = 'low';
    case MEDIUM = 'medium';
    case HIGH = 'high';
    case CRITICAL = 'critical';
    case URGENT = 'urgent';

    /**
     * Specific method for text color classes (not in EnumTrait).
     */
    public function getTextColor(): string
    {
        return match ($this) {
            self::LOW => 'text-green-500',
            self::MEDIUM => 'text-yellow-500',
            self::HIGH => 'text-orange-500',
            self::CRITICAL => 'text-red-500',
            self::URGENT => 'text-danger',
        };
    }

    /**
     * Specific method for background color classes (not in EnumTrait).
     */
    public function getBgColor(): string
    {
        return match ($this) {
            self::LOW => 'bg-green-500',
            self::MEDIUM => 'bg-yellow-500',
            self::HIGH => 'bg-orange-500',
            self::CRITICAL => 'bg-red-500',
            self::URGENT => 'bg-danger',
        };
    }

    /**
     * Specific method for badge color classes (not in EnumTrait).
     */
    public function getColorClass(): string
    {
        return match ($this) {
            self::LOW => 'badge-success',
            self::MEDIUM => 'badge-warning',
            self::HIGH => 'badge-danger',
            self::CRITICAL => 'badge-danger',
            self::URGENT => 'badge-danger',
        };
    }

    public static function default(): static
    {
        return self::LOW;
    }
}