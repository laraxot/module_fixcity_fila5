<?php

declare(strict_types=1);

namespace Modules\Fixcity\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;
use Modules\Xot\Traits\EnumTrait;

enum ReportStatusEnum: string implements HasColor, HasLabel
{
    use EnumTrait;

    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case IN_PROGRESS = 'in_progress';
    case RESOLVED = 'resolved';
    case REJECTED = 'rejected';
}
