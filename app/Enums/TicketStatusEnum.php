<?php

/**
 * ---.
 */

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum TicketStatusEnum: string implements HasColor, HasIcon, HasLabel
{
    // case NEW = 'new';
    case PENDING = 'pending';
    case IN_REVIEW = 'in_review';
    case IN_PROGRESS = 'in_progress';
    case ON_HOLD = 'on_hold';
    case RESOLVED = 'resolved';
    case CLOSED = 'closed';
    case REOPENED = 'reopened';
    case OPEN = 'open';

    public function getColor(): string
    {
        return match ($this) {
            // self::NEW => 'yellow',
            self::PENDING => 'yellow',
            self::IN_REVIEW => 'blue',
            self::IN_PROGRESS => 'orange',
            self::ON_HOLD => 'red',
            self::RESOLVED => 'green',
            self::CLOSED => 'gray',
            self::REOPENED => 'pink',
            self::OPEN => 'warning',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            // self::NEW => 'heroicon-o-plus-circle',
            // self::PENDING => 'heroicon-o-plus-circle',
            self::PENDING => 'ui-hourglass',
            self::IN_REVIEW => 'heroicon-o-clock',
            self::IN_PROGRESS => 'heroicon-o-arrow-path',
            self::ON_HOLD => 'heroicon-o-pause',
            self::RESOLVED => 'heroicon-o-check-circle',
            self::CLOSED => 'heroicon-o-x-circle',
            self::REOPENED => 'heroicon-o-arrow-uturn-left',
            self::OPEN => 'heroicon-o-exclamation-circle',
        };
    }

    public function getLabel(): string
    {
        // return __('ticket::enums.'.$this->name.'.label');

        return match ($this) {
            // self::NEW => 'New',
            self::PENDING => 'Pending',
            self::IN_REVIEW => 'In Review',
            self::IN_PROGRESS => 'In Progress',
            self::ON_HOLD => 'On Hold',
            self::RESOLVED => 'Resolved',
            self::CLOSED => 'Closed',
            self::REOPENED => 'Reopened',
            self::OPEN => 'Open',
            // default => 'Unknown',
        };
    }

    public function getColorClass(): string
    {
        return match ($this) {
            self::PENDING => 'badge-warning',
            self::IN_REVIEW => 'badge-info',
            self::IN_PROGRESS => 'badge-info',
            self::ON_HOLD => 'badge-danger',
            self::RESOLVED => 'badge-success',
            self::CLOSED => 'badge-secondary',
            self::REOPENED => 'badge-secondary',
            self::OPEN => 'badge-warning',
        };
    }

    public function label(): string
    {
        return match ($this) {
            self::PENDING => trans('fixcity::ticket.fields.status.options.pending'),
            self::IN_REVIEW => trans('fixcity::ticket.fields.status.options.in_review'),
            self::IN_PROGRESS => trans('fixcity::ticket.fields.status.options.in_progress'),
            self::ON_HOLD => trans('fixcity::ticket.fields.status.options.on_hold'),
            self::RESOLVED => trans('fixcity::ticket.fields.status.options.resolved'),
            self::CLOSED => trans('fixcity::ticket.fields.status.options.closed'),
            self::REOPENED => trans('fixcity::ticket.fields.status.options.reopened'),
            self::OPEN => trans('fixcity::ticket.fields.status.options.open'),
        };
    }

    public static function canViewByAll(): array
    {
        return [
            self::ON_HOLD,
            self::RESOLVED,
            self::CLOSED,
            self::REOPENED,
        ];
    }

    public static function canNoViewByAll(): array
    {
        return [
            self::PENDING,
            self::IN_REVIEW,
            self::IN_PROGRESS,
        ];
    }

    public static function default(): static
    {
        return self::OPEN;
    }

    /*-- NO
    public static function getArrayValueLabelIcon(): array
    {
        $statuses = [];
        foreach (self::cases() as $item) {
            $statuses[$item->value] = [
                'label' => $item->getLabel(),
                'icon' => $item->getIcon(),
                'color' => $item->getColor()
            ];
        }

        return $statuses;
    }
    */
}
