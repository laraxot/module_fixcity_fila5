<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

enum ReportStatusEnum: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'In attesa',
            self::VERIFIED => 'Verificato',
            self::IN_PROGRESS => 'In lavorazione',
            self::RESOLVED => 'Risolto',
            self::REJECTED => 'Rifiutato'
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::VERIFIED => 'info',
            self::IN_PROGRESS => 'primary',
            self::RESOLVED => 'success',
            self::REJECTED => 'danger'
        };
    }
}
